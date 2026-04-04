<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class AdminController extends Controller
{
    // Các method không cần đăng nhập
    private $publicMethods = ['login', 'processLogin'];

    public function __construct()
    {
        // Cho phép truy cập login mà không cần auth
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        foreach ($this->publicMethods as $m) {
            if (strpos($uri, $m) !== false) return;
        }
        // Bảo mật: Chỉ Admin mới được vào
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            header('Location: ' . BASE_URL . 'admin/login');
            exit;
        }
    }

    // Trang đăng nhập admin riêng
    public function login()
    {
        // Nếu đã đăng nhập admin, vào dashboard
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $this->view('admin/login', ['error' => $_SESSION['admin_login_error'] ?? null]);
        unset($_SESSION['admin_login_error']);
    }

    // Xử lý đăng nhập admin
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . BASE_URL . 'admin/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['admin_login_error'] = 'Email hoặc mật khẩu không đúng!';
            header('Location: ' . BASE_URL . 'admin/login');
            exit;
        }

        if ($user['role'] != 'admin') {
            $_SESSION['admin_login_error'] = 'Tài khoản không có quyền quản trị!';
            header('Location: ' . BASE_URL . 'admin/login');
            exit;
        }

        if (($user['status'] ?? 'active') == 'locked') {
            $_SESSION['admin_login_error'] = 'Tài khoản đã bị khóa!';
            header('Location: ' . BASE_URL . 'admin/login');
            exit;
        }

        // Đăng nhập thành công
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        header('Location: ' . BASE_URL . 'admin');
        exit;
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
        // Lấy bộ lọc từ GET
        $dateFrom = $_GET['from'] ?? '';
        $dateTo = $_GET['to'] ?? '';
        $filterStatus = $_GET['status'] ?? '';
        $sortBy = $_GET['sort'] ?? 'newest';

        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT o.*, u.fullname, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE 1=1";
            $params = [];

            if ($dateFrom) { $sql .= " AND o.created_at >= ?"; $params[] = $dateFrom . ' 00:00:00'; }
            if ($dateTo) { $sql .= " AND o.created_at <= ?"; $params[] = $dateTo . ' 23:59:59'; }
            if ($filterStatus) { $sql .= " AND o.status = ?"; $params[] = $filterStatus; }

            switch ($sortBy) {
                case 'address': $sql .= " ORDER BY o.address ASC, o.id DESC"; break;
                case 'oldest': $sql .= " ORDER BY o.created_at ASC"; break;
                case 'total_desc': $sql .= " ORDER BY o.total_money DESC"; break;
                default: $sql .= " ORDER BY o.id DESC";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
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
            'orders' => $orders,
            'filters' => ['from' => $dateFrom, 'to' => $dateTo, 'status' => $filterStatus, 'sort' => $sortBy]
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
            
            // Lấy trạng thái hiện tại
            $stmt = $db->prepare("SELECT status FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            $oldStatus = $stmt->fetchColumn();
            
            // Cập nhật trạng thái
            $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            
            // Lấy danh sách sản phẩm trong đơn hàng
            $stmt = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nếu chuyển sang "completed" (và trước đó chưa completed) → chỉ tăng sold_count
            // (stock đã trừ khi đặt hàng trong CheckoutController)
            if ($status === 'completed' && $oldStatus !== 'completed') {
                foreach ($items as $item) {
                    $db->prepare("UPDATE products SET sold_count = sold_count + ? WHERE id = ?")
                       ->execute([$item['quantity'], $item['product_id']]);
                }
            }
            
            // Nếu hủy đơn → hoàn lại stock (vì stock đã bị trừ khi đặt hàng)
            if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($items as $item) {
                    $db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?")
                       ->execute([$item['quantity'], $item['product_id']]);
                    
                    // Nếu trước đó là completed → cũng hoàn sold_count
                    if ($oldStatus === 'completed') {
                        $db->prepare("UPDATE products SET sold_count = GREATEST(0, sold_count - ?) WHERE id = ?")
                           ->execute([$item['quantity'], $item['product_id']]);
                    }
                }
            }
            
            // Nếu mở lại đơn đã hủy → trừ lại stock
            if ($oldStatus === 'cancelled' && $status !== 'cancelled') {
                foreach ($items as $item) {
                    $db->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?")
                       ->execute([$item['quantity'], $item['product_id']]);
                }
            }
            
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

    public function importReport()
    {
        $this->view('admin/dashboard', [
            'view' => 'admin/import_report',
            'active' => 'importReport'
        ]);
    }

    // Trang danh sách sản phẩm
    public function products()
    {
        $model = $this->model('ProductModel');
        $catFilter = $_GET['cat'] ?? '';
        if ($catFilter) {
            $products = $model->getProductsByCategory($catFilter);
        } else {
            $products = $model->getAllProducts();
        }

        // Load danh mục cho filter
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $cats = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { $cats = []; }

        $this->view('admin/dashboard', [
            'view' => 'admin/products/index',
            'active' => 'products',
            'products' => $products,
            'categories' => $cats,
            'catFilter' => $catFilter
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
            $desc = $_POST['description'] ?? '';
            $unit = trim($_POST['unit'] ?? 'Cái');
            
            // Mặc định: giá=0, tồn=0, ẩn (chưa mở bán)
            $price = 0;
            $discount = 0;
            $cost_price = 0;
            $is_hidden = 1;

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

            // 4. LƯU SẢN PHẨM VÀO DATABASE (mặc định ẩn, giá=0, tồn=0)
            $model = $this->model('ProductModel');
            $model->insertProduct($name, $category_id, $price, $desc, $dbImageName, $discount, $cost_price, $is_hidden, $unit);

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

            $_SESSION['success_msg'] = 'Đã thêm sản phẩm "' . $name . '" (chưa mở bán). Hãy chỉnh giá và nhập hàng để mở bán.';
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

        // Load import price history
        $importHistory = [];
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->prepare("SELECT iri.quantity, iri.import_price, ir.created_at, ir.receipt_code 
                                  FROM import_receipt_items iri 
                                  JOIN import_receipts ir ON iri.receipt_id = ir.id 
                                  WHERE iri.product_id = ? AND ir.status = 'completed' 
                                  ORDER BY ir.created_at DESC LIMIT 10");
            $stmt->execute([$id]);
            $importHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {}

        $this->view('admin/dashboard', [
            'view' => 'admin/products/edit',
            'active' => 'products',
            'product' => $product,
            'categories' => $categories,
            'images' => $images,
            'importHistory' => $importHistory
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

        // Bidirectional: margin↔price
        if ($cost_price > 0 && $profit_margin > 0) {
            $price = round($cost_price * (1 + $profit_margin / 100));
        } elseif ($cost_price > 0 && $price > 0) {
            $profit_margin = round((($price / $cost_price) - 1) * 100, 2);
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

        // Cập nhật lại giá bán trong lịch sử nhập theo margin mới
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->prepare("UPDATE import_history SET selling_price = new_cost_price * (1 + ? / 100), profit_margin = ? WHERE product_id = ?")
               ->execute([$profit_margin, $profit_margin, $id]);
        } catch (Exception $e) {}

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

    // Xóa sản phẩm: nếu chưa nhập hàng → xóa hẳn, nếu đã nhập → ẩn SP
    public function deleteProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model('ProductModel');
            $product = $model->getProductById($id);

            if (!$product) {
                header('Location: ' . BASE_URL . 'admin/products');
                exit;
            }

            $forceDelete = isset($_POST['force_delete']) && $_POST['force_delete'] == '1';

            if ($product['stock'] > 0 && !$forceDelete) {
                $_SESSION['delete_warning'] = [
                    'product_id' => $id,
                    'product_name' => $product['name'],
                    'stock' => $product['stock']
                ];
                header('Location: ' . BASE_URL . 'admin/products');
                exit;
            }

            // Kiểm tra sản phẩm đã từng nhập hàng chưa
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $stmt = $db->prepare("SELECT COUNT(*) FROM import_history WHERE product_id = ?");
            $stmt->execute([$id]);
            $hasImportHistory = $stmt->fetchColumn() > 0;

            if ($hasImportHistory) {
                // Đã nhập hàng → ẩn sản phẩm (soft delete)
                $db->prepare("UPDATE products SET is_hidden = 1 WHERE id = ?")->execute([$id]);
                $_SESSION['success_msg'] = 'Sản phẩm "' . $product['name'] . '" đã được ẩn khỏi website (vẫn giữ lịch sử nhập hàng).';
            } else {
                // Chưa nhập hàng → xóa hẳn
                if ($product['image']) {
                    $imgPath = dirname(__DIR__, 2) . '/public/images/' . $product['image'];
                    if (file_exists($imgPath)) unlink($imgPath);
                }
                $model->deleteProduct($id);
                $_SESSION['success_msg'] = 'Đã xóa hẳn sản phẩm "' . $product['name'] . '" khỏi CSDL.';
            }

            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }

        header('Location: ' . BASE_URL . 'admin/products');
    }

    // Mở bán lại sản phẩm đã ẩn
    public function restoreProduct($id = null)
    {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $stmt = $db->prepare("SELECT name FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $db->prepare("UPDATE products SET is_hidden = 0 WHERE id = ?")->execute([$id]);
            $_SESSION['success_msg'] = 'Sản phẩm "' . ($product['name'] ?? '') . '" đã được mở bán lại!';
        }
        header('Location: ' . BASE_URL . 'admin/products');
        exit;
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

    // Thêm tài khoản mới
    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '123456';
        $role = $_POST['role'] ?? 'customer';

        if (!$fullname || !$email) {
            $_SESSION['error_msg'] = 'Vui lòng nhập đầy đủ thông tin!';
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        // Check email trùng
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $_SESSION['error_msg'] = 'Email "' . $email . '" đã tồn tại!';
            header('Location: ' . BASE_URL . 'admin/users');
            exit;
        }

        $stmt = $db->prepare("INSERT INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$fullname, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        $_SESSION['success_msg'] = 'Đã tạo tài khoản "' . $fullname . '" thành công! Mật khẩu: ' . $password;
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    // Khởi tạo lại mật khẩu
    public function resetPassword($id = null)
    {
        if (!$id) { header('Location: ' . BASE_URL . 'admin/users'); exit; }

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $stmt = $db->prepare("SELECT fullname FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) { header('Location: ' . BASE_URL . 'admin/users'); exit; }

        $newPw = '123456';
        $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($newPw, PASSWORD_DEFAULT), $id]);
        $_SESSION['success_msg'] = 'Đã reset mật khẩu của "' . $user['fullname'] . '" thành: <strong>' . $newPw . '</strong>';
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    // === QUẢN LÝ DANH MỤC ===
    public function categories()
    {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $cats = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/dashboard', [
            'view' => 'admin/categories',
            'active' => 'categories',
            'categories' => $cats
        ]);
    }

    public function addCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { 
            header('Location: ' . BASE_URL . 'admin/categories'); 
            exit; 
        }
        
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (!$name) { 
            $_SESSION['error_msg'] = 'Tên danh mục không được trống!'; 
            header('Location: ' . BASE_URL . 'admin/categories'); 
            exit; 
        }

        // Tự động dò icon dựa trên tên danh mục (không cần lấy từ form)
        $icon = $this->detectCategoryIcon($name);

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $slug = $this->createSlug($name);
        
        // Lưu name, slug, description và icon tự động vào DB
        $db->prepare("INSERT INTO categories (name, slug, description, icon) VALUES (?, ?, ?, ?)")->execute([$name, $slug, $description, $icon]);
        
        $_SESSION['success_msg'] = 'Đã thêm danh mục "' . $name . '"!';
        header('Location: ' . BASE_URL . 'admin/categories');
        exit;
    }

    /**
     * Hàm tự động nhận diện icon dựa vào Tên danh mục
     */
    private function detectCategoryIcon($categoryName) 
    {
        $name = mb_strtolower(trim($categoryName), 'UTF-8');
        
        if (strpos($name, 'laptop') !== false || strpos($name, 'máy tính') !== false || strpos($name, 'pc') !== false) return 'fa-laptop';
        if (strpos($name, 'điện thoại') !== false || strpos($name, 'phone') !== false || strpos($name, 'smartphone') !== false) return 'fa-mobile-screen-button';
        if (strpos($name, 'linh kiện') !== false || strpos($name, 'chip') !== false || strpos($name, 'ram') !== false) return 'fa-microchip';
        if (strpos($name, 'tai nghe') !== false || strpos($name, 'audio') !== false || strpos($name, 'loa') !== false) return 'fa-headphones';
        if (strpos($name, 'đồng hồ') !== false || strpos($name, 'watch') !== false) return 'fa-stopwatch';
        if (strpos($name, 'màn hình') !== false || strpos($name, 'monitor') !== false) return 'fa-desktop';
        if (strpos($name, 'bàn phím') !== false || strpos($name, 'keyboard') !== false) return 'fa-keyboard';
        if (strpos($name, 'chuột') !== false || strpos($name, 'mouse') !== false) return 'fa-computer-mouse';
        if (strpos($name, 'phụ kiện') !== false || strpos($name, 'sạc') !== false || strpos($name, 'cáp') !== false) return 'fa-plug';
        if (strpos($name, 'kỹ thuật số') !== false || strpos($name, 'phần mềm') !== false || strpos($name, 'tài khoản') !== false || strpos($name, 'key') !== false) return 'fa-cloud-arrow-down';
        
        // Trả về icon thư mục mặc định nếu không có từ khoá nào khớp
        return 'fa-folder';
    }

    public function updateCategory($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] != 'POST') { 
            header('Location: ' . BASE_URL . 'admin/categories'); 
            exit; 
        }
        
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (!$name) { 
            $_SESSION['error_msg'] = 'Tên danh mục không được trống!'; 
            header('Location: ' . BASE_URL . 'admin/categories'); 
            exit; 
        }

        // Tự động dò lại icon trong trường hợp admin đổi tên danh mục
        $icon = $this->detectCategoryIcon($name);

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $slug = $this->createSlug($name);
        
        // Cập nhật thêm cột icon vào database
        $db->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, icon = ? WHERE id = ?")
           ->execute([$name, $slug, $description, $icon, $id]);
           
        $_SESSION['success_msg'] = 'Đã cập nhật danh mục!';
        header('Location: ' . BASE_URL . 'admin/categories');
        exit;
    }
    public function deleteCategory($id = null)
    {
        if (!$id) { header('Location: ' . BASE_URL . 'admin/categories'); exit; }
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        // Check xem có SP nào trong danh mục không
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $cnt = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
        if ($cnt > 0) {
            $_SESSION['error_msg'] = 'Không thể xóa! Danh mục còn ' . $cnt . ' sản phẩm.';
        } else {
            $db->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
            $_SESSION['success_msg'] = 'Đã xóa danh mục!';
        }
        header('Location: ' . BASE_URL . 'admin/categories');
        exit;
    }

    // === TRANG NHẬP HÀNG (PHIẾU NHẬP) ===
    public function import() {
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $products = $db->query("SELECT id, name, stock, cost_price, price, profit_margin FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

            // Lấy danh sách phiếu nhập
            $receipts = $db->query("SELECT ir.*, u.fullname as created_by_name,
                (SELECT COUNT(*) FROM import_receipt_items WHERE receipt_id = ir.id) as item_count
                FROM import_receipts ir 
                LEFT JOIN users u ON ir.created_by = u.id 
                ORDER BY ir.created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            $products = [];
            $receipts = [];
        }
        
        $this->view('admin/dashboard', [
            'view' => 'admin/import',
            'active' => 'import',
            'products' => $products,
            'receipts' => $receipts
        ]);
    }

    // Tạo phiếu nhập mới (draft)
    public function createReceipt() {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $code = 'PN-' . date('YmdHis');
        $note = trim($_POST['note'] ?? '');
        $db->prepare("INSERT INTO import_receipts (receipt_code, status, note, created_by) VALUES (?, 'draft', ?, ?)")
           ->execute([$code, $note, $_SESSION['user_id']]);
        $id = $db->lastInsertId();
        header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $id);
        exit;
    }

    // Xem chi tiết phiếu nhập
    public function viewReceipt($id = null) {
        if (!$id) { header('Location: ' . BASE_URL . 'admin/import'); exit; }
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $receipt = $db->prepare("SELECT ir.*, u.fullname as created_by_name FROM import_receipts ir LEFT JOIN users u ON ir.created_by = u.id WHERE ir.id = ?");
            $receipt->execute([$id]);
            $receipt = $receipt->fetch(PDO::FETCH_ASSOC);
            if (!$receipt) { header('Location: ' . BASE_URL . 'admin/import'); exit; }

            $items = $db->prepare("SELECT iri.*, p.name as product_name, p.stock, p.cost_price, p.image FROM import_receipt_items iri JOIN products p ON iri.product_id = p.id WHERE iri.receipt_id = ?");
            $items->execute([$id]);
            $items = $items->fetchAll(PDO::FETCH_ASSOC);

            $products = $db->query("SELECT id, name, stock, cost_price, price, profit_margin FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            header('Location: ' . BASE_URL . 'admin/import'); exit;
        }

        $this->view('admin/dashboard', [
            'view' => 'admin/receipt_detail',
            'active' => 'import',
            'receipt' => $receipt,
            'items' => $items,
            'products' => $products
        ]);
    }

    // Thêm SP vào phiếu nhập
    public function addReceiptItem($receiptId = null) {
        if (!$receiptId || $_SERVER['REQUEST_METHOD'] != 'POST') { header('Location: ' . BASE_URL . 'admin/import'); exit; }
        $productId = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        $importPrice = floatval($_POST['import_price'] ?? 0);

        if ($productId <= 0 || $quantity <= 0 || $importPrice <= 0) {
            $_SESSION['import_error'] = 'Vui lòng nhập đầy đủ thông tin!';
            header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $receiptId); exit;
        }

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        // Check receipt is draft
        $r = $db->prepare("SELECT status FROM import_receipts WHERE id = ?");
        $r->execute([$receiptId]);
        $receipt = $r->fetch(PDO::FETCH_ASSOC);
        if (!$receipt || $receipt['status'] != 'draft') {
            $_SESSION['import_error'] = 'Phiếu đã hoàn thành, không thể sửa!';
            header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $receiptId); exit;
        }

        $db->prepare("INSERT INTO import_receipt_items (receipt_id, product_id, quantity, import_price) VALUES (?, ?, ?, ?)")
           ->execute([$receiptId, $productId, $quantity, $importPrice]);

        // Update totals
        $this->updateReceiptTotals($db, $receiptId);
        $_SESSION['import_success'] = 'Đã thêm sản phẩm vào phiếu!';
        header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $receiptId);
        exit;
    }

    // Xóa SP khỏi phiếu
    public function removeReceiptItem($itemId = null) {
        if (!$itemId) { header('Location: ' . BASE_URL . 'admin/import'); exit; }
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $stmt = $db->prepare("SELECT iri.receipt_id, ir.status FROM import_receipt_items iri JOIN import_receipts ir ON iri.receipt_id = ir.id WHERE iri.id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item || $item['status'] != 'draft') { header('Location: ' . BASE_URL . 'admin/import'); exit; }

        $receiptId = $item['receipt_id'];
        $db->prepare("DELETE FROM import_receipt_items WHERE id = ?")->execute([$itemId]);
        $this->updateReceiptTotals($db, $receiptId);
        $_SESSION['import_success'] = 'Đã xóa sản phẩm khỏi phiếu!';
        header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $receiptId);
        exit;
    }

    // Sửa item trong phiếu
    public function updateReceiptItem() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { header('Location: ' . BASE_URL . 'admin/import'); exit; }
        $itemId = intval($_POST['item_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        $importPrice = floatval($_POST['import_price'] ?? 0);
        if (!$itemId || $quantity <= 0 || $importPrice <= 0) { header('Location: ' . BASE_URL . 'admin/import'); exit; }

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $stmt = $db->prepare("SELECT iri.receipt_id, ir.status FROM import_receipt_items iri JOIN import_receipts ir ON iri.receipt_id = ir.id WHERE iri.id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item || $item['status'] != 'draft') { header('Location: ' . BASE_URL . 'admin/import'); exit; }

        $db->prepare("UPDATE import_receipt_items SET quantity = ?, import_price = ? WHERE id = ?")->execute([$quantity, $importPrice, $itemId]);
        $this->updateReceiptTotals($db, $item['receipt_id']);
        $_SESSION['import_success'] = 'Đã cập nhật!';
        header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $item['receipt_id']);
        exit;
    }

    // Cập nhật ngày nhập hàng
    public function updateReceiptDate($id = null) {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/import'); exit;
        }
        $importDate = $_POST['import_date'] ?? '';
        if ($importDate) {
            try {
                $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $stmt = $db->prepare("UPDATE import_receipts SET created_at = ? WHERE id = ? AND status = 'draft'");
                $stmt->execute([$importDate, $id]);
                $_SESSION['import_success'] = 'Đã cập nhật ngày nhập hàng.';
            } catch (Exception $e) {
                $_SESSION['import_error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $id);
        exit;
    }

    // Hoàn thành phiếu nhập → áp dụng WAC cho từng SP
    public function completeReceipt($id = null) {
        if (!$id) { header('Location: ' . BASE_URL . 'admin/import'); exit; }
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->beginTransaction();

            $r = $db->prepare("SELECT * FROM import_receipts WHERE id = ? AND status = 'draft'");
            $r->execute([$id]);
            $receipt = $r->fetch(PDO::FETCH_ASSOC);
            if (!$receipt) { throw new Exception('Phiếu không tồn tại hoặc đã hoàn thành!'); }

            $items = $db->prepare("SELECT * FROM import_receipt_items WHERE receipt_id = ?");
            $items->execute([$id]);
            $items = $items->fetchAll(PDO::FETCH_ASSOC);
            if (empty($items)) { throw new Exception('Phiếu chưa có sản phẩm!'); }

            foreach ($items as $item) {
                // Lấy thông tin SP
                $stmt = $db->prepare("SELECT stock, cost_price, price, profit_margin FROM products WHERE id = ?");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$product) continue;

                $oldStock = intval($product['stock']);
                $oldCost = floatval($product['cost_price']);
                $margin = floatval($product['profit_margin']);
                if ($margin <= 0 && $oldCost > 0) {
                    $margin = (($product['price'] / $oldCost) - 1) * 100;
                }

                // WAC
                $qty = $item['quantity'];
                $price = $item['import_price'];
                $newStock = $oldStock + $qty;
                $newCost = ($oldStock * $oldCost + $qty * $price) / $newStock;
                $newPrice = $newCost * (1 + $margin / 100);

                // Update product
                $db->prepare("UPDATE products SET stock = ?, cost_price = ?, price = ?, profit_margin = ? WHERE id = ?")
                   ->execute([$newStock, round($newCost, 2), round($newPrice, 2), round($margin, 2), $item['product_id']]);

                // Log import_history
                $db->prepare("INSERT INTO import_history (product_id, quantity, import_price, old_cost_price, new_cost_price, old_stock, new_stock, selling_price, profit_margin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")
                   ->execute([$item['product_id'], $qty, $price, $oldCost, round($newCost, 2), $oldStock, $newStock, round($newPrice, 2), round($margin, 2)]);
                
                $importId = $db->lastInsertId();
                $db->prepare("INSERT INTO stock_history (product_id, stock_before, stock_after, change_qty, change_type, reference_id) VALUES (?, ?, ?, ?, 'import', ?)")
                   ->execute([$item['product_id'], $oldStock, $newStock, $qty, $importId]);
            }

            // Đánh dấu hoàn thành
            $db->prepare("UPDATE import_receipts SET status = 'completed', completed_at = NOW() WHERE id = ?")->execute([$id]);
            $db->commit();
            $_SESSION['import_success'] = 'Đã hoàn thành phiếu nhập ' . $receipt['receipt_code'] . '! Tồn kho & giá đã được cập nhật.';
        } catch (Exception $e) {
            if (isset($db)) $db->rollBack();
            $_SESSION['import_error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: ' . BASE_URL . 'admin/viewReceipt/' . $id); exit;
        }
        header('Location: ' . BASE_URL . 'admin/import');
        exit;
    }

    // Helper: cập nhật tổng phiếu
    private function updateReceiptTotals($db, $receiptId) {
        $stmt = $db->prepare("SELECT COUNT(*) as cnt, COALESCE(SUM(quantity * import_price), 0) as total FROM import_receipt_items WHERE receipt_id = ?");
        $stmt->execute([$receiptId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->prepare("UPDATE import_receipts SET total_items = ?, total_amount = ? WHERE id = ?")->execute([$r['cnt'], $r['total'], $receiptId]);
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

            // Lấy lịch sử nhập hàng (giá vốn, %LN, giá bán theo lô)
            if ($selectedProductId > 0) {
                try {
                    $stmt = $db->prepare("SELECT ih.*, p.profit_margin, p.price as current_price 
                        FROM import_history ih 
                        JOIN products p ON ih.product_id = p.id
                        WHERE ih.product_id = ? ORDER BY ih.created_at DESC");
                    $stmt->execute([$selectedProductId]);
                    $importBatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(Exception $e2) { $importBatches = []; }
            }
        } catch (Exception $e) {
            $products = [];
            $history = [];
        }

        if (!isset($importBatches)) $importBatches = [];
        
        $this->view('admin/dashboard', [
            'view' => 'admin/stock_history',
            'active' => 'stock',
            'products' => $products,
            'history' => $history,
            'import_batches' => $importBatches,
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