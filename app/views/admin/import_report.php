<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Sổ Nhập Xuất</h2>
</div>

<?php
$dateFrom = $_GET['from'] ?? '';
$dateTo = $_GET['to'] ?? '';
$viewMode = $_GET['mode'] ?? 'receipt'; // receipt | timeline | product

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
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
                          u.fullname as created_by_name
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

    // ===== BÁO CÁO THEO SẢN PHẨM =====
    $importWhereDate = '';
    $importParams2 = [];
    if ($dateFrom) { $importWhereDate .= " AND ih.created_at >= ?"; $importParams2[] = $dateFrom . ' 00:00:00'; }
    if ($dateTo) { $importWhereDate .= " AND ih.created_at <= ?"; $importParams2[] = $dateTo . ' 23:59:59'; }

    $sqlIE = "SELECT p.id, p.name, p.image,
              COALESCE(SUM(ih.quantity), 0) as total_imported,
              COALESCE(SUM(ih.quantity * ih.import_price), 0) as total_import_cost
              FROM products p 
              LEFT JOIN import_history ih ON p.id = ih.product_id" . ($importWhereDate ? " AND 1=1" . $importWhereDate : "") . "
              WHERE 1=1
              GROUP BY p.id HAVING total_imported > 0
              ORDER BY total_imported DESC";
    $stmt = $db->prepare($sqlIE);
    $stmt->execute($importParams2);
    $importExportReport = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Merge sold data + load detail
    $importExportDetails = [];
    foreach ($importExportReport as &$row) {
        // Sold data
        $soldWhere = '';
        $soldP = [$row['id']];
        if ($dateFrom) { $soldWhere .= " AND o.created_at >= ?"; $soldP[] = $dateFrom . ' 00:00:00'; }
        if ($dateTo) { $soldWhere .= " AND o.created_at <= ?"; $soldP[] = $dateTo . ' 23:59:59'; }
        $stmtS = $db->prepare("SELECT COALESCE(SUM(oi.quantity),0) as qty_sold, COALESCE(SUM(oi.quantity*oi.price),0) as revenue 
                               FROM order_items oi JOIN orders o ON oi.order_id = o.id 
                               WHERE oi.product_id = ? AND o.status != 'cancelled'" . $soldWhere);
        $stmtS->execute($soldP);
        $soldInfo = $stmtS->fetch(PDO::FETCH_ASSOC);
        $row['total_sold'] = intval($soldInfo['qty_sold']);
        $row['total_revenue'] = floatval($soldInfo['revenue']);

        // Detail imports
        $dtlImportWhere = '';
        $dtlImportP = [$row['id']];
        if ($dateFrom) { $dtlImportWhere .= " AND ir.created_at >= ?"; $dtlImportP[] = $dateFrom . ' 00:00:00'; }
        if ($dateTo) { $dtlImportWhere .= " AND ir.created_at <= ?"; $dtlImportP[] = $dateTo . ' 23:59:59'; }
        $stmtD = $db->prepare("SELECT iri.quantity, iri.import_price, ir.created_at as import_date, ir.receipt_code, ir.id as receipt_id 
                                FROM import_receipt_items iri 
                                JOIN import_receipts ir ON iri.receipt_id = ir.id 
                                WHERE iri.product_id = ? AND ir.status = 'completed'" . $dtlImportWhere . " 
                                ORDER BY ir.created_at DESC");
        $stmtD->execute($dtlImportP);
        $importExportDetails[$row['id']]['imports'] = $stmtD->fetchAll(PDO::FETCH_ASSOC);

        // Detail exports
        $dtlExportWhere = '';
        $dtlExportP = [$row['id']];
        if ($dateFrom) { $dtlExportWhere .= " AND o.created_at >= ?"; $dtlExportP[] = $dateFrom . ' 00:00:00'; }
        if ($dateTo) { $dtlExportWhere .= " AND o.created_at <= ?"; $dtlExportP[] = $dateTo . ' 23:59:59'; }
        $stmtD = $db->prepare("SELECT oi.quantity, oi.price, o.created_at, o.id as order_id, o.fullname 
                                FROM order_items oi JOIN orders o ON oi.order_id = o.id 
                                WHERE oi.product_id = ? AND o.status != 'cancelled'" . $dtlExportWhere . " 
                                ORDER BY o.created_at DESC");
        $stmtD->execute($dtlExportP);
        $importExportDetails[$row['id']]['exports'] = $stmtD->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($row);
    
} catch (Exception $e) {
    $summary = ['total_receipts' => 0, 'total_cost' => 0];
    $sales = ['total_sales' => 0, 'total_orders' => 0];
    $receipts = [];
    $receiptDetails = [];
    $monthlyData = [];
    $importExportReport = [];
    $importExportDetails = [];
}

$netProfit = $sales['total_sales'] - $summary['total_cost'];
?>

<!-- Bộ lọc -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>admin/importReport" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Từ ngày</label>
                <input type="date" name="from" id="irDateFrom" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Đến ngày</label>
                <input type="date" name="to" id="irDateTo" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Chế độ xem</label>
                <select name="mode" class="form-select">
                    <option value="receipt" <?= $viewMode == 'receipt' ? 'selected' : '' ?>>Theo phiếu nhập</option>
                    <option value="timeline" <?= $viewMode == 'timeline' ? 'selected' : '' ?>>Theo tháng</option>
                    <option value="product" <?= $viewMode == 'product' ? 'selected' : '' ?>>Báo cáo theo sản phẩm</option>
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

<?php elseif ($viewMode == 'receipt'): ?>
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
                    <th class="ps-3" style="width:40%;">Sản phẩm</th>
                    <th class="text-center" style="width:15%;">Số lượng</th>
                    <th class="text-end" style="width:20%;">Giá nhập/SP</th>
                    <th class="text-end pe-3" style="width:25%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td class="ps-3">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= BASE_URL ?>images/<?= $item['image'] ?>" width="30" height="30" class="rounded me-2" style="object-fit:cover;">
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

<?php elseif ($viewMode == 'product'): ?>
<!-- ===== CHẾ ĐỘ XEM THEO SẢN PHẨM ===== -->
<?php if (empty($importExportReport)): ?>
<div class="alert alert-info"><i class="fa-solid fa-info-circle me-2"></i>Không có dữ liệu nhập xuất.</div>
<?php else: ?>
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-arrow-right-arrow-left me-2 text-info"></i>Báo cáo Nhập – Xuất theo sản phẩm
            <?php if ($dateFrom || $dateTo): ?>
                <small class="text-muted fw-normal ms-2">
                    (<?= $dateFrom ? date('d/m/Y', strtotime($dateFrom)) : '...' ?> → <?= $dateTo ? date('d/m/Y', strtotime($dateTo)) : '...' ?>)
                </small>
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 700px;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #f8f9ff 0%, #eef1f8 100%);">
                        <th class="ps-3 py-3 text-muted text-uppercase small fw-bold" style="width: 30%;">Sản phẩm</th>
                        <th class="text-center py-3 text-muted text-uppercase small fw-bold" style="width: 10%;">SL Nhập</th>
                        <th class="text-center py-3 text-muted text-uppercase small fw-bold" style="width: 10%;">SL Bán</th>
                        <th class="text-end py-3 text-muted text-uppercase small fw-bold" style="width: 17%;">Tổng tiền nhập</th>
                        <th class="text-end py-3 text-muted text-uppercase small fw-bold" style="width: 17%;">Tổng doanh thu</th>
                        <th class="text-end py-3 text-muted text-uppercase small fw-bold" style="width: 16%;">Chênh lệch</th>
                        <th class="text-center py-3" style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sumImported = 0; $sumSold = 0; $sumImportCost = 0; $sumRevenue = 0;
                    foreach ($importExportReport as $ie): 
                        $ieSold = intval($ie['total_sold'] ?? 0);
                        $ieRevenue = floatval($ie['total_revenue'] ?? 0);
                        $sumImported += $ie['total_imported'];
                        $sumSold += $ieSold;
                        $sumImportCost += $ie['total_import_cost'];
                        $sumRevenue += $ieRevenue;
                        $diff = $ieRevenue - $ie['total_import_cost'];
                        $dtl = $importExportDetails[$ie['id']] ?? ['imports'=>[],'exports'=>[]];
                    ?>
                    <tr class="border-bottom" style="transition: background 0.15s;">
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($ie['image'])): ?>
                                    <img src="<?= BASE_URL ?>images/<?= $ie['image'] ?>" width="36" height="36" 
                                         class="rounded-2 border" style="object-fit: cover;" onerror="this.style.display='none'">
                                <?php else: ?>
                                    <div class="rounded-2 bg-light d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="fa-solid fa-box text-muted small"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="fw-semibold"><?= htmlspecialchars($ie['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-center py-3">
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2" style="font-size: 0.85rem;">
                                <i class="fa-solid fa-arrow-down me-1 small"></i>+<?= $ie['total_imported'] ?>
                            </span>
                        </td>
                        <td class="text-center py-3">
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2" style="font-size: 0.85rem;">
                                <i class="fa-solid fa-arrow-up me-1 small"></i>-<?= $ieSold ?>
                            </span>
                        </td>
                        <td class="text-end py-3">
                            <span class="text-muted"><?= number_format($ie['total_import_cost'], 0, ',', '.') ?>đ</span>
                        </td>
                        <td class="text-end py-3">
                            <span class="fw-bold text-primary"><?= number_format($ieRevenue, 0, ',', '.') ?>đ</span>
                        </td>
                        <td class="text-end py-3">
                            <span class="fw-bold <?= $diff >= 0 ? 'text-success' : 'text-danger' ?>" style="font-size: 0.95rem;">
                                <?= $diff >= 0 ? '+' : '' ?><?= number_format($diff, 0, ',', '.') ?>đ
                            </span>
                        </td>
                        <td class="text-center py-3">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#ieModal<?= $ie['id'] ?>">
                                <i class="fa-solid fa-eye me-1"></i>Xem
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);">
                        <td class="ps-3 py-3 fw-bold text-dark">
                            <i class="fa-solid fa-calculator me-2 text-warning"></i>TỔNG CỘNG
                        </td>
                        <td class="text-center py-3 fw-bold"><?= $sumImported ?></td>
                        <td class="text-center py-3 fw-bold"><?= $sumSold ?></td>
                        <td class="text-end py-3 fw-bold"><?= number_format($sumImportCost, 0, ',', '.') ?>đ</td>
                        <td class="text-end py-3 fw-bold text-primary"><?= number_format($sumRevenue, 0, ',', '.') ?>đ</td>
                        <?php $totalDiff = $sumRevenue - $sumImportCost; ?>
                        <td class="text-end py-3 fw-bold <?= $totalDiff >= 0 ? 'text-success' : 'text-danger' ?>" style="font-size: 1.05rem;">
                            <?= $totalDiff >= 0 ? '+' : '' ?><?= number_format($totalDiff, 0, ',', '.') ?>đ
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modals (ngoài bảng) -->
<?php foreach ($importExportReport as $ie): 
    $dtl = $importExportDetails[$ie['id']] ?? ['imports'=>[],'exports'=>[]];
?>
<div class="modal fade" id="ieModal<?= $ie['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-3" style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);">
                <h6 class="modal-title text-white fw-bold">
                    <i class="fa-solid fa-chart-bar me-2"></i><?= htmlspecialchars($ie['name']) ?>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Tóm tắt -->
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="border rounded-3 p-3 text-center bg-success bg-opacity-10">
                            <small class="text-muted d-block mb-1">Nhập vào</small>
                            <span class="fw-bold text-success fs-5">+<?= $ie['total_imported'] ?></span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-3 text-center bg-danger bg-opacity-10">
                            <small class="text-muted d-block mb-1">Đã bán</small>
                            <span class="fw-bold text-danger fs-5">-<?= intval($ie['total_sold'] ?? 0) ?></span>
                        </div>
                    </div>
                    <div class="col-4">
                        <?php $mdiff = floatval($ie['total_revenue'] ?? 0) - $ie['total_import_cost']; ?>
                        <div class="border rounded-3 p-3 text-center <?= $mdiff >= 0 ? 'bg-primary bg-opacity-10' : 'bg-warning bg-opacity-10' ?>">
                            <small class="text-muted d-block mb-1">Chênh lệch</small>
                            <span class="fw-bold <?= $mdiff >= 0 ? 'text-primary' : 'text-danger' ?> fs-5">
                                <?= $mdiff >= 0 ? '+' : '' ?><?= number_format($mdiff, 0, ',', '.') ?>đ
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Nhập hàng -->
                <h6 class="fw-bold mb-3">
                    <span class="badge bg-success me-2"><i class="fa-solid fa-arrow-down"></i></span>
                    Nhập hàng <span class="text-muted fw-normal">(<?= count($dtl['imports']) ?> lần)</span>
                </h6>
                <?php if (!empty($dtl['imports'])): ?>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th class="text-center" style="width:40px;">#</th>
                                <th>Ngày</th>
                                <th>Phiếu</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Giá nhập</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dtl['imports'] as $i => $imp): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td><i class="fa-regular fa-clock me-1 text-muted small"></i><?= $imp['import_date'] ? date('d/m/Y H:i', strtotime($imp['import_date'])) : '-' ?></td>
                                <td>
                                    <?php if (!empty($imp['receipt_code'])): ?>
                                        <a href="<?= BASE_URL ?>admin/viewReceipt/<?= $imp['receipt_id'] ?>" class="text-decoration-none fw-semibold"><?= $imp['receipt_code'] ?></a>
                                    <?php else: ?>-<?php endif; ?>
                                </td>
                                <td class="text-center fw-bold"><?= $imp['quantity'] ?></td>
                                <td class="text-end"><?= number_format($imp['import_price'], 0, ',', '.') ?>đ</td>
                                <td class="text-end fw-bold"><?= number_format($imp['quantity'] * $imp['import_price'], 0, ',', '.') ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted small mb-4 fst-italic"><i class="fa-solid fa-info-circle me-1"></i>Không có lần nhập nào.</p>
                <?php endif; ?>

                <!-- Bán hàng -->
                <h6 class="fw-bold mb-3">
                    <span class="badge bg-danger me-2"><i class="fa-solid fa-arrow-up"></i></span>
                    Bán hàng <span class="text-muted fw-normal">(<?= count($dtl['exports']) ?> đơn)</span>
                </h6>
                <?php if (!empty($dtl['exports'])): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th class="text-center" style="width:40px;">#</th>
                                <th>Ngày</th>
                                <th>Đơn</th>
                                <th>Khách hàng</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dtl['exports'] as $i => $exp): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td><i class="fa-regular fa-clock me-1 text-muted small"></i><?= date('d/m/Y H:i', strtotime($exp['created_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>admin/orders?order_id=<?= $exp['order_id'] ?>" 
                                       class="badge bg-primary rounded-pill text-decoration-none" 
                                       title="Xem chi tiết đơn #<?= $exp['order_id'] ?>" target="_blank">
                                        #<?= $exp['order_id'] ?> <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size:0.65rem;"></i>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($exp['fullname']) ?></td>
                                <td class="text-center fw-bold"><?= $exp['quantity'] ?></td>
                                <td class="text-end"><?= number_format($exp['price'], 0, ',', '.') ?>đ</td>
                                <td class="text-end fw-bold"><?= number_format($exp['quantity'] * $exp['price'], 0, ',', '.') ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted small fst-italic"><i class="fa-solid fa-info-circle me-1"></i>Chưa có đơn hàng nào.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i>Đóng
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php endif; ?>

<script>
(function() {
    const fromInput = document.getElementById('irDateFrom');
    const toInput = document.getElementById('irDateTo');
    if (fromInput && toInput) {
        fromInput.addEventListener('change', function() {
            if (toInput.value && this.value > toInput.value) {
                toInput.value = this.value;
            }
            toInput.min = this.value;
        });
        toInput.addEventListener('change', function() {
            if (fromInput.value && this.value < fromInput.value) {
                fromInput.value = this.value;
            }
            fromInput.max = this.value;
        });
        if (fromInput.value) toInput.min = fromInput.value;
        if (toInput.value) fromInput.max = toInput.value;
    }
})();
</script>
