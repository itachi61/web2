<?php
$id = intval($_GET['id']);
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$product) { echo "<div class='text-center p-10'>Sản phẩm không tồn tại</div>"; return; }
$rating_info = getProductRating($conn, $id);
?>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col md:flex-row mb-8">
    <div class="md:w-1/2 h-96 bg-gray-100 relative">
        <img src="<?php echo $product['image']; ?>" class="w-full h-full object-cover">
        <?php if($product['stock'] == 0): ?>
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-bold text-2xl">HẾT HÀNG</div>
        <?php endif; ?>
    </div>
    <div class="md:w-1/2 p-8 flex flex-col justify-center">
        <span class="text-indigo-600 font-bold uppercase"><?php echo $product['category']; ?></span>
        <h1 class="text-4xl font-extrabold text-gray-900 mt-2 mb-2"><?php echo $product['name']; ?></h1>
        <div class="flex items-center gap-2 mb-4">
            <?php echo renderStars(round($rating_info['avg'])); ?>
            <span class="text-sm text-gray-500">(<?php echo $rating_info['count']; ?> đánh giá)</span>
        </div>
        <div class="text-3xl font-bold text-red-600 mb-6"><?php echo formatCurrency($product['price']); ?></div>
        <p class="text-gray-600 mb-8 text-lg leading-relaxed"><?php echo $product['description']; ?></p>
        
        <div class="flex gap-4">
            <?php if($product['stock'] > 0): ?>
                <a href="actions/process.php?action=add_to_cart&id=<?php echo $product['id']; ?>" class="flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold text-center hover:bg-indigo-700 shadow-lg">Thêm vào giỏ</a>
            <?php else: ?>
                <button disabled class="flex-1 bg-gray-400 text-white py-3 rounded-lg font-bold cursor-not-allowed">Hết hàng</button>
            <?php endif; ?>
            <a href="index.php" class="py-3 px-6 border-2 border-gray-200 rounded-lg text-gray-500 hover:border-indigo-600 hover:text-indigo-600">Quay lại</a>
        </div>
        <div class="mt-4 text-sm text-gray-500">Tồn kho: <?php echo $product['stock']; ?></div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-2xl font-bold mb-6 flex items-center gap-2"><i data-lucide="message-square"></i> Đánh giá sản phẩm</h3>
    
    <div class="bg-gray-50 p-6 rounded-lg mb-8">
        <?php if (isLoggedIn()): ?>
            <form action="actions/process.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                <div class="mb-4">
                    <label class="block font-medium mb-2">Đánh giá của bạn:</label>
                    <div class="flex gap-4">
                        <?php for($i=5; $i>=1; $i--): ?>
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="radio" name="rating" value="<?php echo $i; ?>" class="accent-indigo-600" <?php if($i==5) echo 'checked'; ?>> 
                                <span class="font-bold"><?php echo $i; ?> sao</span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <textarea name="comment" required class="w-full p-3 border rounded-lg mb-4 h-24 focus:ring-2 focus:ring-indigo-300 outline-none" placeholder="Nhập cảm nhận của bạn..."></textarea>
                <button type="submit" name="submit_review" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Gửi đánh giá</button>
            </form>
        <?php else: ?>
            <p class="text-gray-600">Vui lòng <a href="index.php?page=auth" class="text-indigo-600 font-bold hover:underline">đăng nhập</a> để viết đánh giá.</p>
        <?php endif; ?>
    </div>
    
    <div class="space-y-6">
        <?php
        $reviews = $conn->query("SELECT r.*, u.fullname FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id=$id ORDER BY r.created_at DESC");
        if ($reviews->num_rows > 0):
            while($rv = $reviews->fetch_assoc()): ?>
                <div class="border-b pb-4 last:border-0">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold">
                                <?php echo substr($rv['fullname'], 0, 1); ?>
                            </div>
                            <div>
                                <div class="font-bold"><?php echo $rv['fullname']; ?></div>
                                <div class="text-xs text-gray-400"><?php echo date("d/m/Y H:i", strtotime($rv['created_at'])); ?></div>
                            </div>
                        </div>
                        <div><?php echo renderStars($rv['rating']); ?></div>
                    </div>
                    <p class="mt-3 text-gray-700 ml-13 pl-13"><?php echo nl2br(htmlspecialchars($rv['comment'])); ?></p>
                </div>
            <?php endwhile;
        else: echo "<p class='text-gray-500 italic'>Chưa có đánh giá nào.</p>"; endif; ?>
    </div>
</div>