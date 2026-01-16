<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Quản lý Sản phẩm</h2>
    <a href="<?= BASE_URL ?>admin/addProduct" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="10%">Hình ảnh</th>
                        <th width="30%">Tên sản phẩm</th>
                        <th width="15%">Giá tiền</th>
                        <th width="15%">Tồn kho</th>
                        <th width="25%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['products'])): ?>
                        <?php foreach($data['products'] as $item): ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td>
                                    <img src="<?= BASE_URL . 'img/' . $item['image'] ?>" 
                                         width="50" height="50" class="rounded border" 
                                         style="object-fit: cover;"
                                         onerror="this.src='https://via.placeholder.com/50'">
                                </td>
                                <td class="fw-bold"><?= $item['name'] ?></td>
                                <td class="text-danger"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td>
                                    <?php if($item['stock'] > 0): ?>
                                        <span class="badge bg-success">Còn hàng (<?= $item['stock'] ?>)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Hết hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-warning me-2"><i class="fa-solid fa-pen"></i> Sửa</a>
                                    
                                    <a href="<?= BASE_URL ?>admin/deleteProduct/<?= $item['id'] ?>" 
                                       class="btn btn-sm btn-danger btn-delete" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có sản phẩm nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>