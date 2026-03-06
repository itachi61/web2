<?php
require_once dirname(__DIR__) . '/core/Database.php';

class ProductModel extends Database
{
    // --- 1. LẤY TẤT CẢ SẢN PHẨM (CÓ PHÂN TRANG) ---
    public function getAllProducts($page = 1, $limit = 12)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        
        // PDO cần bind tham số limit/offset dưới dạng INT
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số sản phẩm (Để tính số trang)
    public function countAllProducts()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM products");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // --- 2. CHI TIẾT SẢN PHẨM & LIÊN QUAN ---
    public function getProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

<<<<<<< HEAD
=======
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

    // Lấy tất cả danh mục
    public function getAllCategories() {
        $stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm sản phẩm cơ bản (theo tên)
    public function searchProduct($keyword)
    {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE name LIKE ?");
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đánh giá
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
    public function getReviews($productId)
    {
        $sql = "SELECT r.*, u.fullname 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = :id 
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductImages($productId)
    {
<<<<<<< HEAD
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
=======
        $stmt = $this->conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $productId, $rating, $comment]);
    }

    // Thêm sản phẩm
    public function insertProduct($name, $cat_id, $price, $desc, $image, $discount = 0, $cost_price = 0)
    {
        $stmt = $this->conn->prepare("INSERT INTO products (name, category_id, price, discount, cost_price, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $cat_id, $price, $discount, $cost_price, $desc, $image]);
    }

    // Cập nhật sản phẩm
    public function updateProduct($id, $name, $cat_id, $price, $desc, $image = null, $discount = 0, $cost_price = 0) {
        if ($image) {
            $stmt = $this->conn->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, discount = ?, cost_price = ?, description = ?, image = ? WHERE id = ?");
            return $stmt->execute([$name, $cat_id, $price, $discount, $cost_price, $desc, $image, $id]);
        } else {
            $stmt = $this->conn->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, discount = ?, cost_price = ?, description = ? WHERE id = ?");
            return $stmt->execute([$name, $cat_id, $price, $discount, $cost_price, $desc, $id]);
        }
    }

    // Xóa sản phẩm
    public function deleteProduct($id)
    {
        // Xóa ảnh phụ trước
        $stmt = $this->conn->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt->execute([$id]);
        // Xóa sản phẩm
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
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 3. LẤY SẢN PHẨM THEO DANH MỤC (CÓ PHÂN TRANG) ---
    public function getProductsByCategory($categoryId, $page = 1, $limit = 12)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM products WHERE category_id = :cid ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':cid', $categoryId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

<<<<<<< HEAD
    public function countProductsByCategory($categoryId)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM products WHERE category_id = :cid");
        $stmt->execute([':cid' => $categoryId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
=======
    // --- SORT HELPER ---
    private function getOrderByClause($sort = 'newest') {
        switch ($sort) {
            case 'price_asc':
                return "ORDER BY price ASC";
            case 'price_desc':
                return "ORDER BY price DESC";
            default:
                return "ORDER BY id DESC";
        }
    }

    // --- PAGINATION METHODS ---
    
    // Lấy tất cả sản phẩm với phân trang + sắp xếp
    public function getAllProductsPaginated($page = 1, $perPage = 8, $sort = 'newest') {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->getOrderByClause($sort);
        $stmt = $this->conn->prepare("SELECT * FROM products $orderBy LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm theo danh mục với phân trang + sắp xếp
    public function getProductsByCategoryPaginated($categoryId, $page = 1, $perPage = 8, $sort = 'newest') {
        $offset = ($page - 1) * $perPage;
        $orderBy = $this->getOrderByClause($sort);
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE category_id = :categoryId $orderBy LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số sản phẩm
    public function getTotalProductsCount() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM products");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Đếm tổng số sản phẩm theo danh mục
    public function getTotalProductsByCategoryCount($categoryId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }


    // --- TÌM KIẾM NÂNG CAO (ĐÃ CHUẨN HÓA VỀ PDO THUẦN) ---
    public function searchProductAdvanced($keyword, $categories = [], $minPrice = null, $maxPrice = null, $sort = 'newest') {
        
        // 1. Khởi tạo câu SQL
        $sql = "SELECT * FROM products WHERE name LIKE :keyword";
        
        // Mảng chứa giá trị để bind vào câu SQL
        $params = [':keyword' => "%$keyword%"];
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f

    public function getCategoryName($id)
    {
        $stmt = $this->conn->prepare("SELECT name FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['name'] : 'Danh mục';
    }

    public function getAllCategories()
    {
        $stmt = $this->conn->query("SELECT * FROM categories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 4. TÌM KIẾM NÂNG CAO & LỌC (QUAN TRỌNG) ---
    public function searchProductAdvanced($keyword, $categories = [], $minPrice = null, $maxPrice = null, $sort = 'newest', $page = 1, $limit = 12)
    {
        // Xây dựng câu SQL động với WHERE 1=1 để linh hoạt
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];

        // A. Lọc từ khóa
        if (!empty($keyword)) {
            $sql .= " AND name LIKE :keyword";
            $params[':keyword'] = "%$keyword%";
        }

        // B. Lọc danh mục (WHERE IN)
        if (!empty($categories) && is_array($categories)) {
            $placeholders = [];
            foreach ($categories as $k => $v) {
                $key = ":cat$k";
                $placeholders[] = $key;
                $params[$key] = $v;
            }
            $sql .= " AND category_id IN (" . implode(',', $placeholders) . ")";
        }

        // C. Lọc giá
        if (!empty($minPrice)) {
            $sql .= " AND price >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }
        if (!empty($maxPrice)) {
            $sql .= " AND price <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        // D. Sắp xếp
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

        // E. Phân trang
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT :limit OFFSET :offset";

        // Thực thi
        $stmt = $this->conn->prepare($sql);

        // Bind các tham số lọc
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Bind tham số phân trang
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm kết quả tìm kiếm (Để tính số trang khi đang search)
    public function countSearchResults($keyword, $categories = [], $minPrice = null, $maxPrice = null)
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND name LIKE :keyword";
            $params[':keyword'] = "%$keyword%";
        }

        if (!empty($categories) && is_array($categories)) {
            $placeholders = [];
            foreach ($categories as $k => $v) {
                $key = ":cat$k";
                $placeholders[] = $key;
                $params[$key] = $v;
            }
            $sql .= " AND category_id IN (" . implode(',', $placeholders) . ")";
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
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // --- 5. CHỨC NĂNG KHÁC ---
    public function addReview($userId, $productId, $rating, $comment)
    {
        $sql = "INSERT INTO reviews (user_id, product_id, rating, comment, created_at) VALUES (:uid, :pid, :rating, :comment, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId,
            ':pid' => $productId,
            ':rating' => $rating,
            ':comment' => $comment
        ]);
    }

    // --- 6. QUẢN LÝ KHO (INVENTORY) & ADMIN ---
    
    // Lấy tất cả sản phẩm kèm tên danh mục (cho admin)
    public function getAllWithCategory() {
        $stmt = $this->conn->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.stock ASC, p.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật tồn kho và giá nhập
    public function updateStock($productId, $newStock, $newCostPrice) {
        $stmt = $this->conn->prepare("SELECT profit_margin FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $profitMargin = $product['profit_margin'] ?? 10;
        $newPrice = $newCostPrice * (1 + $profitMargin / 100);
        
        $stmt = $this->conn->prepare("
            UPDATE products 
            SET stock = ?, cost_price = ?, price = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$newStock, $newCostPrice, $newPrice, $productId]);
    }

    // Cập nhật tồn kho và tỷ lệ lợi nhuận
    public function updateStockAndMargin($productId, $stock, $profitMargin) {
        $stmt = $this->conn->prepare("SELECT cost_price, price FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) return false;
        
        $costPrice = $product['cost_price'] ?? ($product['price'] * 0.8);
        $newPrice = $costPrice * (1 + $profitMargin / 100);
        
        $stmt = $this->conn->prepare("
            UPDATE products 
            SET stock = ?, profit_margin = ?, price = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$stock, $profitMargin, $newPrice, $productId]);
    }

    // Thêm sản phẩm đầy đủ (Admin)
    public function insertProductFull($name, $cat_id, $price, $desc, $image, $stock = 0, $profitMargin = 10) {
        $costPrice = $price / (1 + $profitMargin / 100);
        $stmt = $this->conn->prepare("
            INSERT INTO products (name, category_id, price, description, image, stock, cost_price, profit_margin) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $cat_id, $price, $desc, $image, $stock, $costPrice, $profitMargin]);
    }
    
    // Hàm thêm sản phẩm cơ bản (để tương thích code cũ nếu có)
    public function insertProduct($name, $cat_id, $price, $desc, $image) {
        return $this->insertProductFull($name, $cat_id, $price, $desc, $image, 0, 10);
    }

    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function addProductImage($productId, $imagePath) {
        $stmt = $this->conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
        return $stmt->execute([$productId, $imagePath]);
    }
    
    public function getLastId() {
        return $this->conn->lastInsertId();
    }
}