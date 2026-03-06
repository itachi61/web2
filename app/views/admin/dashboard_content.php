<!-- Dashboard Content -->
<div class="row g-4 mb-4">
    <!-- Stats Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1 small">Đơn chờ xử lý</p>
                    <h3 class="mb-0 fw-bold"><?= $data['pending_orders'] ?? 0 ?></h3>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <a href="<?= BASE_URL ?>admin/orders?status=pending" class="small text-primary text-decoration-none mt-2 d-inline-block">
                Xem chi tiết <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1 small">Đang xử lý</p>
                    <h3 class="mb-0 fw-bold"><?= $data['processing_orders'] ?? 0 ?></h3>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fa-solid fa-spinner"></i>
                </div>
            </div>
            <a href="<?= BASE_URL ?>admin/orders?status=processing" class="small text-primary text-decoration-none mt-2 d-inline-block">
                Xem chi tiết <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1 small">Hoàn thành</p>
                    <h3 class="mb-0 fw-bold"><?= $data['completed_orders'] ?? 0 ?></h3>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
            </div>
            <a href="<?= BASE_URL ?>admin/orders?status=completed" class="small text-primary text-decoration-none mt-2 d-inline-block">
                Xem chi tiết <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1 small">Doanh thu tháng</p>
                    <h3 class="mb-0 fw-bold"><?= number_format($data['monthly_revenue'] ?? 0, 0, ',', '.') ?>đ</h3>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
            </div>
            <span class="small text-success">
                <i class="fa-solid fa-arrow-up me-1"></i> So với tháng trước
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-shopping-cart text-primary me-2"></i>Đơn hàng gần đây
                </h5>
                <a href="<?= BASE_URL ?>admin/orders" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['recent_orders'])): ?>
                                <?php foreach ($data['recent_orders'] as $order): ?>
                                <tr>
                                    <td><strong>#<?= $order['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($order['fullname'] ?? 'N/A') ?></td>
                                    <td class="text-danger fw-bold"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</td>
                                    <td>
                                        <?php
                                        $statusClass = match($order['status'] ?? 'pending') {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                        $statusText = match($order['status'] ?? 'pending') {
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy',
                                            default => 'N/A'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                        Chưa có đơn hàng nào
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Sắp hết hàng
                </h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($data['low_stock'])): ?>
                        <?php foreach ($data['low_stock'] as $product): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block"><?= htmlspecialchars($product['name']) ?></strong>
                                <small class="text-muted">Còn <?= $product['stock'] ?> sản phẩm</small>
                            </div>
                            <span class="badge bg-danger rounded-pill"><?= $product['stock'] ?></span>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted py-4">
                            <i class="fa-solid fa-check-circle text-success fa-2x mb-2 d-block"></i>
                            Kho hàng ổn định
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= BASE_URL ?>admin/inventory" class="text-primary text-decoration-none small">
                    Quản lý tồn kho <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-bolt text-primary me-2"></i>Thao tác nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>admin/addProduct" class="btn btn-outline-primary">
                        <i class="fa-solid fa-plus me-2"></i>Thêm sản phẩm
                    </a>
                    <a href="<?= BASE_URL ?>admin/imports/create" class="btn btn-outline-success">
                        <i class="fa-solid fa-truck-ramp-box me-2"></i>Tạo phiếu nhập
                    </a>
                    <a href="<?= BASE_URL ?>admin/orders" class="btn btn-outline-warning">
                        <i class="fa-solid fa-list-check me-2"></i>Xử lý đơn hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
