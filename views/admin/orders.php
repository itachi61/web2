<?php if (!isAdmin()) die("Access Denied"); ?>
<h2 class="text-2xl font-bold mb-6">Quản lý Đơn hàng</h2>
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4">ID</th>
                <th class="p-4">Khách hàng</th>
                <th class="p-4">Ngày đặt</th>
                <th class="p-4">Tổng tiền</th>
                <th class="p-4">Trạng thái</th>
                <th class="p-4">Cập nhật</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $orders = $conn->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY created_at DESC");
            while($o = $orders->fetch_assoc()):
            ?>
            <tr class="border-b hover:bg-gray-50 last:border-0">
                <td class="p-4 font-mono text-sm">#<?php echo $o['id']; ?></td>
                <td class="p-4 font-bold"><?php echo $o['username']; ?></td>
                <td class="p-4 text-sm text-gray-500"><?php echo date("d/m/Y H:i", strtotime($o['created_at'])); ?></td>
                <td class="p-4 text-red-600 font-bold"><?php echo formatCurrency($o['total_amount']); ?></td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded text-xs font-bold <?php echo $o['status']=='pending'?'bg-yellow-100 text-yellow-700':($o['status']=='shipped'?'bg-blue-100 text-blue-700':'bg-green-100 text-green-700'); ?>">
                        <?php echo ucfirst($o['status']); ?>
                    </span>
                </td>
                <td class="p-4">
                    <form action="actions/process.php" method="POST" class="flex gap-2">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status" class="border rounded text-sm p-1 outline-none focus:border-indigo-500">
                            <option value="pending" <?php if($o['status']=='pending') echo 'selected'; ?>>Pending</option>
                            <option value="shipped" <?php if($o['status']=='shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="delivered" <?php if($o['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                        </select>
                        <button type="submit" name="update_order" class="bg-indigo-600 text-white px-3 py-1 rounded text-xs hover:bg-indigo-700 font-bold">Lưu</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>