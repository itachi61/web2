<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class CheckoutController extends Controller {
    
    /**
     * Hiển thị trang checkout
     */
    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = BASE_URL . 'checkout';
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        // Kiểm tra giỏ hàng
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        // Lấy thông tin user
        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($_SESSION['user_id']);

        $this->view('layouts/header', ['title' => 'Thanh toán']);
        $this->view('checkout/index', [
            'user' => $user,
            'cart' => $cart
        ]);
        $this->view('layouts/footer');
    }

    /**
     * Xử lý đặt hàng
     */
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'checkout');
            exit;
        }

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        // Kiểm tra giỏ hàng
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        // Lấy thông tin từ form
        $addressType = $_POST['address_type'] ?? 'account';
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $note = trim($_POST['note'] ?? '');

        // Xác định thông tin giao hàng
        if ($addressType === 'account') {
            $fullname = $_POST['account_fullname'] ?? '';
            $phone = $_POST['account_phone'] ?? '';
            $address = $_POST['account_address'] ?? '';
            $ward = $_POST['account_ward'] ?? '';
            $district = $_POST['account_district'] ?? '';
        } else {
            $fullname = trim($_POST['new_fullname'] ?? '');
            $phone = trim($_POST['new_phone'] ?? '');
            $address = trim($_POST['new_address'] ?? '');
            $ward = trim($_POST['new_ward'] ?? '');
            $district = trim($_POST['new_district'] ?? '');
        }

        // Validate
        if (empty($fullname) || empty($phone) || empty($address) || empty($ward) || empty($district)) {
            $userModel = $this->model('UserModel');
            $user = $userModel->getUserById($_SESSION['user_id']);
            
            $this->view('layouts/header', ['title' => 'Thanh toán']);
            $this->view('checkout/index', [
                'user' => $user,
                'cart' => $cart,
                'error' => 'Vui lòng nhập đầy đủ thông tin giao hàng!'
            ]);
            $this->view('layouts/footer');
            return;
        }

        // Validate phone
        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $userModel = $this->model('UserModel');
            $user = $userModel->getUserById($_SESSION['user_id']);
            
            $this->view('layouts/header', ['title' => 'Thanh toán']);
            $this->view('checkout/index', [
                'user' => $user,
                'cart' => $cart,
                'error' => 'Số điện thoại không hợp lệ!'
            ]);
            $this->view('layouts/footer');
            return;
        }

        // Kiểm tra thanh toán online
        if ($paymentMethod === 'online') {
            $userModel = $this->model('UserModel');
            $user = $userModel->getUserById($_SESSION['user_id']);
            
            $this->view('layouts/header', ['title' => 'Thanh toán']);
            $this->view('checkout/index', [
                'user' => $user,
                'cart' => $cart,
                'error' => 'Chức năng thanh toán trực tuyến chưa được hỗ trợ!'
            ]);
            $this->view('layouts/footer');
            return;
        }

        // Tính tổng tiền
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Tạo đơn hàng
        $orderModel = $this->model('OrderModel');
        $orderData = [
            'user_id' => $_SESSION['user_id'],
            'fullname' => $fullname,
            'phone' => $phone,
            'address' => $address,
            'ward' => $ward,
            'district' => $district,
            'note' => $note,
            'total_money' => $total,
            'payment_method' => $paymentMethod
        ];

        $orderId = $orderModel->createOrder($orderData, $cart);

        if ($orderId) {
            // Xóa giỏ hàng
            unset($_SESSION['cart']);
            
            // Chuyển đến trang thành công
            header('Location: ' . BASE_URL . 'checkout/success/' . $orderId);
            exit;
        } else {
            $userModel = $this->model('UserModel');
            $user = $userModel->getUserById($_SESSION['user_id']);
            
            $this->view('layouts/header', ['title' => 'Thanh toán']);
            $this->view('checkout/index', [
                'user' => $user,
                'cart' => $cart,
                'error' => 'Đặt hàng thất bại! Vui lòng thử lại.'
            ]);
            $this->view('layouts/footer');
        }
    }

    /**
     * Trang đặt hàng thành công
     */
    public function success($orderId = null) {
        if (!$orderId) {
            header('Location: ' . BASE_URL);
            exit;
        }

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($orderId);

        // Kiểm tra đơn hàng thuộc về user hiện tại
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $orderItems = $orderModel->getOrderItems($orderId);

        $this->view('layouts/header', ['title' => 'Đặt hàng thành công']);
        $this->view('checkout/success', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
        $this->view('layouts/footer');
    }
}
?>