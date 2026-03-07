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
    <div class="col-lg-5">
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
                                        data-margin="<?= $p['profit_margin'] ?? 0 ?>">
                                    <?= htmlspecialchars($p['name']) ?> (Tồn: <?= $p['stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Thông tin hiện tại -->
                    <div id="currentInfo" class="card bg-light border-0 mb-3" style="display: none;">
                        <div class="card-body py-2">
                            <div class="row text-center">
                                <div class="col-3">
                                    <small class="text-muted d-block">Tồn kho</small>
                                    <strong id="curStock" class="text-primary">0</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block">Giá nhập BQ</small>
                                    <strong id="curCost" class="text-info small">0đ</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block">Giá bán</small>
                                    <strong id="curPrice" class="text-success small">0đ</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block">LN%</small>
                                    <strong id="curMargin" class="text-warning">0%</strong>
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
                                <div class="col-3">
                                    <small class="text-muted d-block">Tồn mới</small>
                                    <strong id="newStock" class="fs-5 text-primary">0</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block">BQ mới</small>
                                    <strong id="newCost" class="fs-6 text-info">0đ</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block">Giá bán mới</small>
                                    <strong id="newPrice" class="fs-6 text-success">0đ</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block">Lãi/SP</small>
                                    <strong id="newProfit" class="fs-6 text-warning">0đ</strong>
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

    <!-- Lịch sử nhập hàng đầy đủ -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử nhập hàng</h5>
            </div>
            <div class="card-body pb-2">
                <!-- Bộ lọc -->
                <form method="GET" action="<?= BASE_URL ?>admin/import" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <select name="product_id" class="form-select form-select-sm">
                            <option value="">Tất cả SP</option>
                            <?php foreach ($data['products'] as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($_GET['product_id']) && $_GET['product_id'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="Từ ngày" value="<?= $_GET['date_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control form-control-sm" placeholder="Đến ngày" value="<?= $_GET['date_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($data['history'])): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">SL</th>
                                <th>Giá nhập</th>
                                <th>BQ cũ→mới</th>
                                <th>Giá bán</th>
                                <th>LN%</th>
                                <th>Lãi/SP</th>
                                <th>Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['history'] as $h): ?>
                            <tr>
                                <td class="fw-bold" style="max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($h['product_name']) ?>">
                                    <?= htmlspecialchars($h['product_name']) ?>
                                </td>
                                <td class="text-center"><span class="badge bg-info"><?= $h['quantity'] ?></span></td>
                                <td class="small"><?= number_format($h['import_price'], 0, ',', '.') ?>đ</td>
                                <td class="small">
                                    <?= number_format($h['old_cost_price'], 0, ',', '.') ?> →
                                    <strong class="text-primary"><?= number_format($h['new_cost_price'], 0, ',', '.') ?></strong>
                                </td>
                                <td class="small fw-bold text-success">
                                    <?php if (isset($h['selling_price']) && $h['selling_price'] > 0): ?>
                                        <?= number_format($h['selling_price'], 0, ',', '.') ?>đ
                                    <?php else: ?>
                                        <span class="text-muted">--</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($h['profit_margin']) && $h['profit_margin'] > 0): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning"><?= round($h['profit_margin'], 1) ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">--</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <?php 
                                    if (isset($h['selling_price']) && $h['selling_price'] > 0 && $h['new_cost_price'] > 0):
                                        $profitPerUnit = $h['selling_price'] - $h['new_cost_price'];
                                    ?>
                                        <strong class="<?= $profitPerUnit >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= ($profitPerUnit >= 0 ? '+' : '') . number_format($profitPerUnit, 0, ',', '.') ?>đ
                                        </strong>
                                    <?php else: ?>
                                        <span class="text-muted">--</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
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
    document.getElementById('curMargin').textContent = margin.toFixed(1) + '%';

    const qty = parseInt(qtyInput.value) || 0;
    const impPrice = parseFloat(priceInput.value) || 0;

    if (qty > 0 && impPrice > 0) {
        document.getElementById('wacPreview').style.display = 'block';
        const newStockVal = stock + qty;
        const newCostVal = (stock * cost + qty * impPrice) / newStockVal;
        const newPriceVal = newCostVal * (1 + margin / 100);
        const profitPerUnit = newPriceVal - newCostVal;

        document.getElementById('wacFormula').innerHTML = 
            `(${stock} × ${fmt(cost)} + ${qty} × ${fmt(impPrice)}) / (${stock} + ${qty}) = <strong>${fmt(newCostVal)}đ</strong>`;
        document.getElementById('newStock').textContent = newStockVal;
        document.getElementById('newCost').textContent = fmt(newCostVal) + 'đ';
        document.getElementById('newPrice').textContent = fmt(newPriceVal) + 'đ';
        document.getElementById('newProfit').textContent = '+' + fmt(profitPerUnit) + 'đ';
    } else {
        document.getElementById('wacPreview').style.display = 'none';
    }
}

select.addEventListener('change', updatePreview);
qtyInput.addEventListener('input', updatePreview);
priceInput.addEventListener('input', updatePreview);
</script>
