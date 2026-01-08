<div class="admin-header">
  <h1><?= $product ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h1>
  <a href="<?= BASE_URL ?>/admin/products" class="btn btn-secondary">← Quay lại</a>
</div>

<div class="form-container">
  <form class="admin-form" method="POST" enctype="multipart/form-data">
    <div class="form-row">
      <div class="form-group">
        <label for="name">Tên sản phẩm *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
      </div>
      
      <div class="form-group">
        <label for="category">Danh mục *</label>
        <select id="category" name="category" required>
          <option value="">Chọn danh mục</option>
          <option value="Điện thoại" <?= ($product['category'] ?? '') === 'Điện thoại' ? 'selected' : '' ?>>Điện thoại</option>
          <option value="Laptop" <?= ($product['category'] ?? '') === 'Laptop' ? 'selected' : '' ?>>Laptop</option>
          <option value="Máy tính bảng" <?= ($product['category'] ?? '') === 'Máy tính bảng' ? 'selected' : '' ?>>Máy tính bảng</option>
          <option value="Phụ kiện" <?= ($product['category'] ?? '') === 'Phụ kiện' ? 'selected' : '' ?>>Phụ kiện</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="price">Giá (VNĐ) *</label>
        <input type="number" id="price" name="price" value="<?= $product['price'] ?? '' ?>" required>
      </div>
      
      <div class="form-group">
        <label for="stock">Số lượng *</label>
        <input type="number" id="stock" name="stock" value="<?= $product['stock'] ?? '' ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label for="description">Mô tả</label>
      <textarea id="description" name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label for="image">Hình ảnh</label>
      <input type="file" id="image" name="image" accept="image/*">
      <?php if ($product && isset($product['image'])): ?>
        <p class="form-hint">Hình ảnh hiện tại: <?= basename($product['image']) ?></p>
      <?php endif; ?>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">
        <?= $product ? 'Cập nhật' : 'Thêm mới' ?>
      </button>
      <a href="<?= BASE_URL ?>/admin/products" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
</div>
