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
    <title><?= $data['title'] ?? 'TechSmart - ' . __('home') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dark-mode.css?v=<?= time() ?>">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL ?>img/logo_white.png"
                    alt="TechSmart Logo"
                    class="img-fluid"
                    style="max-height: 50px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                </img>
                TECHSMART
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>"><?= __('home') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>product/category/1"><?= __('laptop') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>product/category/2"><?= __('phones') ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>product/category/3"><?= __('components') ?></a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">

                    <!-- Search Form -->
                    <form action="<?= BASE_URL ?>product/search" method="GET" class="d-none d-md-flex">
                        <div class="input-group" style="width: 200px;">
                            <input class="form-control rounded-start-pill border-end-0" type="search" name="keyword" placeholder="<?= __('search_placeholder') ?>" aria-label="Search">
                            <button class="btn border border-start-0 rounded-end-pill bg-white text-primary btn-search-anim" type="submit">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Language Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm text-white border-0" type="button" data-bs-toggle="dropdown" title="<?= __('language') ?>">
                            <?php if ($currentLang === 'vi'): ?>
                                🇻🇳 VI
                            <?php else: ?>
                                🇺🇸 EN
                            <?php endif; ?>
                            <i class="fa-solid fa-caret-down ms-1 small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 120px;">
                            <li>
                                <a class="dropdown-item <?= $currentLang === 'vi' ? 'active' : '' ?>" href="?lang=vi" data-lang="vi">
                                    🇻🇳 Tiếng Việt
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $currentLang === 'en' ? 'active' : '' ?>" href="?lang=en" data-lang="en">
                                    🇺🇸 English
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button id="themeToggle" class="btn btn-sm text-white border-0 theme-toggle" title="<?= __('dark_mode') ?>">
                        <i class="fa-solid fa-moon"></i>
                        <span class="d-none d-lg-inline ms-1"><?= __('dark_mode') ?></span>
                    </button>

                    <!-- Cart -->
                    <a href="<?= BASE_URL ?>cart" class="text-white position-relative fs-5 text-decoration-none cart-anim" title="<?= __('cart') ?>">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                <?= count($_SESSION['cart']) ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <!-- User Menu -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-login-header dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-regular fa-user me-1"></i> <?= $_SESSION['name'] ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item fw-bold text-primary" href="<?= BASE_URL ?>admin"><i class="fa-solid fa-gauge me-2"></i><?= __('admin_panel') ?></a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>orders/history"><i class="fa-solid fa-clock-rotate-left me-2"></i><?= __('my_orders') ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout"><i class="fa-solid fa-right-from-bracket me-2"></i><?= __('logout') ?></a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>auth/login" class="btn btn-login-header"><?= __('login') ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4" style="min-height: 60vh;">