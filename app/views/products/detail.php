<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $product['name'] ?></li>
        </ol>
    </nav>

    <div class="border-bottom pb-2 mb-4 d-flex align-items-center justify-content-between">
        <h4 class="fw-bold mb-0">
            <?= $product['name'] ?>
            <span class="fs-6 fw-normal text-muted ms-2">Chính hãng VN/A</span>
        </h4>
        <div class="d-flex align-items-center">
            <span class="text-warning me-2">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star-half-stroke"></i>
            </span>
            <small class="text-muted text-decoration-underline">(<?= isset($reviews) ? count($reviews) : 0 ?> đánh giá)</small>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-2 text-center d-flex align-items-center justify-content-center" style="height: 400px; background: #fff;">
                    <img id="mainImage"
                        src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>"
                        class="img-fluid"
                        style="max-height: 100%; object-fit: contain;"
                        alt="<?= $product['name'] ?>"
                        onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'">
                </div>
            </div>

            <div class="d-flex gap-2 overflow-auto mb-4 pb-2">
                <div class="thumb-box active" onclick="changeMainImage(this, '<?= BASE_URL ?>public/images/<?= $product['image'] ?>')">
                    <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" class="img-fluid">
                </div>

                <?php if (isset($images) && count($images) > 0): ?>
                    <?php foreach ($images as $img): ?>
                        <div class="thumb-box" onclick="changeMainImage(this, '<?= BASE_URL ?>public/images/<?= $img['image_path'] ?>')">
                            <img src="<?= BASE_URL ?>public/images/<?= $img['image_path'] ?>" class="img-fluid">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="policy-box p-3 rounded-3 mb-3">
                <h6 class="fw-bold mb-3 text-danger">Ưu đãi & Chính sách</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-rotate text-danger mt-1"></i><span class="small">1 ĐỔI 1 trong 30 ngày nếu lỗi NSX.</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-shield-halved text-danger mt-1"></i><span class="small">Bảo hành chính hãng 12 tháng.</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-truck-fast text-danger mt-1"></i><span class="small">Miễn phí vận chuyển toàn quốc.</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-phone-volume text-danger mt-1"></i><span class="small">Hỗ trợ kỹ thuật 24/7.</span></div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="fw-bold border-bottom pb-2">Mô tả sản phẩm</h5>
                <p class="text-muted text-justify">
                    <?= nl2br($product['description']) ?>
                </p>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="d-flex align-items-end gap-3 mb-3">
                <h3 class="text-danger fw-bold mb-0"><?= number_format($product['price'], 0, ',', '.') ?>đ</h3>
                <span class="text-decoration-line-through text-muted"><?= number_format($product['price'] * 1.1, 0, ',', '.') ?>đ</span>
                <span class="badge bg-danger">-10%</span>
            </div>

            <div class="d-flex align-items-center py-2 px-3 mb-3 bg-danger bg-opacity-10 border border-danger rounded-3">
                <i class="fa-solid fa-gift me-2 text-danger"></i>
                <span class="text-danger fw-bold small">Thu cũ đổi mới: Trợ giá đến 2.000.000đ</span>
            </div>

            <div class="mb-3">
                <label class="fw-bold mb-2 small">Phiên bản</label>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-option active">Tiêu chuẩn</button>
                    <button class="btn btn-option">Cao cấp</button>
                </div>
            </div>

            <div class="border rounded-3 overflow-hidden mb-4">
                <div class="bg-danger text-white p-2 fw-bold">
                    <i class="fa-solid fa-fire me-1"></i> KHUYẾN MÃI HOT
                </div>
                <div class="p-3 bg-white">
                    <ul class="mb-0 ps-3 small">
                        <li class="mb-2">Giảm thêm 300K khi thanh toán qua VNPAY.</li>
                        <li class="mb-2">Tặng Voucher giảm giá 10% cho lần mua sau.</li>
                    </ul>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex gap-2 mb-2">
                    <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-add-cart w-50 py-2 rounded-3 d-flex align-items-center justify-content-center text-decoration-none fw-bold">
                        <i class="fa-solid fa-cart-plus me-2 fs-5"></i> Thêm vào giỏ
                    </a>
                    <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-danger w-50 py-2 rounded-3 d-flex flex-column align-items-center justify-content-center text-decoration-none">
                        <strong class="text-uppercase">MUA NGAY</strong>
                        <small style="font-size: 10px; font-weight: normal;">(Giao tận nơi ngay)</small>
                    </a>
                    <button class="btn btn-outline-danger py-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 50px;">
                        <i class="fa-regular fa-heart fs-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <h4 class="border-bottom pb-2 mb-4 fw-bold">Đánh giá từ khách hàng</h4>

            <div class="card mb-4 bg-light border-0">
                <div class="card-body">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="<?= BASE_URL ?>product/postReview" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <div class="d-flex align-items-center mb-3">
                                <label class="fw-bold me-3">Bạn cảm thấy thế nào?</label>
                                <select name="rating" class="form-select w-auto border-warning text-warning fw-bold">
                                    <option value="5">⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                                    <option value="4">⭐⭐⭐⭐ (Tốt)</option>
                                    <option value="3">⭐⭐⭐ (Bình thường)</option>
                                    <option value="2">⭐⭐ (Tệ)</option>
                                    <option value="1">⭐ (Rất tệ)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ cảm nhận chi tiết của bạn về sản phẩm..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <p class="mb-2">Bạn cần đăng nhập để viết đánh giá.</p>
                            <a href="<?= BASE_URL ?>auth/login" class="btn btn-outline-primary btn-sm">Đăng nhập ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($reviews) && count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="d-flex mb-3 border-bottom pb-3">
                        <div class="flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($review['fullname']) ?>&background=random&color=fff" class="rounded-circle shadow-sm" width="50" alt="User">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="fw-bold mb-1"><?= $review['fullname'] ?></h6>
                            <div class="text-warning small mb-1">
                                <?php for ($i = 0; $i < $review['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                <?php for ($i = $review['rating']; $i < 5; $i++) echo '<i class="fa-regular fa-star text-muted opacity-50"></i>'; ?>
                            </div>
                            <p class="mb-1 text-dark"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            <small class="text-muted fst-italic">Đăng ngày <?= date('d/m/Y', strtotime($review['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fa-regular fa-comment-dots fa-3x mb-3 opacity-50"></i>
                    <p>Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function changeMainImage(element, src) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-box').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    }
</script>