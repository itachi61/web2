<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Sổ Nhập Xuất</h2>
</div>

<?php
$dateFrom = $_GET['from'] ?? '';
$dateTo = $_GET['to'] ?? '';
$viewMode = $_GET['mode'] ?? 'receipt'; // receipt | timeline

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ===== TỔNG QUAN =====
    $whereDate = '';
    $params = [];
    if ($dateFrom) { $whereDate .= " AND ir.created_at >= ?"; $params[] = $dateFrom . ' 00:00:00'; }
    if ($dateTo) { $whereDate .= " AND ir.created_at <= ?"; $params[] = $dateTo . ' 23:59:59'; }
    
    // Tổng tiền nhập
    $stmt = $db->prepare("SELECT COUNT(*) as total_receipts, COALESCE(SUM(ir.total_amount),0) as total_cost 
                          FROM import_receipts ir WHERE ir.status = 'completed'" . $whereDate);
    $stmt->execute($params);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Tổng tiền bán (cùng khoảng thời gian)
    $salesWhere = '';
    $salesParams = [];
    if ($dateFrom) { $salesWhere .= " AND o.created_at >= ?"; $salesParams[] = $dateFrom . ' 00:00:00'; }
    if ($dateTo) { $salesWhere .= " AND o.created_at <= ?"; $salesParams[] = $dateTo . ' 23:59:59'; }
    $stmt = $db->prepare("SELECT COALESCE(SUM(o.total_money),0) as total_sales, COUNT(*) as total_orders 
                          FROM orders o WHERE o.status != 'cancelled'" . $salesWhere);
    $stmt->execute($salesParams);
    $sales = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // ===== DANH SÁCH PHIẾU NHẬP =====
    $stmt = $db->prepare("SELECT ir.*, 
                          (SELECT COUNT(*) FROM import_receipt_items WHERE receipt_id = ir.id) as item_count,
                          u.name as created_by_name
                          FROM import_receipts ir 
                          LEFT JOIN users u ON ir.created_by = u.id
                          WHERE ir.status = 'completed'" . $whereDate . " 
                          ORDER BY ir.created_at DESC");
    $stmt->execute($params);
    $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Load chi tiết từng phiếu
    $receiptDetails = [];
    foreach ($receipts as $r) {
        $stmt = $db->prepare("SELECT iri.*, p.name as product_name, p.image 
                              FROM import_receipt_items iri 
                              JOIN products p ON iri.product_id = p.id 
                              WHERE iri.receipt_id = ?");
        $stmt->execute([$r['id']]);
        $receiptDetails[$r['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ===== THEO THÁNG (timeline) =====
    $stmt = $db->prepare("SELECT DATE_FORMAT(ir.created_at, '%Y-%m') as month_key,
                          DATE_FORMAT(ir.created_at, '%m/%Y') as month_label,
                          COUNT(*) as receipt_count,
                          COALESCE(SUM(ir.total_amount),0) as month_cost,
                          COALESCE(SUM(ir.total_items),0) as month_items
                          FROM import_receipts ir 
                          WHERE ir.status = 'completed'" . $whereDate . "
                          GROUP BY month_key 
                          ORDER BY month_key DESC");
    $stmt->execute($params);
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Doanh thu tương ứng theo tháng
    foreach ($monthlyData as &$m) {
        $mStart = $m['month_key'] . '-01 00:00:00';
        $mEnd = date('Y-m-t 23:59:59', strtotime($mStart));
        $stmt = $db->prepare("SELECT COALESCE(SUM(o.total_money),0) as sales FROM orders o WHERE o.status != 'cancelled' AND o.created_at >= ? AND o.created_at <= ?");
        $stmt->execute([$mStart, $mEnd]);
        $m['month_sales'] = $stmt->fetch(PDO::FETCH_ASSOC)['sales'];
    }
    unset($m);
    
} catch (Exception $e) {
    $summary = ['total_receipts' => 0, 'total_cost' => 0];
    $sales = ['total_sales' => 0, 'total_orders' => 0];
    $receipts = [];
    $receiptDetails = [];
    $monthlyData = [];
}

$netProfit = $sales['total_sales'] - $summary['total_cost'];
?>

<!-- Bộ lọc -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>admin/importReport" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Từ ngày</label>
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Đến ngày</label>
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Chế độ xem</label>
                <select name="mode" class="form-select">
                    <option value="receipt" <?= $viewMode == 'receipt' ? 'selected' : '' ?>>Theo phiếu nhập</option>
                    <option value="timeline" <?= $viewMode == 'timeline' ? 'selected' : '' ?>>Theo tháng</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fa-solid fa-filter me-1"></i> Lọc
                </button>
                <a href="<?= BASE_URL ?>admin/importReport" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tổng quan -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-danger bg-gradient text-white">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Tổng chi nhập hàng</small>
                    <h4 class="fw-bold mb-0"><?= number_format($summary['total_cost'], 0, ',', '.') ?>đ</h4>
                </div>
                <i class="fa-solid fa-arrow-down fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-success bg-gradient text-white">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Tổng doanh thu</small>
                    <h4 class="fw-bold mb-0"><?= number_format($sales['total_sales'], 0, ',', '.') ?>đ</h4>
                </div>
                <i class="fa-solid fa-arrow-up fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 <?= $netProfit >= 0 ? 'bg-primary' : 'bg-warning' ?> bg-gradient text-white">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Chênh lệch (Thu - Chi)</small>
                    <h4 class="fw-bold mb-0"><?= $netProfit >= 0 ? '+' : '' ?><?= number_format($netProfit, 0, ',', '.') ?>đ</h4>
                </div>
                <i class="fa-solid fa-scale-balanced fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 bg-info bg-gradient text-white">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <div>
                    <small class="opacity-75">Số phiếu nhập</small>
                    <h4 class="fw-bold mb-0"><?= $summary['total_receipts'] ?> phiếu</h4>
                </div>
                <i class="fa-solid fa-file-invoice fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<?php if ($viewMode == 'timeline'): ?>
<!-- ===== CHẾ ĐỘ XEM THEO THÁNG ===== -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-calendar-days me-2 text-info"></i>Nhập xuất theo tháng</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tháng</th>
                        <th class="text-center">Số phiếu nhập</th>
                        <th class="text-center">SP nhập</th>
                        <th class="text-end">Chi phí nhập</th>
                        <th class="text-end">Doanh thu bán</th>
                        <th class="text-end">Chênh lệch</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($monthlyData)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
                    <?php else: ?>
                    <?php foreach ($monthlyData as $m): 
                        $mDiff = $m['month_sales'] - $m['month_cost'];
                    ?>
                    <tr>
                        <td class="fw-bold"><i class="fa-regular fa-calendar me-2 text-primary"></i><?= $m['month_label'] ?></td>
                        <td class="text-center"><span class="badge bg-info"><?= $m['receipt_count'] ?></span></td>
                        <td class="text-center"><?= $m['month_items'] ?></td>
                        <td class="text-end text-danger fw-bold"><?= number_format($m['month_cost'], 0, ',', '.') ?>đ</td>
                        <td class="text-end text-success fw-bold"><?= number_format($m['month_sales'], 0, ',', '.') ?>đ</td>
                        <td class="text-end fw-bold <?= $mDiff >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $mDiff >= 0 ? '+' : '' ?><?= number_format($mDiff, 0, ',', '.') ?>đ
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ===== CHẾ ĐỘ XEM THEO PHIẾU NHẬP ===== -->
<?php if (empty($receipts)): ?>
<div class="alert alert-info"><i class="fa-solid fa-info-circle me-2"></i>Không có phiếu nhập nào trong khoảng thời gian này.</div>
<?php else: ?>

<?php foreach ($receipts as $r): 
    $items = $receiptDetails[$r['id']] ?? [];
?>
<div class="card shadow-sm border-0 rounded-3 mb-3">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold">
                <i class="fa-solid fa-file-invoice me-2 text-primary"></i>
                <a href="<?= BASE_URL ?>admin/viewReceipt/<?= $r['id'] ?>" class="text-decoration-none"><?= $r['receipt_code'] ?></a>
            </h6>
            <small class="text-muted">
                <i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                <?php if (!empty($r['created_by_name'])): ?>
                    · <i class="fa-regular fa-user me-1"></i><?= htmlspecialchars($r['created_by_name']) ?>
                <?php endif; ?>
            </small>
        </div>
        <div class="text-end">
            <span class="badge bg-danger-subtle text-danger fs-6"><?= number_format($r['total_amount'], 0, ',', '.') ?>đ</span>
            <br><small class="text-muted"><?= $r['item_count'] ?> sản phẩm</small>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Sản phẩm</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Giá nhập/SP</th>
                    <th class="text-end pe-3">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td class="ps-3">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= BASE_URL ?>public/uploads/<?= $item['image'] ?>" width="30" height="30" class="rounded me-2" style="object-fit:cover;">
                        <?php endif; ?>
                        <?= htmlspecialchars($item['product_name']) ?>
                    </td>
                    <td class="text-center fw-bold"><?= $item['quantity'] ?></td>
                    <td class="text-end"><?= number_format($item['import_price'], 0, ',', '.') ?>đ</td>
                    <td class="text-end pe-3 fw-bold"><?= number_format($item['quantity'] * $item['import_price'], 0, ',', '.') ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($r['note'])): ?>
    <div class="card-footer bg-light small text-muted py-2">
        <i class="fa-solid fa-note-sticky me-1"></i> <?= htmlspecialchars($r['note']) ?>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php endif; ?>
<?php endif; ?>
