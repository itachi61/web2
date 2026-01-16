<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">Thêm sản phẩm mới</h2>
    <a href="<?= BASE_URL ?>admin/products" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card shadow border-0">
    <div class="card-body p-4">
        <form action="<?= BASE_URL ?>admin/store" method="POST" enctype="multipart/form-data">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ví dụ: iPhone 15 Pro Max" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <option value="1">Laptop</option>
                                <option value="2">Điện thoại</option>
                                <option value="3">Linh kiện</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="Ví dụ: 25000000" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="8" placeholder="Nhập thông số kỹ thuật, bài viết mô tả..."></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Hình ảnh sản phẩm</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small text-muted">Ảnh đại diện (Thumbnail)</label>
                                <input type="file" name="image" id="imageUpload" class="form-control mb-2" required>
                                
                                <div class="text-center border bg-white p-2" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <img id="imagePreview" src="https://via.placeholder.com/200?text=Preview" 
                                         style="max-width: 100%; max-height: 200px; display: none;">
                                    <span id="placeholderText" class="text-muted small">Chưa chọn ảnh</span>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Ảnh chi tiết (Chọn nhiều)</label>
                                <input type="file" name="extra_images[]" class="form-control" multiple>
                                <small class="text-muted fst-italic">* Giữ phím Ctrl để chọn nhiều ảnh</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fa-solid fa-save me-2"></i> Lưu sản phẩm
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.getElementById('imageUpload').onchange = function (evt) {
    var tgt = evt.target || window.event.srcElement,
        files = tgt.files;

    // FileReader support
    if (FileReader && files && files.length) {
        var fr = new FileReader();
        fr.onload = function () {
            var preview = document.getElementById('imagePreview');
            var placeholder = document.getElementById('placeholderText');
            
            preview.src = fr.result;
            preview.style.display = 'block';
            if(placeholder) placeholder.style.display = 'none';
        }
        fr.readAsDataURL(files[0]);
    }
}
</script>