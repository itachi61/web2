<?php if (!isAdmin()) die("Access Denied"); 
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
}
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-white p-6 rounded shadow">
        <h3 class="font-bold mb-4"><?php echo $edit_product ? 'Sửa SP' : 'Thêm SP'; ?></h3>
        <!-- Form trỏ về actions/process.php -->
        <form action="actions/process.php" method="POST" class="space-y-4">
            <?php if($edit_product): ?><input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>"><?php endif; ?>
            <input type="text" name="name" placeholder="Tên" required class="w-full p-2 border rounded" value="<?php echo $edit_product['name'] ?? ''; ?>">
            <input type="number" name="price" placeholder="Giá" required class="w-full p-2 border rounded" value="<?php echo $edit_product['price'] ?? ''; ?>">
            <input type="text" name="category" placeholder="Loại" required class="w-full p-2 border rounded" value="<?php echo $edit_product['category'] ?? ''; ?>">
            <input type="number" name="stock" placeholder="Kho" required class="w-full p-2 border rounded" value="<?php echo $edit_product['stock'] ?? ''; ?>">
            <input type="text" name="image" placeholder="Link ảnh" required class="w-full p-2 border rounded" value="<?php echo $edit_product['image'] ?? ''; ?>">
            <textarea name="description" placeholder="Mô tả" class="w-full p-2 border rounded h-24"><?php echo $edit_product['description'] ?? ''; ?></textarea>
            <button type="submit" name="save_product" class="w-full bg-indigo-600 text-white py-2 rounded font-bold">Lưu</button>
        </form>
    </div>
    <!-- List sản phẩm (Giữ nguyên logic hiển thị) -->
    <div class="lg:col-span-2 bg-white rounded shadow p-4">
        <table class="w-full">
            <thead><tr class="text-left bg-gray-100"><th>ID</th><th>Tên</th><th>Giá</th><th>Kho</th><th>Action</th></tr></thead>
            <tbody>
                <?php $prods = $conn->query("SELECT * FROM products ORDER BY id DESC");
                while($p = $prods->fetch_assoc()): ?>
                <tr class="border-b">
                    <td class="p-2">#<?php echo $p['id']; ?></td>
                    <td class="p-2"><?php echo $p['name']; ?></td>
                    <td class="p-2"><?php echo formatCurrency($p['price']); ?></td>
                    <td class="p-2"><?php echo $p['stock']; ?></td>
                    <td class="p-2">
                        <a href="index.php?page=admin_products&edit=<?php echo $p['id']; ?>" class="text-blue-500">Sửa</a> |
                        <a href="actions/process.php?action=delete_product&id=<?php echo $p['id']; ?>" class="text-red-500" onclick="return confirm('Xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>