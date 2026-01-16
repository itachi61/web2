<?php
// 1. THÔNG SỐ DATABASE
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'techsmart');

// 2. XỬ LÝ ĐƯỜNG DẪN URL (Logic của bạn - Giữ nguyên vì đang chạy tốt)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); 
$scriptName = rtrim($scriptName, '/');
$scriptName = str_replace('/public', '', $scriptName);

// Hằng số dùng cho HTML (Link, CSS, JS)
define('BASE_URL', $protocol . $host . $scriptName . '/');

// 3. XỬ LÝ ĐƯỜNG DẪN HỆ THỐNG (Bổ sung cái này)
// Định nghĩa thư mục chứa code (dùng cho require_once trong Controller)
define('APPROOT', dirname(dirname(__FILE__))); 

// Tên website
define('SITENAME', 'TechSmart Store');
?>