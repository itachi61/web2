<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-cart-flatbed me-2"></i>Quản lý Đơn hàng</h2>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="20%">Khách hàng</th>
                        <th width="15%">Tổng tiền</th>
                        <th width="15%">Trạng thái</th>
                        <th width="15%">Ngày đặt</th>
                        <th width="20%">Địa chỉ</th>
                        <th class="text-end pe-4" width="10%">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['orders'])): ?>
                        <?php foreach ($data['orders'] as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $order['id'] ?></td>
                                <td>
                                    <div>
                                        <span class="fw-bold"><?= htmlspecialchars($order['fullname'] ?? 'N/A') ?></span>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($order['email'] ?? '') ?></small>
                                    </div>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>đ
                                </td>
                                <td>
                                    <?php 
                                    $status = $order['status'] ?? 'pending';
                                    $statusMap = [
                                        'pending' => ['bg' => 'warning', 'text' => 'Chờ xử lý', 'icon' => 'clock'],
                                        'processing' => ['bg' => 'info', 'text' => 'Đang xử lý', 'icon' => 'spinner'],
                                        'shipped' => ['bg' => 'primary', 'text' => 'Đang giao', 'icon' => 'truck'],
                                        'completed' => ['bg' => 'success', 'text' => 'Hoàn thành', 'icon' => 'check-circle'],
                                        'cancelled' => ['bg' => 'danger', 'text' => 'Đã hủy', 'icon' => 'xmark-circle'],
                                    ];
                                    $s = $statusMap[$status] ?? $statusMap['pending'];
                                    ?>
                                    <span class="badge bg-<?= $s['bg'] ?>-subtle text-<?= $s['bg'] ?> border border-<?= $s['bg'] ?> px-3 rounded-pill">
                                        <i class="fa-solid fa-<?= $s['icon'] ?> me-1"></i><?= $s['text'] ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?= isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : 'N/A' ?>
                                </td>
                                <td class="text-muted small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($order['shipping_address'] ?? $order['address'] ?? 'N/A') ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa-solid fa-cart-flatbed fa-3x text-muted mb-2"></i>
                                <p class="text-muted mt-2">Chưa có đơn hàng nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
