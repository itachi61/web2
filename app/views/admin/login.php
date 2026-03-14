<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TechSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .admin-login-card {
            max-width: 420px;
            width: 100%;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .admin-login-header {
            background: linear-gradient(135deg, #005bea, #00c6fb);
            padding: 30px;
            text-align: center;
        }
        .admin-login-header .icon {
            width: 70px; height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px; color: #fff;
        }
        .admin-login-header h3 { color: #fff; font-weight: 700; margin: 0; }
        .admin-login-header small { color: rgba(255,255,255,0.7); }
        .admin-login-body { padding: 30px; background: #1a1d23; }
        .admin-login-body .form-control {
            background: #2a2d35; border: 1px solid #3a3d45; color: #fff;
            padding: 12px 15px; border-radius: 10px;
        }
        .admin-login-body .form-control:focus {
            border-color: #005bea; box-shadow: 0 0 0 3px rgba(0,91,234,0.2); background: #2a2d35; color: #fff;
        }
        .admin-login-body .form-label { color: #adb5bd; font-weight: 600; }
        .btn-admin-login {
            background: linear-gradient(135deg, #005bea, #00c6fb);
            border: none; padding: 12px; border-radius: 10px;
            font-weight: 700; font-size: 16px; color: #fff;
            transition: all 0.3s;
        }
        .btn-admin-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,91,234,0.4); color: #fff; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: rgba(255,255,255,0.5); text-decoration: none; font-size: 13px; }
        .back-link a:hover { color: #fff; }
    </style>
</head>
<body>
    <div>
        <div class="admin-login-card card">
            <div class="admin-login-header">
                <div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>ADMIN PANEL</h3>
                <small>TechSmart Management System</small>
            </div>
            <div class="admin-login-body">
                <?php if (!empty($data['error'])): ?>
                    <div class="alert alert-danger py-2 d-flex align-items-center" style="border-radius: 10px;">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <?= $data['error'] ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>admin/processLogin" method="POST">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-envelope me-1"></i> Email</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@techsmart.vn" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-lock me-1"></i> Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-admin-login">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập Quản trị
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="back-link">
            <a href="<?= BASE_URL ?>"><i class="fa-solid fa-arrow-left me-1"></i>Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>
