<?php
session_start();

// 1. Cấu hình hiển thị lỗi (Tắt khi deploy lên host thật)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Định nghĩa thư mục gốc (ROOT) để dùng cho tiện
// C:\Users\THANHDAT\Desktop\techsmart
define('ROOT', dirname(__DIR__)); 

// 3. Load các file hệ thống (Thứ tự rất quan trọng)
require_once ROOT . '/app/config/config.php';   // Cấu hình
require_once ROOT . '/app/core/Database.php';   // Database
require_once ROOT . '/app/core/Controller.php'; // Controller cha
require_once ROOT . '/app/core/App.php';        // Router (Điều hướng)

// 4. Khởi chạy ứng dụng
$app = new App();
?>