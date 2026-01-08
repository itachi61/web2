<div class="page-header">
  <h1>Sản phẩm</h1>
  <div class="search-bar">
    <input type="text" placeholder="Tìm kiếm sản phẩm..." class="search-input">
    <button class="btn btn-primary">Tìm kiếm</button>
  </div>
</div>

<div class="filters">
  <button class="filter-btn active">Tất cả</button>
  <button class="filter-btn">Điện thoại</button>
  <button class="filter-btn">Laptop</button>
  <button class="filter-btn">Máy tính bảng</button>
  <button class="filter-btn">Phụ kiện</button>
</div>

<div class="product-grid">
  <?php foreach ($products as $product): ?>
    <div class="product-card" data-category="<?= htmlspecialchars($product['category']) ?>">
      <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
      <span class="category-badge"><?= htmlspecialchars($product['category']) ?></span>
      <h3><?= htmlspecialchars($product['name']) ?></h3>
      <p class="price"><?= number_format($product['price']) ?>đ</p>
      <p class="stock-info">
        <?php if (isset($product['stock'])): ?>
          <span class="stock-badge <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
            <?= $product['stock'] > 0 ? "Còn {$product['stock']} sản phẩm" : 'Hết hàng' ?>
          </span>
        <?php endif; ?>
      </p>
      <div class="card-actions">
        <a href="<?= BASE_URL ?>/product?id=<?= $product['id'] ?>" class="btn btn-secondary">Xem chi tiết</a>
        <button 
          class="btn btn-primary" 
          onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['stock'] ?? 0 ?>)"
        >
          Thêm vào giỏ
        </button>
      </div>
    </div>
  <?php endforeach; ?>
</div>
