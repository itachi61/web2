<?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i><?= $_SESSION['success_msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-exclamation-circle me-2"></i><?= $_SESSION['error_msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_msg']); ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-tags me-2"></i>Quản lý Danh mục</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCatModal">
        <i class="fa-solid fa-plus me-2"></i>Thêm danh mục
    </button>
</div>

<!-- Modal Thêm -->
<div class="modal fade" id="addCatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i>Thêm danh mục mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>admin/addCategory" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Tạo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4" width="8%">#</th>
                        <th width="25%">Tên danh mục</th>
                        <th width="20%">Slug</th>
                        <th width="25%">Mô tả</th>
                        <th width="10%">Số SP</th>
                        <th class="text-end pe-4" width="12%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['categories'])): ?>
                        <?php foreach ($data['categories'] as $cat): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $cat['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($cat['name']) ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($cat['slug'] ?? '') ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($cat['description'] ?? '—') ?></td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info px-2 rounded-pill">
                                        <?= $cat['product_count'] ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editCat<?= $cat['id'] ?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <a href="<?= BASE_URL ?>admin/deleteCategory/<?= $cat['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Xóa danh mục <?= htmlspecialchars($cat['name']) ?>?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal Sửa -->
                            <div class="modal fade" id="editCat<?= $cat['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i>Sửa danh mục</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= BASE_URL ?>admin/updateCategory/<?= $cat['id'] ?>" method="POST">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Tên danh mục</label>
                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Mô tả</label>
                                                    <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-warning"><i class="fa-solid fa-save me-1"></i>Cập nhật</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-solid fa-tags fa-3x text-muted mb-2"></i>
                                <p class="text-muted mt-2">Chưa có danh mục nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
