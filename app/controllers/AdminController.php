<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class AdminController extends Controller
{
    public function __construct()
    {
        // Security: Only Admin can access
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    // ========== DASHBOARD ==========
    public function index()
    {
<<<<<<< HEAD
        $orderModel = $this->model('OrderModel');
        $productModel = $this->model('ProductModel');
        
        // Get order stats
        $allOrders = $orderModel->getAllOrders() ?? [];
        $pendingOrders = array_filter($allOrders, fn($o) => ($o['status'] ?? 'pending') === 'pending');
        $processingOrders = array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'processing');
        $completedOrders = array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'completed');
        
        // Calculate monthly revenue
        $monthStart = date('Y-m-01');
        $monthlyRevenue = 0;
        foreach ($completedOrders as $order) {
            if (($order['created_at'] ?? '') >= $monthStart) {
                $monthlyRevenue += $order['total_money'] ?? 0;
            }
        }
        
        // Get recent orders (last 5)
        $recentOrders = array_slice($allOrders, 0, 5);
        
        // Get low stock products
        $products = $productModel->getAllProducts() ?? [];
        $lowStock = array_filter($products, fn($p) => ($p['stock'] ?? 0) < 10);
        $lowStock = array_slice($lowStock, 0, 5);

        $this->view('admin/layout', [
            'view' => 'admin/dashboard_content',
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'pending_orders' => count($pendingOrders),
            'processing_orders' => count($processingOrders),
            'completed_orders' => count($completedOrders),
            'monthly_revenue' => $monthlyRevenue,
            'recent_orders' => $recentOrders,
            'low_stock' => $lowStock
        ]);
    }

    // ========== ORDERS ==========
    public function orders()
    {
        $orderModel = $this->model('OrderModel');
        $filter = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        
        $allOrders = $orderModel->getAllOrders() ?? [];
        
        // Filter by status
        $orders = $filter ? array_filter($allOrders, fn($o) => ($o['status'] ?? 'pending') === $filter) : $allOrders;
        
        // Filter by date range
        if ($dateFrom) {
            $orders = array_filter($orders, fn($o) => date('Y-m-d', strtotime($o['created_at'])) >= $dateFrom);
        }
        if ($dateTo) {
            $orders = array_filter($orders, fn($o) => date('Y-m-d', strtotime($o['created_at'])) <= $dateTo);
        }
        
        // Count by status
        $pendingCount = count(array_filter($allOrders, fn($o) => ($o['status'] ?? 'pending') === 'pending'));
        $processingCount = count(array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'processing'));
        $completedCount = count(array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'completed'));
        $cancelledCount = count(array_filter($allOrders, fn($o) => ($o['status'] ?? '') === 'cancelled'));

        $this->view('admin/layout', [
            'view' => 'admin/orders/index',
            'title' => __('orders'),
            'active' => 'orders',
            'orders' => $orders,
            'filter' => $filter,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_count' => count($allOrders),
            'pending_count' => $pendingCount,
            'processing_count' => $processingCount,
            'completed_count' => $completedCount,
            'cancelled_count' => $cancelledCount
        ]);
    }

    public function orderDetail($id)
    {
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($id);
        $items = $orderModel->getOrderItems($id);

        $this->view('admin/layout', [
            'view' => 'admin/orders/detail',
            'title' => 'Chi tiết đơn hàng #' . $id,
            'active' => 'orders',
            'order' => $order,
            'items' => $items
        ]);
    }

    public function updateOrderStatus($id, $status)
    {
        $orderModel = $this->model('OrderModel');
        
        if ($orderModel->updateOrderStatus($id, $status)) {
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công!';
        } else {
            $_SESSION['error'] = 'Không thể cập nhật trạng thái!';
        }
        
        header('Location: ' . BASE_URL . 'admin/orders');
        exit;
    }

    // ========== USERS ==========
    public function users()
    {
        $userModel = $this->model('UserModel');
        $users = $userModel->getAllUsers() ?? [];

        $this->view('admin/layout', [
            'view' => 'admin/users/index',
            'title' => 'Quản lý người dùng',
            'active' => 'users',
            'users' => $users
        ]);
    }

    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('UserModel');
            
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $phone = trim($_POST['phone'] ?? '');
            $role = $_POST['role'] ?? 'user';

            if ($userModel->register($fullname, $email, $password, $phone)) {
                // Update role if admin
                if ($role === 'admin') {
                    $userModel->updateRole($email, 'admin');
                }
                $_SESSION['success'] = 'Tạo tài khoản thành công!';
            } else {
                $_SESSION['error'] = 'Email đã tồn tại hoặc có lỗi xảy ra!';
            }
        }
        
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    public function lockUser($id)
    {
        $userModel = $this->model('UserModel');
        $userModel->updateStatus($id, 'locked');
        $_SESSION['success'] = 'Đã khóa tài khoản!';
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    public function unlockUser($id)
    {
        $userModel = $this->model('UserModel');
        $userModel->updateStatus($id, 'active');
        $_SESSION['success'] = 'Đã mở khóa tài khoản!';
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    public function resetPassword($id)
    {
        $userModel = $this->model('UserModel');
        $userModel->resetPassword($id, '123456');
        $_SESSION['success'] = 'Đã reset mật khẩu thành 123456!';
        header('Location: ' . BASE_URL . 'admin/users');
        exit;
    }

    // ========== CATEGORIES ==========
    public function categories()
    {
        $categoryModel = $this->model('CategoryModel');
        $categories = $categoryModel->getAllWithProductCount() ?? [];

        $this->view('admin/layout', [
            'view' => 'admin/categories/index',
            'title' => 'Quản lý danh mục',
            'active' => 'categories',
            'categories' => $categories
        ]);
    }

    public function createCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryModel = $this->model('CategoryModel');
            
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = $_POST['icon'] ?? 'fa-folder';

            if ($categoryModel->create($name, $description, $icon)) {
                $_SESSION['success'] = 'Tạo danh mục thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra!';
            }
        }
        
        header('Location: ' . BASE_URL . 'admin/categories');
        exit;
    }

    public function updateCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryModel = $this->model('CategoryModel');
            
            $id = $_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($categoryModel->update($id, $name, $description)) {
                $_SESSION['success'] = 'Cập nhật danh mục thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra!';
            }
        }
        
        header('Location: ' . BASE_URL . 'admin/categories');
        exit;
    }

    public function deleteCategory($id)
    {
        $categoryModel = $this->model('CategoryModel');
        $categoryModel->delete($id);
        $_SESSION['success'] = 'Đã xóa danh mục!';
        header('Location: ' . BASE_URL . 'admin/categories');
        exit;
    }

    // ========== PRODUCTS ==========
=======
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
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
    public function products()
    {
        $model = $this->model('ProductModel');
        $products = $model->getAllProducts();

        $this->view('admin/layout', [
            'view' => 'admin/products/index',
            'title' => 'Quản lý sản phẩm',
            'active' => 'products',
            'products' => $products
        ]);
    }

    public function addProduct()
    {
<<<<<<< HEAD
        $categoryModel = $this->model('CategoryModel');
        $categories = $categoryModel->getAll() ?? [];
=======
        $model = $this->model('ProductModel');
        $categories = $model->getAllCategories();
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f

        $this->view('admin/layout', [
            'view' => 'admin/products/add',
            'title' => 'Thêm sản phẩm',
            'active' => 'products',
            'categories' => $categories
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $price = $_POST['price'];
            $desc = $_POST['description'];
<<<<<<< HEAD
            $stock = $_POST['stock'] ?? 0;
            $profit_margin = $_POST['profit_margin'] ?? 10;

=======
            $discount = isset($_POST['discount']) ? intval($_POST['discount']) : 0;
            $cost_price = isset($_POST['cost_price']) ? floatval($_POST['cost_price']) : 0;

            // 2. TẠO FOLDER RIÊNG CHO SẢN PHẨM
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
            $folderName = $this->createSlug($name);
            $targetDir = dirname(__DIR__, 2) . '/public/images/' . $folderName . '/';

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

<<<<<<< HEAD
            $dbImageName = '';
=======
            // 3. XỬ LÝ ẢNH ĐẠI DIỆN (MAIN IMAGE)
            $dbImageName = '';

>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $fileName = time() . '_main_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName);
                $dbImageName = $folderName . '/' . $fileName;
            }

            $model = $this->model('ProductModel');
<<<<<<< HEAD
            $model->insertProduct($name, $category_id, $price, $desc, $dbImageName, $stock, $profit_margin);
            
=======
            $model->insertProduct($name, $category_id, $price, $desc, $dbImageName, $discount, $cost_price);

>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
            $newProductId = $model->getLastId();

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

<<<<<<< HEAD
            $_SESSION['success'] = 'Thêm sản phẩm thành công!';
=======
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }
    }

<<<<<<< HEAD
    public function deleteProduct($id)
    {
        $model = $this->model('ProductModel');
        $model->deleteProduct($id);
        $_SESSION['success'] = 'Đã xóa sản phẩm!';
        header('Location: ' . BASE_URL . 'admin/products');
    }

    // ========== IMPORTS ==========
    public function imports($action = null, $id = null)
    {
        // Handle sub-routes
        if ($action === 'create') {
            return $this->createImport();
        }
        if ($action === 'edit' && $id) {
            return $this->editImport($id);
        }
        
        $importModel = $this->model('ImportModel');
        $filter = $_GET['status'] ?? '';
        
        $imports = $importModel->getAll($filter);

        $this->view('admin/layout', [
            'view' => 'admin/imports/index',
            'title' => 'Quản lý nhập hàng',
            'active' => 'imports',
            'imports' => $imports,
            'filter' => $filter
        ]);
    }

    public function createImport()
    {
        $productModel = $this->model('ProductModel');
        $products = $productModel->getAllProducts();

        $this->view('admin/layout', [
            'view' => 'admin/imports/create',
            'title' => 'Tạo phiếu nhập',
            'active' => 'imports',
            'products' => $products
        ]);
    }

    public function storeImport()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $importModel = $this->model('ImportModel');
            $productModel = $this->model('ProductModel');
            
            $importCode = $_POST['import_code'] ?? '';
            $supplier = $_POST['supplier'] ?? '';
            $note = $_POST['note'] ?? '';
            $action = $_POST['action'] ?? 'draft';
            $items = $_POST['items'] ?? [];
            
            $status = ($action === 'complete') ? 'completed' : 'draft';
            
            // Calculate total
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += ($item['quantity'] ?? 0) * ($item['price'] ?? 0);
            }
            
            // Create import
            $importId = $importModel->create($importCode, $supplier, $note, $totalAmount, $status, $_SESSION['user_id']);
            
            if ($importId) {
                // Add items
                foreach ($items as $item) {
                    $importModel->addItem($importId, $item['product_id'], $item['quantity'], $item['price']);
                    
                    // If completed, update stock with weighted average
                    if ($status === 'completed') {
                        $product = $productModel->getProductById($item['product_id']);
                        $currentStock = $product['stock'] ?? 0;
                        $currentCostPrice = $product['cost_price'] ?? 0;
                        $newQty = $item['quantity'];
                        $newPrice = $item['price'];
                        
                        // Weighted average formula
                        if ($currentStock + $newQty > 0) {
                            $newCostPrice = ($currentStock * $currentCostPrice + $newQty * $newPrice) / ($currentStock + $newQty);
                        } else {
                            $newCostPrice = $newPrice;
                        }
                        
                        $productModel->updateStock($item['product_id'], $currentStock + $newQty, $newCostPrice);
                    }
                }
                
                $_SESSION['success'] = ($status === 'completed') ? 'Nhập hàng thành công!' : 'Đã lưu phiếu nháp!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra!';
            }
        }
        
        header('Location: ' . BASE_URL . 'admin/imports');
        exit;
    }

    public function completeImport($id)
    {
        $importModel = $this->model('ImportModel');
        $productModel = $this->model('ProductModel');
        
        $import = $importModel->getById($id);
        $items = $importModel->getItems($id);
        
        if ($import && $import['status'] === 'draft') {
            // Update stock for each item
            foreach ($items as $item) {
                $product = $productModel->getProductById($item['product_id']);
                $currentStock = $product['stock'] ?? 0;
                $currentCostPrice = $product['cost_price'] ?? 0;
                $newQty = $item['quantity'];
                $newPrice = $item['import_price'];
                
                // Weighted average
                if ($currentStock + $newQty > 0) {
                    $newCostPrice = ($currentStock * $currentCostPrice + $newQty * $newPrice) / ($currentStock + $newQty);
                } else {
                    $newCostPrice = $newPrice;
                }
                
                $productModel->updateStock($item['product_id'], $currentStock + $newQty, $newCostPrice);
            }
            
            $importModel->complete($id);
            $_SESSION['success'] = 'Hoàn thành nhập hàng!';
        } else {
            $_SESSION['error'] = 'Không thể hoàn thành phiếu nhập!';
        }
        
        header('Location: ' . BASE_URL . 'admin/imports');
        exit;
    }

    public function deleteImport($id)
    {
        $importModel = $this->model('ImportModel');
        $importModel->delete($id);
        $_SESSION['success'] = 'Đã xóa phiếu nhập!';
        header('Location: ' . BASE_URL . 'admin/imports');
        exit;
    }

    // ========== INVENTORY ==========
    public function inventory()
    {
        $productModel = $this->model('ProductModel');
        $categoryModel = $this->model('CategoryModel');
        
        $products = $productModel->getAllWithCategory();
        $categories = $categoryModel->getAll() ?? [];

        $this->view('admin/layout', [
            'view' => 'admin/inventory/index',
            'title' => 'Quản lý tồn kho',
            'active' => 'inventory',
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function updateStock()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = $this->model('ProductModel');
            
            $productId = $_POST['product_id'] ?? 0;
            $stock = $_POST['stock'] ?? 0;
            $profitMargin = $_POST['profit_margin'] ?? 10;
            
            $productModel->updateStockAndMargin($productId, $stock, $profitMargin);
            $_SESSION['success'] = 'Cập nhật thành công!';
        }
        
        header('Location: ' . BASE_URL . 'admin/inventory');
        exit;
    }

    // ========== HELPERS ==========
=======
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

    // Hàm hỗ trợ tạo tên folder (Slug)
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
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