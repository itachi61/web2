<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i><?= $_SESSION['success_msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-exclamation-circle me-2"></i><?= $_SESSION['error_msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_msg']); ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-users me-2"></i>Quản lý Khách hàng</h2>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="20%">Họ tên</th>
                        <th width="25%">Email</th>
                        <th width="10%">Vai trò</th>
                        <th width="15%">Trạng thái</th>
                        <th width="15%">Ngày tạo</th>
                        <th class="text-end pe-4" width="10%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['users'])): ?>
                        <?php foreach ($data['users'] as $user): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $user['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['fullname'] ?? 'U') ?>&background=random&color=fff" 
                                             class="rounded-circle me-2" width="35" height="35">
                                        <span class="fw-bold"><?= htmlspecialchars($user['fullname'] ?? 'N/A') ?></span>
                                    </div>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php if ($user['role'] == 'admin'): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-3 rounded-pill">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3 rounded-pill">Khách hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $status = $user['status'] ?? 'active';
                                    if ($status == 'active'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-3 rounded-pill">
                                            <i class="fa-solid fa-check-circle me-1"></i>Hoạt động
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 rounded-pill">
                                            <i class="fa-solid fa-lock me-1"></i>Đã khóa
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($user['role'] != 'admin'): ?>
                                        <?php if (($user['status'] ?? 'active') == 'active'): ?>
                                            <a href="<?= BASE_URL ?>admin/lockUser/<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Bạn có chắc muốn KHÓA tài khoản <?= htmlspecialchars($user['fullname']) ?>?')">
                                                <i class="fa-solid fa-lock me-1"></i>Khóa
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>admin/lockUser/<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-outline-success"
                                               onclick="return confirm('Bạn có chắc muốn MỞ KHÓA tài khoản <?= htmlspecialchars($user['fullname']) ?>?')">
                                                <i class="fa-solid fa-lock-open me-1"></i>Mở khóa
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa-solid fa-users fa-3x text-muted mb-2"></i>
                                <p class="text-muted mt-2">Chưa có khách hàng nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
