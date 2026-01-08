<div class="admin-header">
  <h1>Quản lý sản phẩm</h1>
  <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-primary">+ Thêm sản phẩm</a>
</div>

<div class="table-container">
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Tên sản phẩm</th>
        <th>Giá</th>
        <th>Tồn kho</th>
        <th>Danh mục</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product): ?>
        <tr>
          <td>#<?= $product['id'] ?></td>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= number_format($product['price']) ?>đ</td>
          <td><?= $product['stock'] ?></td>
          <td><?= htmlspecialchars($product['category']) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/admin/products/edit?id=<?= $product['id'] ?>" class="btn-small btn-edit">Sửa</a>
            <button class="btn-small btn-delete">Xóa</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
