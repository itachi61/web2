<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class HomeController extends Controller {

    public function index() {
        // 1. Gọi ProductModel
        $model = $this->model('ProductModel');
        
        // 2. Lấy danh sách sản phẩm (Mới nhất hoặc tất cả)
        $products = $model->getAllProducts(); 

        // 3. Gửi dữ liệu qua View
        $this->view('layouts/header', ['title' => 'Trang chủ - TechSmart']);
        
        $this->view('home/index', [
            'products' => $products // Truyền danh sách sản phẩm (số nhiều)
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