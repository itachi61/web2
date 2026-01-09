<?php

require_once __DIR__ . '/Model.php';

class Category extends Model {
    protected $table = 'categories';
    
    /**
     * Get category by slug
     */
    public function getBySlug($slug) {
        return $this->findWhere('slug', $slug);
    }
    
    /**
     * Get category with products
     */
    public function getWithProducts($categoryId) {
        $category = $this->find($categoryId);
        
        if ($category) {
            $sql = "SELECT * FROM products WHERE category_id = ? AND status = 'active' ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId]);
            $category['products'] = $stmt->fetchAll();
        }
        
        return $category;
    }
    
    /**
     * Get category with product count
     */
    public function getWithProductCount() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                GROUP BY c.id 
                ORDER BY c.name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all categories with products
     */
    public function getAllWithProducts() {
        $categories = $this->findAll('name ASC');
        
        foreach ($categories as &$category) {
            $sql = "SELECT * FROM products WHERE category_id = ? AND status = 'active' ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$category['id']]);
            $category['products'] = $stmt->fetchAll();
        }
        
        return $categories;
    }
    
    /**
     * Create category with slug
     */
    public function createCategory($data) {
        // Generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        return $this->create($data);
    }
    
    /**
     * Update category
     */
    public function updateCategory($id, $data) {
        // Update slug if name changed
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Generate slug from name
     */
    private function generateSlug($name) {
        // Convert Vietnamese characters to ASCII
        $slug = $this->removeVietnamese($name);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
    
    /**
     * Remove Vietnamese characters
     */
    private function removeVietnamese($str) {
        $vietnamese = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];
        
        $ascii = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];
        
        return str_replace($vietnamese, $ascii, $str);
    }
}
