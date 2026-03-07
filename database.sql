-- TechSmart Database Export
-- Generated: 2026-03-07 08:36:06

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fa-folder',
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `name`, `icon`, `description`) VALUES
('1', 'Laptop', 'fa-laptop', NULL),
('2', 'Điện thoại', 'fa-mobile-screen', NULL),
('3', 'Linh Kiện', 'fa-microchip', NULL),
('6', 'Sản phẩm kỹ thuật số', 'fa-folder', NULL);

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discount` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cost_price` decimal(12,2) DEFAULT 0.00,
  `profit_margin` decimal(5,2) DEFAULT 10.00,
  `status` enum('visible','hidden') DEFAULT 'visible',
  `is_hidden` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `discount`, `image`, `stock`, `sold_count`, `created_at`, `cost_price`, `profit_margin`, `status`, `is_hidden`) VALUES
('1', '1', 'MacBook Air M1', 'Chip Apple M1, RAM 8GB, SSD 256GB, man hinh Retina 13.3 inch, pin 18 gio', '24990000.00', '5', 'maytinh/MacBook E M1.jpg', '15', '8', '2026-03-07 09:28:00', '20000000.00', '24.95', 'visible', '0'),
('2', '1', 'MacBook Pro M2', 'Chip Apple M2 Pro, RAM 16GB, SSD 512GB, man hinh Liquid Retina XDR 14 inch', '35990000.00', '0', 'maytinh/MacPro M2.jpg', '10', '5', '2026-03-07 09:28:00', '29000000.00', '24.10', 'visible', '0'),
('3', '1', 'Dell XPS 13', 'Intel Core i7-1365U, RAM 16GB, SSD 512GB, man hinh OLED 13.4 inch, thiet ke sieu mong', '28990000.00', '3', 'maytinh/Del XpX 13.jpg', '12', '6', '2026-03-07 09:28:00', '23000000.00', '26.04', 'visible', '0'),
('4', '1', 'HP Pavilion 15', 'Intel Core i5-1335U, RAM 8GB, SSD 512GB, man hinh FHD 15.6 inch, ban phim so', '16990000.00', '0', 'maytinh/HP.jpg', '20', '10', '2026-03-07 09:28:00', '13500000.00', '25.85', 'visible', '0'),
('5', '1', 'Asus ROG Strix G15', 'AMD Ryzen 7 7735HS, RTX 4060, RAM 16GB, SSD 512GB, man hinh 144Hz 15.6 inch', '32990000.00', '10', 'maytinh/Asus.jpg', '8', '4', '2026-03-07 09:28:00', '27000000.00', '22.19', 'visible', '0'),
('6', '1', 'Acer Aspire 5', 'Intel Core i5-1235U, RAM 8GB, SSD 256GB, man hinh FHD 15.6 inch, gia tot cho hoc tap', '13990000.00', '0', 'maytinh/Acer Aspie 5.jpg', '25', '12', '2026-03-07 09:28:00', '11000000.00', '27.18', 'visible', '0'),
('7', '1', 'MSI GF63 Thin', 'Intel Core i5-12450H, GTX 1650, RAM 8GB, SSD 512GB, man hinh FHD 15.6 inch, gaming gia re', '19990000.00', '5', 'maytinh/MSI GS 63.jpg', '14', '7', '2026-03-07 09:28:00', '16000000.00', '24.94', 'visible', '0'),
('8', '2', 'iPhone 15', 'Chip A16 Bionic, camera 48MP, man hinh Super Retina XDR 6.1 inch, Dynamic Island', '22990000.00', '5', 'dienthoai/Iphone 15.jpg', '20', '15', '2026-03-07 09:28:00', '18500000.00', '24.27', 'visible', '0'),
('9', '2', 'iPhone 17 Pro Max', 'Chip A19 Pro, camera 48MP ProRAW, man hinh Super Retina XDR 6.9 inch, Titanium', '38990000.00', '0', 'dienthoai/IPhone 17 Pro Max.jpg', '10', '8', '2026-03-07 09:28:00', '32000000.00', '21.84', 'visible', '0'),
('10', '2', 'Samsung Galaxy A54', 'Exynos 1380, camera 50MP OIS, man hinh Super AMOLED 6.4 inch, pin 5000mAh, chong nuoc IP67', '9490000.00', '10', 'dienthoai/Samsung Galaxy A54.jpg', '35', '20', '2026-03-07 09:28:00', '7500000.00', '26.53', 'visible', '0'),
('11', '2', 'Google Pixel 10 Pro XL', 'Google Tensor G5, camera 50MP AI, man hinh LTPO OLED 6.8 inch, Android goc', '27990000.00', '0', 'dienthoai/Google Pixel 10 Pro XL.jpg', '12', '6', '2026-03-07 09:28:00', '22000000.00', '27.23', 'visible', '0'),
('12', '2', 'Oppo Find N3', 'Snapdragon 8 Gen 2, man hinh gap 7.82 inch, camera Hasselblad 48MP, sac nhanh 67W', '24990000.00', '5', 'dienthoai/Oppo Find N3.jpg', '7', '3', '2026-03-07 09:28:00', '20000000.00', '24.95', 'visible', '0'),
('13', '2', 'Realme 11 PRO', 'Dimensity 7050, camera 100MP OIS, man hinh AMOLED 120Hz 6.7 inch, sac nhanh 67W', '8990000.00', '8', 'dienthoai/Realme 11 PRO.jpg', '30', '14', '2026-03-07 09:28:00', '7000000.00', '28.43', 'visible', '0'),
('14', '2', 'Vivo V29', 'Snapdragon 778G, camera selfie 50MP Aura Light, man hinh AMOLED 120Hz 6.78 inch', '10490000.00', '0', 'dienthoai/Vivo V29.jpg', '21', '9', '2026-03-07 09:28:00', '8500000.00', '23.41', 'visible', '0'),
('15', '2', 'Xiaomi 13 PRO', 'Snapdragon 8 Gen 2, camera Leica 50MP, man hinh LTPO AMOLED 6.73 inch, sac nhanh 120W', '18990000.00', '5', 'dienthoai/Xiaomi 13 PRO.jpg', '16', '11', '2026-03-07 09:28:00', '15000000.00', '26.60', 'visible', '0'),
('16', '3', 'AMD Ryzen 9 7950X', 'CPU 16 nhan 32 luong, xung nhip 5.7GHz, kien truc Zen 4, socket AM5, TDP 170W', '12990000.00', '0', 'linhkien/AMD ryzen 9 7950X.png', '25', '7', '2026-03-07 09:28:00', '10500000.00', '23.71', 'visible', '0'),
('17', '3', 'Intel Core i9-14900K', 'CPU 24 nhan 32 luong, xung nhip 6.0GHz, kien truc Raptor Lake, socket LGA1700', '13990000.00', '5', 'linhkien/Intel Core I9 14900k.png', '19', '10', '2026-03-07 09:28:00', '11000000.00', '27.18', 'visible', '0'),
('18', '3', 'NVIDIA RTX 4090', 'Card do hoa 24GB GDDR6X, 16384 CUDA cores, ray tracing, DLSS 3.0, 450W TDP', '45990000.00', '0', 'linhkien/RTX 4090.png', '5', '2', '2026-03-07 09:28:00', '38000000.00', '21.03', 'visible', '0'),
('19', '3', 'Corsair Vengeance DDR5', 'RAM DDR5-5600 32GB (2x16GB), tan nhiet nhom, ho tro Intel XMP 3.0', '3590000.00', '0', 'linhkien/Corsair Vengeance DDR5.png', '40', '15', '2026-03-07 09:28:00', '2800000.00', '28.21', 'visible', '0'),
('20', '3', 'Samsung 990 PRO SSD', 'SSD NVMe M.2 2TB, doc 7450MB/s, ghi 6900MB/s, PCIe Gen 4.0, bao hanh 5 nam', '3290000.00', '10', 'linhkien/Samsung SSD 990 PRO.png', '30', '18', '2026-03-07 09:28:00', '2600000.00', '26.54', 'visible', '0'),
('21', '3', 'Corsair RM1000e PSU', 'Nguon 1000W 80+ Gold, quat khong on Zero RPM, modular hoan toan, bao hanh 10 nam', '3990000.00', '0', 'linhkien/Corsair RM1000e PSU.png', '14', '6', '2026-03-07 09:28:00', '3200000.00', '24.69', 'visible', '0'),
('22', '3', 'ASUS ROG Maximus Z790', 'Mainboard Z790, socket LGA1700, DDR5, PCIe 5.0, WiFi 6E, Thunderbolt 4', '10990000.00', '5', 'linhkien/ASUS ROG Maximus Z790.png', '10', '3', '2026-03-07 09:28:00', '8800000.00', '24.89', 'visible', '0'),
('23', '3', 'NZXT Kraken Elite 360', 'Tan nhiet nuoc AIO 360mm, LCD 2.36 inch, quat F120P, ho tro Intel va AMD', '5990000.00', '5', 'linhkien/NZXT-Kraken-Elite360.png', '9', '5', '2026-03-07 09:28:00', '4800000.00', '24.79', 'visible', '0'),
('24', '6', 'Cursor PRO ( chính chủ hoặc cấp acc )', 'Nâng cấp chính chủ bảo hành đầy đủ 1 tháng', '477272.73', '5', 'cursor-pro-chinh-chu-hoac-cap-acc-/1772850922_main_1b49dd49-b4db-41b9-8e93-8aa7e29cf4b8.png', '109', '0', '2026-03-07 09:35:22', '318181.82', '50.00', 'visible', '0'),
('26', '6', 'Antigravity', '', '350000.00', '5', 'antigravity/1772867626_main_d783ce8e-fc1b-4e9b-9cff-f01e8f4bc964-md.jpeg', '10', '0', '2026-03-07 14:13:46', '100000.00', '250.00', 'visible', '0');

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `status` enum('active','locked') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `address`, `ward`, `district`, `password`, `role`, `is_locked`, `created_at`, `status`) VALUES
('1', 'Admin TechSmart', 'admin@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$yRshmSZmBlobizzd665cGOZhb8f/K.8A13lF5ZWqybQtkuA3s/.Bi', 'admin', '0', '2026-01-29 17:44:55', 'active'),
('2', 'thanh', 'abcs@gmail.com', '0909000999', 'dfdfdfdf', 'vip1', 'Quận 1', '$2y$10$P/oPzkKWBVnNbboV1ozKDeBLZ9fT6OR8a1h1pZ.Pys.ut4ysTR/by', 'customer', '0', '2026-02-03 02:00:17', 'active'),
('3', 'trương nhật long', 'longnhat@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$oSeRsbqchPSv1gVwRXEcLugbleL85Igjhp9LYrTMLKSGgauV069qq', 'customer', '0', '2026-03-05 22:51:12', 'active'),
('4', 'vo nhut thanh', 'vnt@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$c10ZC7ma2/tnNO.M81VUgeLMUVMS0q3XocGOcxsvxXfCytymkYqJC', 'customer', '0', '2026-03-05 22:51:53', 'active'),
('5', 'thanhvo', 'abcd@gmail.com', '0971229536', '17 lam van ben', NULL, NULL, '$2y$10$XO7.iSmsxB8ahltAIDTtn.LDHdNNAr1tSNVQuGk6O3osD7.A00a/m', 'customer', '0', '2026-03-07 11:58:33', 'active');

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `import_history`;
CREATE TABLE `import_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `import_price` decimal(12,2) NOT NULL,
  `old_cost_price` decimal(12,2) DEFAULT NULL,
  `new_cost_price` decimal(12,2) DEFAULT NULL,
  `old_stock` int(11) DEFAULT NULL,
  `new_stock` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `selling_price` decimal(12,2) DEFAULT 0.00,
  `profit_margin` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `import_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS=1;
