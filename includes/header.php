<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechSmart - Công nghệ đỉnh cao</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

<nav class="bg-indigo-600 text-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-2 text-xl font-bold">
            <i data-lucide="package" class="bg-white text-indigo-600 p-1 rounded-lg w-8 h-8"></i>
            TechSmart
        </a>

        <form action="index.php" method="GET" class="hidden md:flex flex-1 mx-8 relative">
            <input type="hidden" name="page" value="home">
            <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                   class="w-full px-4 py-2 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <button type="submit" class="absolute right-3 top-2.5 text-gray-500"><i data-lucide="search" class="w-5 h-5"></i></button>
        </form>

        <div class="flex items-center gap-6">
            <?php if (isLoggedIn()): ?>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline">Chào, <b><?php echo $_SESSION['fullname']; ?></b></span>
                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin_dashboard" class="hover:text-indigo-200" title="Quản trị"><i data-lucide="layout-dashboard"></i></a>
                    <?php endif; ?>
                    <a href="actions/process.php?action=logout" class="hover:text-red-300" title="Đăng xuất"><i data-lucide="log-out"></i></a>
                </div>
            <?php else: ?>
                <a href="index.php?page=auth" class="flex items-center gap-2 hover:text-indigo-200"><i data-lucide="user"></i> Đăng nhập</a>
            <?php endif; ?>
            
            <a href="index.php?page=cart" class="relative">
                <i data-lucide="shopping-cart"></i>
                <?php 
                $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                if ($cart_count > 0): ?>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</nav>

<main class="flex-grow container mx-auto px-4 py-8">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 animate-pulse">
            <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        </div>
    <?php endif; ?>