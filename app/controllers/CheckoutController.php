<?php
require_once dirname(__DIR__) . '/core/Controller.php'; 

class CheckoutController extends Controller {
    
    // Hiển thị form checkout
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $total = 0;
        foreach($cart as $item) $total += $item['price'] * $item['quantity'];

        // Lấy thông tin user
        $userModel = $this->model('UserModel');
        $user = $userModel->getUserById($_SESSION['user_id']);

        $this->view('layouts/header', ['title' => 'Thanh toán']);
        $this->view('cart/checkout', [
            'cart' => $cart,
            'total' => $total,
            'user' => $user
        ]);
        $this->view('layouts/footer');
    }

    // Xử lý đặt hàng (POST)
    public function placeOrder() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . BASE_URL . 'checkout');
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $note = trim($_POST['note'] ?? '');

        if (empty($fullname) || empty($phone) || empty($address)) {
            header('Location: ' . BASE_URL . 'checkout');
            exit;
        }

        $total = 0;
        foreach($cart as $item) $total += $item['price'] * $item['quantity'];

        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->beginTransaction();

            // Tạo đơn hàng
            $stmt = $db->prepare("INSERT INTO orders (user_id, fullname, phone, address, note, total_money, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $fullname, $phone, $address, $note, $total]);
            $orderId = $db->lastInsertId();

            // Thêm chi tiết đơn hàng + lưu giá nhập snapshot + trừ tồn kho
            $stmtItem = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, cost_price) VALUES (?, ?, ?, ?, ?)");
            $stmtCost = $db->prepare("SELECT cost_price, stock FROM products WHERE id = ?");
            $stmtStock = $db->prepare("UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?");
            $stmtStockLog = $db->prepare("INSERT INTO stock_history (product_id, stock_before, stock_after, change_qty, change_type, reference_id) VALUES (?, ?, ?, ?, 'sale', ?)");
            foreach ($cart as $item) {
                // Lấy giá nhập + tồn kho hiện tại
                $stmtCost->execute([$item['id']]);
                $pInfo = $stmtCost->fetch(PDO::FETCH_ASSOC);
                $costPrice = $pInfo['cost_price'] ?? 0;
                $stockBefore = intval($pInfo['stock'] ?? 0);
                
                $stmtItem->execute([$orderId, $item['id'], $item['quantity'], $item['price'], $costPrice]);
                
                // Trừ tồn kho
                $stmtStock->execute([$item['quantity'], $item['id']]);
                
                // Log stock history
                $stockAfter = max(0, $stockBefore - $item['quantity']);
                $stmtStockLog->execute([$item['id'], $stockBefore, $stockAfter, $item['quantity'], $orderId]);
            }

            $db->commit();

            // Xóa giỏ hàng
            unset($_SESSION['cart']);

            // Hiển thị trang thành công
            $this->view('layouts/header', ['title' => 'Đặt hàng thành công']);
            $this->view('cart/order_success', ['orderId' => $orderId]);
            $this->view('layouts/footer');

        } catch (Exception $e) {
            $db->rollBack();
            echo "Lỗi đặt hàng: " . $e->getMessage();
        }
    }
}
?>