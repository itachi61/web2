-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 10, 2026 lúc 09:52 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `techsmart`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fa-tag',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Laptop', 'laptop', 'fa-laptop', ''),
(2, 'Điện thoại', 'dien-thoai', 'fa-mobile-screen', ''),
(3, 'Linh Kiện', 'linh-kien', 'fa-microchip', ''),
(6, 'Sản phẩm kỹ thuật số', 'san-pham-ky-thuat-so', 'fa-cloud-arrow-down', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `imports`
--

CREATE TABLE `imports` (
  `id` int(11) NOT NULL,
  `import_code` varchar(50) NOT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','completed') DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `imports`
--

INSERT INTO `imports` (`id`, `import_code`, `supplier`, `note`, `total_amount`, `status`, `created_by`, `created_at`, `completed_at`) VALUES
(1, 'IMP-20260203-320', 'VNT', 'ssss', 64000000.00, 'completed', 1, '2026-02-03 04:35:53', NULL),
(2, 'IMP-20260203-090', 'VNT1', 'new goods', 4000000.00, 'completed', 1, '2026-02-03 04:39:01', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_history`
--

CREATE TABLE `import_history` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `import_price` decimal(12,2) NOT NULL,
  `old_cost_price` decimal(12,2) DEFAULT NULL,
  `new_cost_price` decimal(12,2) DEFAULT NULL,
  `old_stock` int(11) DEFAULT NULL,
  `new_stock` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `selling_price` decimal(12,2) DEFAULT 0.00,
  `profit_margin` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_history`
--

INSERT INTO `import_history` (`id`, `product_id`, `quantity`, `import_price`, `old_cost_price`, `new_cost_price`, `old_stock`, `new_stock`, `created_at`, `selling_price`, `profit_margin`) VALUES
(1, 24, 100, 300000.00, 300000.00, 300000.00, 0, 100, '2026-03-07 02:37:21', 660000.00, 120.00),
(3, 24, 10, 500000.00, 300000.00, 318181.82, 100, 110, '2026-03-07 03:43:18', 700000.00, 120.00),
(4, 26, 10, 100000.00, 100000.00, 100000.00, 0, 10, '2026-03-07 07:14:55', 300000.00, 200.00),
(5, 26, 20, 100000.00, 100000.00, 100000.00, 9, 29, '2026-03-14 03:05:30', 300000.00, 200.00),
(6, 27, 10, 100.00, 0.00, 100.00, 0, 10, '2026-03-28 01:27:24', 130.00, 30.00),
(7, 26, 10, 50000.00, 100000.00, 87179.49, 29, 39, '2026-03-31 19:02:24', 261538.47, 200.00),
(8, 6, 5, 10000000.00, 0.00, 10000000.00, 0, 5, '2026-04-04 06:06:55', 12718000.00, 27.18),
(9, 24, 100, 100000.00, 0.00, 100000.00, 0, 100, '2026-04-04 06:06:55', 220000.00, 120.00),
(10, 16, 3, 5000000.00, 0.00, 5000000.00, 0, 3, '2026-04-04 06:06:55', 16785000.00, 235.70),
(11, 22, 5, 1000000.00, 0.00, 1000000.00, 0, 5, '2026-04-04 06:06:55', 6000000.00, 500.00),
(12, 5, 1, 1000000.00, 0.00, 1000000.00, 0, 1, '2026-04-04 06:06:55', 1500000.00, 50.00),
(13, 9, 2, 45000000.00, 0.00, 45000000.00, 0, 2, '2026-04-04 06:06:55', 54828000.00, 21.84),
(14, 3, 10, 20000000.00, 0.00, 20000000.00, 0, 10, '2026-04-04 06:06:55', 25208000.00, 26.04),
(15, 15, 10, 20000000.00, 0.00, 20000000.00, 0, 10, '2026-04-04 06:06:55', 25320000.00, 26.60);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_items`
--

CREATE TABLE `import_items` (
  `id` int(11) NOT NULL,
  `import_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `import_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_items`
--

INSERT INTO `import_items` (`id`, `import_id`, `product_id`, `quantity`, `import_price`) VALUES
(1, 1, 26, 20, 3200000.00),
(2, 2, 24, 10, 400000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_receipts`
--

CREATE TABLE `import_receipts` (
  `id` int(11) NOT NULL,
  `receipt_code` varchar(50) NOT NULL,
  `status` enum('draft','completed') DEFAULT 'draft',
  `note` text DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `total_items` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_receipts`
--

INSERT INTO `import_receipts` (`id`, `receipt_code`, `status`, `note`, `total_amount`, `total_items`, `created_by`, `created_at`, `completed_at`) VALUES
(1, 'PN-20260314031937', 'completed', '', 2000000.00, 1, 1, '2026-03-14 02:19:37', '2026-03-14 03:05:30'),
(2, 'PN-20260314044433', 'draft', '', 0.00, 0, 1, '2026-03-14 03:44:33', NULL),
(3, 'PN-20260327190625', 'draft', '', 0.00, 0, 1, '2026-03-27 18:06:25', NULL),
(4, 'PN-20260327191746', 'draft', '', 0.00, 0, 1, '2026-03-27 18:17:46', NULL),
(5, 'PN-20260328021306', 'draft', '', 0.00, 0, 1, '2026-03-28 01:13:06', NULL),
(6, 'PN-20260328022543', 'completed', '', 1000.00, 1, 1, '2026-03-28 01:25:43', '2026-03-28 01:27:24'),
(7, 'PN-20260328042605', 'draft', '', 0.00, 0, 1, '2026-03-28 03:26:05', NULL),
(8, 'PN-20260328043729', 'draft', '', 10000000.00, 1, 1, '2026-03-28 03:37:29', NULL),
(9, 'PN-20260331205913', 'completed', '', 500000.00, 1, 1, '2026-03-31 13:01:00', '2026-03-31 19:02:24'),
(10, 'PN-20260404072443', 'draft', '', 0.00, 0, 1, '2026-04-04 05:24:43', NULL),
(11, 'PN-20260404080539', 'completed', '', 571000000.00, 8, 1, '2026-04-04 06:05:39', '2026-04-04 06:06:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_receipt_items`
--

CREATE TABLE `import_receipt_items` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `import_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_receipt_items`
--

INSERT INTO `import_receipt_items` (`id`, `receipt_id`, `product_id`, `quantity`, `import_price`) VALUES
(2, 1, 26, 20, 100000.00),
(3, 6, 27, 10, 100.00),
(4, 8, 6, 10, 1000000.00),
(5, 9, 26, 10, 50000.00),
(6, 11, 6, 5, 10000000.00),
(7, 11, 24, 100, 100000.00),
(8, 11, 16, 3, 5000000.00),
(9, 11, 22, 5, 1000000.00),
(10, 11, 5, 1, 1000000.00),
(11, 11, 9, 2, 45000000.00),
(12, 11, 3, 10, 20000000.00),
(13, 11, 15, 10, 20000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `ward` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','online') DEFAULT 'cash',
  `total_money` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `phone`, `address`, `ward`, `district`, `note`, `payment_method`, `total_money`, `status`, `created_at`) VALUES
(9, 2, 'thanh', '0971229536', '231/35 dương bá trạc phường 1 quận 8', NULL, NULL, '', 'cash', 17980000.00, 'processing', '2026-03-07 02:32:46'),
(10, 2, 'thanh', '55', 'xxx', NULL, NULL, '', 'cash', 53450000.00, 'cancelled', '2026-03-07 03:39:45'),
(11, 5, 'thanhvo', '0971229536', '34 lam van ben, Thành phố Hồ Chí Minh', NULL, NULL, '', 'cash', 477272.73, 'completed', '2026-03-07 05:02:00'),
(12, 6, 'thanhvo', '0971229536', 'vip1, Thị trấn Phước Hải, Huyện Long Đất', NULL, NULL, '', 'cash', 350000.00, 'completed', '2026-03-14 03:03:52'),
(13, 6, 'thanhvo', '0971229536', 'vip1, Thị trấn Phước Hải, Huyện Long Đất', NULL, NULL, '', 'cash', 5990000.00, 'cancelled', '2026-03-14 03:44:01'),
(14, 6, 'thanhvo', '0971229536', 'vip1, Thị trấn Phước Hải, Huyện Long Đất', NULL, NULL, '', 'cash', 13990000.00, 'completed', '2026-03-14 03:54:26'),
(15, 6, 'thanhvo', '0971229536', 'vip1, Thị trấn Phước Hải, Huyện Long Đất', NULL, NULL, '', 'cash', 954545.46, 'completed', '2026-03-14 03:54:58'),
(16, 5, 'thanhvo', '0971229536', '17 lam van ben', NULL, NULL, '', 'cash', 10990000.00, 'completed', '2026-03-27 18:07:47'),
(17, 5, 'thanhvo', '0971229536', '17 lam van ben', NULL, NULL, '', 'cash', 3990000.00, 'cancelled', '2026-03-27 18:17:07'),
(18, 8, 'Thanh Đạt', '0912345678', '123 Nguyễn Huệ, Thành phố Hồ Chí Minh', NULL, NULL, '', 'cash', 35990000.00, 'completed', '2026-03-28 01:09:39'),
(19, 5, 'thanhvo', '0971229536', 'ttt', NULL, NULL, 'ttt', 'cash', 390.00, 'completed', '2026-04-28 01:29:30'),
(20, 5, 'thanhvo', '0971229536', '231/35 dương bá trạc phường 1 quận 8, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh', NULL, NULL, '', 'cash', 477272.73, 'completed', '2026-03-31 18:46:51'),
(21, 5, 'thanhvo', '0971229536', 'test địa chỉ mới', NULL, NULL, '', 'cash', 954545.46, 'completed', '2026-03-31 19:03:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `cost_price`) VALUES
(10, 9, 17, 1, 13990000.00, 11000000.00),
(11, 9, 21, 1, 3990000.00, 3200000.00),
(12, 10, 23, 3, 5990000.00, 4800000.00),
(13, 10, 14, 1, 10490000.00, 8500000.00),
(14, 10, 12, 1, 24990000.00, 20000000.00),
(15, 11, 24, 1, 477272.73, 318181.82),
(16, 12, 26, 1, 350000.00, 100000.00),
(17, 13, 23, 1, 5990000.00, 4800000.00),
(18, 14, 17, 1, 13990000.00, 11000000.00),
(19, 15, 24, 2, 477272.73, 318181.82),
(20, 16, 22, 1, 10990000.00, 8800000.00),
(21, 17, 21, 1, 3990000.00, 3200000.00),
(22, 18, 2, 1, 35990000.00, 29000000.00),
(23, 19, 27, 3, 130.00, 100.00),
(24, 20, 24, 1, 477272.73, 318181.82),
(25, 21, 24, 2, 477272.73, 318181.82);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discount` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cost_price` decimal(12,2) DEFAULT 0.00,
  `profit_margin` decimal(5,2) DEFAULT 10.00,
  `unit` varchar(50) DEFAULT 'Cßi',
  `status` enum('visible','hidden') DEFAULT 'visible',
  `is_hidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `discount`, `image`, `image2`, `stock`, `sold_count`, `created_at`, `cost_price`, `profit_margin`, `unit`, `status`, `is_hidden`) VALUES
(1, 3, 'MacBook Air M1', 'Chip Apple M1, RAM 8GB, SSD 256GB, man hinh Retina 13.3 inch, pin 18 gio', 25000000.00, 5, 'maytinh/MacBook E M1.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 25.00, 'Cßi', 'visible', 0),
(2, 1, 'MacBook Pro M2', 'Chip Apple M2 Pro, RAM 16GB, SSD 512GB, man hinh Liquid Retina XDR 14 inch', 35990000.00, 0, 'maytinh/MacPro M2.jpg', NULL, 0, 1, '2026-03-07 02:28:00', 0.00, 24.10, 'Cßi', 'visible', 0),
(3, 1, 'Dell XPS 13', 'Intel Core i7-1365U, RAM 16GB, SSD 512GB, man hinh OLED 13.4 inch, thiet ke sieu mong', 25208000.00, 3, 'maytinh/Del XpX 13.jpg', NULL, 10, 0, '2026-03-07 02:28:00', 20000000.00, 26.04, 'Cßi', 'visible', 0),
(4, 1, 'HP Pavilion 15', 'Intel Core i5-1335U, RAM 8GB, SSD 512GB, man hinh FHD 15.6 inch, ban phim so', 16990000.00, 0, 'maytinh/HP.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 25.85, 'Cßi', 'visible', 0),
(5, 1, 'Asus ROG Strix G15', 'AMD Ryzen 7 7735HS, RTX 4060, RAM 16GB, SSD 512GB, man hinh 144Hz 15.6 inch', 1500000.00, 10, 'maytinh/Asus.jpg', NULL, 1, 0, '2026-03-07 02:28:00', 1000000.00, 50.00, 'Cßi', 'visible', 0),
(6, 1, 'Acer Aspire 5', 'Intel Core i5-1235U, RAM 8GB, SSD 256GB, man hinh FHD 15.6 inch, gia tot cho hoc tap', 12718000.00, 0, 'maytinh/Acer Aspie 5.jpg', NULL, 5, 0, '2026-03-07 02:28:00', 10000000.00, 27.18, 'Cßi', 'visible', 0),
(7, 1, 'MSI GF63 Thin', 'Intel Core i5-12450H, GTX 1650, RAM 8GB, SSD 512GB, man hinh FHD 15.6 inch, gaming gia re', 19990000.00, 5, 'maytinh/MSI GS 63.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 24.94, 'Cßi', 'visible', 0),
(8, 2, 'iPhone 15', 'Chip A16 Bionic, camera 48MP, man hinh Super Retina XDR 6.1 inch, Dynamic Island', 22990000.00, 5, 'dienthoai/Iphone 15.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 24.27, 'Cßi', 'visible', 0),
(9, 2, 'iPhone 17 Pro Max', 'Chip A19 Pro, camera 48MP ProRAW, man hinh Super Retina XDR 6.9 inch, Titanium', 54828000.00, 0, 'dienthoai/IPhone 17 Pro Max.jpg', NULL, 2, 0, '2026-03-07 02:28:00', 45000000.00, 21.84, 'Cßi', 'visible', 0),
(10, 2, 'Samsung Galaxy A54', 'Exynos 1380, camera 50MP OIS, man hinh Super AMOLED 6.4 inch, pin 5000mAh, chong nuoc IP67', 9490000.00, 10, 'dienthoai/Samsung Galaxy A54.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 26.53, 'Cßi', 'visible', 0),
(11, 2, 'Google Pixel 10 Pro XL', 'Google Tensor G5, camera 50MP AI, man hinh LTPO OLED 6.8 inch, Android goc', 27990000.00, 0, 'dienthoai/Google Pixel 10 Pro XL.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 27.23, 'Cßi', 'visible', 0),
(12, 2, 'Oppo Find N3', 'Snapdragon 8 Gen 2, man hinh gap 7.82 inch, camera Hasselblad 48MP, sac nhanh 67W', 24990000.00, 5, 'dienthoai/Oppo Find N3.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 24.95, 'Cßi', 'visible', 0),
(13, 2, 'Realme 11 PRO', 'Dimensity 7050, camera 100MP OIS, man hinh AMOLED 120Hz 6.7 inch, sac nhanh 67W', 8990000.00, 8, 'dienthoai/Realme 11 PRO.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 28.43, 'Cßi', 'visible', 0),
(14, 2, 'Vivo V29', 'Snapdragon 778G, camera selfie 50MP Aura Light, man hinh AMOLED 120Hz 6.78 inch', 10490000.00, 0, 'dienthoai/Vivo V29.jpg', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 23.41, 'Cßi', 'visible', 0),
(15, 2, 'Xiaomi 13 PRO', 'Snapdragon 8 Gen 2, camera Leica 50MP, man hinh LTPO AMOLED 6.73 inch, sac nhanh 120W', 25320000.00, 5, 'dienthoai/Xiaomi 13 PRO.jpg', NULL, 10, 0, '2026-03-07 02:28:00', 20000000.00, 26.60, 'Cßi', 'visible', 0),
(16, 3, 'AMD Ryzen 9 7950X', 'CPU 16 nhan 32 luong, xung nhip 5.7GHz, kien truc Zen 4, socket AM5, TDP 170W', 16785000.00, 0, 'linhkien/AMD ryzen 9 7950X.png', NULL, 3, 0, '2026-03-07 02:28:00', 5000000.00, 235.70, 'Cßi', 'visible', 0),
(17, 3, 'Intel Core i9-14900K', 'CPU 24 nhan 32 luong, xung nhip 6.0GHz, kien truc Raptor Lake, socket LGA1700', 13990000.00, 5, 'linhkien/Intel Core I9 14900k.png', NULL, 0, 1, '2026-03-07 02:28:00', 0.00, 27.18, 'Cßi', 'visible', 0),
(18, 3, 'NVIDIA RTX 4090', 'Card do hoa 24GB GDDR6X, 16384 CUDA cores, ray tracing, DLSS 3.0, 450W TDP', 45990000.00, 0, 'linhkien/RTX 4090.png', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 21.03, 'Cßi', 'visible', 0),
(19, 3, 'Corsair Vengeance DDR5', 'RAM DDR5-5600 32GB (2x16GB), tan nhiet nhom, ho tro Intel XMP 3.0', 3590000.00, 0, 'linhkien/Corsair Vengeance DDR5.png', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 28.21, 'Cßi', 'visible', 0),
(20, 3, 'Samsung 990 PRO SSD', 'SSD NVMe M.2 2TB, doc 7450MB/s, ghi 6900MB/s, PCIe Gen 4.0, bao hanh 5 nam', 3290000.00, 10, 'linhkien/Samsung SSD 990 PRO.png', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 26.54, 'Cßi', 'visible', 0),
(21, 3, 'Corsair RM1000e PSU', 'Nguon 1000W 80+ Gold, quat khong on Zero RPM, modular hoan toan, bao hanh 10 nam', 3990000.00, 0, 'linhkien/Corsair RM1000e PSU.png', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 24.69, 'Cßi', 'visible', 0),
(22, 3, 'ASUS ROG Maximus Z790', 'Mainboard Z790, socket LGA1700, DDR5, PCIe 5.0, WiFi 6E, Thunderbolt 4', 6000000.00, 5, 'linhkien/ASUS ROG Maximus Z790.png', NULL, 5, 1, '2026-03-07 02:28:00', 1000000.00, 500.00, 'Cßi', 'visible', 0),
(23, 3, 'NZXT Kraken Elite 360', 'Tan nhiet nuoc AIO 360mm, LCD 2.36 inch, quat F120P, ho tro Intel va AMD', 5990000.00, 5, 'linhkien/NZXT-Kraken-Elite360.png', NULL, 0, 0, '2026-03-07 02:28:00', 0.00, 24.79, 'Cßi', 'visible', 0),
(24, 6, 'Cursor PRO ( chính chủ hoặc cấp acc )', 'Nâng cấp chính chủ bảo hành đầy đủ 1 tháng', 220000.00, 5, 'cursor-pro-chinh-chu-hoac-cap-acc-/1772850922_main_1b49dd49-b4db-41b9-8e93-8aa7e29cf4b8.png', NULL, 100, 6, '2026-03-07 02:35:22', 100000.00, 120.00, 'Cßi', 'visible', 0),
(26, 6, 'Antigravity', '', 261537.00, 5, 'antigravity/1775280208_main_cf13f248-0862-45bd-af98-e982a7790bfb.png', 'antigravity/1775543892_sub_tải xuống (2).jpeg', 29, 1, '2026-03-07 07:13:46', 87179.00, 200.00, 'Cßi', 'visible', 0),
(27, 3, 'zzz', 'ZZZ', 130.00, 0, 'zzz/1774661114_main_2.png', NULL, 7, 3, '2026-03-28 01:25:14', 100.00, 30.00, 'Cßi', 'visible', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`) VALUES
(2, 27, 'zzz/1774661114_0_z3726281264346_a22cf7dd3164db06c23f8bc1a5b50e56.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(4, 2, 8, 5, 'Sản phẩm tốt!!!', '2026-03-28 01:10:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `stock_history`
--

CREATE TABLE `stock_history` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_before` int(11) NOT NULL DEFAULT 0,
  `stock_after` int(11) NOT NULL DEFAULT 0,
  `change_qty` int(11) NOT NULL,
  `change_type` enum('import','sale','manual') NOT NULL DEFAULT 'import',
  `reference_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `stock_history`
--

INSERT INTO `stock_history` (`id`, `product_id`, `stock_before`, `stock_after`, `change_qty`, `change_type`, `reference_id`, `created_at`) VALUES
(1, 26, 0, 10, 10, 'import', 4, '2026-03-07 07:14:55'),
(2, 26, 10, 9, 1, 'sale', 12, '2026-03-14 03:03:52'),
(3, 26, 9, 29, 20, 'import', 5, '2026-03-14 03:05:30'),
(4, 23, 9, 8, 1, 'sale', 13, '2026-03-14 03:44:01'),
(5, 17, 19, 18, 1, 'sale', 14, '2026-03-14 03:54:26'),
(6, 24, 109, 107, 2, 'sale', 15, '2026-03-14 03:54:58'),
(7, 22, 10, 9, 1, 'sale', 16, '2026-03-27 18:07:47'),
(8, 21, 14, 13, 1, 'sale', 17, '2026-03-27 18:17:07'),
(9, 2, 10, 9, 1, 'sale', 18, '2026-03-28 01:09:39'),
(10, 27, 0, 10, 10, 'import', 6, '2026-03-28 01:27:24'),
(11, 27, 10, 7, 3, 'sale', 19, '2026-04-28 01:29:30'),
(12, 24, 105, 104, 1, 'sale', 20, '2026-03-31 18:46:51'),
(13, 26, 29, 39, 10, 'import', 7, '2026-03-31 19:02:24'),
(14, 24, 104, 102, 2, 'sale', 21, '2026-03-31 19:03:56'),
(15, 6, 0, 5, 5, 'import', 8, '2026-04-04 06:06:55'),
(16, 24, 0, 100, 100, 'import', 9, '2026-04-04 06:06:55'),
(17, 16, 0, 3, 3, 'import', 10, '2026-04-04 06:06:55'),
(18, 22, 0, 5, 5, 'import', 11, '2026-04-04 06:06:55'),
(19, 5, 0, 1, 1, 'import', 12, '2026-04-04 06:06:55'),
(20, 9, 0, 2, 2, 'import', 13, '2026-04-04 06:06:55'),
(21, 3, 0, 10, 10, 'import', 14, '2026-04-04 06:06:55'),
(22, 15, 0, 10, 10, 'import', 15, '2026-04-04 06:06:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `ward` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `is_locked` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','locked') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `address`, `ward`, `district`, `password`, `role`, `is_locked`, `created_at`, `status`) VALUES
(1, 'Admin TechSmart', 'admin@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$yRshmSZmBlobizzd665cGOZhb8f/K.8A13lF5ZWqybQtkuA3s/.Bi', 'admin', 0, '2026-01-29 10:44:55', 'active'),
(2, 'thanh', 'abcs@gmail.com', '0909000999', 'dfdfdfdf', 'vip1', 'Quận 1', '$2y$10$GvSFQJQNqUlMdMJK4Iie5OoraK/2oLCwYDcJ8i9g7AhuipAvz2sa6', 'customer', 0, '2026-02-02 19:00:17', 'locked'),
(5, 'thanhvo', 'abcd@gmail.com', '0971229536', 'test địa chỉ mới', NULL, NULL, '$2y$10$XO7.iSmsxB8ahltAIDTtn.LDHdNNAr1tSNVQuGk6O3osD7.A00a/m', 'customer', 0, '2026-03-07 04:58:33', 'active'),
(6, 'thanhvo', 'abcde@gmail.com', '0971229536', 'vip1, Thị trấn Phước Hải, Huyện Long Đất', NULL, NULL, '$2y$10$85MalaNR5cDAOJBoMI0lT.m7m5PS9u.y4gX4HKn4fqNB0M4.VyTiC', 'customer', 0, '2026-03-14 02:26:39', 'active'),
(7, 'Test User', 'testuser@example.com', '0987654321', '123 Test Street', NULL, NULL, '$2y$10$HuAfND9Pz1dqN6vCFnNjXOcC4s7xgNvlIAqENeA2qXScjbAgwyhtK', 'customer', 0, '2026-03-14 02:33:19', 'active'),
(8, 'Thanh Đạt', 'kh1@example.com', '0912345678', '123 Nguyễn Huệ, Thành phố Hồ Chí Minh', NULL, NULL, '$2y$10$Mt.gW8lPdFp4OMUc8IjpKu/Q9PIbI1MKnshym6kDyMRp.6RqaBs7.', 'customer', 0, '2026-03-28 00:41:59', 'active');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `import_code` (`import_code`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `import_history`
--
ALTER TABLE `import_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `import_items`
--
ALTER TABLE `import_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_id` (`import_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `import_receipt_items`
--
ALTER TABLE `import_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `imports`
--
ALTER TABLE `imports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `import_history`
--
ALTER TABLE `import_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `import_items`
--
ALTER TABLE `import_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `import_receipt_items`
--
ALTER TABLE `import_receipt_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `imports`
--
ALTER TABLE `imports`
  ADD CONSTRAINT `imports_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `import_history`
--
ALTER TABLE `import_history`
  ADD CONSTRAINT `import_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `import_items`
--
ALTER TABLE `import_items`
  ADD CONSTRAINT `import_items_ibfk_1` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `import_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD CONSTRAINT `import_receipts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `import_receipt_items`
--
ALTER TABLE `import_receipt_items`
  ADD CONSTRAINT `import_receipt_items_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `import_receipts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `import_receipt_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
