<!-- Order Detail View -->
<div class="mb-4">
    <a href="<?= BASE_URL ?>admin/orders" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<div class="row g-4">
    <!-- Order Info -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-receipt text-primary me-2"></i>Đơn hàng #<?= $data['order']['id'] ?>
                </h5>
                <?php
                $statusClass = match($data['order']['status'] ?? 'pending') {
                    'pending' => 'warning',
                    'processing' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    default => 'secondary'
                };
                $statusText = match($data['order']['status'] ?? 'pending') {
                    'pending' => 'Chờ xử lý',
                    'processing' => 'Đang xử lý',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                    default => 'N/A'
                };
                ?>
                <span class="badge bg-<?= $statusClass ?> px-3 py-2"><?= $statusText ?></span>
            </div>
            <div class="card-body">
                <!-- Order Items -->
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-box me-2"></i>Sản phẩm</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="bg-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['items'])): ?>
                                <?php foreach ($data['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= BASE_URL ?>public/images/<?= $item['image'] ?? 'placeholder.jpg' ?>" 
                                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover;"
                                                 onerror="this.src='https://via.placeholder.com/50'">
                                            <span><?= htmlspecialchars($item['name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                    <td class="text-end fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                                <td class="text-end text-danger fw-bold fs-5">
                                    <?= number_format($data['order']['total_money'], 0, ',', '.') ?>đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <?php if (!empty($data['order']['note'])): ?>
                <div class="alert alert-info mt-3">
                    <strong><i class="fa-solid fa-note-sticky me-2"></i>Ghi chú:</strong>
                    <?= htmlspecialchars($data['order']['note']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Actions -->
        <?php if ($data['order']['status'] !== 'cancelled' && $data['order']['status'] !== 'completed'): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-gears text-primary me-2"></i>Cập nhật trạng thái
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <?php if ($data['order']['status'] === 'pending'): ?>
                    <a href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $data['order']['id'] ?>/processing" 
                       class="btn btn-info"
                       data-confirm="Xác nhận đơn hàng #<?= $data['order']['id'] ?>?"
                       data-confirm-title="Xác nhận đơn hàng"
                       data-confirm-type="info"
                       data-confirm-icon="fa-circle-check"
                       data-confirm-btn="Xác nhận">
                        <i class="fa-solid fa-check me-2"></i>Xác nhận đơn
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($data['order']['status'] === 'processing'): ?>
                    <a href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $data['order']['id'] ?>/completed" 
                       class="btn btn-success"
                       data-confirm="Đánh dấu hoàn thành đơn hàng #<?= $data['order']['id'] ?>?"
                       data-confirm-title="Hoàn thành đơn hàng"
                       data-confirm-type="success"
                       data-confirm-icon="fa-circle-check"
                       data-confirm-btn="Hoàn thành">
                        <i class="fa-solid fa-check-double me-2"></i>Hoàn thành
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $data['order']['id'] ?>/cancelled" 
                       class="btn btn-danger"
                       data-confirm="Bạn có chắc muốn hủy đơn hàng #<?= $data['order']['id'] ?>?"
                       data-confirm-title="Hủy đơn hàng"
                       data-confirm-type="danger"
                       data-confirm-icon="fa-triangle-exclamation"
                       data-confirm-btn="Hủy đơn"
                       data-confirm-btnicon="fa-times">
                        <i class="fa-solid fa-times me-2"></i>Hủy đơn
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Customer Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-user text-primary me-2"></i>Thông tin khách hàng
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Họ tên:</strong> <?= htmlspecialchars($data['order']['fullname'] ?? 'N/A') ?></p>
                <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($data['order']['email'] ?? 'N/A') ?></p>
                <p class="mb-0"><strong>SĐT:</strong> <?= htmlspecialchars($data['order']['phone'] ?? 'N/A') ?></p>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-location-dot text-primary me-2"></i>Địa chỉ giao hàng
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    <?= htmlspecialchars($data['order']['address'] ?? '') ?><br>
                    <?= htmlspecialchars($data['order']['ward'] ?? '') ?>, <?= htmlspecialchars($data['order']['district'] ?? '') ?>
                </p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-credit-card text-primary me-2"></i>Thanh toán
                </h5>
            </div>
            <div class="card-body">
                <?php
                $paymentText = match($data['order']['payment_method'] ?? 'cod') {
                    'cod' => 'Thanh toán khi nhận hàng (COD)',
                    'bank' => 'Chuyển khoản ngân hàng',
                    'online' => 'Thanh toán online',
                    default => 'COD'
                };
                ?>
                <p class="mb-2"><strong>Phương thức:</strong> <?= $paymentText ?></p>
                <p class="mb-0"><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($data['order']['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>
