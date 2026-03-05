-- 1. Tạo Database tên là techsmart (nếu chưa có)
CREATE DATABASE IF NOT EXISTS techsmart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Chọn Database này để làm việc
USE techsmart;

-- 3. Tạo bảng Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tạo bảng Categories (Danh mục)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

-- 5. Tạo bảng Products (Sản phẩm)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255),
    description TEXT,
    price DECIMAL(10, 2),
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

--
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 6. Tạo bảng Orders (Đơn hàng) - Đã bổ sung thông tin nhận hàng
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- Người đặt hàng (có thể null nếu cho khách vãng lai mua)
    
    -- Thông tin người nhận (QUAN TRỌNG)
    fullname VARCHAR(100) NOT NULL, 
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    note TEXT, -- Ghi chú đơn hàng
    
    total_money DECIMAL(10, 2),
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Tạo bảng Order Items (Chi tiết đơn hàng)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 8. Tạo bảng Reviews (Đánh giá)
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 9. Thêm tài khoản Admin mẫu để test
-- Mật khẩu là: 123456
INSERT INTO users (fullname, email, password, role) 
VALUES ('Admin TechSmart', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- 10. Thêm danh mục
INSERT INTO categories (name) VALUES ('Laptop'), ('Điện thoại'), ('Linh Kiện');

-- 11. Thêm nhiều sản phẩm mẫu để test phân trang (20 sản phẩm)

-- LAPTOP (Category 1) - 8 sản phẩm
INSERT INTO products (category_id, name, price, image, description, stock) VALUES 
(1, 'MacBook Air M1', 18000000, 'laptop.jpg', 'Chip M1 mạnh mẽ, pin trâu, thiết kế mỏng nhẹ', 15),
(1, 'MacBook Pro M2', 32000000, 'laptop.jpg', 'Hiệu năng đỉnh cao với chip M2, màn hình Retina', 10),
(1, 'Dell XPS 13', 25000000, 'laptop.jpg', 'Laptop cao cấp, màn hình InfinityEdge, hiệu năng mạnh', 12),
(1, 'HP Pavilion 15', 15000000, 'laptop.jpg', 'Laptop đa năng, phù hợp văn phòng và giải trí', 20),
(1, 'Lenovo ThinkPad X1', 28000000, 'laptop.jpg', 'Laptop doanh nhân, bàn phím tốt, bảo mật cao', 8),
(1, 'Asus ROG Strix G15', 35000000, 'laptop.jpg', 'Laptop gaming mạnh mẽ, RTX 3060, màn hình 144Hz', 6),
(1, 'Acer Aspire 5', 12000000, 'laptop.jpg', 'Laptop giá rẻ, cấu hình ổn định cho học tập', 25),
(1, 'MSI GF63 Thin', 18000000, 'laptop.jpg', 'Laptop gaming mỏng nhẹ, GTX 1650, giá tốt', 10);

-- ĐIỆN THOẠI (Category 2) - 8 sản phẩm
INSERT INTO products (category_id, name, price, image, description, stock) VALUES 
(2, 'iPhone 15 Pro Max', 32000000, 'dienthoai.jpg', 'Titanium, chip A17 Pro, camera 48MP', 20),
(2, 'iPhone 14', 20000000, 'dienthoai.jpg', 'Chip A15 Bionic, camera kép, màn hình OLED', 30),
(2, 'Samsung Galaxy S24 Ultra', 28000000, 'dienthoai.jpg', 'Snapdragon 8 Gen 3, camera 200MP, S Pen', 15),
(2, 'Samsung Galaxy A54', 9000000, 'dienthoai.jpg', 'Tầm trung cao cấp, camera 50MP, pin 5000mAh', 40),
(2, 'Xiaomi 13 Pro', 15000000, 'dienthoai.jpg', 'Snapdragon 8 Gen 2, camera Leica, sạc nhanh 120W', 18),
(2, 'OPPO Find N3', 22000000, 'dienthoai.jpg', 'Điện thoại gập, Snapdragon 8 Gen 2, màn hình lớn', 8),
(2, 'Vivo V29', 11000000, 'dienthoai.jpg', 'Camera selfie 50MP, thiết kế đẹp, sạc nhanh', 25),
(2, 'Realme 11 Pro', 8000000, 'dienthoai.jpg', 'Giá rẻ, hiệu năng tốt, pin khủng', 35);

-- LINH KIỆN (Category 3) - 6 sản phẩm
INSERT INTO products (category_id, name, price, image, description, stock) VALUES 
(3, 'Chuột Logitech MX Master 3', 2500000, 'phukien.jpg', 'Chuột không dây cao cấp, đa thiết bị, pin lâu', 50),
(3, 'Bàn phím Keychron K2', 2000000, 'phukien.jpg', 'Bàn phím cơ, kết nối Bluetooth, hot-swap', 30),
(3, 'Tai nghe Sony WH-1000XM5', 8000000, 'phukien.jpg', 'Chống ồn chủ động hàng đầu, âm thanh Hi-Res', 20),
(3, 'Webcam Logitech C920', 1500000, 'phukien.jpg', 'Webcam Full HD, tự động lấy nét, micro tích hợp', 40),
(3, 'USB SanDisk 128GB', 400000, 'phukien.jpg', 'USB 3.0, tốc độ cao, thiết kế nhỏ gọn', 100),
(3, 'Ổ cứng SSD Samsung 1TB', 2500000, 'phukien.jpg', 'SSD NVMe, tốc độ đọc/ghi cực nhanh', 25);
