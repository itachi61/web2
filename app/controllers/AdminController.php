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
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { header('Location: ' . BASE_URL . 'admin/categories'); exit; }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if (!$name) { $_SESSION['error_msg'] = 'Tên danh mục không được trống!'; header('Location: ' . BASE_URL . 'admin/categories'); exit; }

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $slug = $this->createSlug($name);
        $db->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)")->execute([$name, $slug, $description]);
        $_SESSION['success_msg'] = 'Đã thêm danh mục "' . $name . '"!';
        header('Location: ' . BASE_URL . 'admin/categories');
        exit;
    }

    public function updateCategory($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] != 'POST') { header('Location: ' . BASE_URL . 'admin/categories'); exit; }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if (!$name) { $_SESSION['error_msg'] = 'Tên danh mục không được trống!'; header('Location: ' . BASE_URL . 'admin/categories'); exit; }

        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $slug = $this->createSlug($name);
        $db->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?")->execute([$name, $slug, $description, $id]);
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

        // Lấy lịch sử nhập hàng (giá vốn, %LN, giá bán theo lô)
        $importBatches = [];
        if (($selectedProductId ?? 0) > 0) {
            try {
                $stmt = $db->prepare("SELECT ih.*, p.profit_margin, p.price as current_price 
                    FROM import_history ih 
                    JOIN products p ON ih.product_id = p.id
                    WHERE ih.product_id = ? ORDER BY ih.import_date DESC");
                $stmt->execute([$selectedProductId]);
                $importBatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) { $importBatches = []; }
        }
        
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