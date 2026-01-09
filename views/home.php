<?php
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM products WHERE name LIKE '%$search%' OR category LIKE '%$search%' ORDER BY id DESC";
$result = $conn->query($sql);
?>
<h2 class="text-2xl font-bold mb-6 text-gray-800 border-l-4 border-indigo-600 pl-3">
    <?php echo $search ? "Tìm kiếm: $search" : "Sản phẩm mới nhất"; ?>
</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden group">
            <a href="index.php?page=detail&id=<?php echo $row['id']; ?>" class="block h-48 overflow-hidden bg-gray-100">
                <img src="<?php echo $row['image']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            </a>
            <div class="p-4">
                <span class="text-xs text-indigo-600 font-bold uppercase"><?php echo $row['category']; ?></span>
                <h3 class="text-lg font-bold mt-1 truncate">
                    <a href="index.php?page=detail&id=<?php echo $row['id']; ?>" class="hover:text-indigo-600"><?php echo $row['name']; ?></a>
                </h3>
                <div class="flex justify-between items-center mt-3">
                    <span class="text-red-600 font-bold"><?php echo formatCurrency($row['price']); ?></span>
                    <?php if($row['stock'] > 0): ?>
                        <a href="actions/process.php?action=add_to_cart&id=<?php echo $row['id']; ?>" class="p-2 bg-indigo-100 text-indigo-600 rounded-full hover:bg-indigo-600 hover:text-white transition"><i data-lucide="shopping-cart" class="w-5 h-5"></i></a>
                    <?php else: ?>
                        <span class="text-xs text-gray-500 font-bold bg-gray-200 px-2 py-1 rounded">Hết hàng</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<?php if($result->num_rows == 0) echo "<p class='text-center text-gray-500 mt-10'>Không tìm thấy sản phẩm nào.</p>"; ?>