<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm mb-3">
            <img id="mainImage" 
                 src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" 
                 class="img-fluid rounded" 
                 style="width: 100%; object-fit: contain; max-height: 500px;"
                 alt="<?= $product['name'] ?>"
                 onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'">
        </div>

        <?php if (isset($images) && count($images) > 0): ?>
            <div class="d-flex gap-2 overflow-auto pb-2">
                <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" 
                     class="img-thumbnail cursor-pointer sub-image active-thumb"
                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid #0d6efd;"
                     onclick="changeMainImage(this)">

                <?php foreach($images as $img): ?>
                    <img src="<?= BASE_URL ?>public/images/<?= $img['image_path'] ?>" 
                         class="img-thumbnail cursor-pointer sub-image"
                         style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 1px solid #dee2e6;"
                         onclick="changeMainImage(this)">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-7">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
            </ol>
        </nav>

        <h2 class="fw-bold"><?= $product['name'] ?></h2>
        
        <div class="mb-3">
            <span class="text-warning">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star-half-stroke"></i>
            </span>
            <span class="text-muted ms-2">(<?= isset($reviews) ? count($reviews) : 0 ?> đánh giá)</span>
        </div>

        <h3 class="text-danger fw-bold my-3">
            <?= number_format($product['price'], 0, ',', '.') ?>đ
        </h3>

        <p class="text-muted">
            <?= nl2br($product['description']) ?>
        </p>

        <hr>

        <div class="d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-primary btn-lg px-4">
                <i class="fa-solid fa-cart-shopping me-2"></i> Thêm vào giỏ
            </a>
            <button class="btn btn-outline-secondary btn-lg"><i class="fa-regular fa-heart"></i></button>
        </div>

        <div class="mt-4">
            <div class="alert alert-success bg-light border-0">
                <i class="fa-solid fa-truck-fast text-success me-2"></i> Miễn phí vận chuyển toàn quốc
                <br>
                <i class="fa-solid fa-shield-halved text-success me-2"></i> Bảo hành chính hãng 12 tháng
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <h4 class="border-bottom pb-2 mb-4">Đánh giá từ khách hàng</h4>
        
        <div class="card mb-4 bg-light border-0">
            <div class="card-body">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="<?= BASE_URL ?>product/postReview" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn số sao:</label>
                            <select name="rating" class="form-select w-auto">
                                <option value="5">⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                                <option value="4">⭐⭐⭐⭐ (Tốt)</option>
                                <option value="3">⭐⭐⭐ (Bình thường)</option>
                                <option value="2">⭐⭐ (Tệ)</option>
                                <option value="1">⭐ (Rất tệ)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                <?php else: ?>
                    <p class="mb-0">Vui lòng <a href="<?= BASE_URL ?>auth/login">đăng nhập</a> để viết đánh giá.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($reviews) && count($reviews) > 0): ?>
            <?php foreach($reviews as $review): ?>
                <div class="d-flex mb-3 border-bottom pb-3">
                    <div class="flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($review['fullname']) ?>&background=random" class="rounded-circle" width="50" alt="User">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fw-bold mb-1"><?= $review['fullname'] ?></h6>
                        <div class="text-warning small mb-1">
                            <?php for($i=0; $i<$review['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                        </div>
                        <p class="mb-1"><?= $review['comment'] ?></p>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted fst-italic">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function changeMainImage(element) {
        document.getElementById('mainImage').src = element.src;
        
        let thumbs = document.querySelectorAll('.sub-image');
        thumbs.forEach(img => {
            img.style.border = '1px solid #dee2e6'; // Reset viền
        });
        
        element.style.border = '2px solid #0d6efd'; // Active viền xanh
    }
</script>