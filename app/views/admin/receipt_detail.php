<?php if (isset($_SESSION['import_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-check-circle me-2"></i><?= $_SESSION['import_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['import_success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['import_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa-solid fa-exclamation-circle me-2"></i><?= $_SESSION['import_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['import_error']); ?>
<?php endif; ?>

<?php $receipt = $data['receipt']; $isDraft = ($receipt['status'] == 'draft'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= BASE_URL ?>admin/import" class="text-muted text-decoration-none small">
            <i class="fa-solid fa-arrow-left me-1"></i>Danh sách phiếu nhập
        </a>
        <h2 class="fw-bold text-primary mt-1">
            <i class="fa-solid fa-file-invoice me-2"></i><?= htmlspecialchars($receipt['receipt_code']) ?>
            <?php if ($isDraft): ?>
                <span class="badge bg-warning text-dark fs-6 ms-2"><i class="fa-solid fa-pen me-1"></i>Nháp</span>
            <?php else: ?>
                <span class="badge bg-success fs-6 ms-2"><i class="fa-solid fa-check me-1"></i>Hoàn thành</span>
            <?php endif; ?>
        </h2>
    </div>
    <?php if ($isDraft): ?>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>admin/completeReceipt/<?= $receipt['id'] ?>" 
           class="btn btn-success btn-lg"
           onclick="return confirm('Hoàn thành phiếu nhập? Tồn kho & giá sẽ được cập nhật cho tất cả SP trong phiếu. Không thể hoàn tác!')">
            <i class="fa-solid fa-check-circle me-2"></i>Hoàn thành phiếu
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Thông tin phiếu -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <small class="text-muted">Người tạo</small>
            <strong><?= htmlspecialchars($receipt['created_by_name'] ?? '') ?></strong>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <small class="text-muted">Ngày tạo</small>
            <strong><?= date('d/m/Y H:i', strtotime($receipt['created_at'])) ?></strong>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <small class="text-muted">Số sản phẩm</small>
            <strong class="text-primary fs-4"><?= count($data['items']) ?></strong>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <small class="text-muted">Tổng tiền nhập</small>
            <strong class="text-danger fs-5"><?= number_format($receipt['total_amount'], 0, ',', '.') ?>đ</strong>
        </div>
    </div>
</div>

<?php if ($receipt['note']): ?>
<div class="alert alert-info py-2 mb-3"><i class="fa-solid fa-note-sticky me-2"></i><strong>Ghi chú:</strong> <?= htmlspecialchars($receipt['note']) ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Form thêm SP (chỉ khi draft) -->
    <?php if ($isDraft): ?>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="fa-solid fa-plus me-2"></i>Thêm sản phẩm vào phiếu</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>admin/addReceiptItem/<?= $receipt['id'] ?>" method="POST">
                    <!-- Tìm kiếm sản phẩm -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fa-solid fa-box me-1 text-primary"></i>Chọn sản phẩm <span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" id="searchProduct" class="form-control border-start-0" placeholder="Nhập tên sản phẩm để tìm...">
                        </div>
                        <select name="product_id" id="productSelect" class="form-select border-2" required size="10" style="height: auto; font-size: 0.92rem;">
                            <?php foreach ($data['products'] as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> — Tồn: <?= $p['stock'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i>Nhấn chọn sản phẩm từ danh sách</small>
                            <small class="text-primary fw-bold" id="productCount"><?= count($data['products']) ?> SP</small>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- SL và Giá nhập nằm cùng dòng -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-cubes me-1 text-info"></i>Số lượng <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control form-control-lg text-center fw-bold" min="1" placeholder="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-tag me-1 text-success"></i>Giá nhập (đ) <span class="text-danger">*</span></label>
                            <input type="number" name="import_price" class="form-control form-control-lg text-center fw-bold" min="0" placeholder="0" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i>Thêm vào phiếu nhập
                    </button>
                </form>

                <script>
                (function() {
                    const search = document.getElementById('searchProduct');
                    const select = document.getElementById('productSelect');
                    const countEl = document.getElementById('productCount');
                    const options = Array.from(select.options);
                    search.addEventListener('input', function() {
                        const keyword = this.value.toLowerCase().trim();
                        select.innerHTML = '';
                        let count = 0;
                        options.forEach(opt => {
                            if (opt.text.toLowerCase().includes(keyword)) {
                                select.appendChild(opt.cloneNode(true));
                                count++;
                            }
                        });
                        countEl.textContent = count + ' SP';
                    });
                })();
                </script>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Danh sách SP trong phiếu -->
    <div class="<?= $isDraft ? 'col-lg-7' : 'col-12' ?>">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list me-2"></i>Sản phẩm trong phiếu</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">Sản phẩm</th>
                                <th width="10%">Tồn hiện tại</th>
                                <th width="12%">SL nhập</th>
                                <th width="15%">Giá nhập</th>
                                <th width="15%">Thành tiền</th>
                                <?php if ($isDraft): ?>
                                <th class="text-end" width="13%">Thao tác</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['items'])): ?>
                                <?php foreach ($data['items'] as $idx => $item): ?>
                                <tr>
                                    <td class="fw-bold text-muted"><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= BASE_URL ?>images/<?= $item['image'] ?? '' ?>" class="rounded me-2" 
                                                 style="width:35px;height:35px;object-fit:cover"
                                                 onerror="this.src='https://via.placeholder.com/35?text=No'">
                                            <span class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= $item['stock'] ?></span></td>
                                    <td class="fw-bold text-primary"><?= $item['quantity'] ?></td>
                                    <td class="text-info"><?= number_format($item['import_price'], 0, ',', '.') ?>đ</td>
                                    <td class="fw-bold"><?= number_format($item['quantity'] * $item['import_price'], 0, ',', '.') ?>đ</td>
                                    <?php if ($isDraft): ?>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editItem<?= $item['id'] ?>">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <a href="<?= BASE_URL ?>admin/removeReceiptItem/<?= $item['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Xóa sản phẩm này khỏi phiếu?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>

                                <?php if ($isDraft): ?>
                                <!-- Modal Sửa item -->
                                <div class="modal fade" id="editItem<?= $item['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark py-2">
                                                <h6 class="modal-title">Sửa: <?= htmlspecialchars($item['product_name']) ?></h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= BASE_URL ?>admin/updateReceiptItem" method="POST">
                                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Số lượng</label>
                                                        <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" min="1" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Giá nhập (đ)</label>
                                                        <input type="number" name="import_price" class="form-control" value="<?= $item['import_price'] ?>" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2">
                                                    <button type="submit" class="btn btn-warning btn-sm"><i class="fa-solid fa-save me-1"></i>Lưu</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $isDraft ? 7 : 6 ?>" class="text-center py-5">
                                        <i class="fa-solid fa-box-open fa-3x text-muted mb-2"></i>
                                        <p class="text-muted mt-2">Chưa có sản phẩm nào. Thêm sản phẩm ở bên trái.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($data['items'])): ?>
                        <tfoot class="table-warning fw-bold">
                            <tr>
                                <td colspan="<?= $isDraft ? 5 : 5 ?>" class="text-end">TỔNG CỘNG:</td>
                                <td class="text-danger fs-5"><?= number_format($receipt['total_amount'], 0, ',', '.') ?>đ</td>
                                <?php if ($isDraft): ?><td></td><?php endif; ?>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
