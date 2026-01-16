<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'techsmart');

// Tự động phát hiện protocol (http hay https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Lấy đường dẫn thư mục chứa file chạy hiện tại (thường là /techsmart/public)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); 
$scriptName = rtrim($scriptName, '/');

// --- SỬA LỖI QUAN TRỌNG TẠI ĐÂY ---
// Loại bỏ chữ '/public' khỏi đường dẫn gốc
// Để BASE_URL chỉ còn là: http://localhost/techsmart/
$scriptName = str_replace('/public', '', $scriptName);

define('BASE_URL', $protocol . $host . $scriptName . '/');

?>

