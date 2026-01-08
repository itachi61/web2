# Hướng Dẫn Setup Database - TechSmart

## Yêu Cầu
- XAMPP/WAMP/MAMP đã được cài đặt
- MySQL/MariaDB đang chạy
- PHP 7.4 trở lên

## Các Bước Setup

### Bước 1: Khởi động MySQL
1. Mở XAMPP Control Panel
2. Click "Start" cho Apache và MySQL
3. Đợi đến khi cả hai hiển thị màu xanh

### Bước 2: Chạy Setup Script

**Cách 1: Sử dụng Command Line**
```bash
# Nếu đã thêm PHP vào PATH
php storage/database/setup.php

# Hoặc sử dụng đường dẫn đầy đủ (ví dụ với XAMPP)
C:\xampp\php\php.exe storage/database/setup.php
```

**Cách 2: Sử dụng Browser**
1. Copy file `setup.php` vào thư mục `public`
2. Mở browser và truy cập: `http://localhost/techsmart/public/setup.php`
3. Sau khi setup xong, xóa file `setup.php` khỏi thư mục `public`

**Cách 3: Import SQL trực tiếp**
1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Click tab "SQL"
3. Copy toàn bộ nội dung file `storage/database/schema.sql`
4. Paste vào ô SQL và click "Go"

### Bước 3: Kiểm tra Database

1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Kiểm tra database `techsmart_db` đã được tạo
3. Kiểm tra các bảng:
   - users (có 1 admin account)
   - categories (có 4 categories)
   - products (có 6 products)
   - orders
   - order_items
   - cart

## Thông Tin Tài Khoản Admin

- **Username:** admin
- **Password:** 123456
- **Email:** admin@techsmart.com
- **Role:** admin

## Cấu Hình Database

File: `app/config/config.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'techsmart_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Nếu bạn sử dụng cấu hình khác, hãy chỉnh sửa các giá trị này.

## Cấu Trúc Database

### Bảng: users
- Quản lý người dùng và admin
- Password được hash bằng `password_hash()`
- Role: 'admin' hoặc 'user'

### Bảng: categories
- Danh mục sản phẩm
- Có slug để tạo URL thân thiện

### Bảng: products
- Sản phẩm
- Liên kết với categories
- Hỗ trợ giá sale
- Quản lý tồn kho

### Bảng: orders
- Đơn hàng
- Trạng thái: pending, processing, shipped, delivered, cancelled
- Phương thức thanh toán: cod, bank_transfer, credit_card

### Bảng: order_items
- Chi tiết sản phẩm trong đơn hàng
- Lưu giá tại thời điểm đặt hàng

### Bảng: cart
- Giỏ hàng của người dùng
- Tự động tính tổng tiền

## Sử Dụng Models

### User Model
```php
require_once 'app/models/User.php';

$userModel = new User();

// Đăng ký
$userId = $userModel->register([
    'username' => 'john',
    'email' => 'john@example.com',
    'password' => '123456',
    'full_name' => 'John Doe'
]);

// Đăng nhập
$user = $userModel->login('admin', '123456');
```

### Product Model
```php
require_once 'app/models/Product.php';

$productModel = new Product();

// Lấy sản phẩm mới nhất
$products = $productModel->getLatest(8);

// Tìm kiếm
$results = $productModel->search('iPhone');

// Lấy theo category
$products = $productModel->getByCategory(1);
```

### Cart Model
```php
require_once 'app/models/Cart.php';

$cartModel = new Cart();

// Thêm vào giỏ hàng
$cartModel->addItem($userId, $productId, $quantity);

// Lấy giỏ hàng
$items = $cartModel->getUserCart($userId);

// Tính tổng
$total = $cartModel->getTotal($userId);
```

### Order Model
```php
require_once 'app/models/Order.php';

$orderModel = new Order();

// Tạo đơn hàng
$items = [
    ['product_id' => 1, 'quantity' => 2, 'price' => 100000],
    ['product_id' => 2, 'quantity' => 1, 'price' => 200000]
];

$orderId = $orderModel->createOrder(
    $userId, 
    $items, 
    'Địa chỉ giao hàng',
    'cod'
);
```

## Troubleshooting

### Lỗi: "Access denied for user 'root'@'localhost'"
- Kiểm tra MySQL đã khởi động chưa
- Kiểm tra username/password trong `config.php`

### Lỗi: "Unknown database 'techsmart_db'"
- Chạy lại setup script
- Hoặc tạo database thủ công trong phpMyAdmin

### Lỗi: "Table doesn't exist"
- Import lại file `schema.sql`
- Kiểm tra các bảng đã được tạo trong phpMyAdmin

## Lưu Ý Bảo Mật

1. **Đổi mật khẩu admin** sau khi setup xong
2. **Không commit** file `config.php` với thông tin database thật
3. **Sử dụng HTTPS** khi deploy production
4. **Backup database** thường xuyên
