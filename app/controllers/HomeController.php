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
}
?>