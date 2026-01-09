<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
  private $productModel;
  private $orderModel;
  private $userModel;
  private $categoryModel;

  public function __construct()
  {
    // Check if user is admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
      $_SESSION['error'] = 'Bạn không có quyền truy cập trang này';
      header('Location: ' . BASE_URL);
      exit;
    }

    require_once __DIR__ . '/../models/Product.php';
    require_once __DIR__ . '/../models/Order.php';
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../models/Category.php';
    
    $this->productModel = new \Product();
    $this->orderModel = new \Order();
    $this->userModel = new \User();
    $this->categoryModel = new \Category();
  }

  public function dashboard(): void
  {
    // Get real statistics
    $orderStats = $this->orderModel->getStatistics();
    
    $stats = [
      'total_products' => $this->productModel->count("status = 'active'"),
      'total_orders' => $orderStats['total_orders'] ?? 0,
      'total_revenue' => $orderStats['total_revenue'] ?? 0,
      'total_users' => $this->userModel->count("role = 'user'"),
      'pending_orders' => $orderStats['pending_orders'] ?? 0,
      'processing_orders' => $orderStats['processing_orders'] ?? 0,
      'delivered_orders' => $orderStats['delivered_orders'] ?? 0
    ];

    // Get recent orders
    $recentOrders = $this->orderModel->getAllWithUser();
    $recentOrders = array_slice($recentOrders, 0, 10); // Get latest 10

    $this->view('admin/dashboard', [
      'title' => 'Dashboard - Admin',
      'stats' => $stats,
      'recentOrders' => $recentOrders
    ], 'admin');
  }

  public function products(): void
  {
    // Get all products with category info
    $products = $this->productModel->getAllWithCategory();

    $this->view('admin/products/index', [
      'title' => 'Quản lý sản phẩm - Admin',
      'products' => $products
    ], 'admin');
  }

  public function createProduct(): void
  {
    // Get categories for dropdown
    $categories = $this->categoryModel->findAll('name ASC');

    $this->view('admin/products/form', [
      'title' => 'Thêm sản phẩm - Admin',
      'product' => null,
      'categories' => $categories
    ], 'admin');
  }

  public function handleCreateProduct(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/admin/products');
      exit;
    }

    // Validate category exists
    $categoryId = $_POST['category_id'] ?? 0;
    $category = $this->categoryModel->find($categoryId);
    
    if (!$category) {
      $_SESSION['error'] = 'Danh mục không tồn tại. Vui lòng chọn danh mục hợp lệ.';
      header('Location: ' . BASE_URL . '/admin/products/create');
      exit;
    }

    // Get form data
    $data = [
      'category_id' => $categoryId,
      'name' => $_POST['name'] ?? '',
      'slug' => $_POST['slug'] ?? $this->generateSlug($_POST['name'] ?? ''),
      'description' => $_POST['description'] ?? '',
      'price' => $_POST['price'] ?? 0,
      'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
      'stock' => $_POST['stock'] ?? 0,
      'specifications' => !empty($_POST['specifications']) ? json_encode($_POST['specifications']) : null,
      'status' => $_POST['status'] ?? 'active'
    ];

    // Validate required fields
    if (empty($data['name']) || empty($data['price'])) {
      $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
      $_SESSION['old'] = $_POST;
      header('Location: ' . BASE_URL . '/admin/products/create');
      exit;
    }

    // Handle image upload (simplified - you may want to add proper image upload)
    $data['images'] = $_POST['images'] ?? null;

    // Create product
    try {
      $productId = $this->productModel->create($data);

      if ($productId) {
        $_SESSION['success'] = 'Đã thêm sản phẩm thành công';
        header('Location: ' . BASE_URL . '/admin/products');
      } else {
        $_SESSION['error'] = 'Không thể thêm sản phẩm';
        header('Location: ' . BASE_URL . '/admin/products/create');
      }
    } catch (Exception $e) {
      $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
      $_SESSION['old'] = $_POST;
      header('Location: ' . BASE_URL . '/admin/products/create');
    }
    exit;
  }

  public function editProduct(): void
  {
    $id = $_GET['id'] ?? 0;
    
    $product = $this->productModel->find($id);
    
    if (!$product) {
      $_SESSION['error'] = 'Không tìm thấy sản phẩm';
      header('Location: ' . BASE_URL . '/admin/products');
      exit;
    }

    // Get categories for dropdown
    $categories = $this->categoryModel->findAll('name ASC');

    // Parse specifications if JSON
    if (!empty($product['specifications'])) {
      $product['specs'] = json_decode($product['specifications'], true) ?? [];
    }

    $this->view('admin/products/form', [
      'title' => 'Sửa sản phẩm - Admin',
      'product' => $product,
      'categories' => $categories
    ], 'admin');
  }

  public function handleEditProduct(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/admin/products');
      exit;
    }

    $id = $_POST['id'] ?? 0;

    // Get form data
    $data = [
      'category_id' => $_POST['category_id'] ?? 0,
      'name' => $_POST['name'] ?? '',
      'slug' => $_POST['slug'] ?? '',
      'description' => $_POST['description'] ?? '',
      'price' => $_POST['price'] ?? 0,
      'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
      'stock' => $_POST['stock'] ?? 0,
      'specifications' => !empty($_POST['specifications']) ? json_encode($_POST['specifications']) : null,
      'status' => $_POST['status'] ?? 'active'
    ];

    // Handle image upload
    if (!empty($_POST['images'])) {
      $data['images'] = $_POST['images'];
    }

    // Update product
    if ($this->productModel->update($id, $data)) {
      $_SESSION['success'] = 'Đã cập nhật sản phẩm thành công';
    } else {
      $_SESSION['error'] = 'Không thể cập nhật sản phẩm';
    }

    header('Location: ' . BASE_URL . '/admin/products');
    exit;
  }

  public function deleteProduct(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/admin/products');
      exit;
    }

    $id = $_POST['id'] ?? 0;

    if ($this->productModel->delete($id)) {
      $_SESSION['success'] = 'Đã xóa sản phẩm thành công';
    } else {
      $_SESSION['error'] = 'Không thể xóa sản phẩm';
    }

    header('Location: ' . BASE_URL . '/admin/products');
    exit;
  }

  public function orders(): void
  {
    // Get all orders with user info
    $orders = $this->orderModel->getAllWithUser();

    $this->view('admin/orders/index', [
      'title' => 'Quản lý đơn hàng - Admin',
      'orders' => $orders
    ], 'admin');
  }

  public function orderDetail(): void
  {
    $id = $_GET['id'] ?? 0;
    
    $order = $this->orderModel->getOrderWithItems($id);
    
    if (!$order) {
      $_SESSION['error'] = 'Không tìm thấy đơn hàng';
      header('Location: ' . BASE_URL . '/admin/orders');
      exit;
    }

    $this->view('admin/orders/detail', [
      'title' => 'Chi tiết đơn hàng - Admin',
      'order' => $order
    ], 'admin');
  }

  public function updateOrderStatus(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/admin/orders');
      exit;
    }

    $orderId = $_POST['order_id'] ?? 0;
    $status = $_POST['status'] ?? '';

    if ($this->orderModel->updateStatus($orderId, $status)) {
      $_SESSION['success'] = 'Đã cập nhật trạng thái đơn hàng';
    } else {
      $_SESSION['error'] = 'Không thể cập nhật trạng thái';
    }

    header('Location: ' . BASE_URL . '/admin/orders');
    exit;
  }

  private function generateSlug($name): string
  {
    // Simple slug generation (you may want to use the Category model's method)
    $slug = strtolower($name);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
  }
}
