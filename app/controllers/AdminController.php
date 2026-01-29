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
            'view' => 'admin/dashboard', // Load file view con
            'active' => 'dashboard'
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
        // Bạn nên lấy danh sách danh mục để hiển thị ra thẻ <select>
        // Nếu chưa có CategoryModel thì bạn có thể bỏ qua dòng này và fix cứng trong view tạm thời
        // $categoryModel = $this->model('CategoryModel');
        // $categories = $categoryModel->getAllCategories();

        $this->view('admin/dashboard', [
            'view' => 'admin/products/add',
            'active' => 'products',
            // 'categories' => $categories // Truyền biến này nếu có
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

            // 2. TẠO FOLDER RIÊNG CHO SẢN PHẨM
            // Tạo slug từ tên sản phẩm (Ví dụ: "iPhone 15 Pro" -> "iphone-15-pro")
            $folderName = $this->createSlug($name);

            // Đường dẫn thực tế trên máy tính 
            // dirname(__DIR__, 2) lùi về 2 cấp (từ app/controllers -> app -> root) để vào public
            $targetDir = dirname(__DIR__, 2) . '/public/images/' . $folderName . '/';

            // Kiểm tra nếu folder chưa tồn tại thì tạo mới
            if (!file_exists($targetDir)) {
                // 0777 là quyền ghi, true là cho phép tạo thư mục con
                mkdir($targetDir, 0777, true);
            }

            // 3. XỬ LÝ ẢNH ĐẠI DIỆN (MAIN IMAGE)
            $dbImageName = ''; // Biến để lưu vào DB

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Đặt tên file kèm time() để tránh trùng tên
                $fileName = time() . '_main_' . $_FILES['image']['name'];

                // Upload file vào folder vừa tạo
                move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName);

                // Lưu đường dẫn dạng "folder/anh.jpg" vào DB
                $dbImageName = $folderName . '/' . $fileName;
            }

            // 4. LƯU SẢN PHẨM VÀO DATABASE
            $model = $this->model('ProductModel');
            $model->insertProduct($name, $category_id, $price, $desc, $dbImageName);

            // Lấy ID sản phẩm vừa tạo để gán cho ảnh phụ
            $newProductId = $model->getLastId();

            // 5. XỬ LÝ ẢNH PHỤ (EXTRA IMAGES)
            if (isset($_FILES['extra_images'])) {
                $totalFiles = count($_FILES['extra_images']['name']);

                for ($i = 0; $i < $totalFiles; $i++) {
                    // Kiểm tra từng file xem có lỗi không
                    if ($_FILES['extra_images']['error'][$i] == 0) {

                        $extraFileName = time() . '_' . $i . '_' . $_FILES['extra_images']['name'][$i];

                        // Upload vào cùng folder đó
                        move_uploaded_file($_FILES['extra_images']['tmp_name'][$i], $targetDir . $extraFileName);

                        // Đường dẫn lưu DB
                        $dbExtraPath = $folderName . '/' . $extraFileName;

                        // Gọi Model lưu vào bảng product_images
                        $model->addProductImage($newProductId, $dbExtraPath);
                    }
                }
            }

            // Xử lý xong thì quay về trang danh sách
            header('Location: ' . BASE_URL . 'admin/products');
            exit;
        }
    }

    // Hàm xóa sản phẩm
    public function deleteProduct($id)
    {
        $model = $this->model('ProductModel');
        
        // (Nâng cao: Nếu muốn xóa sạch, bạn cần viết thêm code xóa ảnh và folder trên server tại đây trước khi xóa DB)
        
        $model->deleteProduct($id);
        header('Location: ' . BASE_URL . 'admin/products');
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