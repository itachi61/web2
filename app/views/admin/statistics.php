<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-chart-line me-2"></i>Thống kê & Báo cáo</h2>
</div>

<?php
// Lọc theo thời gian
$dateFrom = $_GET['from'] ?? '';
$dateTo = $_GET['to'] ?? '';
$filterProducts = $_GET['product_id'] ?? [];
if (!is_array($filterProducts)) $filterProducts = $filterProducts ? [$filterProducts] : [];
$filterProducts = array_filter($filterProducts);

$whereDate = '';
$params = [];
if ($dateFrom) { $whereDate .= " AND o.created_at >= ?"; $params[] = $dateFrom . ' 00:00:00'; }
if ($dateTo) { $whereDate .= " AND o.created_at <= ?"; $params[] = $dateTo . ' 23:59:59'; }

// Filter nhiều sản phẩm
$whereProduct = '';
$productParams = [];
if (!empty($filterProducts)) {
    $ph = implode(',', array_fill(0, count($filterProducts), '?'));
    $whereProduct = " AND oi.product_id IN ($ph)";
    $productParams = $filterProducts;
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tổng doanh thu (lọc theo SP nếu có)
    if (!empty($filterProducts)) {
        $sql = "SELECT COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue, COUNT(DISTINCT o.id) as total_orders 
                FROM order_items oi JOIN orders o ON oi.order_id = o.id 
                WHERE o.status != 'cancelled'" . $whereDate . $whereProduct;
        $stmt = $db->prepare($sql);
        $stmt->execute(array_merge($params, $productParams));
    } else {
        $sql = "SELECT COALESCE(SUM(o.total_money), 0) as total_revenue, COUNT(*) as total_orders 
                FROM orders o WHERE o.status != 'cancelled'" . $whereDate;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Tổng SP đã bán
    $sql2 = "SELECT COALESCE(SUM(oi.quantity), 0) as total_sold 
             FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE o.status != 'cancelled'" . $whereDate . $whereProduct;
    $stmt = $db->prepare($sql2);
    $stmt->execute(array_merge($params, $productParams));
    $totalSold = $stmt->fetch(PDO::FETCH_ASSOC)['total_sold'];
    
    // Tổng lợi nhuận
    $sql3 = "SELECT COALESCE(SUM((oi.price - COALESCE(oi.cost_price, p.cost_price)) * oi.quantity), 0) as total_profit 
             FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             JOIN products p ON oi.product_id = p.id 
             WHERE o.status != 'cancelled'" . $whereDate . $whereProduct;
    $stmt = $db->prepare($sql3);
    $stmt->execute(array_merge($params, $productParams));
    $totalProfit = $stmt->fetch(PDO::FETCH_ASSOC)['total_profit'];
    
    // Doanh thu theo từng SP (lọc nếu chọn SP)
    $whereProductDirect = '';
    $sql4Params = [];
    if (!empty($filterProducts)) {
        $ph = implode(',', array_fill(0, count($filterProducts), '?'));
        $whereProductDirect = " WHERE p.id IN ($ph)";
        $sql4Params = $filterProducts;
    }
    $sql4 = "SELECT p.id, p.name, p.price, p.cost_price, p.stock, p.discount, p.image,
             COALESCE(SUM(oi.quantity), 0) as qty_sold,
             COALESCE(SUM(oi.price * oi.quantity), 0) as revenue, 
             COALESCE(SUM((oi.price - COALESCE(oi.cost_price, p.cost_price)) * oi.quantity), 0) as profit,
             c.name as category_name
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN order_items oi ON p.id = oi.product_id
             LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'" . $whereDate . "
             " . $whereProductDirect . "
             GROUP BY p.id
             ORDER BY revenue DESC";
    $stmt = $db->prepare($sql4);
    $stmt->execute(array_merge($params, $sql4Params));
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

    // Chi tiết SP (được chọn) — bao gồm giá nhập lúc bán
    $productDetail = null;
    $productOrders = [];
    if (count($filterProducts) === 1) {
        $fpId = $filterProducts[0];
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$fpId]);
        $productDetail = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlDetail = "SELECT o.id as order_id, o.created_at, o.status, o.fullname, 
                      oi.quantity, oi.price, oi.cost_price as snapshot_cost,
                      (oi.price * oi.quantity) as subtotal,
                      ((oi.price - COALESCE(oi.cost_price, 0)) * oi.quantity) as profit
                      FROM order_items oi 
                      JOIN orders o ON oi.order_id = o.id 
                      WHERE oi.product_id = ? AND o.status != 'cancelled'
                      ORDER BY o.created_at DESC";
        $stmt = $db->prepare($sqlDetail);
        $stmt->execute([$fpId]);
        $productOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lịch sử nhập hàng (toàn bộ)
        $stmtD = $db->prepare("SELECT iri.quantity, iri.import_price, ir.created_at as import_date, ir.receipt_code, ir.id as receipt_id 
                                FROM import_receipt_items iri 
                                JOIN import_receipts ir ON iri.receipt_id = ir.id 
                                WHERE iri.product_id = ? AND ir.status = 'completed' 
                                ORDER BY ir.created_at DESC");
        $stmtD->execute([$fpId]);
        $productImports = $stmtD->fetchAll(PDO::FETCH_ASSOC);

        // Tổng nhập
        $totalImportQty = array_sum(array_column($productImports, 'quantity'));
        $totalImportCost = 0;
        foreach ($productImports as $pi) { $totalImportCost += $pi['quantity'] * $pi['import_price']; }
    }


    // Load danh sách tất cả SP cho dropdown (không bị filter)
    $allProducts = $db->query("SELECT id, name FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (\Throwable $e) {
    $revenue = ['total_revenue' => 0, 'total_orders' => 0];
    $totalSold = 0;
    $totalProfit = 0;
    $productStats = [];
    $categoryStats = [];
    $productDetail = null;
    $productOrders = [];
    $productImports = [];
    $totalImportQty = 0;
    $totalImportCost = 0;
    $allProducts = [];
}
?>

<!-- Bộ lọc thời gian -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>admin/statistics" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Từ ngày</label>
                <input type="date" name="from" id="statDateFrom" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Đến ngày</label>
                <input type="date" name="to" id="statDateTo" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Sản phẩm</label>
                <div class="dropdown" id="productDropdown">
                    <button type="button" class="btn btn-outline-secondary w-100 d-flex justify-content-between align-items-center" 
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <span id="productDropdownLabel">
                            <?php if (!empty($filterProducts)): ?>
                                Đã chọn <?= count($filterProducts) ?> SP
                            <?php else: ?>
                                -- Tất cả --
                            <?php endif; ?>
                        </span>
                        <i class="fa-solid fa-chevron-down ms-2 small"></i>
                    </button>
                    <div class="dropdown-menu w-100 p-2 shadow" style="max-height: 280px; overflow-y: auto;">
                        <input type="text" class="form-control form-control-sm mb-2" id="searchStatProduct" placeholder="🔍 Tìm SP...">
                        <div id="productCheckboxList">
                            <?php foreach ($allProducts as $ap): ?>
                                <label class="dropdown-item d-flex align-items-center gap-2 py-1 rounded product-check-label" style="cursor:pointer; font-size: 0.9rem;">
                                    <input type="checkbox" name="product_id[]" value="<?= $ap['id'] ?>" 
                                           <?= in_array($ap['id'], $filterProducts) ? 'checked' : '' ?>
                                           class="form-check-input m-0" style="min-width:16px;">
                                    <?= htmlspecialchars($ap['name']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
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
        <?php else: ?>
            <p class="text-muted">Chưa có đơn hàng nào cho sản phẩm này trong khoảng thời gian đã chọn.</p>
        <?php endif; ?>

        <!-- Lịch sử nhập hàng -->
        <?php if (!empty($productImports)): ?>
        <div class="mt-4 pt-3 border-top">
            <h6 class="fw-bold mb-3">
                <span class="badge bg-success me-2"><i class="fa-solid fa-arrow-down"></i></span>
                Lịch sử nhập hàng <span class="text-muted fw-normal">(<?= count($productImports) ?> lần · <?= $totalImportQty ?> SP · <?= number_format($totalImportCost, 0, ',', '.') ?>đ)</span>
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center" style="width:40px;">#</th>
                            <th>Ngày nhập</th>
                            <th>Phiếu nhập</th>
                            <th class="text-center">SL</th>
                            <th class="text-end">Giá nhập</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productImports as $i => $imp): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td><i class="fa-regular fa-clock me-1 text-muted small"></i><?= date('d/m/Y H:i', strtotime($imp['import_date'])) ?></td>
                            <td>
                                <?php if (!empty($imp['receipt_code'])): ?>
                                    <a href="<?= BASE_URL ?>admin/viewReceipt/<?= $imp['receipt_id'] ?>" class="text-decoration-none fw-semibold">
                                        <i class="fa-solid fa-file-invoice me-1"></i><?= $imp['receipt_code'] ?>
                                    </a>
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
        </div>
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
                            <?php if ($productDetail && $productDetail['id'] == $ps['id']): ?>
                                <span class="btn btn-sm btn-primary disabled" title="Đang xem">
                                    <i class="fa-solid fa-eye me-1"></i>Đang xem
                                </span>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>admin/statistics?product_id=<?= $ps['id'] ?><?= $dateFrom ? '&from='.$dateFrom : '' ?><?= $dateTo ? '&to='.$dateTo : '' ?>" 
                                   class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    // Search filter
    const searchInput = document.getElementById('searchStatProduct');
    const labels = document.querySelectorAll('.product-check-label');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const kw = this.value.toLowerCase().trim();
            labels.forEach(label => {
                label.style.display = label.textContent.toLowerCase().includes(kw) ? '' : 'none';
            });
        });
    }

    // Update label on checkbox change
    const checkboxes = document.querySelectorAll('#productCheckboxList input[type="checkbox"]');
    const labelEl = document.getElementById('productDropdownLabel');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checked = document.querySelectorAll('#productCheckboxList input[type="checkbox"]:checked');
            if (checked.length === 0) {
                labelEl.textContent = '-- Tất cả --';
            } else if (checked.length === 1) {
                labelEl.textContent = checked[0].parentElement.textContent.trim();
            } else {
                labelEl.textContent = 'Đã chọn ' + checked.length + ' SP';
            }
        });
    });
})();

// Date range validation
(function() {
    const fromInput = document.getElementById('statDateFrom');
    const toInput = document.getElementById('statDateTo');
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
        // Set initial min/max
        if (fromInput.value) toInput.min = fromInput.value;
        if (toInput.value) fromInput.max = toInput.value;
    }
})();
</script>
