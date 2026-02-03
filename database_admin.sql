-- ========================================
-- TechSmart Admin Database Update
-- Run this after the initial database.sql
-- ========================================

-- 1. Update products table for inventory management
ALTER TABLE products ADD COLUMN IF NOT EXISTS stock INT DEFAULT 0;
ALTER TABLE products ADD COLUMN IF NOT EXISTS cost_price DECIMAL(12,2) DEFAULT 0;
ALTER TABLE products ADD COLUMN IF NOT EXISTS profit_margin DECIMAL(5,2) DEFAULT 10;
ALTER TABLE products ADD COLUMN IF NOT EXISTS status ENUM('visible', 'hidden') DEFAULT 'visible';

-- 2. Create imports table (Phiếu nhập hàng)
CREATE TABLE IF NOT EXISTS imports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    import_code VARCHAR(50) UNIQUE NOT NULL,
    supplier VARCHAR(255),
    note TEXT,
    total_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft', 'completed') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- 3. Create import_items table (Chi tiết phiếu nhập)
CREATE TABLE IF NOT EXISTS import_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    import_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    import_price DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (import_id) REFERENCES imports(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 4. Create categories table if not exists
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fa-folder',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add icon column if not exists (for existing tables)
ALTER TABLE categories ADD COLUMN IF NOT EXISTS icon VARCHAR(50) DEFAULT 'fa-folder';
ALTER TABLE categories ADD COLUMN IF NOT EXISTS description TEXT;

-- 5. Insert default categories if empty (without icon if column doesn't support)
INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Laptop'),
(2, 'Điện thoại'),
(3, 'Linh kiện');

-- Update icons for existing categories
UPDATE categories SET icon = 'fa-laptop' WHERE id = 1 AND (icon IS NULL OR icon = 'fa-folder');
UPDATE categories SET icon = 'fa-mobile-screen' WHERE id = 2 AND (icon IS NULL OR icon = 'fa-folder');
UPDATE categories SET icon = 'fa-microchip' WHERE id = 3 AND (icon IS NULL OR icon = 'fa-folder');

-- 6. Update users table for admin features
ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active', 'locked') DEFAULT 'active';
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin') DEFAULT 'user';

-- 7. Set initial stock for existing products (optional - set to 100 for demo)
UPDATE products SET stock = 100 WHERE stock = 0 OR stock IS NULL;
UPDATE products SET cost_price = price * 0.8 WHERE cost_price = 0 OR cost_price IS NULL;
