<?php $f = $data['filters'] ?? []; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-cart-flatbed me-2"></i>Quản lý Đơn hàng</h2>
</div>

<!-- Bộ lọc -->
<div class="card shadow-sm border-0 rounded-3 mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?= BASE_URL ?>admin/orders" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Từ ngày</label>
                <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($f['from'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Đến ngày</label>
                <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($f['to'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Trạng thái</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tất cả</option>
                    <option value="pending" <?= ($f['status'] ?? '') == 'pending' ? 'selected' : '' ?>>🕐 Chờ xử lý</option>
                    <option value="processing" <?= ($f['status'] ?? '') == 'processing' ? 'selected' : '' ?>>🚚 Đang giao</option>
                    <option value="completed" <?= ($f['status'] ?? '') == 'completed' ? 'selected' : '' ?>>✅ Hoàn thành</option>
                    <option value="cancelled" <?= ($f['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>❌ Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Sắp xếp</label>
                <select name="sort" class="form-select form-select-sm">
                    <option value="newest" <?= ($f['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="oldest" <?= ($f['sort'] ?? '') == 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                    <option value="address" <?= ($f['sort'] ?? '') == 'address' ? 'selected' : '' ?>>Theo địa chỉ (phường)</option>
                    <option value="total_desc" <?= ($f['sort'] ?? '') == 'total_desc' ? 'selected' : '' ?>>Tổng tiền giảm</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
                <a href="<?= BASE_URL ?>admin/orders" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
            <div class="col-md-2 text-end">
                <span class="badge bg-primary-subtle text-primary fs-6"><?= count($data['orders'] ?? []) ?> đơn</span>
            </div>
        </form>
    </div>
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
                                    $colorMap = ['pending' => '#fff3cd', 'processing' => '#cff4fc', 'completed' => '#d1e7dd', 'cancelled' => '#f8d7da'];
                                    ?>
                                    <?php if ($status === 'completed'): ?>
                                        <span class="badge fw-bold" style="background-color:#d1e7dd; color:#0f5132; border-radius:20px; font-size:0.8rem; padding:6px 16px;" title="Đơn hàng đã hoàn thành, không thể thay đổi">
                                            ✅ Hoàn thành <i class="fa-solid fa-lock ms-1" style="font-size:0.7rem;"></i>
                                        </span>
                                    <?php else: ?>
                                        <select class="form-select form-select-sm fw-bold" style="width:140px; background-color:<?= $colorMap[$status] ?? '#fff' ?>; border-radius:20px; font-size:0.8rem;" onchange="if(this.value) window.location='<?= BASE_URL ?>admin/updateOrderStatus/<?= $order['id'] ?>/'+this.value">
                                            <option value="pending" <?= $status=='pending'?'selected':'' ?>>🕐 Chờ xử lý</option>
                                            <option value="processing" <?= $status=='processing'?'selected':'' ?>>🚚 Đang giao</option>
                                            <option value="completed" <?= $status=='completed'?'selected':'' ?>>✅ Hoàn thành</option>
                                            <option value="cancelled" <?= $status=='cancelled'?'selected':'' ?>>❌ Đã hủy</option>
                                        </select>
                                    <?php endif; ?>
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
                                <?php 
                                $mStatus = $order['status'] ?? 'pending';
                                $mMap = ['pending'=>['warning','Chờ xử lý'],'processing'=>['info','Đang giao'],'completed'=>['success','Hoàn thành'],'cancelled'=>['danger','Đã hủy']];
                                $mS = $mMap[$mStatus] ?? $mMap['pending'];
                                ?>
                                <span class="badge bg-<?= $mS[0] ?>"><?= $mS[1] ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-box me-1"></i> Sản phẩm trong đơn</h6>
                    <?php if (!empty($order['items'])): ?>
                    <?php $totalRevenue = 0; $totalCost = 0; ?>
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá bán</th>
                                <th>Giá vốn</th>
                                <th>SL</th>
                                <th>Thành tiền</th>
                                <th>Lãi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): 
                                $itemRevenue = $item['price'] * $item['quantity'];
                                $itemCost = ($item['cost_price'] ?? 0) * $item['quantity'];
                                $itemProfit = $itemRevenue - $itemCost;
                                $totalRevenue += $itemRevenue;
                                $totalCost += $itemCost;
                            ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($item['product_name'] ?? 'SP #' . $item['product_id']) ?></td>
                                <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td class="text-muted small"><?= number_format($item['cost_price'] ?? 0, 0, ',', '.') ?>đ</td>
                                <td><?= $item['quantity'] ?></td>
                                <td class="fw-bold text-primary"><?= number_format($itemRevenue, 0, ',', '.') ?>đ</td>
                                <td class="fw-bold <?= $itemProfit >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= ($itemProfit >= 0 ? '+' : '') . number_format($itemProfit, 0, ',', '.') ?>đ
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Tổng cộng:</td>
                                <td class="text-primary"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</td>
                                <td class="<?= ($totalRevenue - $totalCost) >= 0 ? 'text-success' : 'text-danger' ?>" style="font-size:1.05em;">
                                    <?= ($totalRevenue - $totalCost) >= 0 ? '+' : '' ?><?= number_format($totalRevenue - $totalCost, 0, ',', '.') ?>đ
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end text-muted small">Chi tiết:</td>
                                <td colspan="2" class="small">
                                    Doanh thu: <strong class="text-primary"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</strong> | 
                                    Vốn: <strong class="text-danger"><?= number_format($totalCost, 0, ',', '.') ?>đ</strong> | 
                                    Lãi: <strong class="<?= ($totalRevenue - $totalCost) >= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format($totalRevenue - $totalCost, 0, ',', '.') ?>đ</strong>
                                </td>
                            </tr>
                        </tfoot>
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
