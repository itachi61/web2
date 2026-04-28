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

    /**
     * Kiểm tra xem user đã mua sản phẩm này chưa
     */
    public function checkUserPurchasedProduct($userId, $productId) {
        try {
            // Lưu ý: Nếu bảng 'orders' của bạn có cột lưu trạng thái đơn hàng (ví dụ: status = 'completed'), 
            // bạn nên thêm "AND o.status = 'completed'" vào cuối câu WHERE để đảm bảo khách đã nhận hàng mới được đánh giá.
            $sql = "SELECT COUNT(*) as count 
                    FROM orders o 
                    JOIN order_items oi ON o.id = oi.order_id 
                    WHERE o.user_id = ? 
                    AND o.status = 'completed'
                    AND oi.product_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId, $productId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (Exception $e) {
            // Ghi log lỗi nếu cần thiết
            return false;
        }
    }
}
?>