<?php
// FIX LỖI SESSION PERMISSION DENIED TRÊN XAMPP
$sessDir = __DIR__ . '/sessions';
if (!is_dir($sessDir)) {
    @mkdir($sessDir, 0777, true);
}
session_save_path($sessDir);
session_start();
require 'config/db.php';
require 'includes/functions.php';
require 'includes/header.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        require 'views/home.php';
        break;
    case 'detail':
        require 'views/detail.php';
        break;
    case 'cart':
        require 'views/cart.php';
        break;
    case 'auth':
        require 'views/auth.php';
        break;
    case 'admin_dashboard':
        require 'views/admin/dashboard.php';
        break;
    case 'admin_products':
        require 'views/admin/products.php';
        break;
    case 'admin_orders':
        require 'views/admin/orders.php';
        break;
    default:
        require 'views/home.php';
        break;
}

require 'includes/footer.php';
?>