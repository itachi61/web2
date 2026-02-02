<?php
require_once dirname(__DIR__) . '/core/Controller.php';

class OrderController extends Controller {
    
    /**
     * Kiểm tra đăng nhập
     */
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    /**
     * Trang lịch sử đơn hàng
     */
    public function history() {
        $this->requireLogin();

        // Get time filter
        $filter = $_GET['filter'] ?? 'all';
        $dateFrom = null;

        switch ($filter) {
            case '7days':
                $dateFrom = date('Y-m-d', strtotime('-7 days'));
                break;
            case '30days':
                $dateFrom = date('Y-m-d', strtotime('-30 days'));
                break;
            case '3months':
                $dateFrom = date('Y-m-d', strtotime('-3 months'));
                break;
            default:
                $filter = 'all';
                $dateFrom = null;
        }

        $orderModel = $this->model('OrderModel');
        $orders = $orderModel->getOrdersByUserId($_SESSION['user_id'], null, $dateFrom);

        $this->view('layouts/header', ['title' => __('order_history')]);
        $this->view('orders/history', [
            'orders' => $orders,
            'filter' => $filter
        ]);
        $this->view('layouts/footer');
    }

    /**
     * Xem chi tiết đơn hàng
     */
    public function detail($orderId = null) {
        $this->requireLogin();

        if (!$orderId) {
            header('Location: ' . BASE_URL . 'orders/history');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($orderId);

        // Kiểm tra đơn hàng thuộc về user hiện tại
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = __('order_not_found', 'Đơn hàng không tồn tại hoặc bạn không có quyền xem.');
            header('Location: ' . BASE_URL . 'orders/history');
            exit;
        }

        $orderItems = $orderModel->getOrderItems($orderId);

        $this->view('layouts/header', ['title' => __('order_detail') . ' #' . str_pad($orderId, 6, '0', STR_PAD_LEFT)]);
        $this->view('orders/detail', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
        $this->view('layouts/footer');
    }

    /**
     * Hủy đơn hàng (chỉ khi đang pending)
     */
    public function cancel($orderId = null) {
        $this->requireLogin();

        if (!$orderId) {
            header('Location: ' . BASE_URL . 'orders/history');
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($orderId);

        // Kiểm tra đơn hàng thuộc về user hiện tại
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = __('order_not_found', 'Đơn hàng không tồn tại hoặc bạn không có quyền thao tác.');
            header('Location: ' . BASE_URL . 'orders/history');
            exit;
        }

        // Chỉ cho phép hủy khi đang pending
        if ($order['status'] !== 'pending') {
            $_SESSION['error'] = __('cancel_pending_only', 'Chỉ có thể hủy đơn hàng đang chờ xử lý.');
            header('Location: ' . BASE_URL . 'orders/detail/' . $orderId);
            exit;
        }

        if ($orderModel->updateStatus($orderId, 'cancelled')) {
            $_SESSION['success'] = __('order_cancel_success');
        } else {
            $_SESSION['error'] = __('order_cancel_failed');
        }

        header('Location: ' . BASE_URL . 'orders/detail/' . $orderId);
        exit;
    }
}
?>
