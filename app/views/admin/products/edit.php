<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">Chỉnh sửa sản phẩm</h2>
    <a href="<?= BASE_URL ?>admin/products" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card shadow border-0">
    <div class="card-body p-4">
        <form action="<?= BASE_URL ?>admin/updateProduct/<?= $data['product']['id'] ?>" method="POST" enctype="multipart/form-data">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['product']['name']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <?php if (!empty($data['categories'])): ?>
                                    <?php foreach ($data['categories'] as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $data['product']['category_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="priceInput" class="form-control" value="<?= $data['product']['price'] ?>" min="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Giảm giá (%)</label>
                            <input type="number" name="discount" id="discountInput" class="form-control" min="0" max="100" value="<?= $data['product']['discount'] ?? 0 ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Giá nhập (VNĐ)</label>
                            <input type="number" name="cost_price" id="costPriceInput" class="form-control" min="0" value="<?= $data['product']['cost_price'] ?? 0 ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold text-warning">% Lợi nhuận <span class="text-danger">*</span></label>
                            <input type="number" name="profit_margin" id="marginInput" class="form-control border-warning" step="0.1" min="0" value="<?= $data['product']['profit_margin'] ?? 0 ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">Lãi/SP</label>
                            <div class="form-control bg-light" style="cursor:default;">
                                <span class="text-success fw-bold" id="profitValue">0đ</span>
                            </div>
                        </div>
                    </div>
                    <script>
                    const _cost = document.getElementById('costPriceInput');
                    const _margin = document.getElementById('marginInput');
                    const _price = document.getElementById('priceInput');
                    const _discount = document.getElementById('discountInput');
                    const _profit = document.getElementById('profitValue');
                    const fmt = n => new Intl.NumberFormat('vi-VN').format(Math.round(n));

                    // Công thức: Giá bán = Giá nhập × (1 + LN%)
                    function calcFromMargin() {
                        const cost = parseFloat(_cost.value) || 0;
                        const margin = parseFloat(_margin.value) || 0;
                        const price = Math.round(cost * (1 + margin / 100));
                        _price.value = price;
                        updateProfitDisplay(cost, price);
                    }
                    function calcFromPrice() {
                        const cost = parseFloat(_cost.value) || 0;
                        const price = parseFloat(_price.value) || 0;
                        if (cost > 0) {
                            const margin = Math.round(((price / cost) - 1) * 100 * 10) / 10;
                            _margin.value = margin;
                        }
                        updateProfitDisplay(cost, price);
                    }
                    function updateProfitDisplay(cost, price) {
                        const discount = parseFloat(_discount.value) || 0;
                        const profit = price - cost;
                        const realProfit = price * (1 - discount / 100) - cost;
                        let text = fmt(profit) + 'đ';
                        if (discount > 0) text += ' (KM: ' + fmt(realProfit) + 'đ)';
                        _profit.textContent = text;
                    }
                    _cost.addEventListener('input', calcFromMargin);
                    _margin.addEventListener('input', calcFromMargin);
                    _price.addEventListener('input', calcFromPrice);
                    _discount.addEventListener('input', () => updateProfitDisplay(
                        parseFloat(_cost.value)||0, parseFloat(_price.value)||0
                    ));
                    // Init
                    calcFromPrice();
                    </script>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="8"><?= htmlspecialchars($data['product']['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Hình ảnh sản phẩm</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small text-muted">Ảnh đại diện hiện tại</label>
                                <div class="text-center border bg-white p-2 mb-2" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <?php if ($data['product']['image']): ?>
                                        <img src="<?= BASE_URL ?>images/<?= $data['product']['image'] ?>" 
                                             style="max-width: 100%; max-height: 200px;"
                                             onerror="this.src='https://via.placeholder.com/200?text=No+Image'">
                                    <?php else: ?>
                                        <span class="text-muted small">Chưa có ảnh</span>
                                    <?php endif; ?>
                                </div>
                                <label class="form-label small text-muted">Chọn ảnh mới (bỏ trống nếu giữ ảnh cũ)</label>
                                <input type="file" name="image" id="imageUpload" class="form-control mb-2">
                                
                                <div class="text-center border bg-white p-2" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <img id="imagePreview" src="" style="max-width: 100%; max-height: 100px; display: none;">
                                    <span id="placeholderText" class="text-muted small">Preview ảnh mới</span>
                                </div>
                            </div>

                            <hr>

                            <?php if (!empty($data['images'])): ?>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Ảnh phụ hiện tại</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($data['images'] as $img): ?>
                                            <img src="<?= BASE_URL ?>images/<?= $img['image_path'] ?>" 
                                                 class="rounded border" style="width: 60px; height: 60px; object-fit: cover;"
                                                 onerror="this.src='https://via.placeholder.com/60?text=X'">
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Thêm ảnh chi tiết mới</label>
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
                    <i class="fa-solid fa-save me-2"></i> Lưu thay đổi
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function calcProfit() {
    const price = parseFloat(document.getElementById('priceInput')?.value) || 0;
    const cost = parseFloat(document.getElementById('costPriceInput')?.value) || 0;
    const profit = price - cost;
    const pct = cost > 0 ? ((profit / cost) * 100).toFixed(1) : 0;
    const el = document.getElementById('profitValue');
    const elPct = document.getElementById('profitPercent');
    if (el) { el.textContent = profit.toLocaleString('vi-VN') + 'đ'; el.className = profit >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold'; }
    if (elPct) elPct.textContent = '(' + pct + '%)';
}
document.getElementById('priceInput')?.addEventListener('input', calcProfit);
document.getElementById('costPriceInput')?.addEventListener('input', calcProfit);
calcProfit();

document.getElementById('imageUpload').onchange = function (evt) {
    var files = evt.target.files;
    if (FileReader && files && files.length) {
        var fr = new FileReader();
        fr.onload = function () {
            document.getElementById('imagePreview').src = fr.result;
            document.getElementById('imagePreview').style.display = 'block';
            var ph = document.getElementById('placeholderText');
            if(ph) ph.style.display = 'none';
        }
        fr.readAsDataURL(files[0]);
    }
}
</script>
