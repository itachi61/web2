<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị hệ thống - TechSmart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/admin.css?v=<?= time() ?>">
</head>

<body>

    <div class="d-flex">
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="<?= BASE_URL ?>" target="_blank" class="d-block text-center">
                    <img src="<?= BASE_URL ?>img/logo_full.png"
                        alt="TechSmart Logo"
                        class="img-fluid"
                        style="max-height: 50px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                </a>
            </div>

            <div class="sidebar-menu">
                <a href="<?= BASE_URL ?>admin" class="<?= (isset($data['active']) && $data['active'] == 'dashboard') ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge"></i> Tổng quan
                </a>

                <a href="<?= BASE_URL ?>admin/products" class="<?= (isset($data['active']) && $data['active'] == 'products') ? 'active' : '' ?>">
                    <i class="fa-solid fa-box"></i> Quản lý Sản phẩm
                </a>

                <a href="<?= BASE_URL ?>admin/orders" class="<?= (isset($data['active']) && $data['active'] == 'orders') ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Quản lý Đơn hàng
                </a>

                <a href="<?= BASE_URL ?>admin/users" class="<?= (isset($data['active']) && $data['active'] == 'users') ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> Khách hàng
                </a>
            </div>

            <div class="sidebar-footer">
                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-globe me-2"></i> Xem Website
                    </a>
                    <a href="<?= BASE_URL ?>auth/logout" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="top-navbar mb-4">
                <div>
                    <h4 class="m-0 fw-bold text-secondary">
                        <?php
                        // Hiển thị tiêu đề động dựa trên trang hiện tại
                        if (isset($data['active'])) {
                            switch ($data['active']) {
                                case 'dashboard':
                                    echo 'Tổng quan hệ thống';
                                    break;
                                case 'products':
                                    echo 'Danh sách sản phẩm';
                                    break;
                                case 'orders':
                                    echo 'Danh sách đơn hàng';
                                    break;
                                case 'users':
                                    echo 'Danh sách khách hàng';
                                    break;
                                default:
                                    echo 'Quản trị viên';
                            }
                        }
                        ?>
                    </h4>
                </div>

                <div class="d-flex align-items-center">
                    <div class="text-end me-3 d-none d-md-block">
                        <small class="text-muted d-block">Xin chào,</small>
                        <span class="fw-bold text-dark"><?= $_SESSION['name'] ?? 'Admin' ?></span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['name'] ?? 'Admin') ?>&background=0d6efd&color=fff"
                        class="rounded-circle border border-2 border-white shadow-sm"
                        width="40" height="40" alt="Avatar">
                </div>
            </div>

            <div class="content-body">
                <?php
                if (isset($data['view']) && file_exists('../app/views/' . $data['view'] . '.php')) {
                    require_once '../app/views/' . $data['view'] . '.php';
                } else {
                    echo '<div class="alert alert-danger shadow-sm">';
                    echo '<i class="fa-solid fa-triangle-exclamation me-2"></i>';
                    echo 'Lỗi: Không tìm thấy file giao diện <strong>' . ($data['view'] ?? 'Unknown') . '</strong>';
                    echo '</div>';
                }
                ?>
            </div>

            <div class="mt-5 text-center text-muted small py-3">
                &copy; 2026 TechSmart Admin System. Version 1.0
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Hàm xác nhận xóa (dùng chung cho toàn trang Admin)
        function confirmDelete(message) {
            return confirm(message || 'Bạn có chắc chắn muốn xóa dữ liệu này không? Hành động này không thể hoàn tác.');
        }
    </script>

</body>

</html>