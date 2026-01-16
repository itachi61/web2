<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TechSmart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/admin.css?v=<?= time() ?>">
</head>

<body>

    <div class="d-flex">
        <nav class="sidebar">
            <div class="sidebar-header">

                Admin Panel
            </div>

            <div class="sidebar-menu">
                <a href="<?= BASE_URL ?>admin" class="<?= (isset($data['active']) && $data['active'] == 'dashboard') ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge"></i> Tổng quan
                </a>

                <a href="<?= BASE_URL ?>admin/products" class="<?= (isset($data['active']) && $data['active'] == 'products') ? 'active' : '' ?>">
                    <i class="fa-solid fa-box"></i> Quản lý Sản phẩm
                </a>

                <a href="<?= BASE_URL ?>admin/orders" class="<?= (isset($data['active']) && $data['active'] == 'orders') ? 'active' : '' ?>">
                    <i class="fa-solid fa-cart-flatbed"></i> Quản lý Đơn hàng
                </a>

                <a href="<?= BASE_URL ?>admin/users" class="<?= (isset($data['active']) && $data['active'] == 'users') ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> Khách hàng
                </a>
            </div>

            <div class="p-3 border-top border-secondary">
                <a href="<?= BASE_URL ?>auth/logout" class="btn btn-danger w-100 btn-sm">
                    <i class="fa-solid fa-power-off me-2"></i> Đăng xuất
                </a>
                <a href="<?= BASE_URL ?>" class="btn btn-outline-light w-100 btn-sm mt-2">
                    <i class="fa-solid fa-globe me-2"></i> Xem Website
                </a>
            </div>
        </nav>

        <div class="main-content">
            <div class="top-navbar">
                <h4 class="m-0 fw-bold text-secondary">
                    <?php
                    if (isset($data['active'])) {
                        if ($data['active'] == 'dashboard') echo 'Tổng quan hệ thống';
                        elseif ($data['active'] == 'products') echo 'Danh sách sản phẩm';
                        else echo 'Quản trị viên';
                    }
                    ?>
                </h4>
                <div class="d-flex align-items-center">
                    <span class="me-2">Xin chào, <strong><?= $_SESSION['name'] ?? 'Admin' ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0d6efd&color=fff" class="rounded-circle" width="35">
                </div>
            </div>

            <div class="content-body">
                <?php
                if (file_exists('../app/views/' . $data['view'] . '.php')) {
                    require_once '../app/views/' . $data['view'] . '.php';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(msg) {
            return confirm(msg ?? 'Bạn có chắc chắn muốn xóa?');
        }
    </script>
</body>

</html>