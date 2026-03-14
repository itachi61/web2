<?php if (isset($_SESSION['import_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-check-circle me-2"></i><?= $_SESSION['import_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['import_success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['import_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa-solid fa-exclamation-circle me-2"></i><?= $_SESSION['import_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['import_error']); ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-truck-ramp-box me-2"></i>Phiếu Nhập Hàng</h2>
    <form action="<?= BASE_URL ?>admin/createReceipt" method="POST" class="d-inline">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Tạo phiếu nhập mới</button>
    </form>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="15%">Mã phiếu</th>
                        <th width="10%">Số SP</th>
                        <th width="15%">Tổng tiền</th>
                        <th width="12%">Trạng thái</th>
                        <th width="13%">Ngày tạo</th>
                        <th width="13%">Ngày HT</th>
                        <th width="10%">Người tạo</th>
                        <th class="text-end pe-4" width="7%">Xem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['receipts'])): ?>
                        <?php foreach ($data['receipts'] as $r): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $r['id'] ?></td>
                            <td><span class="fw-bold text-primary"><?= htmlspecialchars($r['receipt_code']) ?></span></td>
                            <td><span class="badge bg-info-subtle text-info"><?= $r['item_count'] ?> SP</span></td>
                            <td class="fw-bold"><?= number_format($r['total_amount'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php if ($r['status'] == 'draft'): ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-pen me-1"></i>Nháp</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Hoàn thành</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                            <td class="text-muted small"><?= $r['completed_at'] ? date('d/m/Y H:i', strtotime($r['completed_at'])) : '—' ?></td>
                            <td class="small"><?= htmlspecialchars($r['created_by_name'] ?? '') ?></td>
                            <td class="text-end pe-4">
                                <a href="<?= BASE_URL ?>admin/viewReceipt/<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fa-solid fa-file-invoice fa-3x text-muted mb-2"></i>
                                <p class="text-muted mt-2">Chưa có phiếu nhập nào. Nhấn "Tạo phiếu nhập mới" để bắt đầu.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
