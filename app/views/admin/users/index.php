<!-- Users Management -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fa-solid fa-users text-primary me-2"></i>Quản lý người dùng
    </h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-plus me-2"></i>Thêm tài khoản
    </button>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['users'])): ?>
                    <?php foreach ($data['users'] as $user): ?>
                    <tr>
                        <td><strong>#<?= $user['id'] ?></strong></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 35px; height: 35px; font-size: 0.8rem;">
                                    <?= strtoupper(substr($user['fullname'], 0, 2)) ?>
                                </div>
                                <?= htmlspecialchars($user['fullname']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-primary">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= date('d/m/Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                <a href="<?= BASE_URL ?>admin/lockUser/<?= $user['id'] ?>" 
                                   class="btn btn-outline-warning" title="Khóa tài khoản"
                                   data-confirm="Khóa tài khoản <?= htmlspecialchars($user['fullname']) ?>?"
                                   data-confirm-title="Khóa tài khoản"
                                   data-confirm-type="warning"
                                   data-confirm-icon="fa-lock"
                                   data-confirm-btn="Khóa">
                                    <i class="fa-solid fa-lock"></i>
                                </a>
                                <?php else: ?>
                                <a href="<?= BASE_URL ?>admin/unlockUser/<?= $user['id'] ?>" 
                                   class="btn btn-outline-success" title="Mở khóa"
                                   data-confirm="Mở khóa tài khoản <?= htmlspecialchars($user['fullname']) ?>?"
                                   data-confirm-title="Mở khóa tài khoản"
                                   data-confirm-type="success"
                                   data-confirm-icon="fa-unlock"
                                   data-confirm-btn="Mở khóa">
                                    <i class="fa-solid fa-unlock"></i>
                                </a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>admin/resetPassword/<?= $user['id'] ?>" 
                                   class="btn btn-outline-info" title="Reset mật khẩu"
                                   data-confirm="Reset mật khẩu tài khoản <?= htmlspecialchars($user['fullname']) ?> về 123456?"
                                   data-confirm-title="Reset mật khẩu"
                                   data-confirm-type="info"
                                   data-confirm-icon="fa-key"
                                   data-confirm-btn="Reset">
                                    <i class="fa-solid fa-key"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fa-solid fa-users-slash fa-3x mb-3 d-block opacity-50"></i>
                            Chưa có người dùng nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>admin/createUser" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-user-plus text-primary me-2"></i>Thêm tài khoản
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Điện thoại</label>
                        <input type="tel" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
