<?php

require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/core/Database.php';

echo "=== TechSmart Database Setup ===\n\n";

try {
    // First, create database connection without database name
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL server\n";
    
    // Read and execute SQL schema
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    echo "✓ SQL schema loaded (" . count($statements) . " statements)\n\n";
    echo "Executing SQL statements...\n";
    
    foreach ($statements as $index => $statement) {
        try {
            $pdo->exec($statement);
            
            // Show progress for important statements
            if (stripos($statement, 'CREATE DATABASE') !== false) {
                echo "✓ Database created\n";
            } elseif (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'unknown';
                echo "✓ Table created: $tableName\n";
            } elseif (stripos($statement, 'INSERT INTO users') !== false) {
                echo "✓ Admin account created (username: admin, password: 123456)\n";
            } elseif (stripos($statement, 'INSERT INTO categories') !== false) {
                echo "✓ Sample categories inserted\n";
            } elseif (stripos($statement, 'INSERT INTO products') !== false) {
                echo "✓ Sample products inserted\n";
            }
        } catch (PDOException $e) {
            echo "✗ Error executing statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Database Setup Complete ===\n\n";
    
    // Test connection with Database class
    echo "Testing Database class connection...\n";
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Count records
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM categories");
    $categoryCount = $stmt->fetch()['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM products");
    $productCount = $stmt->fetch()['count'];
    
    echo "✓ Database connection successful\n";
    echo "✓ Users: $userCount\n";
    echo "✓ Categories: $categoryCount\n";
    echo "✓ Products: $productCount\n";
    
    echo "\n=== Setup Summary ===\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Admin Username: admin\n";
    echo "Admin Password: 123456\n";
    echo "Admin Email: admin@techsmart.com\n";
    echo "\n✓ All done! You can now use the application.\n";
    
} catch (PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
