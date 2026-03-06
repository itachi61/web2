<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class ProductController extends Controller
{
    private $perPage = 12; // Số sản phẩm mỗi trang

    /**
     * Trang danh sách tất cả sản phẩm
     */
    public function index()
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        
        $model = $this->model('ProductModel');
        $products = $model->getAllProducts($page, $this->perPage);
        $totalProducts = $model->countAllProducts();
        $totalPages = ceil($totalProducts / $this->perPage);

        $this->view('layouts/header', ['title' => 'Danh sách sản phẩm']);

        $this->view('products/category', [
            'products' => $products,
            'title' => 'Tất cả sản phẩm',
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'baseUrl' => BASE_URL . 'product'
        ]);

        $this->view('layouts/footer');
    }

    /**
     * Chi tiết sản phẩm
     */
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

        $reviews = $model->getReviews($id);
        $images = $model->getProductImages($id);

        $this->view('layouts/header', ['title' => $product['name']]);
        $this->view('products/detail', [
            'product' => $product,
            'reviews' => $reviews,
            'images'  => $images
        ]);
        $this->view('layouts/footer');
    }

    /**
     * Tìm kiếm sản phẩm (cơ bản và nâng cao)
     */
    public function search()
    {
        // 1. Lấy tất cả tham số từ URL (GET)
        $keyword = $_GET['keyword'] ?? '';
        $categories = $_GET['cat'] ?? [];
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        // 2. Gọi Model xử lý
        $model = $this->model('ProductModel');

        $products = $model->searchProductAdvanced($keyword, $categories, $minPrice, $maxPrice, $sort, $page, $this->perPage);
        $totalProducts = $model->countSearchResults($keyword, $categories, $minPrice, $maxPrice);
        $totalPages = ceil($totalProducts / $this->perPage);

        // 3. Build query string cho pagination (không bao gồm page)
        $queryParams = [];
        if (!empty($keyword)) $queryParams['keyword'] = $keyword;
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $queryParams['cat'][] = $cat;
            }
        }
        if (!empty($minPrice)) $queryParams['min_price'] = $minPrice;
        if (!empty($maxPrice)) $queryParams['max_price'] = $maxPrice;
        if (!empty($sort)) $queryParams['sort'] = $sort;
        
        $queryString = http_build_query($queryParams);

        // 4. Lấy danh sách categories cho filter
        $allCategories = $model->getAllCategories();

        // 5. Trả về View
        $this->view('layouts/header', ['title' => 'Tìm kiếm: ' . $keyword]);

        $this->view('products/search', [
            'products' => $products,
            'keyword' => $keyword,
            'categories' => $allCategories,
            'selectedCategories' => $categories,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sort' => $sort,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'baseUrl' => BASE_URL . 'product/search',
            'queryString' => $queryString
        ]);

        $this->view('layouts/footer');
    }

    /**
     * Gửi đánh giá sản phẩm
     */
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

            $model = $this->model('ProductModel');
            $model->addReview($user_id, $product_id, $rating, $comment);

            header('Location: ' . BASE_URL . 'product/detail/' . $product_id);
            exit;
        }
    }

    /**
     * Lọc theo danh mục (có phân trang)
     */
    public function category($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        $model = $this->model('ProductModel');
        $products = $model->getProductsByCategory($id, $page, $this->perPage);
        $totalProducts = $model->countProductsByCategory($id);
        $totalPages = ceil($totalProducts / $this->perPage);

        $categoryName = $model->getCategoryName($id);

        $this->view('layouts/header', ['title' => $categoryName]);

        $this->view('products/category', [
            'products' => $products,
            'title' => $categoryName,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'baseUrl' => BASE_URL . 'product/category/' . $id,
            'categoryId' => $id
        ]);

        $this->view('layouts/footer');
    }
}
