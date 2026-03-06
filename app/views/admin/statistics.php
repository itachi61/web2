<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-chart-line me-2"></i>Thống kê & Báo cáo</h2>
</div>

<?php
// Query statistics
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tổng doanh thu
    $stmt = $db->query("SELECT COALESCE(SUM(total_money), 0) as total_revenue, COUNT(*) as total_orders FROM orders WHERE status != 'cancelled'");
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Tổng SP đã bán
    $stmt = $db->query("SELECT COALESCE(SUM(sold_count), 0) as total_sold FROM products");
    $totalSold = $stmt->fetch(PDO::FETCH_ASSOC)['total_sold'];
    
    // Tổng lợi nhuận ước tính
    $stmt = $db->query("SELECT COALESCE(SUM((price - cost_price) * sold_count), 0) as total_profit FROM products WHERE cost_price > 0");
    $totalProfit = $stmt->fetch(PDO::FETCH_ASSOC)['total_profit'];
    
    // Doanh thu theo từng SP
    $stmt = $db->query("SELECT p.id, p.name, p.price, p.cost_price, p.sold_count, p.stock, p.discount, 
                         (p.price * p.sold_count) as revenue, 
                         ((p.price - p.cost_price) * p.sold_count) as profit,
                         c.name as category_name
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id
                         ORDER BY revenue DESC");
    $productStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Doanh thu theo danh mục
    $stmt = $db->query("SELECT c.name, SUM(p.price * p.sold_count) as revenue, SUM(p.sold_count) as sold
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id
                         GROUP BY c.id, c.name
                         ORDER BY revenue DESC");
    $categoryStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $revenue = ['total_revenue' => 0, 'total_orders' => 0];
    $totalSold = 0;
    $totalProfit = 0;
    $productStats = [];
    $categoryStats = [];
}
?>

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
                        <small class="opacity-75">Lợi nhuận ước tính</small>
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

<!-- Doanh thu theo danh mục -->
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
                        <td><span class="badge bg-info text-dark rounded-pill"><?= $ps['sold_count'] ?></span></td>
                        <td class="fw-bold text-primary"><?= number_format($ps['revenue'], 0, ',', '.') ?>đ</td>
                        <td class="fw-bold <?= $ps['profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= number_format($ps['profit'], 0, ',', '.') ?>đ
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
