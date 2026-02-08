<?php
// Load Language Helper
require_once dirname(__DIR__) . '/../core/Language.php';
$lang = Language::getInstance();
$currentLang = $lang->getCurrentLang();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'TechSmart' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dark-mode.css?v=<?= time() ?>">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center me-4" href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>img/logo_white.png" alt="Logo" class="img-fluid me-2" style="max-height: 40px;">
                TECHSMART
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

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
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>product/category/4">
                                <i class="fa-solid fa-tablet-screen-button text-muted" style="width: 20px;"></i> <span>Máy tính bảng</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= BASE_URL ?>product/category/5">
                                <i class="fa-solid fa-headphones text-muted" style="width: 20px;"></i> <span>Phụ kiện & Âm thanh</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
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
                                    <li><hr class="dropdown-divider"></li>
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
                    
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4" style="min-height: 60vh;">