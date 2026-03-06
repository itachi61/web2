-- Migration: Thêm cột status cho users và sold_count cho products
-- Chạy SQL này trong phpMyAdmin

-- Bug 5: Thêm cột status cho bảng users (khóa/mở khóa tài khoản)
ALTER TABLE users ADD COLUMN status ENUM('active', 'locked') DEFAULT 'active' AFTER role;

-- Bug 7: Thêm cột sold_count cho bảng products
ALTER TABLE products ADD COLUMN sold_count INT DEFAULT 0 AFTER stock;

-- Seed random stock (30-50) và sold_count (2-10) cho sản phẩm hiện có
UPDATE products SET 
    stock = FLOOR(30 + RAND() * 21),
    sold_count = FLOOR(2 + RAND() * 9);
