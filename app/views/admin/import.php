<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-truck-ramp-box me-2"></i>Nhập hàng</h2>
</div>

<?php if (isset($_SESSION['import_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-check-circle me-2"></i><?= $_SESSION['import_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['import_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['import_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa-solid fa-exclamation-circle me-2"></i><?= $_SESSION['import_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['import_error']); ?>
<?php endif; ?>

<div class="row g-4">
    <!-- Form nhập hàng -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Nhập hàng mới</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>admin/processImport" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn sản phẩm <span class="text-danger">*</span></label>
                        <select name="product_id" id="productSelect" class="form-select form-select-lg" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php foreach ($data['products'] as $p): ?>
                                <option value="<?= $p['id'] ?>" 
                                        data-stock="<?= $p['stock'] ?>" 
                                        data-cost="<?= $p['cost_price'] ?>" 
                                        data-price="<?= $p['price'] ?>"
                                        data-margin="<?= ($p['cost_price'] > 0) ? round(($p['price'] / $p['cost_price'] - 1) * 100, 1) : 0 ?>">
                                    <?= htmlspecialchars($p['name']) ?> (Tồn: <?= $p['stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Thông tin hiện tại -->
                    <div id="currentInfo" class="card bg-light border-0 mb-3" style="display: none;">
                        <div class="card-body py-2">
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted d-block">Tồn kho</small>
                                    <strong id="curStock" class="text-primary">0</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Giá nhập BQ</small>
                                    <strong id="curCost" class="text-info">0đ</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Giá bán</small>
                                    <strong id="curPrice" class="text-success">0đ</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số lượng nhập <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="importQty" class="form-control form-control-lg" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá nhập mới (đ/sp) <span class="text-danger">*</span></label>
                            <input type="number" name="import_price" id="importPrice" class="form-control form-control-lg" min="0" required>
                        </div>
                    </div>

                    <!-- Preview WAC -->
                    <div id="wacPreview" class="card border-primary mb-3" style="display: none;">
                        <div class="card-header bg-primary bg-opacity-10 py-2">
                            <h6 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-calculator me-2"></i>Preview tính WAC</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Công thức:</small><br>
                                <code id="wacFormula">Giá nhập BQ mới = (tồn × giá cũ + SL mới × giá mới) / (tồn + SL mới)</code>
                            </div>
                            <div class="row text-center mt-3">
                                <div class="col-4">
                                    <small class="text-muted d-block">Tồn kho mới</small>
                                    <strong id="newStock" class="fs-5 text-primary">0</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Giá nhập BQ mới</small>
                                    <strong id="newCost" class="fs-5 text-info">0đ</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Giá bán mới</small>
                                    <strong id="newPrice" class="fs-5 text-success">0đ</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fa-solid fa-file-import me-2"></i>Xác nhận nhập hàng
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lịch sử nhập hàng -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử nhập gần đây</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($data['history'])): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>SP</th>
                                <th>SL</th>
                                <th>Giá nhập</th>
                                <th>BQ cũ→mới</th>
                                <th>Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['history'] as $h): ?>
                            <tr>
                                <td class="fw-bold" style="max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($h['product_name']) ?></td>
                                <td><span class="badge bg-info"><?= $h['quantity'] ?></span></td>
                                <td><?= number_format($h['import_price'], 0, ',', '.') ?>đ</td>
                                <td class="small">
                                    <?= number_format($h['old_cost_price'], 0, ',', '.') ?> →
                                    <strong class="text-primary"><?= number_format($h['new_cost_price'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="small text-muted"><?= date('d/m H:i', strtotime($h['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">Chưa có lịch sử nhập hàng</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const select = document.getElementById('productSelect');
const qtyInput = document.getElementById('importQty');
const priceInput = document.getElementById('importPrice');

function fmt(n) { return new Intl.NumberFormat('vi-VN').format(Math.round(n)); }

function updatePreview() {
    const opt = select.selectedOptions[0];
    if (!opt || !opt.value) {
        document.getElementById('currentInfo').style.display = 'none';
        document.getElementById('wacPreview').style.display = 'none';
        return;
    }

    const stock = parseInt(opt.dataset.stock) || 0;
    const cost = parseFloat(opt.dataset.cost) || 0;
    const price = parseFloat(opt.dataset.price) || 0;
    const margin = parseFloat(opt.dataset.margin) || 0;

    document.getElementById('currentInfo').style.display = 'block';
    document.getElementById('curStock').textContent = stock;
    document.getElementById('curCost').textContent = fmt(cost) + 'đ';
    document.getElementById('curPrice').textContent = fmt(price) + 'đ';

    const qty = parseInt(qtyInput.value) || 0;
    const impPrice = parseFloat(priceInput.value) || 0;

    if (qty > 0 && impPrice > 0) {
        document.getElementById('wacPreview').style.display = 'block';
        const newStockVal = stock + qty;
        const newCostVal = (stock * cost + qty * impPrice) / newStockVal;
        const newPriceVal = newCostVal * (1 + margin / 100);

        document.getElementById('wacFormula').innerHTML = 
            `(${stock} × ${fmt(cost)} + ${qty} × ${fmt(impPrice)}) / (${stock} + ${qty}) = <strong>${fmt(newCostVal)}đ</strong>`;
        document.getElementById('newStock').textContent = newStockVal;
        document.getElementById('newCost').textContent = fmt(newCostVal) + 'đ';
        document.getElementById('newPrice').textContent = fmt(newPriceVal) + 'đ';
    } else {
        document.getElementById('wacPreview').style.display = 'none';
    }
}

select.addEventListener('change', updatePreview);
qtyInput.addEventListener('input', updatePreview);
priceInput.addEventListener('input', updatePreview);
</script>
