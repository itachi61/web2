<?php

require_once __DIR__ . '/Model.php';

class Order extends Model {
    protected $table = 'orders';
    
    /**
     * Create order with items
     */
    public function createOrder($userId, $items, $shippingAddress, $paymentMethod = 'cod', $notes = '') {
        try {
            $this->beginTransaction();
            
            // Calculate total
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            
            // Generate order number
            $orderNumber = $this->generateOrderNumber();
            
            // Create order
            $orderData = [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'shipping_address' => $shippingAddress,
                'payment_method' => $paymentMethod,
                'notes' => $notes,
                'status' => 'pending'
            ];
            
            $orderId = $this->create($orderData);
            
            if (!$orderId) {
                $this->rollback();
                return false;
            }
            
            // Create order items
            foreach ($items as $item) {
                $itemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ];
                
                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array_values($itemData));
                
                // Update product stock
                $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            $this->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }
    
    /**
     * Get order with items
     */
    public function getOrderWithItems($orderId) {
        $order = $this->find($orderId);
        
        if ($order) {
            $order['items'] = $this->getOrderItems($orderId);
        }
        
        return $order;
    }
    
    /**
     * Get order items
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.name as product_name, p.images 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get user orders
     */
    public function getUserOrders($userId, $limit = null) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get orders by status
     */
    public function getByStatus($status) {
        return $this->where('status', $status);
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        return $this->update($orderId, ['status' => $status]);
    }
    
    /**
     * Cancel order
     */
    public function cancelOrder($orderId) {
        try {
            $this->beginTransaction();
            
            // Get order items
            $items = $this->getOrderItems($orderId);
            
            // Restore product stock
            foreach ($items as $item) {
                $sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Update order status
            $this->updateStatus($orderId, 'cancelled');
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }
    
    /**
     * Get all orders with user info
     */
    public function getAllWithUser() {
        $sql = "SELECT o.*, u.username, u.email, u.full_name 
                FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Generate unique order number
     */
    private function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    /**
     * Get order statistics
     */
    public function getStatistics() {
        $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(total_amount) as total_revenue
                FROM {$this->table}";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
}
