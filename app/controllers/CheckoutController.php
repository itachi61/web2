<?php
require_once dirname(__DIR__) . '/core/Controller.php'; 

class CheckoutController extends Controller {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        foreach($cart as $item) $total += $item['price'] * $item['quantity'];

        $orderModel = $this->model('OrderModel');
        $result = $orderModel->createOrder($_SESSION['user_id'], $total, $cart);

        if ($result) {
            unset($_SESSION['cart']); // Xóa giỏ hàng sau khi mua
            echo "Đặt hàng thành công! Mã đơn: " . $result;
        } else {
            echo "Lỗi đặt hàng.";
        }
    }
}
?>