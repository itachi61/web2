<!-- Categories Management -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fa-solid fa-folder text-primary me-2"></i>Quản lý danh mục
    </h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fa-solid fa-plus me-2"></i>Thêm danh mục
    </button>
</div>

<div class="row g-4">
    <?php if (!empty($data['categories'])): ?>
        <?php foreach ($data['categories'] as $cat): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 70px; height: 70px;">
                        <i class="fa-solid <?= $cat['icon'] ?? 'fa-folder' ?> fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold"><?= htmlspecialchars($cat['name']) ?></h5>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($cat['description'] ?? 'Chưa có mô tả') ?></p>
                    <span class="badge bg-secondary"><?= $cat['product_count'] ?? 0 ?> sản phẩm</span>
                </div>
                <div class="card-footer bg-white border-0 text-center">
                    <button class="btn btn-sm btn-outline-primary me-1" 
                            onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name']) ?>', '<?= htmlspecialchars($cat['description'] ?? '') ?>')">
                        <i class="fa-solid fa-edit"></i> Sửa
                    </button>
                    <?php if (($cat['product_count'] ?? 0) == 0): ?>
                    <a href="<?= BASE_URL ?>admin/deleteCategory/<?= $cat['id'] ?>" 
                       class="btn btn-sm btn-outline-danger"
                       data-confirm="Xóa danh mục '<?= htmlspecialchars($cat['name']) ?>'?"
                       data-confirm-title="Xóa danh mục"
                       data-confirm-type="danger"
                       data-confirm-icon="fa-trash"
                       data-confirm-btn="Xóa">
                        <i class="fa-solid fa-trash"></i> Xóa
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <i class="fa-solid fa-folder-open fa-4x text-muted opacity-50 mb-3"></i>
            <p class="text-muted">Chưa có danh mục nào</p>
        </div>
    <?php endif; ?>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>admin/createCategory" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-folder-plus text-primary me-2"></i>Thêm danh mục
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Font Awesome)</label>
                        <select name="icon" class="form-select">
                            <option value="fa-laptop">💻 Laptop</option>
                            <option value="fa-mobile-screen">📱 Điện thoại</option>
                            <option value="fa-microchip">🔌 Linh kiện</option>
                            <option value="fa-headphones">🎧 Phụ kiện</option>
                            <option value="fa-gamepad">🎮 Gaming</option>
                            <option value="fa-folder">📁 Khác</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>admin/updateCategory" method="POST">
                <input type="hidden" name="id" id="editCategoryId">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-edit text-primary me-2"></i>Sửa danh mục
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" id="editCategoryDesc" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i>Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, desc) {
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryDesc').value = desc;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>
