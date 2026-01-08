-- TechSmart E-commerce Database Schema
-- Created: 2026-01-08

-- Create database
CREATE DATABASE IF NOT EXISTS techsmart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techsmart_db;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2),
    stock INT DEFAULT 0,
    images TEXT,
    specifications TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    payment_method ENUM('cod', 'bank_transfer', 'credit_card') DEFAULT 'cod',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cart
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin account
-- Password: 123456 (hashed with password_hash)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@techsmart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert sample categories
INSERT INTO categories (name, slug, description) VALUES
('Laptop', 'laptop', 'Laptop và máy tính xách tay'),
('Smartphone', 'smartphone', 'Điện thoại thông minh'),
('Tablet', 'tablet', 'Máy tính bảng'),
('Phụ kiện', 'phu-kien', 'Phụ kiện công nghệ');

-- Insert sample products
INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, status) VALUES
(1, 'MacBook Pro 14 M3', 'macbook-pro-14-m3', 'MacBook Pro 14 inch với chip M3 mạnh mẽ', 45990000, 43990000, 10, 'active'),
(1, 'Dell XPS 13', 'dell-xps-13', 'Laptop Dell XPS 13 siêu mỏng nhẹ', 28990000, NULL, 15, 'active'),
(2, 'iPhone 15 Pro Max', 'iphone-15-pro-max', 'iPhone 15 Pro Max 256GB', 32990000, 31990000, 20, 'active'),
(2, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Samsung Galaxy S24 Ultra 512GB', 29990000, NULL, 12, 'active'),
(3, 'iPad Pro 11', 'ipad-pro-11', 'iPad Pro 11 inch M2', 21990000, 20990000, 8, 'active'),
(4, 'AirPods Pro 2', 'airpods-pro-2', 'Tai nghe AirPods Pro thế hệ 2', 5990000, NULL, 30, 'active');
