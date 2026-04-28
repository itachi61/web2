<?php
// Sửa lại đường dẫn require cho chuẩn (tránh lỗi No such file)
require_once dirname(__DIR__) . '/core/Controller.php';

class ProductController extends Controller
{

    // --- TRANG DANH SÁCH TẤT CẢ SẢN PHẨM ---
    public function index()
    {
        // Lấy số trang từ URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $perPage = 8;
        $sort = $_GET['sort'] ?? 'newest';

        $model = $this->model('ProductModel');
        $products = $model->getAllProductsPaginated($page, $perPage, $sort);

        // Tính toán phân trang
        $totalProducts = $model->getTotalProductsCount();
        $totalPages = ceil($totalProducts / $perPage);

        $this->view('layouts/header', ['title' => 'Danh sách sản phẩm']);

        $this->view('products/category', [
            'products' => $products,
            'title' => 'Tất cả sản phẩm',
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'baseUrl' => BASE_URL . 'product/index',
            'sort' => $sort
        ]);

        $this->view('layouts/footer');
    }

    // --- CHI TIẾT SẢN PHẨM ---
    public function detail($id = null)
    {
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

        // =============================================================
        // Lấy thêm Tên Danh Mục cho thanh Breadcrumb một cách an toàn
        // =============================================================
        if (!empty($product['category_id'])) {
            $categoryName = $model->getCategoryName($product['category_id']);
            $product['category_name'] = $categoryName ? $categoryName : 'Sản phẩm';
        } else {
             $product['category_name'] = 'Sản phẩm';
        }

        $reviews = $model->getReviews($id);
        $images = $model->getProductImages($id);

        // --- BẮT ĐẦU: KIỂM TRA TRẠNG THÁI MUA HÀNG ---
        $hasPurchased = false;
        if (isset($_SESSION['user_id'])) {
            $orderModel = $this->model('OrderModel');
            $hasPurchased = $orderModel->checkUserPurchasedProduct($_SESSION['user_id'], $id);
        }
        // --- KẾT THÚC: KIỂM TRA TRẠNG THÁI MUA HÀNG ---

        $this->view('layouts/header', ['title' => $product['name']]);

        $this->view('products/detail', [
            'product' => $product,
            'reviews' => $reviews,
            'images'  => $images,
            'hasPurchased' => $hasPurchased // Truyền biến này ra View
        ]);
        
        $this->view('layouts/footer');
    }

    // --- TÌM KIẾM ---
    public function search()
    {
        // 1. Lấy tất cả tham số từ URL (GET)
        $keyword = $_GET['keyword'] ?? '';
        $categories = $_GET['cat'] ?? []; // Mảng danh mục (ví dụ: [1, 3])
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $sort = $_GET['sort'] ?? 'newest'; // Mặc định là mới nhất

        // 2. Gọi Model xử lý
        $model = $this->model('ProductModel');

        // Hàm này sẽ viết ở bước dưới
        $products = $model->searchProductAdvanced($keyword, $categories, $minPrice, $maxPrice, $sort);

        // 3. Trả về View
        $this->view('layouts/header', ['title' => 'Tìm kiếm: ' . $keyword]);

        $this->view('products/search', [
            'products' => $products,
            'keyword' => $keyword // Truyền lại để view hiển thị tiêu đề
        ]);

        $this->view('layouts/footer');
    }

    // --- GỬI ĐÁNH GIÁ ---
    public function postReview()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            }

            $product_id = $_POST['product_id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];
            $user_id = $_SESSION['user_id'];

            // --- BẮT ĐẦU: RÀNG BUỘC BẢO MẬT BACKEND ---
            $orderModel = $this->model('OrderModel');
            $hasPurchased = $orderModel->checkUserPurchasedProduct($user_id, $product_id);

            if (!$hasPurchased) {
                // Tùy chọn: Bạn có thể lưu câu thông báo lỗi vào $_SESSION['error'] 
                // để hiển thị ngoài file views/products/detail.php nếu muốn
                $_SESSION['error'] = "Bạn cần mua và nhận sản phẩm này để có thể đánh giá.";
                header('Location: ' . BASE_URL . 'product/detail/' . $product_id);
                exit;
            }
            // --- KẾT THÚC: RÀNG BUỘC BẢO MẬT BACKEND ---

            $model = $this->model('ProductModel');
            $model->addReview($user_id, $product_id, $rating, $comment);

            // Lưu thông báo thành công (tùy chọn)
            $_SESSION['success'] = "Cảm ơn bạn đã đánh giá sản phẩm!";

            header('Location: ' . BASE_URL . 'product/detail/' . $product_id);
            exit;
        }

        header('Location: ' . BASE_URL);
        exit;
    }

    // --- LỌC THEO DANH MỤC ---
    public function category($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $perPage = 8;
        $sort = $_GET['sort'] ?? 'newest';

        $model = $this->model('ProductModel');
        $products = $model->getProductsByCategoryPaginated($id, $page, $perPage, $sort);

        $totalProducts = $model->getTotalProductsByCategoryCount($id);
        $totalPages = (int)ceil($totalProducts / $perPage);

        $categoryName = $model->getCategoryName($id);

        $this->view('layouts/header', ['title' => $categoryName]);

        $this->view('products/category', [
            'products' => $products,
            'title' => $categoryName,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'baseUrl' => BASE_URL . 'product/category/' . $id,
            'sort' => $sort
        ]);

        $this->view('layouts/footer');
    }
}