<div class="auth-page">
  <div class="auth-card">
    <h1>Đăng ký</h1>
    <p class="auth-subtitle">Tạo tài khoản mới tại TechSmart</p>
    
    <form class="auth-form" method="POST">
      <div class="form-group">
        <label for="name">Họ và tên</label>
        <input type="text" id="name" name="name" placeholder="Nguyễn Văn A" required>
      </div>
      
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="example@email.com" required>
      </div>
      
      <div class="form-group">
        <label for="phone">Số điện thoại</label>
        <input type="tel" id="phone" name="phone" placeholder="0123456789" required>
      </div>
      
      <div class="form-group">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      
      <div class="form-group">
        <label for="confirm_password">Xác nhận mật khẩu</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
      </div>
      
      <label class="checkbox-label">
        <input type="checkbox" name="terms" required>
        <span>Tôi đồng ý với <a href="#" class="link">điều khoản sử dụng</a></span>
      </label>
      
      <button type="submit" class="btn btn-primary btn-large">Đăng ký</button>
    </form>
    
    <p class="auth-footer">
      Đã có tài khoản? <a href="<?= BASE_URL ?>/login" class="link">Đăng nhập</a>
    </p>
  </div>
</div>
