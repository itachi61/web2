<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
                <h4 class="fw-bold my-2"><i class="fa-solid fa-right-to-bracket me-2"></i><?= __('login') ?></h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($_SESSION['register_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i><?= $_SESSION['register_success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['register_success']); ?>
                <?php endif; ?>

                <?php if(isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= $data['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/login" method="POST" id="loginForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= __('email') ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input class="form-control py-2" type="email" name="email" placeholder="name@example.com" required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= __('password') ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input class="form-control py-2" type="password" name="password" id="password" 
                                   placeholder="<?= getCurrentLang() === 'en' ? 'Enter password' : 'Nhập mật khẩu' ?>" required />
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fa-solid fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe"><?= __('remember_me') ?></label>
                        </div>
                        <a href="#" class="text-decoration-none small"><?= __('forgot_password') ?></a>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-lg text-white fw-bold py-2" type="submit"
                                style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
                            <i class="fa-solid fa-right-to-bracket me-2"></i><?= __('login') ?>
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light text-center py-3">
                <span class="text-muted"><?= __('no_account') ?></span>
                <a href="<?= BASE_URL ?>auth/register" class="fw-bold text-primary text-decoration-none ms-1">
                    <?= __('register') ?> <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
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
</script>