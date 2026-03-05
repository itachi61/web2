<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i><?= $_SESSION['success_msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['delete_warning'])): 
    $warn = $_SESSION['delete_warning'];
    unset($_SESSION['delete_warning']);
?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        <strong>Cảnh báo!</strong> Sản phẩm "<strong><?= htmlspecialchars($warn['product_name']) ?></strong>" 
        còn <strong><?= $warn['stock'] ?></strong> sản phẩm tồn kho.
        <br>
        <form action="<?= BASE_URL ?>admin/deleteProduct/<?= $warn['product_id'] ?>" method="POST" class="d-inline mt-2">
            <input type="hidden" name="force_delete" value="1">
            <button type="submit" class="btn btn-danger btn-sm mt-2">
                <i class="fa-solid fa-trash me-1"></i>Xác nhận xóa
            </button>
            <button type="button" class="btn btn-secondary btn-sm mt-2" data-bs-dismiss="alert">Hủy</button>
        </form>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-box-open me-2"></i>Quản lý Sản phẩm</h2>
    <a href="<?= BASE_URL ?>admin/addProduct" class="btn btn-primary shadow-sm">
        <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="10%">Hình ảnh</th>
                        <th width="25%">Tên sản phẩm</th>
                        <th width="15%">Giá tiền</th>
                        <th width="10%">Đã bán</th>
                        <th width="15%">Trạng thái</th>
                        <th class="text-end pe-4" width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['products'])): ?>
                        <?php foreach ($data['products'] as $item): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $item['id'] ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>">

                                        <img src="<?= BASE_URL ?>images/<?= $item['image'] ?>"
                                            class="img-fluid rounded border shadow-sm"
                                            style="width: 60px; height: 60px; object-fit: cover;"
                                            alt="<?= $item['name'] ?>"
                                            onerror="this.src='https://via.placeholder.com/60?text=No+Img'">
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= $item['name'] ?></span>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?= number_format($item['price'], 0, ',', '.') ?>đ
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info px-2 rounded-pill">
                                        <?= $item['sold_count'] ?? 0 ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['stock'] > 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-3 rounded-pill">
                                            Còn hàng (<?= $item['stock'] ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 rounded-pill">
                                            Hết hàng
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_URL ?>admin/editProduct/<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning me-2">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form action="<?= BASE_URL ?>admin/deleteProduct/<?= $item['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" alt="Empty" width="100">
                                <p class="text-muted mt-2">Chưa có sản phẩm nào trong kho.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>