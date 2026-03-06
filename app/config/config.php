<?php
// 1. THÔNG SỐ DATABASE
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'techsmart');

// 2. XỬ LÝ ĐƯỜNG DẪN URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Auto-detect subdirectory from script location
// SCRIPT_NAME = /techmark/web2/public/index.php => dirname = /techmark/web2/public/
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$scriptDir = rtrim($scriptDir, '/') . '/';

define('BASE_URL', $protocol . $host . $scriptDir);

// 3. XỬ LÝ ĐƯỜNG DẪN HỆ THỐNG (Bổ sung cái này)
// Định nghĩa thư mục chứa code (dùng cho require_once trong Controller)
define('APPROOT', dirname(dirname(__FILE__))); 

// Tên website
define('SITENAME', 'TechSmart Store');
?>