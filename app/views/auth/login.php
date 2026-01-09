<div class="auth-page">
  <div class="auth-card">
    <h1>Đăng nhập</h1>
    <p class="auth-subtitle">Chào mừng bạn quay lại TechSmart</p>
    
    <form class="auth-form" method="POST" action="<?= BASE_URL ?>/login">
      <div class="form-group">
        <label for="username">Tên đăng nhập hoặc Email</label>
        <input type="text" id="username" name="username" placeholder="Nhập tài khoản của bạn" required>
      </div>
      
      <div class="form-group">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      
      <div class="form-options">
        <label class="checkbox-label">
          <input type="checkbox" name="remember">
          <span>Ghi nhớ đăng nhập</span>
        </label>
        <a href="#" class="link">Quên mật khẩu?</a>
      </div>
      
      <button type="submit" class="btn btn-primary btn-large">Đăng nhập</button>
    </form>
    
    <p class="auth-footer">
      Chưa có tài khoản? <a href="<?= BASE_URL ?>/register" class="link">Đăng ký ngay</a>
    </p>
  </div>
</div>
