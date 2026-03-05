<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white text-center py-4">
                <div class="mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['name'] ?? 'User') ?>&background=0d6efd&color=fff&size=100" 
                         class="rounded-circle border border-3 border-white shadow" width="100">
                </div>
                <h4 class="mb-0 fw-bold"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></h4>
                <small class="opacity-75"><?= $_SESSION['role'] ?? 'customer' ?></small>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($data['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fa-solid fa-check-circle me-2"></i><?= $data['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fa-solid fa-xmark-circle me-2"></i><?= $data['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/updateProfile" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fa-solid fa-user me-1"></i> Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($data['user']['fullname'] ?? $_SESSION['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fa-solid fa-envelope me-1"></i> Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['user']['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fa-solid fa-calendar me-1"></i> Ngày đăng ký</label>
                        <input type="text" class="form-control bg-light" value="<?= isset($data['user']['created_at']) ? date('d/m/Y H:i', strtotime($data['user']['created_at'])) : 'N/A' ?>" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fa-solid fa-shield me-1"></i> Vai trò</label>
                        <input type="text" class="form-control bg-light" 
                               value="<?= ($_SESSION['role'] ?? 'customer') == 'admin' ? 'Quản trị viên' : 'Khách hàng' ?>" disabled>
                    </div>

                    <hr>
                    
                    <h6 class="fw-bold text-muted mb-3"><i class="fa-solid fa-lock me-1"></i> Đổi mật khẩu (tùy chọn)</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Nhập nếu muốn đổi mật khẩu">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-save me-2"></i>Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
