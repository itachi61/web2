<div class="cart-page">
  <h1>Giỏ hàng của bạn</h1>
  
  <?php if (empty($cartItems)): ?>
    <div class="empty-cart">
      <p>Giỏ hàng trống</p>
      <a href="<?= BASE_URL ?>/products" class="btn btn-primary">Tiếp tục mua sắm</a>
    </div>
  <?php else: ?>
    <div class="cart-content">
      <div class="cart-items">
        <?php foreach ($cartItems as $item): ?>
          <div class="cart-item">
            <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
            <div class="item-info">
              <h3><?= htmlspecialchars($item['product_name']) ?></h3>
              <p class="item-price"><?= number_format($item['price']) ?>đ</p>
            </div>
            <div class="item-quantity">
              <button class="qty-btn">-</button>
              <input type="number" value="<?= $item['quantity'] ?>" min="1" class="qty-input">
              <button class="qty-btn">+</button>
            </div>
            <div class="item-total">
              <p><?= number_format($item['price'] * $item['quantity']) ?>đ</p>
            </div>
            <button class="remove-btn">×</button>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="cart-summary">
        <h2>Tổng đơn hàng</h2>
        <div class="summary-row">
          <span>Tạm tính:</span>
          <span><?= number_format($total) ?>đ</span>
        </div>
        <div class="summary-row">
          <span>Phí vận chuyển:</span>
          <span>Miễn phí</span>
        </div>
        <div class="summary-row total">
          <span>Tổng cộng:</span>
          <span><?= number_format($total) ?>đ</span>
        </div>
        <button class="btn btn-primary btn-large">Thanh toán</button>
        <a href="<?= BASE_URL ?>/products" class="btn btn-secondary btn-large">Tiếp tục mua sắm</a>
      </div>
    </div>
  <?php endif; ?>
</div>
