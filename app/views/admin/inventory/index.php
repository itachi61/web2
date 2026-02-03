<!-- Inventory Management -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fa-solid fa-warehouse text-primary me-2"></i>Quản lý tồn kho
    </h4>
    <a href="<?= BASE_URL ?>admin/imports/create" class="btn btn-primary">
        <i class="fa-solid fa-truck-ramp-box me-2"></i>Nhập hàng
    </a>
</div>

<!-- Low Stock Alert -->
<?php 
$lowStockCount = count(array_filter($data['products'] ?? [], fn($p) => ($p['stock'] ?? 0) < 10));
if ($lowStockCount > 0):
?>
<div class="alert alert-warning d-flex align-items-center mb-4">
    <i class="fa-solid fa-triangle-exclamation fa-2x me-3"></i>
    <div>
        <strong>Cảnh báo!</strong> Có <strong><?= $lowStockCount ?></strong> sản phẩm sắp hết hàng (dưới 10 SP).
    </div>
</div>
<?php endif; ?>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Danh mục</label>
                <select name="category" class="form-select">
                    <option value="">Tất cả</option>
                    <?php foreach ($data['categories'] ?? [] as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái kho</label>
                <select name="stock_status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="low" <?= ($_GET['stock_status'] ?? '') === 'low' ? 'selected' : '' ?>>Sắp hết (< 10)</option>
                    <option value="out" <?= ($_GET['stock_status'] ?? '') === 'out' ? 'selected' : '' ?>>Hết hàng</option>
                    <option value="ok" <?= ($_GET['stock_status'] ?? '') === 'ok' ? 'selected' : '' ?>>Còn hàng (≥ 10)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter me-1"></i>Lọc
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 admin-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th class="text-center">Tồn kho</th>
                    <th>Giá nhập</th>
                    <th>Lợi nhuận</th>
                    <th>Giá bán</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['products'])): ?>
                    <?php foreach ($data['products'] as $product): ?>
                    <?php
                    $stock = $product['stock'] ?? 0;
                    $stockClass = $stock <= 0 ? 'danger' : ($stock < 10 ? 'warning' : 'success');
                    $costPrice = $product['cost_price'] ?? ($product['price'] * 0.8);
                    $profitMargin = $product['profit_margin'] ?? 10;
                    $sellingPrice = $costPrice * (1 + $profitMargin / 100);
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?? 'placeholder.jpg' ?>" 
                                     class="rounded" style="width: 45px; height: 45px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/45'">
                                <div>
                                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    <br><small class="text-muted">#<?= $product['id'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $stockClass ?> px-3 py-2"><?= $stock ?></span>
                        </td>
                        <td><?= number_format($costPrice, 0, ',', '.') ?>đ</td>
                        <td>
                            <span class="badge bg-info"><?= $profitMargin ?>%</span>
                        </td>
                        <td class="text-danger fw-bold"><?= number_format($sellingPrice, 0, ',', '.') ?>đ</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="editStock(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $stock ?>, <?= $profitMargin ?>)">
                                <i class="fa-solid fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                            Không có sản phẩm nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>admin/updateStock" method="POST">
                <input type="hidden" name="product_id" id="editStockId">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-edit text-primary me-2"></i>Cập nhật kho
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Sản phẩm:</strong> <span id="editStockName"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Số lượng tồn kho</label>
                        <input type="number" name="stock" id="editStockQty" class="form-control" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tỷ lệ lợi nhuận (%)</label>
                        <input type="number" name="profit_margin" id="editStockMargin" class="form-control" min="0" max="200" step="0.5">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editStock(id, name, stock, margin) {
    document.getElementById('editStockId').value = id;
    document.getElementById('editStockName').textContent = name;
    document.getElementById('editStockQty').value = stock;
    document.getElementById('editStockMargin').value = margin;
    new bootstrap.Modal(document.getElementById('editStockModal')).show();
}
</script>
