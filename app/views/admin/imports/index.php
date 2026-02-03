<!-- Imports Management (Phiếu nhập hàng) -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fa-solid fa-truck-ramp-box text-primary me-2"></i>Quản lý nhập hàng
    </h4>
    <a href="<?= BASE_URL ?>admin/imports/create" class="btn btn-primary">
        <i class="fa-solid fa-plus me-2"></i>Tạo phiếu nhập
    </a>
</div>

<!-- Filter Tabs -->
<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === '' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/imports">
            Tất cả
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === 'draft' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/imports?status=draft">
            <i class="fa-solid fa-file-pen text-warning me-1"></i>Nháp
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($data['filter'] ?? '') === 'completed' ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/imports?status=completed">
            <i class="fa-solid fa-check text-success me-1"></i>Hoàn thành
        </a>
    </li>
</ul>

<!-- Imports Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 admin-table">
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Nhà cung cấp</th>
                    <th>Số SP</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Người tạo</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['imports'])): ?>
                    <?php foreach ($data['imports'] as $import): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($import['import_code']) ?></strong></td>
                        <td><?= htmlspecialchars($import['supplier'] ?? 'N/A') ?></td>
                        <td><?= $import['item_count'] ?? 0 ?> SP</td>
                        <td class="text-primary fw-bold"><?= number_format($import['total_amount'] ?? 0, 0, ',', '.') ?>đ</td>
                        <td>
                            <?php if ($import['status'] === 'draft'): ?>
                                <span class="badge bg-warning">Nháp</span>
                            <?php else: ?>
                                <span class="badge bg-success">Hoàn thành</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($import['created_at'])) ?></td>
                        <td><?= htmlspecialchars($import['creator_name'] ?? 'N/A') ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>admin/importDetail/<?= $import['id'] ?>" class="btn btn-outline-primary" title="Xem">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($import['status'] === 'draft'): ?>
                                <a href="<?= BASE_URL ?>admin/imports/edit/<?= $import['id'] ?>" class="btn btn-outline-warning" title="Sửa">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>admin/completeImport/<?= $import['id'] ?>" 
                                   class="btn btn-outline-success" title="Hoàn thành"
                                   data-confirm="Hoàn thành phiếu nhập <?= htmlspecialchars($import['import_code']) ?>? Sau khi hoàn thành sẽ không thể sửa."
                                   data-confirm-title="Hoàn thành phiếu nhập"
                                   data-confirm-type="success"
                                   data-confirm-icon="fa-check-circle"
                                   data-confirm-btn="Hoàn thành">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                                <a href="<?= BASE_URL ?>admin/deleteImport/<?= $import['id'] ?>" 
                                   class="btn btn-outline-danger" title="Xóa"
                                   data-confirm="Xóa phiếu nhập <?= htmlspecialchars($import['import_code']) ?>?"
                                   data-confirm-title="Xóa phiếu nhập"
                                   data-confirm-type="danger"
                                   data-confirm-icon="fa-trash"
                                   data-confirm-btn="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fa-solid fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                            Chưa có phiếu nhập nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
