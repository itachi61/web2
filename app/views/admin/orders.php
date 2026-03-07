<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-cart-flatbed me-2"></i>Quản lý Đơn hàng</h2>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive" style="overflow:visible;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="15%">Khách hàng</th>
                        <th width="10%">SĐT</th>
                        <th width="12%">Tổng tiền</th>
                        <th width="15%">Trạng thái</th>
                        <th width="13%">Ngày đặt</th>
                        <th width="20%">Địa chỉ</th>
                        <th class="text-end pe-4" width="10%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['orders'])): ?>
                        <?php foreach ($data['orders'] as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $order['id'] ?></td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($order['fullname'] ?? 'N/A') ?></span>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($order['phone'] ?? '') ?></td>
                                <td class="fw-bold text-primary">
                                    <?= number_format($order['total_money'] ?? 0, 0, ',', '.') ?>đ
                                </td>
                                <td>
                                    <?php 
                                    $status = $order['status'] ?? 'pending';
                                    $statusMap = [
                                        'pending' => ['bg' => 'warning', 'text' => 'Chờ xử lý', 'icon' => 'clock'],
                                        'processing' => ['bg' => 'info', 'text' => 'Đang giao', 'icon' => 'truck'],
                                        'completed' => ['bg' => 'success', 'text' => 'Hoàn thành', 'icon' => 'check-circle'],
                                        'cancelled' => ['bg' => 'danger', 'text' => 'Đã hủy', 'icon' => 'xmark-circle'],
                                    ];
                                    $s = $statusMap[$status] ?? $statusMap['pending'];
                                    ?>
                                    <div class="dropdown">
                                        <span class="badge bg-<?= $s['bg'] ?>-subtle text-<?= $s['bg'] ?> border border-<?= $s['bg'] ?> px-3 rounded-pill dropdown-toggle" role="button" data-bs-toggle="dropdown" style="cursor:pointer;">
                                            <i class="fa-solid fa-<?= $s['icon'] ?> me-1"></i><?= $s['text'] ?>
                                        </span>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/pending"><i class="fa-solid fa-clock text-warning me-2"></i>Chờ xử lý</a></li>
                                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/processing"><i class="fa-solid fa-truck text-info me-2"></i>Đang giao</a></li>
                                            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/completed"><i class="fa-solid fa-check-circle text-success me-2"></i>Hoàn thành</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/cancelled"><i class="fa-solid fa-xmark-circle me-2"></i>Hủy đơn</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?= isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : 'N/A' ?>
                                </td>
                                <td class="text-muted small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($order['address'] ?? 'N/A') ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id'] ?>" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
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

<!-- Order Detail Modals -->
<?php if (!empty($data['orders'])): ?>
    <?php foreach ($data['orders'] as $order): ?>
    <div class="modal fade" id="orderModal<?= $order['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-receipt me-2"></i>Đơn hàng #<?= $order['id'] ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2"><i class="fa-solid fa-user me-1"></i> Thông tin người nhận</h6>
                            <p class="mb-1"><strong>Tên:</strong> <?= htmlspecialchars($order['fullname'] ?? '') ?></p>
                            <p class="mb-1"><strong>SĐT:</strong> <?= htmlspecialchars($order['phone'] ?? '') ?></p>
                            <p class="mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address'] ?? '') ?></p>
                            <p class="mb-0"><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note'] ?? 'Không có') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2"><i class="fa-solid fa-info-circle me-1"></i> Thông tin đơn hàng</h6>
                            <p class="mb-1"><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                            <p class="mb-1"><strong>Tổng tiền:</strong> <span class="text-danger fw-bold"><?= number_format($order['total_money'] ?? 0, 0, ',', '.') ?>đ</span></p>
                            <p class="mb-0"><strong>Trạng thái:</strong> 
                                <span class="badge bg-<?= $statusMap[$order['status']]['bg'] ?? 'secondary' ?>"><?= $statusMap[$order['status']]['text'] ?? 'N/A' ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-box me-1"></i> Sản phẩm trong đơn</h6>
                    <?php if (!empty($order['items'])): ?>
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>SL</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($item['product_name'] ?? 'SP #' . $item['product_id']) ?></td>
                                <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td><?= $item['quantity'] ?></td>
                                <td class="fw-bold text-primary"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p class="text-muted">Không có dữ liệu chi tiết sản phẩm.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
