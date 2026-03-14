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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-user-plus me-2"></i>Thêm tài khoản
    </button>
</div>

<!-- Modal Thêm tài khoản -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i>Thêm tài khoản mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>admin/createUser" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu</label>
                        <input type="text" name="password" class="form-control" value="123456" placeholder="Mặc định: 123456">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="customer">Khách hàng</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
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
                                        <a href="<?= BASE_URL ?>admin/resetPassword/<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning me-1"
                                           onclick="return confirm('Reset mật khẩu của <?= htmlspecialchars($user['fullname']) ?> về 123456?')"
                                           title="Reset mật khẩu">
                                            <i class="fa-solid fa-key"></i>
                                        </a>
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
