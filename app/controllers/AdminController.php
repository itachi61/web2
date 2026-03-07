<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class AdminController extends Controller
{
    public function __construct()
    {
        // Bảo mật: Chỉ Admin mới được vào
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    // Trang Dashboard
    public function index()
    {
        $this->view('admin/dashboard', [
            'view' => 'admin/dashboard_content',
            'active' => 'dashboard'
        ]);
    }

    // Trang quản lý đơn hàng
    public function orders()
    {
        // Lấy đơn hàng từ DB
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->prepare("SELECT o.*, u.fullname, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $orders = [];
        }

        $this->view('admin/dashboard', [
            'view' => 'admin/orders',
            'active' => 'orders',
            'orders' => $orders
        ]);
    }
    // Trang thống kê báo cáo
    public function statistics()
    {
        $this->view('admin/dashboard', [
            'view' => 'admin/statistics',
            'active' => 'statistics'
        ]);
    }

    // Trang danh sách sản phẩm
    public function products()
    {
        $model = $this->model('ProductModel');
        $products = $model->getAllProducts();

        $this->view('admin/dashboard', [
            'view' => 'admin/products/index',
            'active' => 'products',
            'products' => $products
        ]);
    }

    // Trang hiển thị Form thêm sản phẩm (GET)
    public function addProduct()
    {
        $model = $this->model('ProductModel');
        $categories = $model->getAllCategories();

        $this->view('admin/dashboard', [
            'view' => 'admin/products/add',
            'active' => 'products',
            'categories' => $categories
        ]);
    }

    // Hàm xử lý lưu sản phẩm và tạo folder ảnh (POST)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Lấy dữ liệu từ Form
            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $price = $_POST['price'];
            $desc = $_POST['description'];
            $discount = isset($_POST['discount']) ? intval($_POST['discount']) : 0;
            $cost_price = isset($_POST['cost_price']) ? floatval($_POST['cost_price']) : 0;

            // 2. TẠO FOLDER RIÊNG CHO SẢN PHẨM
            $folderName = $this->createSlug($name);
            $targetDir = dirname(__DIR__, 2) . '/public/images/' . $folderName . '/';

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // 3. XỬ LÝ ẢNH ĐẠI DIỆN (MAIN IMAGE)
            $dbImageName = '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $fileName = time() . '_main_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName);
                $dbImageName = $folderName . '/' . $fileName;
            }

            // 4. LƯU SẢN PHẨM VÀO DATABASE
            $model = $this->model('ProductModel');
            $model->insertProduct($name, $category_id, $price, $desc, $dbImageName, $discount, $cost_price);

            $newProductId = $model->getLastId();

            // 5. XỬ LÝ ẢNH PHỤ (EXTRA IMAGES)
            if (isset($_FILES['extra_images'])) {
                $totalFiles = count($_FILES['extra_images']['name']);

                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['extra_images']['error'][$i] == 0) {
                        $extraFileName = time() . '_' . $i . '_' . $_FILES['extra_images']['name'][$i];
                        move_uploaded_file($_FILES['extra_images']['tmp_name'][$i], $targetDir . $extraFileName);
                        $dbExtraPath = $folderName . '/' . $extraFileName;
                        $model->addProductImage($newProductId, $dbExtraPath);
                    }
                }
            }

            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }
    }

    // === Bug 3: Trang chỉnh sửa sản phẩm (GET) ===
    public function editProduct($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $model = $this->model('ProductModel');
        $product = $model->getProductById($id);
        $categories = $model->getAllCategories();
        $images = $model->getProductImages($id);

        if (!$product) {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $this->view('admin/dashboard', [
            'view' => 'admin/products/edit',
            'active' => 'products',
            'product' => $product,
            'categories' => $categories,
            'images' => $images
        ]);
    }

    // === Bug 3: Xử lý cập nhật sản phẩm (POST) ===
    public function updateProduct($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $desc = $_POST['description'];
        $discount = isset($_POST['discount']) ? intval($_POST['discount']) : 0;
        $cost_price = isset($_POST['cost_price']) ? floatval($_POST['cost_price']) : 0;

        $model = $this->model('ProductModel');
        $product = $model->getProductById($id);
        
        $dbImageName = null;

        // Xử lý ảnh mới (nếu có upload)
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $folderName = $this->createSlug($name);
            $targetDir = dirname(__DIR__, 2) . '/public/images/' . $folderName . '/';

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . '_main_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName);
            $dbImageName = $folderName . '/' . $fileName;
        }

        $model->updateProduct($id, $name, $category_id, $price, $desc, $dbImageName, $discount, $cost_price);

        // Xử lý ảnh phụ mới (nếu có)
        if (isset($_FILES['extra_images'])) {
            $folderName = $this->createSlug($name);
            $targetDir = dirname(__DIR__, 2) . '/public/images/' . $folderName . '/';
            
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $totalFiles = count($_FILES['extra_images']['name']);
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES['extra_images']['error'][$i] == 0) {
                    $extraFileName = time() . '_' . $i . '_' . $_FILES['extra_images']['name'][$i];
                    move_uploaded_file($_FILES['extra_images']['tmp_name'][$i], $targetDir . $extraFileName);
                    $dbExtraPath = $folderName . '/' . $extraFileName;
                    $model->addProductImage($id, $dbExtraPath);
                }
            }
        }

        header('Location: ' . BASE_URL . 'admin/products');
        exit;
    }

    // === Bug 4: Hàm xóa sản phẩm (cải tiến với cảnh báo stock) ===
    public function deleteProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('ProductModel');
            $product = $model->getProductById($id);

            if (!$product) {
                header('Location: ' . BASE_URL . 'admin/products');
                exit;
            }

            // Kiểm tra nếu có param force_delete hoặc stock = 0 thì xóa
            $forceDelete = isset($_POST['force_delete']) && $_POST['force_delete'] == '1';

            if ($product['stock'] > 0 && !$forceDelete) {
                // Hiển thị cảnh báo - truyền thông tin stock qua session
                $_SESSION['delete_warning'] = [
                    'product_id' => $id,
                    'product_name' => $product['name'],
                    'stock' => $product['stock']
                ];
                header('Location: ' . BASE_URL . 'admin/products');
                exit;
            }

            // Xóa ảnh và folder trên server
            if ($product['image']) {
                $imgPath = dirname(__DIR__, 2) . '/public/images/' . $product['image'];
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
            }

            $model->deleteProduct($id);
            $_SESSION['success_msg'] = 'Đã xóa sản phẩm "' . $product['name'] . '" thành công!';
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        header('Location: ' . BASE_URL . 'admin/products');
    }

    // === Bug 5: Trang quản lý khách hàng ===
    public function users()
    {
        $model = $this->model('UserModel');
        $users = $model->getAllUsers();

        $this->view('admin/dashboard', [
            'view' => 'admin/users',
            'active' => 'users',
            'users' => $users
        ]);
    }

    // === Bug 5: Khóa/Mở khóa tài khoản ===
    public function lockUser($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }

        $model = $this->model('UserModel');
        $user = $model->getUserById($id);

        if (!$user) {
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }

        // Không cho phép khóa chính mình
        if ($user['id'] == $_SESSION['user_id']) {
            $_SESSION['error_msg'] = 'Không thể khóa tài khoản của chính bạn!';
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }

        $model->toggleUserStatus($id);
        
        $newStatus = ($user['status'] ?? 'active') == 'active' ? 'khóa' : 'mở khóa';
        $_SESSION['success_msg'] = 'Đã ' . $newStatus . ' tài khoản "' . $user['fullname'] . '"!';
        
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    // === TRANG NHẬP HÀNG ===
    public function import() {
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $db->query("SELECT id, name, stock, cost_price, price FROM products ORDER BY name ASC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $db->query("SELECT ih.*, p.name as product_name FROM import_history ih JOIN products p ON ih.product_id = p.id ORDER BY ih.created_at DESC LIMIT 20");
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $products = [];
            $history = [];
        }
        
        $this->view('admin/dashboard', [
            'view' => 'admin/import',
            'active' => 'import',
            'products' => $products,
            'history' => $history
        ]);
    }

    // === XỬ LÝ NHẬP HÀNG WAC ===
    public function processImport() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . BASE_URL . 'admin/import');
            exit;
        }

        $productId = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        $importPrice = floatval($_POST['import_price'] ?? 0);

        if ($productId <= 0 || $quantity <= 0 || $importPrice <= 0) {
            $_SESSION['import_error'] = 'Vui lòng nhập đầy đủ thông tin hợp lệ!';
            header('Location: ' . BASE_URL . 'admin/import');
            exit;
        }

        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->beginTransaction();

            // Lấy thông tin SP hiện tại
            $stmt = $db->prepare("SELECT stock, cost_price, price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại!');
            }

            $oldStock = intval($product['stock']);
            $oldCostPrice = floatval($product['cost_price']);
            $oldPrice = floatval($product['price']);

            // Tính tỷ lệ lợi nhuận hiện tại
            $margin = ($oldCostPrice > 0) ? (($oldPrice / $oldCostPrice) - 1) * 100 : 0;

            // === TÍNH GIÁ NHẬP BQ MỚI (WAC) ===
            // Công thức: (tồn × giá_cũ + SL_mới × giá_mới) / (tồn + SL_mới)
            $newStock = $oldStock + $quantity;
            $newCostPrice = ($oldStock * $oldCostPrice + $quantity * $importPrice) / $newStock;

            // Giá bán mới = Giá nhập BQ × (1 + tỷ lệ LN%)
            $newPrice = $newCostPrice * (1 + $margin / 100);

            // Cập nhật SP
            $stmt = $db->prepare("UPDATE products SET stock = ?, cost_price = ?, price = ? WHERE id = ?");
            $stmt->execute([$newStock, round($newCostPrice, 2), round($newPrice, 2), $productId]);

            // Lưu lịch sử nhập
            $stmt = $db->prepare("INSERT INTO import_history (product_id, quantity, import_price, old_cost_price, new_cost_price, old_stock, new_stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$productId, $quantity, $importPrice, $oldCostPrice, round($newCostPrice, 2), $oldStock, $newStock]);

            $db->commit();

            $_SESSION['import_success'] = "Nhập hàng thành công! Tồn kho: {$oldStock} → {$newStock}, Giá nhập BQ: " . number_format($oldCostPrice, 0, ',', '.') . "đ → " . number_format($newCostPrice, 0, ',', '.') . "đ";
        } catch (Exception $e) {
            if (isset($db)) $db->rollBack();
            $_SESSION['import_error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'admin/import');
        exit;
    }

    // Hàm hỗ trợ tạo tên folder (Slug)
    private function createSlug($str)
    {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
}
?>