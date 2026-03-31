<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TechSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .login-header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            padding: 2rem;
            text-align: center;
        }
        .login-header i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }
        .login-body {
            padding: 2rem;
            background: #fff;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .btn-admin {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-admin:hover {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(59,130,246,0.4);
        }
        .login-footer {
            background: #f8fafc;
            padding: 1rem 2rem;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<div class="login-card card">
    <div class="login-header text-white">
        <i class="fa-solid fa-shield-halved d-block"></i>
        <h4 class="fw-bold mb-1">ADMIN PANEL</h4>
        <small class="opacity-75">Đăng nhập quản trị viên</small>
    </div>
    <div class="login-body">
        <?php if(isset($data['error'])): ?>
            <div class="alert alert-danger py-2 small">
                <i class="fa-solid fa-circle-exclamation me-1"></i><?= $data['error'] ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>auth/adminLogin" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-primary"></i></span>
                    <input class="form-control py-2" type="email" name="email" placeholder="admin@techsmart.com" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold small text-muted">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-primary"></i></span>
                    <input class="form-control py-2" type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>
            <div class="d-grid">
                <button class="btn btn-admin btn-lg text-white" type="submit">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập
                </button>
            </div>
        </form>
    </div>
    <div class="login-footer">
        <a href="<?= BASE_URL ?>" class="text-muted text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i>Quay về trang chủ
        </a>
    </div>
</div>

</body>
</html>
