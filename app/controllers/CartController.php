<?php
namespace App\Controllers;

use App\Core\Controller;

class CartController extends Controller
{
  private $cartModel;
  private $productModel;

  public function __construct()
  {
    require_once __DIR__ . '/../models/Cart.php';
    require_once __DIR__ . '/../models/Product.php';
    
    $this->cartModel = new \Cart();
    $this->productModel = new \Product();
  }

  public function index(): void
  {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
      $_SESSION['error'] = 'Vui lòng đăng nhập để xem giỏ hàng';
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    $userId = $_SESSION['user_id'];

    // Get cart items
    $cartItems = $this->cartModel->getUserCart($userId);

    // Calculate total
    $total = $this->cartModel->getTotal($userId);

    // Validate cart (check stock availability)
    $errors = $this->cartModel->validateCart($userId);

    $this->view('cart/index', [
      'title' => 'Giỏ hàng - TechSmart',
      'cartItems' => $cartItems,
      'total' => $total,
      'stockErrors' => $errors
    ]);
  }

  public function add(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL);
      exit;
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
      $_SESSION['error'] = 'Vui lòng đăng nhập để thêm vào giỏ hàng';
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    // Validate product exists and in stock
    if (!$this->productModel->isInStock($productId, $quantity)) {
      $_SESSION['error'] = 'Sản phẩm không đủ hàng trong kho';
      header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL);
      exit;
    }

    // Add to cart
    if ($this->cartModel->addItem($userId, $productId, $quantity)) {
      $_SESSION['success'] = 'Đã thêm sản phẩm vào giỏ hàng';
    } else {
      $_SESSION['error'] = 'Không thể thêm sản phẩm vào giỏ hàng';
    }

    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL);
    exit;
  }

  public function update(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/cart');
      exit;
    }

    if (!isset($_SESSION['user_id'])) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    // Validate stock
    if ($quantity > 0 && !$this->productModel->isInStock($productId, $quantity)) {
      $_SESSION['error'] = 'Số lượng vượt quá hàng trong kho';
      header('Location: ' . BASE_URL . '/cart');
      exit;
    }

    // Update quantity
    if ($this->cartModel->updateQuantity($userId, $productId, $quantity)) {
      $_SESSION['success'] = 'Đã cập nhật giỏ hàng';
    } else {
      $_SESSION['error'] = 'Không thể cập nhật giỏ hàng';
    }

    header('Location: ' . BASE_URL . '/cart');
    exit;
  }

  public function remove(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . BASE_URL . '/cart');
      exit;
    }

    if (!isset($_SESSION['user_id'])) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    $userId = $_SESSION['user_id'];
    $productId = $_POST['product_id'] ?? 0;

    // Remove item
    if ($this->cartModel->removeItem($userId, $productId)) {
      $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
    } else {
      $_SESSION['error'] = 'Không thể xóa sản phẩm';
    }

    header('Location: ' . BASE_URL . '/cart');
    exit;
  }

  public function clear(): void
  {
    if (!isset($_SESSION['user_id'])) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    $userId = $_SESSION['user_id'];

    if ($this->cartModel->clearCart($userId)) {
      $_SESSION['success'] = 'Đã xóa toàn bộ giỏ hàng';
    } else {
      $_SESSION['error'] = 'Không thể xóa giỏ hàng';
    }

    header('Location: ' . BASE_URL . '/cart');
    exit;
  }
}
