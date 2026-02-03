<?php
require_once dirname(__DIR__) . '/core/Database.php';

class CategoryModel extends Database
{
    // Get all categories
    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get all with product count
    public function getAllWithProductCount()
    {
        $stmt = $this->conn->query("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
            FROM categories c
            ORDER BY c.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get category by ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Create category
    public function create($name, $description = '', $icon = 'fa-folder')
    {
        $stmt = $this->conn->prepare("INSERT INTO categories (name, description, icon) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $description, $icon]);
    }
    
    // Update category
    public function update($id, $name, $description = '')
    {
        $stmt = $this->conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $id]);
    }
    
    // Delete category (only if no products)
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ? AND id NOT IN (SELECT DISTINCT category_id FROM products)");
        return $stmt->execute([$id]);
    }
}
