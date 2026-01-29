<?php
require_once dirname(__DIR__) . '/core/Database.php';

class OrderModel extends Database {
    public function createOrder($userId, $total, $cartItems) {
        try {
            $this->conn->beginTransaction();

            // 1. Tạo đơn hàng
            $stmt = $this->conn->prepare("INSERT INTO orders (user_id, total_money) VALUES (?, ?)");
            $stmt->execute([$userId, $total]);
            $orderId = $this->conn->lastInsertId();

            // 2. Lưu chi tiết đơn hàng
            $stmtDetail = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $stmtDetail->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>