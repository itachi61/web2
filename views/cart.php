<h2 class="text-2xl font-bold mb-6 flex items-center gap-2"><i data-lucide="shopping-cart"></i> Giỏ hàng</h2>
<?php if (empty($_SESSION['cart'])): ?>
    <div class="text-center py-10 bg-white rounded shadow">
        <p class="text-gray-500 mb-4">Giỏ hàng của bạn đang trống.</p>
        <a href="index.php" class="text-indigo-600 font-bold hover:underline">Tiếp tục mua sắm</a>
    </div>
<?php else: ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 space-y-4">
            <?php 
            $total = 0;
            foreach ($_SESSION['cart'] as $pid => $qty):
                $p = $conn->query("SELECT * FROM products WHERE id=$pid")->fetch_assoc();
                $subtotal = $p['price'] * $qty;
                $total += $subtotal;
            ?>
            <div class="flex items-center justify-between border-b pb-4 last:border-0">
                <div class="flex items-center gap-4">
                    <img src="<?php echo $p['image']; ?>" class="w-16 h-16 object-cover rounded border">
                    <div>
                        <h3 class="font-bold text-gray-800"><?php echo $p['name']; ?></h3>
                        <p class="text-sm text-gray-500"><?php echo formatCurrency($p['price']); ?> x <?php echo $qty; ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <span class="font-bold text-indigo-600"><?php echo formatCurrency($subtotal); ?></span>
                    <a href="actions/process.php?action=remove_cart&id=<?php echo $pid; ?>" class="text-red-400 hover:text-red-600 p-2"><i data-lucide="trash-2"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="bg-gray-50 p-6 flex justify-between items-center">
            <div class="text-xl font-bold">Tổng cộng: <span class="text-red-600 text-2xl"><?php echo formatCurrency($total); ?></span></div>
            <?php if (isLoggedIn()): ?>
                <form action="actions/process.php" method="POST">
                    <button type="submit" name="checkout" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 shadow-lg transition">Thanh toán</button>
                </form>
            <?php else: ?>
                <a href="index.php?page=auth" class="bg-gray-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-gray-700">Đăng nhập để thanh toán</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>