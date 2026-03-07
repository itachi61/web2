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
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->query("SELECT o.*, u.fullname, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Load order items for each order
            $stmtItems = $db->prepare("SELECT oi.*, p.name as product_name, p.image as product_image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            foreach ($orders as &$order) {
                $stmtItems->execute([$order['id']]);
                $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($order);
        } catch (Exception $e) {
            $orders = [];
        }

        $this->view('admin/dashboard', [
            'view' => 'admin/orders',
            'active' => 'orders',
            'orders' => $orders
        ]);
    }

    // === CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG ===
    public function updateOrderStatus($id = null, $status = null) {
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        if (!$id || !$status || !in_array($status, $validStatuses)) {
            header('Location: ' . BASE_URL . 'admin/orders');
            exit;
        }
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            $_SESSION['success_msg'] = 'Đã cập nhật trạng thái đơn hàng #' . $id;
        } catch (Exception $e) {
            $_SESSION['error_msg'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: ' . BASE_URL . 'admin/orders');
        exit;
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
        $profit_margin = isset($_POST['profit_margin']) ? floatval($_POST['profit_margin']) : 0;

        // Auto-calculate price from cost and margin
        if ($cost_price > 0 && $profit_margin > 0) {
            $price = round($cost_price * (1 + $profit_margin / 100));
        }

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

        $model->updateProduct($id, $name, $category_id, $price, $desc, $dbImageName, $discount, $cost_price, $profit_margin);

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
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $db->query("SELECT id, name, stock, cost_price, price, profit_margin FROM products ORDER BY name ASC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build filter query
            $where = [];
            $params = [];
            if (!empty($_GET['product_id'])) {
                $where[] = 'ih.product_id = ?';
                $params[] = intval($_GET['product_id']);
            }
            if (!empty($_GET['date_from'])) {
                $where[] = 'ih.created_at >= ?';
                $params[] = $_GET['date_from'] . ' 00:00:00';
            }
            if (!empty($_GET['date_to'])) {
                $where[] = 'ih.created_at <= ?';
                $params[] = $_GET['date_to'] . ' 23:59:59';
            }
            $whereSQL = $where ? ' WHERE ' . implode(' AND ', $where) : '';
            
            $stmt = $db->prepare("SELECT ih.*, p.name as product_name FROM import_history ih JOIN products p ON ih.product_id = p.id" . $whereSQL . " ORDER BY ih.created_at DESC LIMIT 50");
            $stmt->execute($params);
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
            $stmt = $db->prepare("SELECT stock, cost_price, price, profit_margin FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại!');
            }

            $oldStock = intval($product['stock']);
            $oldCostPrice = floatval($product['cost_price']);
            $oldPrice = floatval($product['price']);
            $margin = floatval($product['profit_margin']);
            if ($margin <= 0 && $oldCostPrice > 0) {
                $margin = (($oldPrice / $oldCostPrice) - 1) * 100;
            }

            // === TÍNH GIÁ NHẬP BQ MỚI (WAC) ===
            $newStock = $oldStock + $quantity;
            $newCostPrice = ($oldStock * $oldCostPrice + $quantity * $importPrice) / $newStock;
            $newPrice = $newCostPrice * (1 + $margin / 100);

            // Cập nhật SP
            $stmt = $db->prepare("UPDATE products SET stock = ?, cost_price = ?, price = ?, profit_margin = ? WHERE id = ?");
            $stmt->execute([$newStock, round($newCostPrice, 2), round($newPrice, 2), round($margin, 2), $productId]);

            // Lưu lịch sử nhập (với giá bán + LN%)
            $stmt = $db->prepare("INSERT INTO import_history (product_id, quantity, import_price, old_cost_price, new_cost_price, old_stock, new_stock, selling_price, profit_margin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$productId, $quantity, $importPrice, $oldCostPrice, round($newCostPrice, 2), $oldStock, $newStock, round($newPrice, 2), round($margin, 2)]);

            // Log stock history
            $importId = $db->lastInsertId();
            $stmt = $db->prepare("INSERT INTO stock_history (product_id, stock_before, stock_after, change_qty, change_type, reference_id) VALUES (?, ?, ?, ?, 'import', ?)");
            $stmt->execute([$productId, $oldStock, $newStock, $quantity, $importId]);

            $db->commit();

            $_SESSION['import_success'] = "Nhập hàng thành công! Tồn kho: {$oldStock} → {$newStock}, Giá nhập BQ: " . number_format($oldCostPrice, 0, ',', '.') . "đ → " . number_format($newCostPrice, 0, ',', '.') . "đ";
        } catch (Exception $e) {
            if (isset($db)) $db->rollBack();
            $_SESSION['import_error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . 'admin/import');
        exit;
    }

    // === TRA CỨU TỒN KHO ===
    public function stockHistory() {
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $products = $db->query("SELECT id, name, stock FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
            
            $history = [];
            $stockAtDate = null;
            $selectedProduct = null;
            $selectedDate = $_GET['date'] ?? '';
            $selectedProductId = intval($_GET['product_id'] ?? 0);
            
            if ($selectedProductId > 0) {
                $stmt = $db->prepare("SELECT sh.*, p.name as product_name FROM stock_history sh JOIN products p ON sh.product_id = p.id WHERE sh.product_id = ? ORDER BY sh.created_at DESC LIMIT 50");
                $stmt->execute([$selectedProductId]);
                $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get product info
                $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$selectedProductId]);
                $selectedProduct = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Calculate stock at specific date
                if ($selectedDate && $selectedProduct) {
                    $currentStock = intval($selectedProduct['stock']);
                    // Get all changes AFTER the selected date
                    $stmt = $db->prepare("SELECT change_qty, change_type FROM stock_history WHERE product_id = ? AND created_at > ? ORDER BY created_at ASC");
                    $stmt->execute([$selectedProductId, $selectedDate . ' 23:59:59']);
                    $changes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $stockAtDate = $currentStock;
                    foreach ($changes as $c) {
                        if ($c['change_type'] == 'import') {
                            $stockAtDate -= $c['change_qty'];
                        } else {
                            $stockAtDate += $c['change_qty'];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $products = [];
            $history = [];
        }
        
        $this->view('admin/dashboard', [
            'view' => 'admin/stock_history',
            'active' => 'stock',
            'products' => $products,
            'history' => $history,
            'stockAtDate' => $stockAtDate,
            'selectedProduct' => $selectedProduct,
            'selectedDate' => $selectedDate,
            'selectedProductId' => $selectedProductId ?? 0
        ]);
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