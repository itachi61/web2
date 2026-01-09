<div class="product-detail">
  <div class="product-images">
    <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-image">
  </div>
  
  <div class="product-info">
    <span class="category-badge"><?= htmlspecialchars($product['category']) ?></span>
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <p class="price-large"><?= number_format($product['price']) ?>đ</p>
    
    <div class="product-description">
      <h3>Mô tả sản phẩm</h3>
      <p><?= htmlspecialchars($product['description']) ?></p>
    </div>

    <div class="product-specs">
      <h3>Thông số kỹ thuật</h3>
      <table class="specs-table">
        <?php foreach ($product['specs'] as $key => $value): ?>
          <tr>
            <td class="spec-label"><?= htmlspecialchars($key) ?></td>
            <td class="spec-value"><?= htmlspecialchars($value) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="product-actions">
      <div class="quantity-selector">
        <button class="qty-btn">-</button>
        <input type="number" value="1" min="1" class="qty-input">
        <button class="qty-btn">+</button>
      </div>
      <button class="btn btn-primary btn-large">Thêm vào giỏ hàng</button>
      <button class="btn btn-secondary btn-large">Mua ngay</button>
    </div>
  </div>
</div>

<section class="section">
  <h2 class="section-title">Sản phẩm tương tự</h2>
  <div class="product-grid">
    <?php for ($i = 0; $i < 4; $i++): ?>
      <div class="product-card">
        <img src="<?= BASE_URL ?>/assets/img/bd6a9ca0-75ea-40b0-bf7c-3a531350a291.png" alt="Product">
        <h3>Sản phẩm tương tự</h3>
        <p class="price">15.990.000đ</p>
        <a href="#" class="btn btn-secondary">Xem chi tiết</a>
      </div>
    <?php endfor; ?>
  </div>
</section>
