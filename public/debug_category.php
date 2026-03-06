<?php
// Debug script - Test category controller
session_start();
define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/config/config.php';
require_once ROOT . '/app/core/Database.php';
require_once ROOT . '/app/core/Controller.php';
require_once ROOT . '/app/models/ProductModel.php';

echo "<h1>Category Controller Debug</h1>";

// Simulate category ID = 2 (Điện thoại)
$categoryId = 2;
$page = 1;
$perPage = 8;

// Create model
$db = new Database();
$model = new ProductModel($db->conn);

// Get products
$products = $model->getProductsByCategoryPaginated($categoryId, $page, $perPage);
$totalProducts = $model->getTotalProductsByCategoryCount($categoryId);
$categoryName = $model->getCategoryName($categoryId);

echo "<h2>Category: $categoryName (ID: $categoryId)</h2>";
echo "<p><strong>Total products:</strong> $totalProducts</p>";
echo "<p><strong>Products on page $page:</strong> " . count($products) . "</p>";

if (!empty($products)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Category ID</th><th>Image</th><th>Price</th></tr>";
    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $product['category_id'] . "</td>";
        echo "<td>" . $product['image'] . "</td>";
        echo "<td>" . number_format($product['price'], 0, ',', '.') . "đ</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Image Paths:</h3>";
    foreach ($products as $product) {
        $imagePath = BASE_URL . 'public/images/' . $product['image'];
        echo "<p><strong>{$product['name']}:</strong> <a href='$imagePath' target='_blank'>$imagePath</a></p>";
        echo "<img src='$imagePath' style='max-width: 100px; height: auto;' onerror='this.style.border=\"2px solid red\"'><br><br>";
    }
} else {
    echo "<p style='color: red;'>No products found!</p>";
}

// Test if view file exists
$viewPath = ROOT . '/app/views/products/category.php';
echo "<hr><h3>View File Check:</h3>";
echo "<p><strong>Path:</strong> $viewPath</p>";
echo "<p><strong>Exists:</strong> " . (file_exists($viewPath) ? "✅ YES" : "❌ NO") . "</p>";

if (file_exists($viewPath)) {
    echo "<p><strong>File size:</strong> " . filesize($viewPath) . " bytes</p>";
}
?>
