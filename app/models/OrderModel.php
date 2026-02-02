<?php
require_once dirname(__DIR__) . '/core/Database.php';

class OrderModel extends Database {
    
    /**
     * Tạo đơn hàng mới
     */
    public function createOrder($orderData, $cartItems) {
        try {
            $this->conn->beginTransaction();

            // 1. Tạo đơn hàng
            $stmt = $this->conn->prepare("
                INSERT INTO orders (user_id, fullname, phone, address, ward, district, note, total_money, payment_method, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $orderData['user_id'],
                $orderData['fullname'],
                $orderData['phone'],
                $orderData['address'],
                $orderData['ward'] ?? null,
                $orderData['district'] ?? null,
                $orderData['note'] ?? null,
                $orderData['total_money'],
                $orderData['payment_method'] ?? 'cash'
            ]);
            $orderId = $this->conn->lastInsertId();

            // 2. Lưu chi tiết đơn hàng
            $stmtDetail = $this->conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (?, ?, ?, ?)
            ");
            foreach ($cartItems as $item) {
                $stmtDetail->execute([
                    $orderId, 
                    $item['id'], 
                    $item['quantity'], 
                    $item['price']
                ]);
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Lấy thông tin đơn hàng theo ID
     */
    public function getOrderById($orderId) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm trong đơn hàng
     */
    public function getOrderItems($orderId) {
        $stmt = $this->conn->prepare("
            SELECT oi.*, p.name, p.image 
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách đơn hàng của user (mới nhất ở trên)
     */
    public function getOrdersByUserId($userId, $limit = null, $dateFrom = null) {
        $sql = "SELECT * FROM orders WHERE user_id = ?";
        $params = [$userId];

        if ($dateFrom) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $dateFrom;
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus($orderId, $status) {
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    /**
     * Lấy tất cả đơn hàng (cho admin)
     */
    public function getAllOrders($filters = []) {
        $sql = "SELECT o.*, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE 1=1";
        $params = [];

        // Lọc theo trạng thái
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        // Lọc theo khoảng thời gian
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        // Sắp xếp
        if (!empty($filters['sort_by']) && $filters['sort_by'] === 'ward') {
            $sql .= " ORDER BY o.ward ASC, o.created_at DESC";
        } else {
            $sql .= " ORDER BY o.created_at DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm số đơn hàng theo trạng thái (cho dashboard)
     */
    public function countByStatus() {
        $stmt = $this->conn->query("
            SELECT status, COUNT(*) as count 
            FROM orders 
            GROUP BY status
        ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $counts = [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'total' => 0
        ];
        
        foreach ($results as $row) {
            $counts[$row['status']] = (int)$row['count'];
            $counts['total'] += (int)$row['count'];
        }
        
        return $counts;
    }

    /**
     * Tính tổng doanh thu (đơn hoàn thành)
     */
    public function getTotalRevenue($dateFrom = null, $dateTo = null) {
        $sql = "SELECT SUM(total_money) as revenue FROM orders WHERE status = 'completed'";
        $params = [];

        if ($dateFrom) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $dateTo;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['revenue'] ?? 0;
    }
}
?>