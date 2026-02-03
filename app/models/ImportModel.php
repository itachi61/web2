<?php
require_once dirname(__DIR__) . '/core/Database.php';

class ImportModel extends Database
{
    // Get all imports with optional status filter
    public function getAll($status = '')
    {
        $sql = "SELECT i.*, u.fullname as creator_name,
                (SELECT COUNT(*) FROM import_items WHERE import_id = i.id) as item_count
                FROM imports i
                LEFT JOIN users u ON i.created_by = u.id";
        
        if ($status) {
            $sql .= " WHERE i.status = ?";
            $sql .= " ORDER BY i.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $sql .= " ORDER BY i.created_at DESC";
            $stmt = $this->conn->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get import by ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM imports WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get import items
    public function getItems($importId)
    {
        $stmt = $this->conn->prepare("
            SELECT ii.*, p.name as product_name, p.image
            FROM import_items ii
            JOIN products p ON ii.product_id = p.id
            WHERE ii.import_id = ?
        ");
        $stmt->execute([$importId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Create new import
    public function create($importCode, $supplier, $note, $totalAmount, $status, $createdBy)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO imports (import_code, supplier, note, total_amount, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$importCode, $supplier, $note, $totalAmount, $status, $createdBy]);
        return $this->conn->lastInsertId();
    }
    
    // Add import item
    public function addItem($importId, $productId, $quantity, $price)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO import_items (import_id, product_id, quantity, import_price)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$importId, $productId, $quantity, $price]);
    }
    
    // Complete import
    public function complete($id)
    {
        $stmt = $this->conn->prepare("UPDATE imports SET status = 'completed', completed_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Delete import (only drafts)
    public function delete($id)
    {
        // Items will be deleted by CASCADE
        $stmt = $this->conn->prepare("DELETE FROM imports WHERE id = ? AND status = 'draft'");
        return $stmt->execute([$id]);
    }
}
