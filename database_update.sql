-- =====================================================
-- TechSmart Database Update - User Frontend Features
-- Run this script to add new columns for shipping info
-- =====================================================

USE techsmart;

-- 1. Thêm các trường thông tin giao hàng vào bảng users
ALTER TABLE users 
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER email,
    ADD COLUMN IF NOT EXISTS address VARCHAR(255) AFTER phone,
    ADD COLUMN IF NOT EXISTS ward VARCHAR(100) AFTER address,
    ADD COLUMN IF NOT EXISTS district VARCHAR(100) AFTER ward,
    ADD COLUMN IF NOT EXISTS is_locked TINYINT(1) DEFAULT 0 AFTER role;

-- 2. Thêm các trường mới vào bảng orders
ALTER TABLE orders 
    ADD COLUMN IF NOT EXISTS fullname VARCHAR(100) AFTER user_id,
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER fullname,
    ADD COLUMN IF NOT EXISTS ward VARCHAR(100) AFTER address,
    ADD COLUMN IF NOT EXISTS district VARCHAR(100) AFTER ward,
    ADD COLUMN IF NOT EXISTS payment_method ENUM('cash', 'bank_transfer', 'online') DEFAULT 'cash' AFTER note;

-- 3. Đổi status sang ENUM nếu chưa có
ALTER TABLE orders 
    MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending';

-- =====================================================
-- Xong! Chạy script này trong phpMyAdmin hoặc MySQL CLI
-- =====================================================
