<?php $baseUrl = BASE_URL; ?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Admin - TechSmart') ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>
<body class="admin-body">
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <a href="<?= $baseUrl ?>/" class="brand">
          <img class="logo" src="<?= $baseUrl ?>/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png" alt="TechSmart">
          <span>TechSmart Admin</span>
        </a>
      </div>
      
      <nav class="sidebar-nav">
        <a href="<?= $baseUrl ?>/admin" class="nav-item">
          <span>📊</span> Dashboard
        </a>
        <a href="<?= $baseUrl ?>/admin/products" class="nav-item">
          <span>📦</span> Sản phẩm
        </a>
        <a href="#" class="nav-item">
          <span>📋</span> Đơn hàng
        </a>
        <a href="#" class="nav-item">
          <span>👥</span> Khách hàng
        </a>
        <a href="#" class="nav-item">
          <span>📂</span> Danh mục
        </a>
        <a href="#" class="nav-item">
          <span>⚙️</span> Cài đặt
        </a>
      </nav>
      
      <div class="sidebar-footer">
        <a href="<?= $baseUrl ?>/" class="nav-item">
          <span>🏠</span> Về trang chủ
        </a>
        <a href="#" class="nav-item">
          <span>🚪</span> Đăng xuất
        </a>
      </div>
    </aside>

    <main class="admin-content">
      <div class="admin-container">
        <?php require $viewFile; ?>
      </div>
    </main>
  </div>
</body>
</html>
