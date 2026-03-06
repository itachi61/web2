<div class="container my-4">
    
    <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-3" style="font-size: 0.9rem;">
        <li class="breadcrumb-item">
            <a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a>
        </li>

        <?php if (isset($product)): ?>
            <li class="breadcrumb-item">
                <a href="<?= BASE_URL ?>product/category/<?= $product['category_id'] ?? 0 ?>" class="text-decoration-none text-muted">
                    <?= $product['category_name'] ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $product['name'] ?>
            </li>

        <?php else: ?>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $title ?? 'Sản phẩm' ?>
            </li>
            
        <?php endif; ?>
    </ol>
</nav>

    <div class="border-bottom pb-2 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h4 class="fw-bold mb-0">
            <?= $product['name'] ?>
            <span class="fs-6 fw-normal text-muted ms-2 bg-light px-2 py-1 rounded">Chính hãng VN/A</span>
        </h4>
        <div class="d-flex align-items-center">
            <span class="text-warning me-2">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star-half-stroke"></i>
            </span>
            <a href="#reviews" class="text-muted text-decoration-underline small">(<?= isset($reviews) ? count($reviews) : 0 ?> đánh giá)</a>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-7 mb-4">
            
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-2 text-center d-flex align-items-center justify-content-center position-relative" style="height: 450px; background: #fff;">
                    <img id="mainImage"
                        src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>"
                        class="img-fluid"
                        style="max-height: 100%; object-fit: contain; transition: 0.3s;"
                        alt="<?= $product['name'] ?>"
                        onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'">
                </div>
            </div>

            <div class="d-flex gap-2 overflow-auto mb-4 pb-2 custom-scrollbar">
                <div class="thumb-box active" onclick="changeMainImage(this, '<?= BASE_URL ?>public/images/<?= $product['image'] ?>')">
                    <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" class="img-fluid" alt="Thumb">
                </div>

                <?php if (isset($images) && count($images) > 0): ?>
                    <?php foreach ($images as $img): ?>
                        <div class="thumb-box" onclick="changeMainImage(this, '<?= BASE_URL ?>public/images/<?= $img['image_path'] ?>')">
                            <img src="<?= BASE_URL ?>public/images/<?= $img['image_path'] ?>" class="img-fluid" alt="Thumb">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="card border-0 bg-light shadow-sm mb-4 policy-box" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-gem me-2"></i> Ưu đãi & Chính sách</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-rotate-left text-danger fs-5 mt-1 w-25px text-center"></i><span class="small fw-medium">1 ĐỔI 1 trong 30 ngày nếu có lỗi NSX.</span></div></div>
                        <div class="col-md-6"><div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-shield-halved text-danger fs-5 mt-1 w-25px text-center"></i><span class="small fw-medium">Bảo hành chính hãng 12-24 tháng.</span></div></div>
                        <div class="col-md-6"><div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-truck-fast text-danger fs-5 mt-1 w-25px text-center"></i><span class="small fw-medium">Miễn phí vận chuyển toàn quốc.</span></div></div>
                        <div class="col-md-6"><div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-headset text-danger fs-5 mt-1 w-25px text-center"></i><span class="small fw-medium">Hỗ trợ kỹ thuật 24/7 trọn đời.</span></div></div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">Đặc điểm nổi bật</h5>
                <div class="text-secondary lh-lg" style="font-size: 0.95rem; text-align: justify;">
                    <?= nl2br($product['description']) ?>
                </div>
            </div>
            
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; position: sticky; top: 90px;">
                <div class="card-body p-4">
                    
                    <div class="d-flex align-items-end gap-3 mb-3 pb-3 border-bottom">
                        <h2 class="text-danger fw-bold mb-0"><?= number_format($product['price'], 0, ',', '.') ?>đ</h2>
                        <span class="text-decoration-line-through text-muted mb-1"><?= number_format($product['price'] * 1.1, 0, ',', '.') ?>đ</span>
                        <span class="badge bg-danger mb-2">-10%</span>
                    </div>

                    <div class="d-flex align-items-center py-2 px-3 mb-4 bg-danger bg-opacity-10 border border-danger rounded-3">
                        <i class="fa-solid fa-gift me-2 text-danger fs-5"></i>
                        <span class="text-danger fw-bold small">Thu cũ đổi mới: Trợ giá thêm đến 2.000.000đ</span>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2 small text-uppercase text-muted">Lựa chọn phiên bản</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-option active">Tiêu chuẩn</button>
                        </div>
                    </div>

                    <div class="border rounded-3 overflow-hidden mb-4 border-danger">
                        <div class="bg-danger text-white p-2 fw-bold text-center">
                            <i class="fa-solid fa-fire-flame-curved me-1"></i> KHUYẾN MÃI ĐẶC BIỆT
                        </div>
                        <div class="p-3 bg-white">
                            <ul class="mb-0 ps-3 small lh-lg">
                                <li>Giảm ngay <strong>300.000đ</strong> khi thanh toán qua ví VNPAY.</li>
                                <li>Tặng Balo Laptop TechSmart hoặc Ốp lưng thời trang.</li>
                                <li>Tặng Voucher giảm giá 10% (tối đa 500k) cho lần mua sau.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-add-cart w-100 h-100 py-3 rounded-3 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm">
                                    <i class="fa-solid fa-cart-plus mb-1 fs-5"></i>
                                    <span class="fw-bold small">THÊM VÀO GIỎ</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-danger w-100 h-100 py-3 rounded-3 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm">
                                    <strong class="text-uppercase mb-1">MUA NGAY</strong>
                                    <small style="font-size: 11px; font-weight: normal;">(Giao tận nơi nhanh chóng)</small>
                                </a>
                            </div>
                        </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5" id="reviews">
    <div class="row">
        <div class="col-12">
            <h4 class="border-bottom pb-2 mb-4 fw-bold border-primary border-3 border-start-0 border-end-0 border-top-0 d-inline-block">
                Đánh giá từ khách hàng
            </h4>

            <div class="card mb-5 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 bg-white rounded-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="<?= BASE_URL ?>product/postReview" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6 d-flex align-items-center">
                                    <label class="fw-bold me-3 text-nowrap">Bạn cảm thấy thế nào?</label>
                                    <select name="rating" class="form-select border-warning text-warning fw-bold shadow-none">
                                        <option value="5">⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                                        <option value="4">⭐⭐⭐⭐ (Tốt)</option>
                                        <option value="3">⭐⭐⭐ (Bình thường)</option>
                                        <option value="2">⭐⭐ (Tệ)</option>
                                        <option value="1">⭐ (Rất tệ)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea name="comment" class="form-control shadow-none bg-light" rows="3" placeholder="Xin mời chia sẻ cảm nhận chi tiết của bạn về sản phẩm..." required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">Gửi đánh giá</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa-regular fa-comment-dots fs-1 text-muted mb-3"></i>
                            <p class="mb-3">Vui lòng đăng nhập để gửi nhận xét của bạn về sản phẩm.</p>
                            <a href="<?= BASE_URL ?>auth/login" class="btn btn-outline-primary px-4 rounded-pill fw-bold">Đăng nhập ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 bg-white rounded-4">
                    <?php if (isset($reviews) && count($reviews) > 0): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="d-flex mb-4 border-bottom pb-4 last-no-border">
                                <div class="flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($review['fullname']) ?>&background=random&color=fff&rounded=true" class="shadow-sm" width="55" alt="Avatar">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1">
                                        <?= htmlspecialchars($review['fullname']) ?>
                                        <span class="badge bg-success bg-opacity-10 text-success ms-2 fw-normal" style="font-size: 0.7rem;"><i class="fa-solid fa-circle-check"></i> Đã mua hàng</span>
                                    </h6>
                                    <div class="text-warning small mb-2">
                                        <?php for ($i = 0; $i < $review['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                        <?php for ($i = $review['rating']; $i < 5; $i++) echo '<i class="fa-regular fa-star text-muted opacity-25"></i>'; ?>
                                    </div>
                                    <p class="mb-2 text-dark" style="font-size: 0.95rem;"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                    <small class="text-muted fst-italic"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50" alt="No reviews">
                            <p class="fst-italic mb-0">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function changeMainImage(element, src) {
        // Làm mờ ảnh chính 1 chút tạo hiệu ứng mượt
        let mainImg = document.getElementById('mainImage');
        mainImg.style.opacity = '0.5';
        
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        }, 150);

        // Xử lý viền xanh cho thumbnail
        document.querySelectorAll('.thumb-box').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    }
</script>