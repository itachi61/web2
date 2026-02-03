<?php
// Admin Layout Template
$active = $data['active'] ?? 'dashboard';
$title = $data['title'] ?? 'Admin Panel';
$currentLang = getCurrentLang();
$isDark = $_COOKIE['admin_theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" data-theme="<?= $isDark ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - TechSmart Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #0d6efd;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --body-bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-color: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --topbar-bg: #ffffff;
        }
        
        [data-theme="dark"] {
            --body-bg: #0f172a;
            --card-bg: #1e293b;
            --text-color: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --topbar-bg: #1e293b;
        }
        
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-color);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .sidebar-brand i {
            color: #60a5fa;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 15px 0;
            margin: 0;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            margin: 2px 8px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: var(--sidebar-hover);
            color: white;
        }
        
        .sidebar-menu li a.active {
            background: var(--primary-color);
        }
        
        .sidebar-menu li a i {
            width: 18px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 10px 15px;
        }
        
        .sidebar-section-title {
            padding: 8px 20px;
            font-size: 0.7rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            letter-spacing: 1px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            max-width: calc(100vw - var(--sidebar-width));
        }
        
        /* Top Bar */
        .topbar {
            background: var(--topbar-bg);
            padding: 12px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .topbar-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-color);
        }
        
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Content Area */
        .content-area {
            padding: 20px;
        }
        
        /* Cards for dark theme */
        [data-theme="dark"] .card,
        [data-theme="dark"] .stat-card {
            background: var(--card-bg);
            border-color: var(--border-color);
        }
        
        [data-theme="dark"] .table {
            color: var(--text-color);
        }
        
        [data-theme="dark"] .table-light {
            background: var(--sidebar-hover);
            color: var(--text-color);
        }
        
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-color);
        }
        
        [data-theme="dark"] .modal-content {
            background: var(--card-bg);
            color: var(--text-color);
        }
        
        /* Theme toggle button */
        .theme-toggle, .lang-toggle {
            background: none;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .theme-toggle:hover, .lang-toggle:hover {
            background: var(--sidebar-hover);
            color: white;
        }
        
        /* Cards */
        .stat-card {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                max-width: 100vw;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-bolt me-2"></i> TechSmart
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="<?= BASE_URL ?>admin" class="<?= $active === 'dashboard' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span><?= __('dashboard') ?></span>
                </a>
            </li>
            
            <div class="sidebar-section-title"><?= __('management') ?></div>
            
            <li>
                <a href="<?= BASE_URL ?>admin/orders" class="<?= $active === 'orders' ? 'active' : '' ?>">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span><?= __('orders') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>admin/products" class="<?= $active === 'products' ? 'active' : '' ?>">
                    <i class="fa-solid fa-box"></i>
                    <span><?= __('products') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>admin/categories" class="<?= $active === 'categories' ? 'active' : '' ?>">
                    <i class="fa-solid fa-folder"></i>
                    <span><?= __('categories') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>admin/users" class="<?= $active === 'users' ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i>
                    <span><?= __('users') ?></span>
                </a>
            </li>
            
            <div class="sidebar-section-title"><?= __('inventory') ?></div>
            
            <li>
                <a href="<?= BASE_URL ?>admin/imports" class="<?= $active === 'imports' ? 'active' : '' ?>">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span><?= __('imports') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>admin/inventory" class="<?= $active === 'inventory' ? 'active' : '' ?>">
                    <i class="fa-solid fa-warehouse"></i>
                    <span><?= __('stock') ?></span>
                </a>
            </li>
            
            <div class="sidebar-divider"></div>
            
            <li>
                <a href="<?= BASE_URL ?>">
                    <i class="fa-solid fa-store"></i>
                    <span><?= __('view_store') ?></span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>auth/logout" class="text-danger">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span><?= __('logout') ?></span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-title">
                <button class="btn btn-sm d-lg-none me-2" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <?= $title ?>
            </div>
            <div class="topbar-user">
                <!-- Language Toggle -->
                <a href="<?= BASE_URL ?>home/setLanguage/<?= $currentLang === 'vi' ? 'en' : 'vi' ?>?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                   class="lang-toggle" title="<?= __('change_language') ?>">
                    <i class="fa-solid fa-globe me-1"></i>
                    <?= strtoupper($currentLang) ?>
                </a>
                
                <!-- Theme Toggle -->
                <button class="theme-toggle" id="themeToggle" title="<?= __('change_theme') ?>">
                    <i class="fa-solid fa-moon"></i>
                </button>
                
                <span class="text-muted d-none d-md-inline"><?= __('hello') ?>,</span>
                <strong><?= $_SESSION['name'] ?? 'Admin' ?></strong>
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                     style="width: 32px; height: 32px; font-size: 0.8rem;">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i><?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i><?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Dynamic Content -->
            <?php 
            if (isset($data['view'])) {
                include dirname(__DIR__) . '/' . $data['view'] . '.php';
            }
            ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="confirmModalTitle">
                        <i class="fa-solid fa-circle-question text-primary me-2"></i>Xác nhận
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0" id="confirmModalMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmModalBtn">
                        <i class="fa-solid fa-check me-1"></i>Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        // Load saved theme
        const savedTheme = localStorage.getItem('adminTheme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
        
        themeToggle?.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('adminTheme', newTheme);
            updateThemeIcon(newTheme);
        });
        
        function updateThemeIcon(theme) {
            const icon = themeToggle?.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }
        
        // Sidebar Toggle for Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Confirmation Modal Function
        let pendingForm = null;
        let pendingHref = null;
        
        function showConfirmModal(options) {
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            const title = document.getElementById('confirmModalTitle');
            const message = document.getElementById('confirmModalMessage');
            const btn = document.getElementById('confirmModalBtn');
            
            // Set content
            title.innerHTML = `<i class="fa-solid ${options.icon || 'fa-circle-question'} text-${options.type || 'primary'} me-2"></i>${options.title || 'Xác nhận'}`;
            message.textContent = options.message || 'Bạn có chắc chắn muốn thực hiện hành động này?';
            btn.className = `btn btn-${options.type || 'primary'}`;
            btn.innerHTML = `<i class="fa-solid ${options.btnIcon || 'fa-check'} me-1"></i>${options.btnText || 'Xác nhận'}`;
            
            // Store pending action
            pendingForm = options.form || null;
            pendingHref = options.href || null;
            
            modal.show();
        }
        
        // Handle confirm button click
        document.getElementById('confirmModalBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (pendingForm) {
                pendingForm.submit();
            } else if (pendingHref) {
                window.location.href = pendingHref;
            }
            bootstrap.Modal.getInstance(document.getElementById('confirmModal'))?.hide();
        });
        
        // Auto-bind confirm links (data-confirm without data-confirm-form)
        document.querySelectorAll('[data-confirm]:not([data-confirm-form])').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                showConfirmModal({
                    title: this.dataset.confirmTitle || 'Xác nhận',
                    message: this.dataset.confirm,
                    href: this.href,
                    type: this.dataset.confirmType || 'primary',
                    icon: this.dataset.confirmIcon || 'fa-circle-question',
                    btnText: this.dataset.confirmBtn || 'Xác nhận',
                    btnIcon: this.dataset.confirmBtnicon || 'fa-check'
                });
            });
        });
        
        // Auto-bind form confirm buttons (data-confirm-form)
        document.querySelectorAll('[data-confirm-form]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                showConfirmModal({
                    title: this.dataset.confirmTitle || 'Xác nhận',
                    message: this.dataset.confirm,
                    form: this.closest('form'),
                    type: this.dataset.confirmType || 'primary',
                    icon: this.dataset.confirmIcon || 'fa-circle-question',
                    btnText: this.dataset.confirmBtn || 'Xác nhận',
                    btnIcon: this.dataset.confirmBtnicon || 'fa-check'
                });
            });
        });
    </script>
</body>
</html>

