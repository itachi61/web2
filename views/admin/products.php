<?php if (!isAdmin()) die("Access Denied"); 
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
}
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form -->
    <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow h-fit sticky top-20">
        <h3 class="text-xl font-bold mb-4 border-b pb-2"><?php echo $edit_product ? 'Sửa sản phẩm' : 'Thêm mới'; ?></h3>
        <form action="actions/process.php" method="POST" class="space-y-4">
            <?php if($edit_product): ?><input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>"><?php endif; ?>
            
            <input type="text" name="name" placeholder="Tên sản phẩm" required class="w-full p-2 border rounded" value="<?php echo $edit_product['name'] ?? ''; ?>">
            <input type="number" name="price" placeholder="Giá (VNĐ)" required class="w-full p-2 border rounded" value="<?php echo $edit_product['price'] ?? ''; ?>">
            <input type="text" name="category" placeholder="Danh mục" required class="w-full p-2 border rounded" value="<?php echo $edit_product['category'] ?? ''; ?>">
            <input type="number" name="stock" placeholder="Số lượng kho" required class="w-full p-2 border rounded" value="<?php echo $edit_product['stock'] ?? ''; ?>">
            <input type="text" name="image" placeholder="URL Hình ảnh" required class="w-full p-2 border rounded" value="<?php echo $edit_product['image'] ?? ''; ?>">
            <textarea name="description" placeholder="Mô tả chi tiết" class="w-full p-2 border rounded h-24"><?php echo $edit_product['description'] ?? ''; ?></textarea>
            
            <button type="submit" name="save_product" class="w-full bg-indigo-600 text-white py-2 rounded font-bold hover:bg-indigo-700 transition">Lưu sản phẩm</button>
            <?php if($edit_product): ?>
                <a href="index.php?page=admin_products" class="block text-center mt-2 text-gray-500 hover:text-gray-700">Hủy bỏ</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- List -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b bg-gray-50 font-bold text-gray-700">Danh sách sản phẩm</div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-3">ID</th>
                        <th class="p-3">Hình</th>
                        <th class="p-3">Tên</th>
                        <th class="p-3">Giá</th>
                        <th class="p-3">Kho</th>
                        <th class="p-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $prods = $conn->query("SELECT * FROM products ORDER BY id DESC");
                    while($p = $prods->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50 last:border-0">
                        <td class="p-3 text-gray-500">#<?php echo $p['id']; ?></td>
                        <td class="p-3"><img src="<?php echo $p['image']; ?>" class="w-10 h-10 object-cover rounded"></td>
                        <td class="p-3 font-semibold text-indigo-700"><?php echo $p['name']; ?></td>
                        <td class="p-3 text-red-600 font-bold"><?php echo formatCurrency($p['price']); ?></td>
                        <td class="p-3"><?php echo $p['stock']; ?></td>
                        <td class="p-3 flex gap-2">
                            <a href="index.php?page=admin_products&edit=<?php echo $p['id']; ?>" class="text-blue-500 hover:bg-blue-100 p-1 rounded"><i data-lucide="edit" class="w-4 h-4"></i></a>
                            <a href="actions/process.php?action=delete_product&id=<?php echo $p['id']; ?>" onclick="return confirm('Chắc chắn xóa?')" class="text-red-500 hover:bg-red-100 p-1 rounded"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>