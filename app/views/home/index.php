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
    <?php if (!empty($featuredProducts)): ?>
      <?php foreach ($featuredProducts as $product): ?>
        <?php
          // Get image path
          $imagePath = $product['images'] ?? '/assets/img/logo.png';
          if (str_starts_with($imagePath, '/assets')) {
            $imagePath = BASE_URL . $imagePath;
          }
          
          // Get display price
          $displayPrice = $product['sale_price'] ?? $product['price'];
        ?>
        <div class="product-card">
          <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <?php if (!empty($product['sale_price'])): ?>
            <span class="sale-badge">Giảm giá</span>
          <?php endif; ?>
          <h3><?= htmlspecialchars($product['name']) ?></h3>
          <p class="price"><?= number_format($displayPrice) ?>đ</p>
          <?php if (!empty($product['sale_price'])): ?>
            <p class="old-price"><?= number_format($product['price']) ?>đ</p>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/product?id=<?= $product['id'] ?>" class="btn btn-secondary">Xem chi tiết</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Chưa có sản phẩm nổi bật</p>
    <?php endif; ?>
  </div>
</section>

<section class="section categories">
  <h2 class="section-title">Danh mục sản phẩm</h2>
  <div class="category-grid">
    <?php if (!empty($categories)): ?>
      <?php foreach ($categories as $category): ?>
        <a href="<?= BASE_URL ?>/products?category=<?= $category['id'] ?>" class="category-card">
          <span class="category-icon">
            <?php
              // Icon mapping based on category name
              $icon = '📦'; // default
              if (stripos($category['name'], 'điện thoại') !== false || stripos($category['name'], 'phone') !== false) {
                $icon = '📱';
              } elseif (stripos($category['name'], 'laptop') !== false) {
                $icon = '💻';
              } elseif (stripos($category['name'], 'tablet') !== false || stripos($category['name'], 'máy tính bảng') !== false || stripos($category['name'], 'ipad') !== false) {
                $icon = '📲'; // Different icon for tablets
              } elseif (stripos($category['name'], 'phụ kiện') !== false || stripos($category['name'], 'airpod') !== false) {
                $icon = '🎧';
              }
              echo $icon;
            ?>
          </span>
          <h3><?= htmlspecialchars($category['name']) ?></h3>
          <?php if (isset($category['product_count'])): ?>
            <p class="category-count"><?= $category['product_count'] ?> sản phẩm</p>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Fallback if no categories -->
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
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <h2 class="section-title">Sản phẩm mới nhất</h2>
  <div class="product-grid">
    <?php if (!empty($latestProducts)): ?>
      <?php foreach ($latestProducts as $product): ?>
        <?php
          // Get image path
          $imagePath = $product['images'] ?? '/assets/img/logo.png';
          if (str_starts_with($imagePath, '/assets')) {
            $imagePath = BASE_URL . $imagePath;
          }
        ?>
        <div class="product-card">
          <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <span class="category-badge"><?= htmlspecialchars($product['category_name'] ?? 'Khác') ?></span>
          <h3><?= htmlspecialchars($product['name']) ?></h3>
          <p class="price"><?= number_format($product['price']) ?>đ</p>
          <?php if (isset($product['stock'])): ?>
            <p class="stock-info">
              <span class="stock-badge <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                <?= $product['stock'] > 0 ? "Còn {$product['stock']} sp" : 'Hết hàng' ?>
              </span>
            </p>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/product?id=<?= $product['id'] ?>" class="btn btn-secondary">Xem chi tiết</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Chưa có sản phẩm mới</p>
    <?php endif; ?>
  </div>
</section>
