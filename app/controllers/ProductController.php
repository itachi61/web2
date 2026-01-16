<?php
// Sửa lại đường dẫn require cho chuẩn (tránh lỗi No such file)
require_once dirname(__DIR__) . '/core/Controller.php';

class ProductController extends Controller {

    // --- TRANG DANH SÁCH TẤT CẢ SẢN PHẨM ---
    public function index() {
        $model = $this->model('ProductModel');
        $products = $model->getAllProducts(); 
        
        $this->view('layouts/header', ['title' => 'Danh sách sản phẩm']);
        
        // LƯU Ý: Bạn đang dùng chung view 'products/category' cho cả trang chủ
        // Hãy chắc chắn file app/views/products/category.php ĐÃ TỒN TẠI
        $this->view('products/category', [
            'products' => $products,
            'title' => 'Tất cả sản phẩm' // Truyền thêm title để view hiển thị
        ]);
        
        $this->view('layouts/footer');
    }

    // --- CHI TIẾT SẢN PHẨM ---
    public function detail($id = null) {
        if (!$id) {
            header('Location: ' . BASE_URL); 
            exit;
        }

        $model = $this->model('ProductModel');
        $product = $model->getProductById($id);

        if (!$product) {
            echo "Sản phẩm không tồn tại!";
            return;
        }

        $reviews = $model->getReviews($id);

        $this->view('layouts/header', ['title' => $product['name']]);
        $this->view('products/detail', [
            'product' => $product,
            'reviews' => $reviews
        ]);
        $this->view('layouts/footer');
    }

    // --- TÌM KIẾM ---
    public function search() {
        $keyword = $_GET['keyword'] ?? '';
        $model = $this->model('ProductModel');
        $products = $model->searchProduct($keyword);

        $this->view('layouts/header', ['title' => 'Tìm kiếm: ' . $keyword]);
        
        // Bạn có thể tái sử dụng view category hoặc dùng view search riêng
        $this->view('products/category', [
            'products' => $products,
            'title' => 'Kết quả tìm kiếm: "' . $keyword . '"'
        ]);
        $this->view('layouts/footer');
    }

    // --- GỬI ĐÁNH GIÁ ---
    public function postReview() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            }

            $product_id = $_POST['product_id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];
            $user_id = $_SESSION['user_id'];

            $model = $this->model('ProductModel');
            $model->addReview($user_id, $product_id, $rating, $comment);

            // SỬA LỖI: Chuyển 'products' thành 'product' (số ít) để đúng với Router
            header('Location: ' . BASE_URL . 'product/detail/' . $product_id);
            exit;
        }
    }

    // --- LỌC THEO DANH MỤC ---
    public function category($id = null) {
        if (!$id) {
            header('Location: ' . BASE_URL);
            exit;
        }
        $model = $this->model('ProductModel');
        $products = $model->getProductsByCategory($id);

        // SỬA LỖI: Đổi tên hàm cho khớp với Model (getCategoryName)
        $categoryName = $model->getCategoryName($id); 

        $this->view('layouts/header', ['title' => $categoryName]);

        $this->view('products/category', [
            'products' => $products,
            'title' => $categoryName
        ]);

        $this->view('layouts/footer');
    }

    
}
?>