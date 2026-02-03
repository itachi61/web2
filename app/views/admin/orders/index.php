<!-- Orders Management -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fa-solid fa-cart-shopping text-primary me-2"></i>Quản lý đơn hàng
    </h4>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/orders">
            Tất cả <span class="badge bg-secondary ms-1"><?= $data['total_count'] ?? 0 ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === 'pending' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/orders?status=pending">
            <i class="fa-solid fa-clock text-warning me-1"></i>Chờ xử lý 
            <span class="badge bg-warning text-dark ms-1"><?= $data['pending_count'] ?? 0 ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === 'processing' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/orders?status=processing">
            <i class="fa-solid fa-spinner text-info me-1"></i>Đang xử lý
            <span class="badge bg-info ms-1"><?= $data['processing_count'] ?? 0 ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === 'completed' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/orders?status=completed">
            <i class="fa-solid fa-check text-success me-1"></i>Hoàn thành
            <span class="badge bg-success ms-1"><?= $data['completed_count'] ?? 0 ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === 'cancelled' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/orders?status=cancelled">
            <i class="fa-solid fa-times text-danger me-1"></i>Đã hủy
            <span class="badge bg-danger ms-1"><?= $data['cancelled_count'] ?? 0 ?></span>
        </a>
    </li>
</ul>

<!-- Date Range Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?= BASE_URL ?>admin/orders" class="row g-2 align-items-end">
            <input type="hidden" name="status" value="<?= htmlspecialchars($data['filter'] ?? '') ?>">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1"><?= __('date_from') ?></label>
                <input type="date" name="date_from" class="form-control form-control-sm" 
                       value="<?= htmlspecialchars($data['date_from'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1"><?= __('date_to') ?></label>
                <input type="date" name="date_to" class="form-control form-control-sm" 
                       value="<?= htmlspecialchars($data['date_to'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-filter me-1"></i><?= __('filter') ?>
                </button>
                <a href="<?= BASE_URL ?>admin/orders" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 admin-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Địa chỉ</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['orders'])): ?>
                    <?php foreach ($data['orders'] as $order): ?>
                    <tr>
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($order['fullname'] ?? 'N/A') ?></div>
                            <small class="text-muted"><?= htmlspecialchars($order['email'] ?? '') ?></small>
                        </td>
                        <td>
                            <small><?= htmlspecialchars($order['address'] ?? '') ?>, <?= htmlspecialchars($order['ward'] ?? '') ?>, <?= htmlspecialchars($order['district'] ?? '') ?></small>
                        </td>
                        <td class="text-danger fw-bold"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</td>
                        <td>
                            <?php
                            $paymentIcon = match($order['payment_method'] ?? 'cod') {
                                'cod' => 'fa-money-bill',
                                'bank' => 'fa-building-columns',
                                'online' => 'fa-credit-card',
                                default => 'fa-money-bill'
                            };
                            $paymentText = match($order['payment_method'] ?? 'cod') {
                                'cod' => 'COD',
                                'bank' => 'Chuyển khoản',
                                'online' => 'Online',
                                default => 'COD'
                            };
                            ?>
                            <i class="fa-solid <?= $paymentIcon ?> me-1"></i><?= $paymentText ?>
                        </td>
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
                            <span class="badge bg-<?= $statusClass ?> px-3 py-2"><?= $statusText ?></span>
                        </td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>admin/orderDetail/<?= $order['id'] ?>" class="btn btn-outline-primary" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($order['status'] === 'pending'): ?>
                                <a href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/processing" 
                                   class="btn btn-outline-info" title="Xác nhận"
                                   data-confirm="Xác nhận đơn hàng #<?= $order['id'] ?>?"
                                   data-confirm-title="Xác nhận đơn hàng"
                                   data-confirm-type="info"
                                   data-confirm-icon="fa-circle-check"
                                   data-confirm-btn="Xác nhận">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'processing'): ?>
                                <a href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/completed" 
                                   class="btn btn-outline-success" title="Hoàn thành"
                                   data-confirm="Đánh dấu hoàn thành đơn hàng #<?= $order['id'] ?>?"
                                   data-confirm-title="Hoàn thành đơn hàng"
                                   data-confirm-type="success"
                                   data-confirm-icon="fa-circle-check"
                                   data-confirm-btn="Hoàn thành">
                                    <i class="fa-solid fa-check-double"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                                <a href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/cancelled" 
                                   class="btn btn-outline-danger" title="Hủy đơn"
                                   data-confirm="Bạn có chắc muốn hủy đơn hàng #<?= $order['id'] ?>?"
                                   data-confirm-title="Hủy đơn hàng"
                                   data-confirm-type="danger"
                                   data-confirm-icon="fa-triangle-exclamation"
                                   data-confirm-btn="Hủy đơn"
                                   data-confirm-btnicon="fa-times">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                            Không có đơn hàng nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
