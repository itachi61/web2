<?php $baseUrl = "/techsmart/public"; ?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'TechSmart') ?></title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="<?= $baseUrl ?>/">
      <img class="logo" src="<?= $baseUrl ?>/assets/img/logo.png" alt="TechSmart">
      <span>TechSmart</span>
    </a>
    <nav class="nav">
      <a href="<?= $baseUrl ?>/">Trang chủ</a>
      <a href="<?= $baseUrl ?>/products">Sản phẩm</a>
      <a href="<?= $baseUrl ?>/cart">Giỏ hàng</a>
      <a href="<?= $baseUrl ?>/login">Đăng nhập</a>
    </nav>
  </header>

  <main class="container">
    <?php require $viewFile; ?>
  </main>

  <footer class="footer">© <?= date('Y') ?> TechSmart</footer>
  
  <script src="<?= $baseUrl ?>/assets/js/app.js"></script>
</body>
</html>
