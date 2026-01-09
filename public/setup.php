<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechSmart Database Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .output {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .success {
            color: #28a745;
            font-weight: bold;
        }
        
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        
        .info {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .info h3 {
            color: #2196F3;
            margin-bottom: 10px;
        }
        
        .credentials {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .credentials h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .credentials p {
            margin: 5px 0;
            color: #856404;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .warning {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 TechSmart Database Setup</h1>
        <p class="subtitle">Thiết lập database cho website TechSmart E-commerce</p>
        
        <?php
        require_once __DIR__ . '/../app/config/config.php';
        require_once __DIR__ . '/../app/core/Database.php';
        
        echo '<div class="output">';
        
        try {
            // First, create database connection without database name
            $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<span class="success">✓ Kết nối MySQL server thành công</span><br>';
            
            // Read and execute SQL schema
            $sql = file_get_contents(__DIR__ . '/../storage/database/schema.sql');
            
            // Split SQL into individual statements
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                function($stmt) {
                    return !empty($stmt) && !preg_match('/^--/', $stmt);
                }
            );
            
            echo '<span class="success">✓ Đã tải SQL schema (' . count($statements) . ' câu lệnh)</span><br><br>';
            echo '<strong>Đang thực thi các câu lệnh SQL...</strong><br>';
            
            foreach ($statements as $index => $statement) {
                try {
                    $pdo->exec($statement);
                    
                    // Show progress for important statements
                    if (stripos($statement, 'CREATE DATABASE') !== false) {
                        echo '<span class="success">✓ Đã tạo database</span><br>';
                    } elseif (stripos($statement, 'CREATE TABLE') !== false) {
                        preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches);
                        $tableName = $matches[1] ?? 'unknown';
                        echo '<span class="success">✓ Đã tạo bảng: ' . $tableName . '</span><br>';
                    } elseif (stripos($statement, 'INSERT INTO users') !== false) {
                        echo '<span class="success">✓ Đã tạo tài khoản admin (username: admin, password: 123456)</span><br>';
                    } elseif (stripos($statement, 'INSERT INTO categories') !== false) {
                        echo '<span class="success">✓ Đã thêm danh mục mẫu</span><br>';
                    } elseif (stripos($statement, 'INSERT INTO products') !== false) {
                        echo '<span class="success">✓ Đã thêm sản phẩm mẫu</span><br>';
                    }
                } catch (PDOException $e) {
                    echo '<span class="error">✗ Lỗi câu lệnh ' . ($index + 1) . ': ' . $e->getMessage() . '</span><br>';
                }
            }
            
            echo '<br><strong class="success">✓ Hoàn tất thiết lập database!</strong><br><br>';
            
            // Test connection with Database class
            echo '<strong>Đang kiểm tra kết nối Database class...</strong><br>';
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            // Count records
            $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
            $userCount = $stmt->fetch()['count'];
            
            $stmt = $conn->query("SELECT COUNT(*) as count FROM categories");
            $categoryCount = $stmt->fetch()['count'];
            
            $stmt = $conn->query("SELECT COUNT(*) as count FROM products");
            $productCount = $stmt->fetch()['count'];
            
            echo '<span class="success">✓ Kết nối database thành công</span><br>';
            echo '<span class="success">✓ Số người dùng: ' . $userCount . '</span><br>';
            echo '<span class="success">✓ Số danh mục: ' . $categoryCount . '</span><br>';
            echo '<span class="success">✓ Số sản phẩm: ' . $productCount . '</span><br>';
            
            echo '</div>';
            
            echo '<div class="credentials">';
            echo '<h3>🔐 Thông Tin Tài Khoản Admin</h3>';
            echo '<p><strong>Username:</strong> admin</p>';
            echo '<p><strong>Password:</strong> 123456</p>';
            echo '<p><strong>Email:</strong> admin@techsmart.com</p>';
            echo '<p><strong>Role:</strong> admin</p>';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<h3>📊 Thông Tin Database</h3>';
            echo '<p><strong>Database:</strong> ' . DB_NAME . '</p>';
            echo '<p><strong>Host:</strong> ' . DB_HOST . '</p>';
            echo '<p><strong>Charset:</strong> ' . DB_CHARSET . '</p>';
            echo '</div>';
            
            echo '<div class="warning">';
            echo '<strong>⚠️ Lưu ý bảo mật:</strong><br>';
            echo '1. Xóa file này (setup.php) sau khi hoàn tất thiết lập<br>';
            echo '2. Đổi mật khẩu admin ngay sau khi đăng nhập lần đầu<br>';
            echo '3. Không chia sẻ thông tin database với người khác';
            echo '</div>';
            
            echo '<a href="' . BASE_URL . '" class="btn">🏠 Về Trang Chủ</a>';
            
        } catch (PDOException $e) {
            echo '<span class="error">✗ Lỗi database: ' . $e->getMessage() . '</span><br>';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<h3>💡 Hướng dẫn khắc phục:</h3>';
            echo '<p>1. Kiểm tra MySQL đã khởi động chưa (XAMPP/WAMP)</p>';
            echo '<p>2. Kiểm tra thông tin database trong file config.php</p>';
            echo '<p>3. Đảm bảo user "root" có quyền tạo database</p>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<span class="error">✗ Lỗi: ' . $e->getMessage() . '</span><br>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
