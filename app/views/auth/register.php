<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-success text-white text-center py-3">
                <h4 class="font-weight-light my-2">Đăng Ký Tài Khoản</h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($data['error'])): ?>
                    <div class="alert alert-danger"><?= $data['error'] ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/register" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input class="form-control py-2" type="text" name="fullname" placeholder="Nguyễn Văn A" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input class="form-control py-2" type="email" name="email" placeholder="name@example.com" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input class="form-control py-2" type="tel" name="phone" placeholder="0901234567" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ giao hàng mặc định <span class="text-danger">*</span></label>
                        <textarea class="form-control py-2" name="address" rows="2" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" required></textarea>
                        <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i>Địa chỉ này sẽ được sử dụng làm mặc định khi đặt hàng</small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input class="form-control py-2" type="password" name="password" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                            <input class="form-control py-2" type="password" name="confirm_password" required />
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button class="btn btn-success btn-lg" type="submit">Tạo tài khoản</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <div class="small">
                    <a href="<?= BASE_URL ?>auth/login">Đã có tài khoản? Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>