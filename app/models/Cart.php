<?php

require_once __DIR__ . '/Model.php';

class Cart extends Model {
    protected $table = 'cart';
    
    /**
     * Add item to cart
     */
    public function addItem($userId, $productId, $quantity = 1) {
        // Check if item already exists
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $productId]);
        $existingItem = $stmt->fetch();
        
        if ($existingItem) {
            // Update quantity
            return $this->updateQuantity($userId, $productId, $existingItem['quantity'] + $quantity);
        } else {
            // Add new item
            return $this->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }
    
    /**
     * Update item quantity
     */
    public function updateQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $productId);
        }
        
        $sql = "UPDATE {$this->table} SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $userId, $productId]);
    }
    
    /**
     * Remove item from cart
     */
    public function removeItem($userId, $productId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $productId]);
    }
    
    /**
     * Get user cart items
     */
    public function getUserCart($userId) {
        $sql = "SELECT c.*, p.name, p.price, p.sale_price, p.images, p.stock,
                (p.sale_price IS NOT NULL ? p.sale_price : p.price) as current_price,
                (c.quantity * (p.sale_price IS NOT NULL ? p.sale_price : p.price)) as subtotal
                FROM {$this->table} c 
                LEFT JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ? AND p.status = 'active'
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get cart total
     */
    public function getTotal($userId) {
        $sql = "SELECT SUM(c.quantity * (p.sale_price IS NOT NULL ? p.sale_price : p.price)) as total
                FROM {$this->table} c 
                LEFT JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ? AND p.status = 'active'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Get cart item count
     */
    public function getItemCount($userId) {
        $sql = "SELECT SUM(quantity) as count FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Clear user cart
     */
    public function clearCart($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    /**
     * Check if product is in cart
     */
    public function isInCart($userId, $productId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $productId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get cart item quantity
     */
    public function getItemQuantity($userId, $productId) {
        $sql = "SELECT quantity FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $productId]);
        $result = $stmt->fetch();
        
        return $result ? $result['quantity'] : 0;
    }
    
    /**
     * Validate cart items (check stock availability)
     */
    public function validateCart($userId) {
        $items = $this->getUserCart($userId);
        $errors = [];
        
        foreach ($items as $item) {
            if ($item['stock'] < $item['quantity']) {
                $errors[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'requested' => $item['quantity'],
                    'available' => $item['stock']
                ];
            }
        }
        
        return $errors;
    }
}
