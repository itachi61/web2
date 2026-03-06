<?php
// Load Language Helper
require_once dirname(__DIR__) . '/../core/Language.php';
$lang = Language::getInstance();
$currentLang = $lang->getCurrentLang();
?>
<!DOCTYPE html>
<<<<<<< HEAD
<html lang="<?= $currentLang ?>">

=======
<html lang="vi">
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'TechSmart' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<<<<<<< HEAD
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dark-mode.css?v=<?= time() ?>">
=======
    <!-- CSS local trong public/css -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css?v=1">
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
</head>

<body>

<<<<<<< HEAD
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center me-4" href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>img/logo_white.png" alt="Logo" class="img-fluid me-2" style="max-height: 40px;">
                TECHSMART
            </a>
=======
<?php
// tiện dùng cho asset trong public/
$CSS = BASE_URL . 'css/';
$IMG = BASE_URL . 'img/';
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f

// active menu theo URL
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
$isHome = (strpos($currentUrl, 'product/category') === false && strpos($currentUrl, 'product/index') === false) 
          || $currentUrl == BASE_URL || $currentUrl == rtrim(BASE_URL, '/');
$isLaptop = strpos($currentUrl, 'product/category/1') !== false;
$isPhone = strpos($currentUrl, 'product/category/2') !== false;
$isAccessory = strpos($currentUrl, 'product/category/3') !== false;
?>

<<<<<<< HEAD
            <div class="collapse navbar-collapse" id="navbarContent">

                <div class="dropdown me-lg-4 mb-3 mb-lg-0">
                    <button class="btn btn-category" type="button" id="catDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                        <span class="fw-bold">Danh mục</span>
                    </button>
                    <ul class="dropdown-menu border-0 shadow-lg animate__animated animate__fadeIn" aria-labelledby="catDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>product/category/1">
                                <i class="fa-solid fa-laptop text-muted" style="width: 20px;"></i> <span>Laptop</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>product/category/2">
                                <i class="fa-solid fa-mobile-screen-button text-muted" style="width: 20px;"></i> <span>Điện thoại</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>product/category/3">
                                <i class="fa-solid fa-microchip text-muted" style="width: 20px;"></i> <span>Linh kiện PC</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>product/category/4">
                                <i class="fa-solid fa-tablet-screen-button text-muted" style="width: 20px;"></i> <span>Màn hình</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>product/category/5">
                                <i class="fa-solid fa-headphones text-muted" style="width: 20px;"></i> <span>Phụ kiện & Âm thanh</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-bold text-primary" href="<?= BASE_URL ?>product">
                                <i class="fa-solid fa-boxes-stacked" style="width: 20px;"></i> <span>Xem tất cả</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <form action="<?= BASE_URL ?>product/search" method="GET" class="d-flex flex-grow-1 me-lg-4 mb-3 mb-lg-0">
                    <div class="input-group w-100">
                        <input class="form-control rounded-start-pill border-0 ps-3" type="search" name="keyword" placeholder="Bạn cần tìm gì hôm nay?..." style="font-size: 0.95rem;">
                        <button class="btn border-0 rounded-end-pill bg-white text-primary btn-search-anim pe-3" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-2 justify-content-end">

                    <div class="dropdown">
                        <button class="btn btn-sm text-white border-0 fw-bold" type="button" data-bs-toggle="dropdown">
                            <?= $currentLang === 'vi' ? '🇻🇳 VN' : '🇺🇸 EN' ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 100px;">
                            <li><a class="dropdown-item" href="?lang=vi">🇻🇳 Tiếng Việt</a></li>
                            <li><a class="dropdown-item" href="?lang=en">🇺🇸 English</a></li>
                        </ul>
                    </div>

                    <button id="themeToggle" class="btn btn-sm text-white border-0 theme-toggle">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    <a href="<?= BASE_URL ?>cart" class="text-white position-relative fs-5 text-decoration-none mx-2 cart-anim">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                <?= count($_SESSION['cart']) ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-login-header dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-regular fa-user"></i>
                                <span class="d-none d-lg-inline small fw-bold ms-1"><?= $_SESSION['name'] ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item fw-bold text-primary" href="<?= BASE_URL ?>admin"><i class="fa-solid fa-gauge me-2"></i>Admin Panel</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>orders/history">Đơn hàng của tôi</a></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>auth/login" class="btn btn-login-header fw-bold text-nowrap">
                            <i class="fa-regular fa-circle-user"></i> Đăng nhập
                        </a>
                    <?php endif; ?>

=======
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>">
            <img src="<?= $IMG ?>logo_white.png"
                 alt="TechSmart Logo"
                 class="img-fluid"
                 style="max-height: 50px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
            TECHSMART
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $isHome ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isLaptop ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>product/category/1">Laptop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isPhone ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>product/category/2">Điện thoại</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $isAccessory ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>product/category/3">Linh kiện</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <form action="<?= BASE_URL ?>product/search" method="GET" class="d-flex mt-2 mt-md-0">
                    <div class="input-group" style="width: 250px;">
                        <input class="form-control rounded-start-pill border-end-0" type="search" name="keyword" placeholder="Bạn tìm gì..." aria-label="Search">
                        <button class="btn border border-start-0 rounded-end-pill bg-white text-primary btn-search-anim" type="submit">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <a href="<?= BASE_URL ?>cart" id="cart-icon-link" class="text-white position-relative fs-5 text-decoration-none cart-anim">
                    <i class="fa-solid fa-cart-shopping me-1"></i>
                    <span id="cart-badge" class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger"
                          style="font-size: 0.6rem; left: 10px !important; <?= (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) ? 'display:none;' : '' ?>">
                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                    </span>
                </a>

                <!-- Toggle Sáng/Tối -->
                <button id="darkModeToggle" class="btn btn-sm text-white border-0 fs-5" title="Chế độ sáng/tối" style="background:none;">
                    <i class="fa-solid fa-moon"></i>
                </button>

                <!-- Ngôn ngữ -->
                <div class="dropdown">
                    <button class="btn btn-sm text-white border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background:none;">
                        <i class="fa-solid fa-globe me-1"></i><span id="currentLang">VI</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:100px;">
                        <li><a class="dropdown-item lang-option" href="#" data-lang="VI"><img src="https://flagcdn.com/w20/vn.png" class="me-2">Tiếng Việt</a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="EN"><img src="https://flagcdn.com/w20/gb.png" class="me-2">English</a></li>
                    </ul>
>>>>>>> b7f9bc1aad5e0bb2e8c46cd310269574efa9718f
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-login-header dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-user me-1"></i> <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <li>
                                    <a class="dropdown-item fw-bold text-primary" href="<?= BASE_URL ?>admin">
                                        <i class="fa-solid fa-gauge me-2"></i>Trang quản trị
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>auth/profile"><i class="fa-solid fa-user-pen me-2"></i>Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fa-solid fa-box me-2"></i>Đơn hàng của tôi</a></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout">Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>auth/login" class="btn btn-login-header">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container my-4" style="min-height: 60vh;">
