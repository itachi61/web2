<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class HomeController extends Controller {

    public function index() {
        // 1. Lấy số trang từ URL (mặc định là trang 1)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Đảm bảo page >= 1
        $perPage = 8; // Số sản phẩm mỗi trang

        // 2. Gọi ProductModel
        $model = $this->model('ProductModel');
        
        // 3. Lấy danh sách sản phẩm với phân trang
        $products = $model->getAllProductsPaginated($page, $perPage);
        
        // 4. Tính toán thông tin phân trang
        $totalProducts = $model->getTotalProductsCount();
        $totalPages = ceil($totalProducts / $perPage);

        // 5. Gửi dữ liệu qua View
        $this->view('layouts/header', ['title' => 'Trang chủ - TechSmart']);
        
        $this->view('home/index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ]);
        
        $this->view('layouts/footer');
    }
    
    /**
     * Set language and redirect back
     */
    public function setLanguage($lang = 'vi') {
        // Validate language
        $allowedLangs = ['vi', 'en'];
        if (!in_array($lang, $allowedLangs)) {
            $lang = 'vi';
        }
        
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set language in session
        $_SESSION['lang'] = $lang;
        
        // Get redirect URL from query string
        $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : BASE_URL;
        
        // Validate redirect URL to prevent open redirect
        if (strpos($redirect, '/') !== 0 && strpos($redirect, BASE_URL) !== 0) {
            $redirect = BASE_URL;
        }
        
        // Redirect back to the original page
        header('Location: ' . $redirect);
        exit;
    }
}
?>