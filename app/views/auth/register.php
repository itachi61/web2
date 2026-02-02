<div class="row justify-content-center mt-4 mb-5">
    <div class="col-lg-8 col-md-10">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- Header -->
            <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
                <h3 class="fw-bold mb-1"><i class="fa-solid fa-user-plus me-2"></i>Đăng Ký Tài Khoản</h3>
                <p class="mb-0 opacity-75">Tạo tài khoản để mua sắm dễ dàng hơn</p>
            </div>
            
            <div class="card-body p-4 p-lg-5">
                <?php if(isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= $data['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if(isset($data['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i><?= $data['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/register" method="POST" id="registerForm" novalidate>
                    
                    <!-- Section 1: Thông tin tài khoản -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-user me-2"></i>Thông tin tài khoản
                        </h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                                    <input type="text" class="form-control py-2" name="fullname" id="fullname"
                                           placeholder="Nguyễn Văn A" required 
                                           value="<?= htmlspecialchars($data['old']['fullname'] ?? '') ?>">
                                </div>
                                <div class="invalid-feedback">Vui lòng nhập họ tên</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Điện thoại <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-muted"></i></span>
                                    <input type="tel" class="form-control py-2" name="phone" id="phone"
                                           placeholder="0901234567" required pattern="[0-9]{10,11}"
                                           value="<?= htmlspecialchars($data['old']['phone'] ?? '') ?>">
                                </div>
                                <div class="invalid-feedback">Điện thoại không hợp lệ (10-11 số)</div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control py-2" name="email" id="email"
                                           placeholder="email@example.com" required
                                           value="<?= htmlspecialchars($data['old']['email'] ?? '') ?>">
                                </div>
                                <div class="invalid-feedback">Email không hợp lệ</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control py-2" name="password" id="password"
                                           placeholder="Tối thiểu 6 ký tự" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Mật khẩu tối thiểu 6 ký tự</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control py-2" name="confirm_password" id="confirm_password"
                                           placeholder="Nhập lại mật khẩu" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Mật khẩu không khớp</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 2: Địa chỉ giao hàng -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-location-dot me-2"></i>Địa chỉ giao hàng mặc định
                        </h5>
                        <p class="text-muted small mb-3">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            Thông tin này sẽ được sử dụng khi bạn đặt hàng. Bạn có thể thay đổi sau.
                        </p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quận/Huyện <span class="text-danger">*</span></label>
                                <select class="form-select py-2" name="district" id="district" required>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                    <option value="Quận 1" <?= ($data['old']['district'] ?? '') == 'Quận 1' ? 'selected' : '' ?>>Quận 1</option>
                                    <option value="Quận 2" <?= ($data['old']['district'] ?? '') == 'Quận 2' ? 'selected' : '' ?>>Quận 2</option>
                                    <option value="Quận 3" <?= ($data['old']['district'] ?? '') == 'Quận 3' ? 'selected' : '' ?>>Quận 3</option>
                                    <option value="Quận 4" <?= ($data['old']['district'] ?? '') == 'Quận 4' ? 'selected' : '' ?>>Quận 4</option>
                                    <option value="Quận 5" <?= ($data['old']['district'] ?? '') == 'Quận 5' ? 'selected' : '' ?>>Quận 5</option>
                                    <option value="Quận 6" <?= ($data['old']['district'] ?? '') == 'Quận 6' ? 'selected' : '' ?>>Quận 6</option>
                                    <option value="Quận 7" <?= ($data['old']['district'] ?? '') == 'Quận 7' ? 'selected' : '' ?>>Quận 7</option>
                                    <option value="Quận 8" <?= ($data['old']['district'] ?? '') == 'Quận 8' ? 'selected' : '' ?>>Quận 8</option>
                                    <option value="Quận 9" <?= ($data['old']['district'] ?? '') == 'Quận 9' ? 'selected' : '' ?>>Quận 9</option>
                                    <option value="Quận 10" <?= ($data['old']['district'] ?? '') == 'Quận 10' ? 'selected' : '' ?>>Quận 10</option>
                                    <option value="Quận 11" <?= ($data['old']['district'] ?? '') == 'Quận 11' ? 'selected' : '' ?>>Quận 11</option>
                                    <option value="Quận 12" <?= ($data['old']['district'] ?? '') == 'Quận 12' ? 'selected' : '' ?>>Quận 12</option>
                                    <option value="Quận Bình Thạnh" <?= ($data['old']['district'] ?? '') == 'Quận Bình Thạnh' ? 'selected' : '' ?>>Quận Bình Thạnh</option>
                                    <option value="Quận Gò Vấp" <?= ($data['old']['district'] ?? '') == 'Quận Gò Vấp' ? 'selected' : '' ?>>Quận Gò Vấp</option>
                                    <option value="Quận Phú Nhuận" <?= ($data['old']['district'] ?? '') == 'Quận Phú Nhuận' ? 'selected' : '' ?>>Quận Phú Nhuận</option>
                                    <option value="Quận Tân Bình" <?= ($data['old']['district'] ?? '') == 'Quận Tân Bình' ? 'selected' : '' ?>>Quận Tân Bình</option>
                                    <option value="Quận Tân Phú" <?= ($data['old']['district'] ?? '') == 'Quận Tân Phú' ? 'selected' : '' ?>>Quận Tân Phú</option>
                                    <option value="Quận Thủ Đức" <?= ($data['old']['district'] ?? '') == 'Quận Thủ Đức' ? 'selected' : '' ?>>Quận Thủ Đức</option>
                                    <option value="Huyện Bình Chánh" <?= ($data['old']['district'] ?? '') == 'Huyện Bình Chánh' ? 'selected' : '' ?>>Huyện Bình Chánh</option>
                                    <option value="Huyện Củ Chi" <?= ($data['old']['district'] ?? '') == 'Huyện Củ Chi' ? 'selected' : '' ?>>Huyện Củ Chi</option>
                                    <option value="Huyện Hóc Môn" <?= ($data['old']['district'] ?? '') == 'Huyện Hóc Môn' ? 'selected' : '' ?>>Huyện Hóc Môn</option>
                                    <option value="Huyện Nhà Bè" <?= ($data['old']['district'] ?? '') == 'Huyện Nhà Bè' ? 'selected' : '' ?>>Huyện Nhà Bè</option>
                                    <option value="Huyện Cần Giờ" <?= ($data['old']['district'] ?? '') == 'Huyện Cần Giờ' ? 'selected' : '' ?>>Huyện Cần Giờ</option>
                                </select>
                                <div class="invalid-feedback">Vui lòng chọn Quận/Huyện</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phường/Xã <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-2" name="ward" id="ward"
                                       placeholder="VD: Phường Bến Nghé" required
                                       value="<?= htmlspecialchars($data['old']['ward'] ?? '') ?>">
                                <div class="invalid-feedback">Vui lòng nhập Phường/Xã</div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-semibold">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-house text-muted"></i></span>
                                    <input type="text" class="form-control py-2" name="address" id="address"
                                           placeholder="Số nhà, tên đường, tòa nhà..." required
                                           value="<?= htmlspecialchars($data['old']['address'] ?? '') ?>">
                                </div>
                                <div class="invalid-feedback">Vui lòng nhập địa chỉ cụ thể</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            Tôi đồng ý với <a href="#" class="text-primary">Điều khoản dịch vụ</a> và <a href="#" class="text-primary">Chính sách bảo mật</a>
                        </label>
                        <div class="invalid-feedback">Bạn phải đồng ý với điều khoản</div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-lg text-white fw-bold py-3" 
                                style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
                            <i class="fa-solid fa-user-plus me-2"></i>Tạo Tài Khoản
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="card-footer bg-light text-center py-3">
                <span class="text-muted">Đã có tài khoản?</span>
                <a href="<?= BASE_URL ?>auth/login" class="fw-bold text-primary text-decoration-none ms-1">
                    Đăng nhập ngay <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Reset validation states
    this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    // Check required fields
    this.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        }
    });
    
    // Check phone format
    const phone = document.getElementById('phone');
    if (phone.value && !/^[0-9]{10,11}$/.test(phone.value)) {
        phone.classList.add('is-invalid');
        isValid = false;
    }
    
    // Check email format
    const email = document.getElementById('email');
    if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        email.classList.add('is-invalid');
        isValid = false;
    }
    
    // Check password length
    const password = document.getElementById('password');
    if (password.value.length < 6) {
        password.classList.add('is-invalid');
        isValid = false;
    }
    
    // Check password match
    const confirmPassword = document.getElementById('confirm_password');
    if (password.value !== confirmPassword.value) {
        confirmPassword.classList.add('is-invalid');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});

// Real-time validation for password confirmation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    if (this.value && this.value !== password) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>