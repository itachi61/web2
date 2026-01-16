<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="font-weight-light my-2">Đăng Nhập</h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($data['error'])): ?>
                    <div class="alert alert-danger"><?= $data['error'] ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/login" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ Email</label>
                        <input class="form-control py-2" type="email" name="email" placeholder="name@example.com" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input class="form-control py-2" type="password" name="password" placeholder="Nhập mật khẩu" required />
                    </div>
                    <div class="d-grid mt-4">
                        <button class="btn btn-primary btn-lg" type="submit">Đăng nhập</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="small">
                    <a href="<?= BASE_URL ?>auth/register">Chưa có tài khoản? Đăng ký ngay!</a>
                </div>
            </div>
        </div>
    </div>
</div>