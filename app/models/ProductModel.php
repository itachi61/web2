<?php
// LƯU Ý: Model chỉ được gọi Database, KHÔNG gọi Controller
require_once dirname(__DIR__) . '/core/Database.php';

class ProductModel extends Database
{ 

// Truy vấn lấy tất cả sản phẩm
    public function getAllProducts()
    {
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Truy vấn lấy 1 sản phẩm
    public function getProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

// Lấy sản phẩm theo danh mục
    public function getProductsByCategory($categoryId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryName($id) {
        $stmt = $this->conn->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : 'Danh mục';
    }

// Tìm sản phẩm
    public function searchProduct($keyword)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE name LIKE ?");
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Thêm các hàm đánh giá (review) nếu cần
    public function getReviews($productId)
    {
        $stmt = $this->conn->prepare("SELECT r.*, u.fullname FROM reviews r JOIN users u ON r.user_id = u.id WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addReview($userId, $productId, $rating, $comment)
    {
        $stmt = $this->conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $productId, $rating, $comment]);
    }

// Thêm sản phẩm
    public function insertProduct($name, $cat_id, $price, $desc, $image)
    {
        $stmt = $this->conn->prepare("INSERT INTO products (name, category_id, price, description, image) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $cat_id, $price, $desc, $image]);
    }

// Xóa sản phẩm
    public function deleteProduct($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

// 1. Hàm thêm nhiều ảnh phụ
    public function addProductImage($productId, $imagePath) {
        $stmt = $this->conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
        return $stmt->execute([$productId, $imagePath]);
    }

    // 2. Hàm lấy danh sách ảnh phụ của 1 sản phẩm
    public function getProductImages($productId) {
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 3. Hàm lấy ID vừa insert (Để biết sản phẩm mới ID bao nhiêu mà gắn ảnh)
    public function getLastId() {
        return $this->conn->lastInsertId();
    }
}
