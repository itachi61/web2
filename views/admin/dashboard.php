<?php if (!isAdmin()) die("<div class='text-red-500 text-center p-10 font-bold'>Bạn không có quyền truy cập!</div>"); ?>

<?php
$revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
$order_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'] ?? 0;
$stock_count = $conn->query("SELECT SUM(stock) as total FROM products")->fetch_assoc()['total'] ?? 0;
?>
<div class="mb-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Tổng quan Quản trị</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-xl text-white shadow-lg">
            <h3 class="text-blue-100 font-semibold mb-2">Tổng doanh thu</h3>
            <div class="text-4xl font-bold"><?php echo formatCurrency($revenue); ?></div>
        </div>
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-6 rounded-xl text-white shadow-lg">
            <h3 class="text-green-100 font-semibold mb-2">Số đơn hàng</h3>
            <div class="text-4xl font-bold"><?php echo $order_count; ?></div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-red-600 p-6 rounded-xl text-white shadow-lg">
            <h3 class="text-orange-100 font-semibold mb-2">Tổng tồn kho</h3>
            <div class="text-4xl font-bold"><?php echo $stock_count; ?> <span class="text-lg font-normal">SP</span></div>
        </div>
    </div>
    
    <div class="flex gap-4">
        <a href="index.php?page=admin_products" class="flex items-center gap-2 px-6 py-4 bg-white border shadow rounded-xl hover:bg-gray-50 font-bold text-indigo-600 transition">
            <i data-lucide="package"></i> Quản lý Sản phẩm
        </a>
        <a href="index.php?page=admin_orders" class="flex items-center gap-2 px-6 py-4 bg-white border shadow rounded-xl hover:bg-gray-50 font-bold text-indigo-600 transition">
            <i data-lucide="shopping-bag"></i> Quản lý Đơn hàng
        </a>
    </div>
</div>