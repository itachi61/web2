<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'TechSmart - Công nghệ đỉnh cao' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css?v=1">
</head>

<body>

<?php
// tiện dùng cho asset trong public/
$CSS = BASE_URL . 'css/';
$IMG = BASE_URL . 'img/';

// --- LẤY DANH MỤC TỪ MODEL ---
// Lùi 2 cấp thư mục (từ layouts -> views -> app) để vào đúng thư mục models
require_once dirname(dirname(__DIR__)) . '/models/ProductModel.php';
$productModel = new ProductModel();
$categories = [];

try {
    $categories = $productModel->getAllCategories();
} catch (Exception $e) {
    // Bỏ qua lỗi nếu có vấn đề về kết nối
}

// --- HÀM TỰ ĐỘNG MAP ICON THEO TÊN DANH MỤC ---
function getCategoryIcon($categoryName) {
    // Chuyển tên thành chữ thường để dễ so sánh (hỗ trợ tiếng Việt có dấu)
    $name = mb_strtolower(trim($categoryName), 'UTF-8');
    
    if (strpos($name, 'laptop') !== false || strpos($name, 'máy tính') !== false || strpos($name, 'pc') !== false) return 'fa-laptop';
    if (strpos($name, 'điện thoại') !== false || strpos($name, 'phone') !== false || strpos($name, 'smartphone') !== false) return 'fa-mobile-screen-button';
    if (strpos($name, 'linh kiện') !== false || strpos($name, 'chip') !== false || strpos($name, 'ram') !== false) return 'fa-microchip';
    if (strpos($name, 'tai nghe') !== false || strpos($name, 'audio') !== false || strpos($name, 'loa') !== false) return 'fa-headphones';
    if (strpos($name, 'đồng hồ') !== false || strpos($name, 'watch') !== false) return 'fa-stopwatch';
    if (strpos($name, 'màn hình') !== false || strpos($name, 'monitor') !== false) return 'fa-desktop';
    if (strpos($name, 'bàn phím') !== false || strpos($name, 'keyboard') !== false) return 'fa-keyboard';
    if (strpos($name, 'chuột') !== false || strpos($name, 'mouse') !== false) return 'fa-computer-mouse';
    if (strpos($name, 'phụ kiện') !== false || strpos($name, 'sạc') !== false || strpos($name, 'cáp') !== false) return 'fa-plug';
    
    // Icon mặc định
    return 'fa-tag';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center me-4" href="<?= BASE_URL ?>">
            <img src="<?= $IMG ?>logo_white.png"
                 alt="TechSmart Logo"
                 class="img-fluid me-2"
                 style="max-height: 40px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
            <span class="d-none d-xl-block fw-bold">TECHSMART</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarContent">
            
            <div class="dropdown me-lg-4 mb-3 mb-lg-0 mt-3 mt-lg-0">
                <button class="btn btn-category dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-bars"></i>
                    <span class="fw-bold d-none d-lg-block">Danh mục</span>
                </button>
                
                <ul class="dropdown-menu border-0 shadow-lg animate__animated animate__fadeIn">
                    <!-- ĐỔ DỮ LIỆU DANH MỤC Ở ĐÂY -->
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <?php 
                                // Nếu có icon trong Database thì ưu tiên dùng, nếu không thì dùng hàm tự động
                                $iconClass = !empty($cat['icon']) ? htmlspecialchars($cat['icon']) : getCategoryIcon($cat['name']); 
                            ?>
                            <li>
                                <a class="dropdown-item py-2 rounded-2" href="<?= BASE_URL ?>product/category/<?= $cat['id'] ?>">
                                    <i class="fa-solid <?= $iconClass ?> text-muted me-2" style="width: 20px; text-align: center;"></i> 
                                    <?= htmlspecialchars($cat['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="dropdown-item text-muted py-2">Đang cập nhật...</span></li>
                    <?php endif; ?>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 rounded-2 fw-bold text-primary" href="<?= BASE_URL ?>product"><i class="fa-solid fa-boxes-stacked me-2" style="width: 20px; text-align: center;"></i> Xem tất cả</a></li>
                </ul>
            </div>

            <form action="<?= BASE_URL ?>product/search" method="GET" class="d-flex flex-grow-1 me-lg-4 mb-3 mb-lg-0">
                <div class="input-group w-100">
                    <input class="form-control rounded-start-pill border-0 ps-3" type="search" name="keyword" placeholder="Bạn tìm gì..." aria-label="Search" style="font-size: 0.95rem;" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    <button class="btn border-0 rounded-end-pill bg-white text-primary btn-search-anim pe-3" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <div class="d-flex align-items-center gap-3 justify-content-end">
                
                <button id="darkModeToggle" class="btn btn-sm text-white border-0 fs-5 theme-toggle" title="Chế độ sáng/tối" style="background:none;">
                    <i class="fa-solid fa-moon"></i>
                </button>

                <div class="dropdown">
                    <button class="btn btn-sm text-white border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background:none;">
                        <i class="fa-solid fa-globe me-1"></i><span id="currentLang">VI</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:130px;">
                        <li><a class="dropdown-item lang-option" href="#" data-lang="VI"><img src="https://flagcdn.com/w20/vn.png" class="me-2" style="width: 20px;">Tiếng Việt</a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="EN"><img src="https://flagcdn.com/w20/gb.png" class="me-2" style="width: 20px;">English</a></li>
                    </ul>
                </div>

                <a href="<?= BASE_URL ?>cart" id="cart-icon-link" class="text-white position-relative fs-5 text-decoration-none mx-2 cart-anim">
                    <i class="fa-solid fa-cart-shopping me-1"></i>
                    <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="font-size: 0.6rem; <?= (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) ? 'display:none;' : '' ?>">
                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                    </span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-login-header dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-user me-1"></i> 
                            <span class="d-none d-lg-inline small fw-bold"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
                        </button>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li>
                                    <a class="dropdown-item fw-bold text-primary" href="<?= BASE_URL ?>admin">
                                        <i class="fa-solid fa-gauge me-2"></i>Trang quản trị
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/profile"><i class="fa-solid fa-user-pen me-2"></i>Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/myOrders"><i class="fa-solid fa-box me-2"></i>Đơn hàng của tôi</a></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>auth/login" class="btn btn-login-header fw-bold text-nowrap">
                        <i class="fa-regular fa-circle-user me-1"></i> Đăng nhập
                    </a>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</nav>

<div class="container my-4" style="min-height: 60vh;">