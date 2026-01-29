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

-- 5.1. Tạo bảng product_images (Ảnh phụ của sản phẩm)
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

-- 9. (TÙY CHỌN) Thêm tài khoản Admin mẫu để test
-- Mật khẩu là: 123456
INSERT INTO users (fullname, email, password, role) 
VALUES ('Admin TechSmart', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- 10. (TÙY CHỌN) Thêm danh mục và sản phẩm mẫu
INSERT INTO categories (name) VALUES ('Laptop'), ('Điện thoại'), ('Linh Kiện'), ('Màn hình'), ('Bàn phím');

INSERT INTO products (category_id, name, price, image, description, stock) VALUES 
(1, 'MacBook Air M1', 18000000, 'macbook-air-m1.jpg', 'MacBook Air M1 - Chip M1 mạnh mẽ, pin trâu, thiết kế siêu mỏng nhẹ', 10),
(2, 'iPhone 15 Pro Max', 32000000, 'iphone-15-pro-max.jpg', 'iPhone 15 Pro Max - Vỏ Titanium, chip A17 Pro, camera 48MP', 15),
(1, 'Dell XPS 13', 25000000, 'dell-xps-13.jpg', 'Dell XPS 13 - Màn hình InfinityEdge, Core i7 Gen 12, SSD 512GB', 8),
(2, 'Samsung Galaxy S24 Ultra', 28000000, 'samsung-s24-ultra.jpg', 'Samsung Galaxy S24 Ultra - S Pen tích hợp, màn hình AMOLED 120Hz', 12),
(3, 'RTX 4090', 45000000, 'rtx-4090.jpg', 'NVIDIA GeForce RTX 4090 - Card đồ họa mạnh nhất thế giới', 5),
(4, 'LG UltraGear 27"', 8000000, 'lg-ultragear-27.jpg', 'Màn hình gaming LG 27" - 144Hz, IPS, 1ms response time', 20),
(5, 'Keychron K2', 2500000, 'keychron-k2.jpg', 'Bàn phím cơ Keychron K2 - Wireless, hot-swappable, RGB', 30),
(1, 'ASUS ROG Strix', 35000000, 'asus-rog.jpg', 'Laptop Gaming ASUS ROG - Core i9, RTX 4070, Màn hình 240Hz', 10),
(1, 'Lenovo ThinkPad X1', 42000000, 'lenovo-thinkpad.jpg', 'Lenovo ThinkPad X1 Carbon - Siêu bền, bàn phím trứ danh, bảo mật cao', 15),
(1, 'HP Pavilion 15', 15000000, 'hp-pavilion.jpg', 'HP Pavilion 15 - Thiết kế thời trang, hiệu năng ổn định cho văn phòng', 20),
(1, 'Acer Predator Helios', 38000000, 'acer-predator.jpg', 'Acer Predator Helios 300 - Chiến game đỉnh cao, tản nhiệt AeroBlade', 8),
(1, 'MSI Gaming GF63', 22000000, 'msi-gaming.jpg', 'MSI Gaming GF63 - Mỏng nhẹ, hiệu năng cao với RTX 3050', 12),
(1, 'Microsoft Surface Laptop 5', 29000000, 'surface-laptop.jpg', 'Surface Laptop 5 - Màn hình cảm ứng PixelSense, thiết kế kim loại nguyên khối', 10),
(1, 'LG Gram 2024', 33000000, 'lg-gram.jpg', 'LG Gram - Siêu nhẹ chỉ 999g, pin trâu cả ngày dài', 10),
(1, 'Razer Blade 15', 65000000, 'razer-blade.jpg', 'Razer Blade 15 - Laptop gaming đẹp nhất thế giới, màn hình OLED 240Hz', 5),
(1, 'Gigabyte Aero 16', 55000000, 'gigabyte-aero.jpg', 'Gigabyte Aero - Chuyên đồ họa, màn hình 4K OLED HDR', 7),
(1, 'HP Envy x360', 26000000, 'hp-envy.jpg', 'HP Envy x360 - Xoay gập 360 độ, màn hình cảm ứng, bút Stylus đi kèm', 12);
