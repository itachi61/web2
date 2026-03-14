<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-chart-line me-2"></i>Thống kê & Báo cáo</h2>
</div>

<?php
// Lọc theo thời gian
$dateFrom = $_GET['from'] ?? '';
$dateTo = $_GET['to'] ?? '';
$filterProduct = $_GET['product_id'] ?? '';

$whereDate = '';
$params = [];
if ($dateFrom) { $whereDate .= " AND o.created_at >= ?"; $params[] = $dateFrom . ' 00:00:00'; }
if ($dateTo) { $whereDate .= " AND o.created_at <= ?"; $params[] = $dateTo . ' 23:59:59'; }

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tổng doanh thu
    $sql = "SELECT COALESCE(SUM(o.total_money), 0) as total_revenue, COUNT(*) as total_orders 
            FROM orders o WHERE o.status != 'cancelled'" . $whereDate;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Tổng SP đã bán
    $sql2 = "SELECT COALESCE(SUM(oi.quantity), 0) as total_sold 
             FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE o.status != 'cancelled'" . $whereDate;
    $stmt = $db->prepare($sql2);
    $stmt->execute($params);
    $totalSold = $stmt->fetch(PDO::FETCH_ASSOC)['total_sold'];
    
    // Tổng lợi nhuận (dùng cost_price snapshot từ order_items)
    $sql3 = "SELECT COALESCE(SUM((oi.price - COALESCE(oi.cost_price, p.cost_price)) * oi.quantity), 0) as total_profit 
             FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             JOIN products p ON oi.product_id = p.id 
             WHERE o.status != 'cancelled'" . $whereDate;
    $stmt = $db->prepare($sql3);
    $stmt->execute($params);
    $totalProfit = $stmt->fetch(PDO::FETCH_ASSOC)['total_profit'];
    
    // Doanh thu theo từng SP
    $sql4 = "SELECT p.id, p.name, p.price, p.cost_price, p.stock, p.discount, p.image,
             COALESCE(SUM(oi.quantity), 0) as qty_sold,
             COALESCE(SUM(oi.price * oi.quantity), 0) as revenue, 
             COALESCE(SUM((oi.price - COALESCE(oi.cost_price, p.cost_price)) * oi.quantity), 0) as profit,
             c.name as category_name
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN order_items oi ON p.id = oi.product_id
             LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'" . $whereDate . "
             GROUP BY p.id
             ORDER BY revenue DESC";
    $stmt = $db->prepare($sql4);
    $stmt->execute($params);
    $productStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Doanh thu theo danh mục
    $sql5 = "SELECT c.name, 
             COALESCE(SUM(oi.price * oi.quantity), 0) as revenue, 
             COALESCE(SUM(oi.quantity), 0) as sold
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             JOIN products p ON oi.product_id = p.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE o.status != 'cancelled'" . $whereDate . "
             GROUP BY c.id, c.name
             ORDER BY revenue DESC";
    $stmt = $db->prepare($sql5);
    $stmt->execute($params);
    $categoryStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chi tiết 1 SP (nếu chọn) — bao gồm giá nhập lúc bán
    $productDetail = null;
    $productOrders = [];
    if ($filterProduct) {
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$filterProduct]);
        $productDetail = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlDetail = "SELECT o.id as order_id, o.created_at, o.status, o.fullname, 
                      oi.quantity, oi.price, oi.cost_price as snapshot_cost,
                      (oi.price * oi.quantity) as subtotal,
                      ((oi.price - COALESCE(oi.cost_price, 0)) * oi.quantity) as profit
                      FROM order_items oi 
                      JOIN orders o ON oi.order_id = o.id 
                      WHERE oi.product_id = ? AND o.status != 'cancelled'" . $whereDate . "
                      ORDER BY o.created_at DESC";
        $stmt = $db->prepare($sqlDetail);
        $stmt->execute(array_merge([$filterProduct], $params));
        $productOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Báo cáo nhập-xuất theo khoảng thời gian
    $importExportReport = [];
    $importWhereDate = '';
    $importParams = [];
    if ($dateFrom) { $importWhereDate .= " AND ih.import_date >= ?"; $importParams[] = $dateFrom . ' 00:00:00'; }
    if ($dateTo) { $importWhereDate .= " AND ih.import_date <= ?"; $importParams[] = $dateTo . ' 23:59:59'; }

    $sqlIE = "SELECT p.id, p.name, p.image,
              COALESCE(SUM(ih.quantity), 0) as total_imported,
              COALESCE(SUM(ih.quantity * ih.import_price), 0) as total_import_cost
              FROM products p 
              LEFT JOIN import_history ih ON p.id = ih.product_id" . ($importWhereDate ? " AND 1=1" . $importWhereDate : "") . "
              GROUP BY p.id HAVING total_imported > 0
              ORDER BY total_imported DESC";
    $stmt = $db->prepare($sqlIE);
    $stmt->execute($importParams);
    $importExportReport = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Merge with sold data
    foreach ($importExportReport as &$row) {
        foreach ($productStats as $ps) {
            if ($ps['id'] == $row['id']) {
                $row['total_sold'] = $ps['qty_sold'];
                $row['total_revenue'] = $ps['revenue'];
                break;
            }
        }
        if (!isset($row['total_sold'])) { $row['total_sold'] = 0; $row['total_revenue'] = 0; }
    }
    unset($row);
    
} catch (Exception $e) {
    $revenue = ['total_revenue' => 0, 'total_orders' => 0];
    $totalSold = 0;
    $totalProfit = 0;
    $productStats = [];
    $categoryStats = [];
    $productDetail = null;
    $productOrders = [];
}
?>

<!-- Bộ lọc thời gian -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>admin/statistics" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Từ ngày</label>
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Đến ngày</label>
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Sản phẩm cụ thể</label>
                <select name="product_id" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($productStats as $ps): ?>
                        <option value="<?= $ps['id'] ?>" <?= $filterProduct == $ps['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ps['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fa-solid fa-filter me-1"></i> Lọc
                </button>
                <a href="<?= BASE_URL ?>admin/statistics" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<?php if ($dateFrom || $dateTo): ?>
<div class="alert alert-info mb-4">
    <i class="fa-solid fa-calendar-days me-2"></i>
    Đang xem thống kê 
    <?php if ($dateFrom): ?>từ <strong><?= date('d/m/Y', strtotime($dateFrom)) ?></strong><?php endif; ?>
    <?php if ($dateTo): ?> đến <strong><?= date('d/m/Y', strtotime($dateTo)) ?></strong><?php endif; ?>
</div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Tổng doanh thu</small>
                        <h4 class="mb-0 fw-bold"><?= number_format($revenue['total_revenue'], 0, ',', '.') ?>đ</h4>
                    </div>
                    <i class="fa-solid fa-coins fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Lợi nhuận</small>
                        <h4 class="mb-0 fw-bold"><?= number_format($totalProfit, 0, ',', '.') ?>đ</h4>
                    </div>
                    <i class="fa-solid fa-arrow-trend-up fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">SP đã bán</small>
                        <h4 class="mb-0 fw-bold"><?= number_format($totalSold) ?></h4>
                    </div>
                    <i class="fa-solid fa-box-open fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Tổng đơn hàng</small>
                        <h4 class="mb-0 fw-bold"><?= $revenue['total_orders'] ?></h4>
                    </div>
                    <i class="fa-solid fa-receipt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chi tiết 1 sản phẩm (nếu chọn) -->
<?php if ($productDetail): ?>
<div class="card shadow-sm border-0 rounded-3 mb-4 border-start border-primary border-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-magnifying-glass-chart me-2"></i>Chi tiết: <?= htmlspecialchars($productDetail['name']) ?></h5>
        <a href="<?= BASE_URL ?>admin/statistics<?= $dateFrom ? '?from='.$dateFrom.'&to='.$dateTo : '' ?>" class="btn btn-sm btn-outline-secondary">✕ Đóng</a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-2 text-center">
                <img src="<?= BASE_URL ?>images/<?= $productDetail['image'] ?>" class="img-fluid rounded" style="max-height: 100px;" onerror="this.style.display='none'">
            </div>
            <div class="col-md-10">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block">Giá bán</small>
                            <strong class="text-primary"><?= number_format($productDetail['price'], 0, ',', '.') ?>đ</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block">Giá nhập BQ</small>
                            <strong><?= number_format($productDetail['cost_price'], 0, ',', '.') ?>đ</strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block">Tồn kho</small>
                            <strong class="<?= $productDetail['stock'] > 0 ? 'text-success' : 'text-danger' ?>"><?= $productDetail['stock'] ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block">Giảm giá</small>
                            <strong class="text-danger"><?= $productDetail['discount'] ?>%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BIỂU ĐỒ CỘT -->
        <?php if (!empty($productOrders)): ?>
        <div class="card bg-light border-0 mb-3">
            <div class="card-body">
                <h6 class="fw-bold"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Biểu đồ doanh thu & lợi nhuận theo đơn hàng</h6>
                <canvas id="productChart" height="250"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <h6 class="fw-bold mt-3 mb-2"><i class="fa-solid fa-list me-1"></i> Đơn hàng chứa sản phẩm này (<?= count($productOrders) ?> đơn)</h6>
        <?php if (!empty($productOrders)): ?>
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>SL</th>
                    <th>Giá bán</th>
                    <th>Giá nhập lúc bán</th>
                    <th>Thành tiền</th>
                    <th>Lợi nhuận</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalQty = 0; $totalRev = 0; $totalProf = 0;
                foreach ($productOrders as $po): 
                    $totalQty += $po['quantity'];
                    $totalRev += $po['subtotal'];
                    $totalProf += $po['profit'];
                ?>
                <tr>
                    <td class="fw-bold">#<?= $po['order_id'] ?></td>
                    <td><?= htmlspecialchars($po['fullname']) ?></td>
                    <td><?= $po['quantity'] ?></td>
                    <td><?= number_format($po['price'], 0, ',', '.') ?>đ</td>
                    <td class="text-muted"><?= number_format($po['snapshot_cost'], 0, ',', '.') ?>đ</td>
                    <td class="fw-bold text-primary"><?= number_format($po['subtotal'], 0, ',', '.') ?>đ</td>
                    <td class="fw-bold <?= $po['profit'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format($po['profit'], 0, ',', '.') ?>đ</td>
                    <td>
                        <?php
                        $sMap = ['pending'=>'warning','processing'=>'info','completed'=>'success','cancelled'=>'danger'];
                        $sText = ['pending'=>'Chờ','processing'=>'Đang giao','completed'=>'Xong','cancelled'=>'Hủy'];
                        ?>
                        <span class="badge bg-<?= $sMap[$po['status']] ?? 'secondary' ?>"><?= $sText[$po['status']] ?? $po['status'] ?></span>
                    </td>
                    <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($po['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="2">Tổng cộng</td>
                    <td><?= $totalQty ?></td>
                    <td></td>
                    <td></td>
                    <td class="text-primary"><?= number_format($totalRev, 0, ',', '.') ?>đ</td>
                    <td class="<?= $totalProf >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format($totalProf, 0, ',', '.') ?>đ</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Chart.js Script -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
        const ctx = document.getElementById('productChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php foreach($productOrders as $po) echo "'#" . $po['order_id'] . " (" . date('d/m', strtotime($po['created_at'])) . ")',"; ?>],
                datasets: [{
                    label: 'Doanh thu (đ)',
                    data: [<?php foreach($productOrders as $po) echo $po['subtotal'] . ','; ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }, {
                    label: 'Lợi nhuận (đ)',
                    data: [<?php foreach($productOrders as $po) echo $po['profit'] . ','; ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }, {
                    label: 'Giá nhập × SL (đ)',
                    data: [<?php foreach($productOrders as $po) echo ($po['snapshot_cost'] * $po['quantity']) . ','; ?>],
                    backgroundColor: 'rgba(255, 159, 64, 0.5)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + new Intl.NumberFormat('vi-VN').format(context.raw) + 'đ';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    }
                }
            }
        });
        </script>
        <?php else: ?>
            <p class="text-muted">Chưa có đơn hàng nào cho sản phẩm này trong khoảng thời gian đã chọn.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Doanh thu theo danh mục -->
<?php if (!empty($categoryStats)): ?>
<div class="row g-3 mb-4">
    <?php foreach ($categoryStats as $cs): ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h6 class="text-muted"><?= htmlspecialchars($cs['name'] ?? 'N/A') ?></h6>
                <h4 class="fw-bold text-primary"><?= number_format($cs['revenue'], 0, ',', '.') ?>đ</h4>
                <small class="text-muted"><?= $cs['sold'] ?> sản phẩm đã bán</small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Bảng chi tiết từng SP -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-table me-2"></i>Chi tiết theo sản phẩm</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá bán</th>
                        <th>Giá nhập</th>
                        <th>Giảm giá</th>
                        <th>Đã bán</th>
                        <th>Doanh thu</th>
                        <th>Lợi nhuận</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productStats as $ps): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $ps['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($ps['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($ps['category_name'] ?? 'N/A') ?></span></td>
                        <td><?= number_format($ps['price'], 0, ',', '.') ?>đ</td>
                        <td class="text-muted"><?= number_format($ps['cost_price'], 0, ',', '.') ?>đ</td>
                        <td>
                            <?php if ($ps['discount'] > 0): ?>
                                <span class="badge bg-danger"><?= $ps['discount'] ?>%</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-info text-dark rounded-pill"><?= $ps['qty_sold'] ?></span></td>
                        <td class="fw-bold text-primary"><?= number_format($ps['revenue'], 0, ',', '.') ?>đ</td>
                        <td class="fw-bold <?= $ps['profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= number_format($ps['profit'], 0, ',', '.') ?>đ
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>admin/statistics?product_id=<?= $ps['id'] ?><?= $dateFrom ? '&from='.$dateFrom : '' ?><?= $dateTo ? '&to='.$dateTo : '' ?>" 
                               class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Báo cáo Nhập - Xuất -->
<?php if (!empty($importExportReport)): ?>
<div class="card shadow-sm border-0 rounded-3 mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-arrow-right-arrow-left me-2 text-info"></i>Báo cáo Nhập – Xuất
            <?php if ($dateFrom || $dateTo): ?>
                <small class="text-muted fw-normal">
                    (<?= $dateFrom ? date('d/m/Y', strtotime($dateFrom)) : '...' ?> → <?= $dateTo ? date('d/m/Y', strtotime($dateTo)) : '...' ?>)
                </small>
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">SL Nhập</th>
                        <th class="text-center">SL Bán</th>
                        <th class="text-end">Tổng tiền nhập</th>
                        <th class="text-end">Tổng doanh thu</th>
                        <th class="text-end">Chênh lệch</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sumImported = 0; $sumSold = 0; $sumImportCost = 0; $sumRevenue = 0;
                    foreach ($importExportReport as $ie): 
                        $sumImported += $ie['total_imported'];
                        $sumSold += $ie['total_sold'];
                        $sumImportCost += $ie['total_import_cost'];
                        $sumRevenue += $ie['total_revenue'];
                        $diff = $ie['total_revenue'] - $ie['total_import_cost'];
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($ie['name']) ?></td>
                        <td class="text-center"><span class="badge bg-success-subtle text-success">+<?= $ie['total_imported'] ?></span></td>
                        <td class="text-center"><span class="badge bg-danger-subtle text-danger">-<?= $ie['total_sold'] ?></span></td>
                        <td class="text-end text-info"><?= number_format($ie['total_import_cost'], 0, ',', '.') ?>đ</td>
                        <td class="text-end text-primary fw-bold"><?= number_format($ie['total_revenue'], 0, ',', '.') ?>đ</td>
                        <td class="text-end fw-bold <?= $diff >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $diff >= 0 ? '+' : '' ?><?= number_format($diff, 0, ',', '.') ?>đ
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-warning fw-bold">
                    <tr>
                        <td>TỔNG CỘNG</td>
                        <td class="text-center"><?= $sumImported ?></td>
                        <td class="text-center"><?= $sumSold ?></td>
                        <td class="text-end"><?= number_format($sumImportCost, 0, ',', '.') ?>đ</td>
                        <td class="text-end"><?= number_format($sumRevenue, 0, ',', '.') ?>đ</td>
                        <?php $totalDiff = $sumRevenue - $sumImportCost; ?>
                        <td class="text-end <?= $totalDiff >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $totalDiff >= 0 ? '+' : '' ?><?= number_format($totalDiff, 0, ',', '.') ?>đ
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
