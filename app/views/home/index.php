<div class="hero">
  <div class="hero-content">
    <h1>Chào mừng đến TechSmart 🚀</h1>
    <p>Cửa hàng công nghệ hàng đầu Việt Nam - Sản phẩm chính hãng, giá tốt nhất</p>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/products">Khám phá ngay</a>
  </div>
</div>

<section class="section">
  <h2 class="section-title">Sản phẩm nổi bật</h2>
  <div class="product-grid">
    <?php
    $featuredProducts = [
      ['name' => 'iPhone 15 Pro Max', 'price' => 29990000, 'image' => BASE_URL . '/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png'],
      ['name' => 'MacBook Pro M3', 'price' => 45990000, 'image' => BASE_URL . '/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png'],
      ['name' => 'iPad Air', 'price' => 15990000, 'image' => BASE_URL . '/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png'],
      ['name' => 'AirPods Pro', 'price' => 6990000, 'image' => BASE_URL . '/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png']
    ];
    foreach ($featuredProducts as $product): ?>
      <div class="product-card">
        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
        <h3><?= $product['name'] ?></h3>
        <p class="price"><?= number_format($product['price']) ?>đ</p>
        <a href="<?= BASE_URL ?>/product?id=1" class="btn btn-secondary">Xem chi tiết</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section categories">
  <h2 class="section-title">Danh mục sản phẩm</h2>
  <div class="category-grid">
    <a href="<?= BASE_URL ?>/products" class="category-card">
      <span class="category-icon">📱</span>
      <h3>Điện thoại</h3>
    </a>
    <a href="<?= BASE_URL ?>/products" class="category-card">
      <span class="category-icon">💻</span>
      <h3>Laptop</h3>
    </a>
    <a href="<?= BASE_URL ?>/products" class="category-card">
      <span class="category-icon">⌚</span>
      <h3>Phụ kiện</h3>
    </a>
    <a href="<?= BASE_URL ?>/products" class="category-card">
      <span class="category-icon">🎧</span>
      <h3>Âm thanh</h3>
    </a>
  </div>
</section>
