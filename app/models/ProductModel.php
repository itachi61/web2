<?php
// LƯU Ý: Model chỉ được gọi Database, KHÔNG gọi Controller
require_once dirname(__DIR__) . '/core/Database.php';

class ProductModel extends Database
{ 
    /**
     * Lấy tất cả sản phẩm (có phân trang)
     */
    public function getAllProducts($page = 1, $perPage = 12)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số sản phẩm
     */
    public function countAllProducts()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM products");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Lấy 1 sản phẩm theo ID
     */
    public function getProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm theo danh mục (có phân trang)
     */
    public function getProductsByCategory($categoryId, $page = 1, $perPage = 12)
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE category_id = :catId ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':catId', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm sản phẩm theo danh mục
     */
    public function countProductsByCategory($categoryId)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Lấy tên danh mục
     */
    public function getCategoryName($id) {
        $stmt = $this->conn->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : 'Danh mục';
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAllCategories() {
        $stmt = $this->conn->query("SELECT * FROM categories ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm sản phẩm cơ bản (theo tên)
     */
    public function searchProduct($keyword)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE name LIKE ?");
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy đánh giá sản phẩm
     */
    public function getReviews($productId)
    {
        $stmt = $this->conn->prepare("SELECT r.*, u.fullname FROM reviews r JOIN users u ON r.user_id = u.id WHERE product_id = ? ORDER BY r.created_at DESC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm đánh giá
     */
    public function addReview($userId, $productId, $rating, $comment)
    {
        $stmt = $this->conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $productId, $rating, $comment]);
    }

    /**
     * Thêm sản phẩm
     */
    public function insertProduct($name, $cat_id, $price, $desc, $image)
    {
        $stmt = $this->conn->prepare("INSERT INTO products (name, category_id, price, description, image) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $cat_id, $price, $desc, $image]);
    }

    /**
     * Xóa sản phẩm
     */
    public function deleteProduct($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Thêm ảnh phụ cho sản phẩm
     */
    public function addProductImage($productId, $imagePath) {
        $stmt = $this->conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
        return $stmt->execute([$productId, $imagePath]);
    }

    /**
     * Lấy danh sách ảnh phụ của sản phẩm
     */
    public function getProductImages($productId) {
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy ID vừa insert
     */
    public function getLastId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Tìm kiếm nâng cao (có phân trang)
     */
    public function searchProductAdvanced($keyword, $categories = [], $minPrice = null, $maxPrice = null, $sort = 'newest', $page = 1, $perPage = 12) {
        
        $offset = ($page - 1) * $perPage;
        
        // 1. Khởi tạo câu SQL
        $sql = "SELECT * FROM products WHERE name LIKE :keyword";
        
        // Mảng chứa giá trị để bind vào câu SQL
        $params = [':keyword' => "%$keyword%"];

        // 2. Xử lý danh mục (WHERE IN)
        if (!empty($categories)) {
            $placeholders = [];
            foreach ($categories as $key => $catId) {
                $ph = ":cat_$key"; 
                $placeholders[] = $ph;
                $params[$ph] = $catId;
            }
            $sql .= " AND category_id IN (" . implode(', ', $placeholders) . ")";
        }

        // 3. Xử lý khoảng giá
        if (!empty($minPrice)) {
            $sql .= " AND price >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }
        if (!empty($maxPrice)) {
            $sql .= " AND price <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        // 4. Xử lý sắp xếp
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY price DESC";
                break;
            default: // newest
                $sql .= " ORDER BY created_at DESC";
                break;
        }

        // 5. Thêm LIMIT và OFFSET
        $sql .= " LIMIT $perPage OFFSET $offset";

        // 6. Thực thi
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm kết quả tìm kiếm nâng cao
     */
    public function countSearchResults($keyword, $categories = [], $minPrice = null, $maxPrice = null) {
        
        $sql = "SELECT COUNT(*) as total FROM products WHERE name LIKE :keyword";
        $params = [':keyword' => "%$keyword%"];

        if (!empty($categories)) {
            $placeholders = [];
            foreach ($categories as $key => $catId) {
                $ph = ":cat_$key"; 
                $placeholders[] = $ph;
                $params[$ph] = $catId;
            }
            $sql .= " AND category_id IN (" . implode(', ', $placeholders) . ")";
        }

        if (!empty($minPrice)) {
            $sql .= " AND price >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }
        if (!empty($maxPrice)) {
            $sql .= " AND price <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }
}