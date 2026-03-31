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

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold text-primary mb-0"><i class="fa-solid fa-truck-ramp-box me-2"></i>Nhập hàng & Tồn kho</h2>
    <form action="<?= BASE_URL ?>admin/createReceipt" method="POST" class="d-inline">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Tạo phiếu nhập mới</button>
    </form>
</div>

<!-- TAB NAV -->
<ul class="nav nav-tabs mb-0" id="importTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold" id="tab-receipts" data-bs-toggle="tab" data-bs-target="#pane-receipts" type="button" role="tab">
            <i class="fa-solid fa-file-invoice me-1"></i>Phiếu nhập
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold" id="tab-stock" data-bs-toggle="tab" data-bs-target="#pane-stock" type="button" role="tab">
            <i class="fa-solid fa-warehouse me-1"></i>Tra cứu tồn kho
        </button>
    </li>
</ul>

<!-- TAB CONTENT -->
<div class="tab-content border border-top-0 rounded-bottom-3 bg-white shadow-sm">

    <!-- === TAB 1: PHIẾU NHẬP === -->
    <div class="tab-pane fade show active p-0" id="pane-receipts" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="15%">Mã phiếu</th>
                        <th width="10%">Số SP</th>
                        <th width="15%">Tổng tiền</th>
                        <th width="12%">Trạng thái</th>
                        <th width="13%">Ngày tạo</th>
                        <th width="13%">Ngày HT</th>
                        <th width="10%">Người tạo</th>
                        <th class="text-end pe-4" width="7%">Xem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['receipts'])): ?>
                        <?php foreach ($data['receipts'] as $r): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $r['id'] ?></td>
                            <td><span class="fw-bold text-primary"><?= htmlspecialchars($r['receipt_code']) ?></span></td>
                            <td><span class="badge bg-info-subtle text-info"><?= $r['item_count'] ?> SP</span></td>
                            <td class="fw-bold"><?= number_format($r['total_amount'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php if ($r['status'] == 'draft'): ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-pen me-1"></i>Nháp</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Hoàn thành</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                            <td class="text-muted small"><?= $r['completed_at'] ? date('d/m/Y H:i', strtotime($r['completed_at'])) : '—' ?></td>
                            <td class="small"><?= htmlspecialchars($r['created_by_name'] ?? '') ?></td>
                            <td class="text-end pe-4">
                                <a href="<?= BASE_URL ?>admin/viewReceipt/<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fa-solid fa-file-invoice fa-3x text-muted mb-2"></i>
                                <p class="text-muted mt-2">Chưa có phiếu nhập nào. Nhấn "Tạo phiếu nhập mới" để bắt đầu.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- === TAB 2: TRA CỨU TỒN KHO === -->
    <div class="tab-pane fade p-4" id="pane-stock" role="tabpanel">
        <?php
        // Load data cho tra cứu tồn kho
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stockProducts = $db->query("SELECT id, name, stock, cost_price, price, profit_margin FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
            
            $stockHistory = [];
            $stockAtDate = null;
            $selectedProduct = null;
            $importBatches = [];
            $selectedDate = $_GET['stock_date'] ?? '';
            $selectedProductId = intval($_GET['stock_product'] ?? 0);
            
            if ($selectedProductId > 0) {
                $stmt = $db->prepare("SELECT sh.* FROM stock_history sh WHERE sh.product_id = ? ORDER BY sh.created_at DESC LIMIT 50");
                $stmt->execute([$selectedProductId]);
                $stockHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$selectedProductId]);
                $selectedProduct = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($selectedDate && $selectedProduct) {
                    $currentStock = intval($selectedProduct['stock']);
                    $stmt = $db->prepare("SELECT change_qty, change_type FROM stock_history WHERE product_id = ? AND created_at > ? ORDER BY created_at ASC");
                    $stmt->execute([$selectedProductId, $selectedDate . ' 23:59:59']);
                    $changes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $stockAtDate = $currentStock;
                    foreach ($changes as $c) {
                        if ($c['change_type'] == 'import') $stockAtDate -= $c['change_qty'];
                        else $stockAtDate += $c['change_qty'];
                    }
                }

                // Lịch sử nhập hàng
                $stmt = $db->prepare("SELECT ih.*, p.profit_margin FROM import_history ih JOIN products p ON ih.product_id = p.id WHERE ih.product_id = ? ORDER BY ih.created_at DESC");
                $stmt->execute([$selectedProductId]);
                $importBatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $stockProducts = [];
            $stockHistory = [];
        }
        ?>

        <div class="row g-4">
            <!-- Cột trái: Form tra cứu -->
            <div class="col-lg-5">
                <div class="card border shadow-sm rounded-3">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0"><i class="fa-solid fa-magnifying-glass me-2"></i>Tra cứu tồn kho</h6>
                    </div>
                    <div class="card-body p-3">
                        <form method="GET" action="<?= BASE_URL ?>admin/import">
                            <input type="hidden" name="tab" value="stock">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Chọn sản phẩm <span class="text-danger">*</span></label>
                                <select name="stock_product" class="form-select" required>
                                    <option value="">-- Chọn sản phẩm --</option>
                                    <?php foreach ($stockProducts as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= ($selectedProductId == $p['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['name']) ?> (Tồn: <?= $p['stock'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Tồn kho tại ngày</label>
                                <input type="date" name="stock_date" class="form-control" value="<?= htmlspecialchars($selectedDate) ?>">
                                <small class="text-muted">Để trống = chỉ xem lịch sử</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-search me-2"></i>Tra cứu
                            </button>
                        </form>
                    </div>
                </div>

                <?php if ($selectedProduct && $stockAtDate !== null): ?>
                <div class="card border shadow-sm rounded-3 mt-3">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted mb-1 small">Tồn kho của</h6>
                        <h6 class="fw-bold text-primary"><?= htmlspecialchars($selectedProduct['name']) ?></h6>
                        <p class="text-muted small mb-2">Tại ngày <?= date('d/m/Y', strtotime($selectedDate)) ?></p>
                        <div class="display-4 fw-bold text-success"><?= $stockAtDate ?></div>
                        <small class="text-muted">sản phẩm</small>
                        <hr class="my-2">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted d-block">Tồn hiện tại</small>
                                <strong class="text-primary fs-5"><?= $selectedProduct['stock'] ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Chênh lệch</small>
                                <?php $diff = intval($selectedProduct['stock']) - $stockAtDate; ?>
                                <strong class="fs-5 <?= $diff >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $diff >= 0 ? '+' : '' ?><?= $diff ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($selectedProduct && $stockAtDate === null): ?>
                <div class="card border shadow-sm rounded-3 mt-3">
                    <div class="card-body py-3">
                        <h6 class="fw-bold text-primary mb-3"><?= htmlspecialchars($selectedProduct['name']) ?></h6>
                        <div class="row text-center g-2">
                            <div class="col-3">
                                <div class="border rounded-3 py-2">
                                    <small class="text-muted d-block" style="font-size:0.7rem">Tồn kho</small>
                                    <strong class="text-success"><?= $selectedProduct['stock'] ?></strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded-3 py-2">
                                    <small class="text-muted d-block" style="font-size:0.7rem">Giá vốn</small>
                                    <strong class="text-info" style="font-size:0.85rem"><?= number_format($selectedProduct['cost_price'], 0, ',', '.') ?>đ</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded-3 py-2">
                                    <small class="text-muted d-block" style="font-size:0.7rem">Giá bán</small>
                                    <strong class="text-primary" style="font-size:0.85rem"><?= number_format($selectedProduct['price'], 0, ',', '.') ?>đ</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded-3 py-2">
                                    <small class="text-muted d-block" style="font-size:0.7rem">% LN</small>
                                    <strong class="text-warning"><?= $selectedProduct['profit_margin'] ?? 0 ?>%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Cột phải: Lịch sử -->
            <div class="col-lg-7">
                <?php if (!empty($stockHistory)): ?>
                <div class="card border shadow-sm rounded-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử thay đổi kho</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Loại</th>
                                        <th>SL</th>
                                        <th>Trước</th>
                                        <th>Sau</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stockHistory as $h): ?>
                                    <tr>
                                        <td class="small"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                                        <td>
                                            <?php if ($h['change_type'] == 'import'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-arrow-down me-1"></i>Nhập</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-arrow-up me-1"></i>Bán</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="<?= $h['change_type'] == 'import' ? 'text-success' : 'text-danger' ?>">
                                                <?= $h['change_type'] == 'import' ? '+' : '-' ?><?= $h['change_qty'] ?>
                                            </strong>
                                        </td>
                                        <td><?= $h['stock_before'] ?></td>
                                        <td class="fw-bold"><?= $h['stock_after'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if (!empty($importBatches)): ?>
                <div class="card border shadow-sm rounded-3 mt-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 fw-bold"><i class="fa-solid fa-money-bill-trend-up me-2 text-success"></i>Giá vốn theo lô nhập</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Lần</th>
                                        <th>Ngày</th>
                                        <th>SL</th>
                                        <th>Giá nhập</th>
                                        <th>% LN</th>
                                        <th>Giá bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($importBatches as $idx => $batch): ?>
                                    <tr>
                                        <td><span class="badge bg-primary-subtle text-primary">#<?= count($importBatches) - $idx ?></span></td>
                                        <td class="small"><?= date('d/m/Y H:i', strtotime($batch['created_at'] ?? '')) ?></td>
                                        <td class="fw-bold"><?= $batch['quantity'] ?></td>
                                        <td class="text-info fw-bold"><?= number_format($batch['import_price'], 0, ',', '.') ?>đ</td>
                                        <td><span class="badge bg-success-subtle text-success"><?= $batch['profit_margin'] ?? 0 ?>%</span></td>
                                        <td class="text-primary fw-bold">
                                            <?php
                                            $margin = floatval($batch['profit_margin'] ?? 0);
                                            $sellPrice = $batch['import_price'] * (1 + $margin / 100);
                                            echo number_format($sellPrice, 0, ',', '.') . 'đ';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php elseif ($selectedProductId > 0): ?>
                <div class="card border shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có lịch sử thay đổi kho cho sản phẩm này.</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="card border shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fa-solid fa-warehouse fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chọn sản phẩm để xem lịch sử thay đổi kho</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Auto-switch to stock tab if queried -->
<script>
(function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'stock' || params.get('stock_product')) {
        const stockTab = document.getElementById('tab-stock');
        if (stockTab) {
            const tab = new bootstrap.Tab(stockTab);
            tab.show();
        }
    }
})();
</script>
