<?php
// Test script to verify category filtering
session_start();
define('ROOT', dirname(__DIR__));
require_once ROOT . '/app/config/config.php';
require_once ROOT . '/app/core/Database.php';

// Create database connection
$db = new Database();
$conn = $db->getConnection();

echo "<h1>Category Filter Test</h1>";

// Test category 2 (Điện thoại)
$categoryId = 2;
$page = 1;
$perPage = 8;
$offset = ($page - 1) * $perPage;

echo "<h2>Testing Category ID: $categoryId (Điện thoại)</h2>";

// Run the same query as ProductModel
$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p><strong>Query:</strong> SELECT * FROM products WHERE category_id = $categoryId ORDER BY id DESC LIMIT $perPage OFFSET $offset</p>";
echo "<p><strong>Products found:</strong> " . count($products) . "</p>";

if (!empty($products)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Category ID</th><th>Price</th></tr>";
    foreach ($products as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $product['category_id'] . "</td>";
        echo "<td>" . number_format($product['price'], 0, ',', '.') . "đ</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No products found!</p>";
}

// Test all categories
echo "<hr><h2>All Categories Summary</h2>";
for ($catId = 1; $catId <= 3; $catId++) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $stmt->execute([$catId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $catNames = [1 => 'Laptop', 2 => 'Điện thoại', 3 => 'Linh kiện'];
    echo "<p><strong>Category $catId ({$catNames[$catId]}):</strong> {$result['count']} products</p>";
}
?>
