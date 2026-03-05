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
    public function getReviews($productId)
    {
        $stmt = $this->conn->prepare("SELECT r.*, u.fullname FROM reviews r JOIN users u ON r.user_id = u.id WHERE product_id = ? ORDER BY r.created_at DESC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm đánh giá
    public function addReview($userId, $productId, $rating, $comment)
    {
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 3. Hàm lấy ID vừa insert
    public function getLastId() {
        return $this->conn->lastInsertId();
    }

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

        // 2. Xử lý danh mục (WHERE IN)
        if (!empty($categories)) {
            $placeholders = [];
            foreach ($categories as $key => $catId) {
                // Tạo tên tham số động: :cat_0, :cat_1...
                $ph = ":cat_$key"; 
                $placeholders[] = $ph;
                $params[$ph] = $catId;
            }
            // Nối chuỗi: AND category_id IN (:cat_0, :cat_1)
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

        // 5. Thực thi bằng PDO chuẩn (Đồng bộ với các hàm trên)
        $stmt = $this->conn->prepare($sql);
        
        // PDO cho phép truyền thẳng mảng params vào execute() rất tiện
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}