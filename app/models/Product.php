<?php

require_once __DIR__ . '/Model.php';

class Product extends Model {
    protected $table = 'products';
    
    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.status = 'active'
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get product by slug
     */
    public function getBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.slug = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Search products
     */
    public function search($keyword, $limit = null) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE (p.name LIKE ? OR p.description LIKE ?) 
                AND p.status = 'active'
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $searchTerm = "%{$keyword}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get featured products
     */
    public function getFeatured($limit = 8) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active' AND p.sale_price IS NOT NULL
                ORDER BY p.created_at DESC 
                LIMIT {$limit}";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get latest products
     */
    public function getLatest($limit = 8) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC 
                LIMIT {$limit}";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all products with category info
     */
    public function getAllWithCategory() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Update stock
     */
    public function updateStock($productId, $quantity) {
        $sql = "UPDATE {$this->table} SET stock = stock + ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $productId]);
    }
    
    /**
     * Decrease stock
     */
    public function decreaseStock($productId, $quantity) {
        $sql = "UPDATE {$this->table} SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $productId, $quantity]);
    }
    
    /**
     * Check if product is in stock
     */
    public function isInStock($productId, $quantity = 1) {
        $product = $this->find($productId);
        return $product && $product['stock'] >= $quantity;
    }
    
    /**
     * Get product price (sale price if available)
     */
    public function getPrice($productId) {
        $product = $this->find($productId);
        if ($product) {
            return $product['sale_price'] ?? $product['price'];
        }
        return 0;
    }
}
