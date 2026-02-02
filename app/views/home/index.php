<!-- ========== SECTION 1: HERO CAROUSEL ========== -->
<div id="heroCarousel" class="carousel slide mb-5 rounded-4 overflow-hidden shadow-lg" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="d-flex align-items-center justify-content-center text-white text-center p-5" 
                 style="min-height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div>
                    <span class="badge bg-danger mb-3 px-3 py-2"><?= getCurrentLang() === 'en' ? 'HOT DEAL' : 'SIÊU GIẢM GIÁ' ?></span>
                    <h1 class="display-4 fw-bold mb-3">iPhone 15 Pro Max</h1>
                    <p class="lead mb-4"><?= getCurrentLang() === 'en' ? 'Up to 20% off for new customers' : 'Giảm đến 20% cho khách hàng mới' ?></p>
                    <a href="<?= BASE_URL ?>product/category/2" class="btn btn-light btn-lg px-4 fw-bold">
                        <i class="fa-solid fa-shopping-bag me-2"></i><?= getCurrentLang() === 'en' ? 'Shop Now' : 'Mua Ngay' ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-flex align-items-center justify-content-center text-white text-center p-5" 
                 style="min-height: 400px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div>
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2"><?= getCurrentLang() === 'en' ? 'NEW ARRIVAL' : 'HÀNG MỚI VỀ' ?></span>
                    <h1 class="display-4 fw-bold mb-3">Gaming Laptop 2024</h1>
                    <p class="lead mb-4"><?= getCurrentLang() === 'en' ? 'RTX 4080 - Unleash the power' : 'RTX 4080 - Giải phóng sức mạnh' ?></p>
                    <a href="<?= BASE_URL ?>product/category/1" class="btn btn-light btn-lg px-4 fw-bold">
                        <i class="fa-solid fa-laptop me-2"></i><?= getCurrentLang() === 'en' ? 'Explore' : 'Khám Phá' ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-flex align-items-center justify-content-center text-white text-center p-5" 
                 style="min-height: 400px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div>
                    <span class="badge bg-info mb-3 px-3 py-2"><?= getCurrentLang() === 'en' ? 'FREE SHIPPING' : 'MIỄN PHÍ SHIP' ?></span>
                    <h1 class="display-4 fw-bold mb-3"><?= getCurrentLang() === 'en' ? 'Spring Tech Sale' : 'Sale Mùa Xuân' ?></h1>
                    <p class="lead mb-4"><?= getCurrentLang() === 'en' ? 'Free shipping for all orders' : 'Miễn phí vận chuyển toàn quốc' ?></p>
                    <a href="<?= BASE_URL ?>" class="btn btn-light btn-lg px-4 fw-bold">
                        <i class="fa-solid fa-gift me-2"></i><?= getCurrentLang() === 'en' ? 'Get Deals' : 'Nhận Ưu Đãi' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- ========== SECTION 2: CATEGORY GRID ========== -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <a href="<?= BASE_URL ?>product/category/1" class="text-decoration-none">
            <div class="card category-card border-0 shadow-sm h-100 overflow-hidden" 
                 style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body text-white text-center py-5">
                    <i class="fa-solid fa-laptop fa-4x mb-3 category-icon"></i>
                    <h3 class="fw-bold"><?= __('laptop') ?></h3>
                    <p class="mb-0 opacity-75"><?= getCurrentLang() === 'en' ? 'Gaming & Business' : 'Gaming & Văn phòng' ?></p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= BASE_URL ?>product/category/2" class="text-decoration-none">
            <div class="card category-card border-0 shadow-sm h-100 overflow-hidden" 
                 style="background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%);">
                <div class="card-body text-white text-center py-5">
                    <i class="fa-solid fa-mobile-screen fa-4x mb-3 category-icon"></i>
                    <h3 class="fw-bold"><?= __('phones') ?></h3>
                    <p class="mb-0 opacity-75"><?= getCurrentLang() === 'en' ? 'iPhone, Samsung, Xiaomi' : 'iPhone, Samsung, Xiaomi' ?></p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= BASE_URL ?>product/category/3" class="text-decoration-none">
            <div class="card category-card border-0 shadow-sm h-100 overflow-hidden" 
                 style="background: linear-gradient(135deg, #834d9b 0%, #d04ed6 100%);">
                <div class="card-body text-white text-center py-5">
                    <i class="fa-solid fa-microchip fa-4x mb-3 category-icon"></i>
                    <h3 class="fw-bold"><?= __('components') ?></h3>
                    <p class="mb-0 opacity-75"><?= getCurrentLang() === 'en' ? 'RAM, SSD, GPU' : 'RAM, SSD, Card đồ họa' ?></p>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- ========== SECTION 3: FLASH SALE ========== -->
<div class="card border-0 shadow-sm mb-5 overflow-hidden">
    <div class="card-header py-3 text-white d-flex justify-content-between align-items-center" 
         style="background: linear-gradient(90deg, #ff416c 0%, #ff4b2b 100%);">
        <h4 class="mb-0 fw-bold">
            <i class="fa-solid fa-bolt me-2"></i>
            <?= getCurrentLang() === 'en' ? 'FLASH SALE' : 'FLASH SALE' ?>
        </h4>
        <div class="d-flex align-items-center gap-2">
            <span class="opacity-75"><?= getCurrentLang() === 'en' ? 'Ends in:' : 'Kết thúc sau:' ?></span>
            <div id="countdown" class="d-flex gap-1">
                <span class="badge bg-dark px-2 py-1" id="hours">02</span>:
                <span class="badge bg-dark px-2 py-1" id="minutes">45</span>:
                <span class="badge bg-dark px-2 py-1" id="seconds">30</span>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <?php 
            $flashProducts = array_slice($products ?? [], 0, 4);
            foreach($flashProducts as $product): 
            ?>
            <div class="col-6 col-lg-3">
                <div class="card product-card h-100 border-0 shadow-sm position-relative">
                    <span class="position-absolute top-0 start-0 badge bg-danger m-2 z-1">
                        <i class="fa-solid fa-fire me-1"></i>-15%
                    </span>
                    <div class="p-3 text-center bg-light" style="height: 180px;">
                        <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>">
                            <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" 
                                 class="img-fluid h-100" style="object-fit: contain;"
                                 onerror="this.src='https://via.placeholder.com/200'">
                        </a>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-2 text-truncate">
                            <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>" class="text-dark text-decoration-none">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-danger fw-bold"><?= number_format($product['price'] * 0.85, 0, ',', '.') ?>đ</span>
                            <small class="text-decoration-line-through text-muted"><?= number_format($product['price'], 0, ',', '.') ?>đ</small>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: <?= rand(50, 90) ?>%"></div>
                        </div>
                        <small class="text-muted"><?= getCurrentLang() === 'en' ? 'Sold' : 'Đã bán' ?>: <?= rand(20, 100) ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ========== SECTION 4: USP FEATURES ========== -->
<div class="row g-4 mb-5">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 usp-card">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 70px; height: 70px;">
                    <i class="fa-solid fa-truck-fast fa-2x text-primary"></i>
                </div>
                <h6 class="fw-bold"><?= getCurrentLang() === 'en' ? 'Free Shipping' : 'Miễn phí giao hàng' ?></h6>
                <small class="text-muted"><?= getCurrentLang() === 'en' ? 'Orders over 500K' : 'Đơn từ 500K' ?></small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 usp-card">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 70px; height: 70px;">
                    <i class="fa-solid fa-rotate-left fa-2x text-success"></i>
                </div>
                <h6 class="fw-bold"><?= getCurrentLang() === 'en' ? '30-Day Returns' : 'Đổi trả 30 ngày' ?></h6>
                <small class="text-muted"><?= getCurrentLang() === 'en' ? 'No questions asked' : 'Không cần lý do' ?></small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 usp-card">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 70px; height: 70px;">
                    <i class="fa-solid fa-shield-halved fa-2x text-warning"></i>
                </div>
                <h6 class="fw-bold"><?= getCurrentLang() === 'en' ? 'Official Warranty' : 'Bảo hành chính hãng' ?></h6>
                <small class="text-muted"><?= getCurrentLang() === 'en' ? '12-24 months' : '12-24 tháng' ?></small>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 usp-card">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 70px; height: 70px;">
                    <i class="fa-solid fa-headset fa-2x text-info"></i>
                </div>
                <h6 class="fw-bold"><?= getCurrentLang() === 'en' ? '24/7 Support' : 'Hỗ trợ 24/7' ?></h6>
                <small class="text-muted">Hotline: 1900 xxxx</small>
            </div>
        </div>
    </div>
</div>

<!-- ========== SECTION 5: FEATURED PRODUCTS ========== -->
<div id="products-list" class="d-flex flex-wrap justify-content-between align-items-center mb-4 border-bottom pb-2">
    <h3 class="fw-bold text-primary m-0">
        <i class="fa-solid fa-fire text-danger me-2"></i><?= __('featured_products') ?>
    </h3>
    <a href="<?= BASE_URL ?>product/category/1" class="btn btn-outline-primary btn-sm">
        <?= getCurrentLang() === 'en' ? 'View All' : 'Xem tất cả' ?> <i class="fa-solid fa-arrow-right ms-1"></i>
    </a>
</div>

<div class="row g-4 mb-5">
    <?php if (!empty($products)): ?>
        <?php foreach($products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100 shadow-sm border-0">
                    <div class="position-relative p-3 text-center bg-white rounded-top" style="height: 220px;">
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">-10%</span>
                        
                        <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>">
                            <img src="<?= BASE_URL ?>public/images/<?= $product['image'] ?>" 
                                 class="img-fluid h-100" 
                                 style="object-fit: contain; transition: transform 0.3s;" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 onmouseover="this.style.transform='scale(1.05)'"
                                 onmouseout="this.style.transform='scale(1)'"
                                 onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                        </a>
                    </div>
                    
                    <div class="card-body d-flex flex-column border-top bg-light bg-opacity-10">
                        <h6 class="card-title mb-2" style="height: 40px; overflow: hidden; line-height: 1.4;">
                            <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>" class="text-dark text-decoration-none fw-bold">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h6>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-danger fw-bold fs-5"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                <small class="text-decoration-line-through text-muted small">
                                    <?= number_format($product['price'] * 1.1, 0, ',', '.') ?>đ
                                </small>
                            </div>
                            <div class="d-grid">
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="fa-solid fa-cart-plus me-1"></i> <?= __('add_to_cart') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="opacity-50 mb-3">
                <i class="fa-solid fa-box-open fa-4x text-muted"></i>
            </div>
            <p class="h5 text-muted"><?= __('no_products') ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- ========== SECTION 6: TESTIMONIALS ========== -->
<div class="mb-5">
    <h3 class="fw-bold text-center mb-4">
        <i class="fa-solid fa-star text-warning me-2"></i>
        <?= getCurrentLang() === 'en' ? 'What Our Customers Say' : 'Khách hàng nói gì về chúng tôi' ?>
    </h3>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 testimonial-card">
                <div class="card-body p-4">
                    <div class="mb-3 text-warning">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-muted mb-4">
                        "<?= getCurrentLang() === 'en' 
                            ? 'Amazing service! My laptop arrived within 24 hours and works perfectly. Will definitely buy again!' 
                            : 'Dịch vụ tuyệt vời! Laptop của tôi được giao trong vòng 24 giờ và hoạt động hoàn hảo. Chắc chắn sẽ mua lại!' ?>"
                    </p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; font-weight: bold;">NT</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Nguyễn Thanh</h6>
                            <small class="text-muted">TP. Hồ Chí Minh</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 testimonial-card">
                <div class="card-body p-4">
                    <div class="mb-3 text-warning">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-muted mb-4">
                        "<?= getCurrentLang() === 'en' 
                            ? 'Best prices in town! I compared with many stores and TechSmart always has the best deals.' 
                            : 'Giá tốt nhất thị trường! Tôi đã so sánh với nhiều cửa hàng và TechSmart luôn có giá ưu đãi nhất.' ?>"
                    </p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; font-weight: bold;">LM</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Lê Minh</h6>
                            <small class="text-muted">Hà Nội</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 testimonial-card">
                <div class="card-body p-4">
                    <div class="mb-3 text-warning">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <p class="text-muted mb-4">
                        "<?= getCurrentLang() === 'en' 
                            ? 'Great customer support! They helped me choose the right phone for my needs. Very professional.' 
                            : 'Hỗ trợ khách hàng tuyệt vời! Họ giúp tôi chọn đúng chiếc điện thoại phù hợp. Rất chuyên nghiệp.' ?>"
                    </p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; font-weight: bold;">PH</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Phạm Hương</h6>
                            <small class="text-muted">Đà Nẵng</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== SECTION 7: NEWS/BLOG ========== -->
<div class="mb-5">
    <h3 class="fw-bold mb-4">
        <i class="fa-solid fa-newspaper text-primary me-2"></i>
        <?= getCurrentLang() === 'en' ? 'Tech News' : 'Tin tức công nghệ' ?>
    </h3>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 news-card">
                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                     style="height: 180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fa-solid fa-mobile-screen fa-4x text-white opacity-50"></i>
                </div>
                <div class="card-body">
                    <span class="badge bg-primary mb-2"><?= getCurrentLang() === 'en' ? 'Smartphones' : 'Điện thoại' ?></span>
                    <h5 class="card-title fw-bold">
                        <?= getCurrentLang() === 'en' ? 'iPhone 16 Pro: What to Expect' : 'iPhone 16 Pro: Những điều cần biết' ?>
                    </h5>
                    <p class="text-muted small">
                        <?= getCurrentLang() === 'en' 
                            ? 'Apple is expected to launch the new iPhone 16 series with revolutionary AI features...' 
                            : 'Apple dự kiến ra mắt dòng iPhone 16 mới với các tính năng AI đột phá...' ?>
                    </p>
                    <a href="#" class="text-primary text-decoration-none fw-bold">
                        <?= getCurrentLang() === 'en' ? 'Read More' : 'Đọc thêm' ?> <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 news-card">
                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                     style="height: 180px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <i class="fa-solid fa-laptop fa-4x text-white opacity-50"></i>
                </div>
                <div class="card-body">
                    <span class="badge bg-success mb-2"><?= getCurrentLang() === 'en' ? 'Laptops' : 'Laptop' ?></span>
                    <h5 class="card-title fw-bold">
                        <?= getCurrentLang() === 'en' ? 'Best Gaming Laptops 2024' : 'Laptop gaming tốt nhất 2024' ?>
                    </h5>
                    <p class="text-muted small">
                        <?= getCurrentLang() === 'en' 
                            ? 'Our comprehensive guide to choosing the perfect gaming laptop for your needs...' 
                            : 'Hướng dẫn toàn diện để chọn laptop gaming phù hợp nhất cho bạn...' ?>
                    </p>
                    <a href="#" class="text-primary text-decoration-none fw-bold">
                        <?= getCurrentLang() === 'en' ? 'Read More' : 'Đọc thêm' ?> <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 news-card">
                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                     style="height: 180px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fa-solid fa-microchip fa-4x text-white opacity-50"></i>
                </div>
                <div class="card-body">
                    <span class="badge bg-danger mb-2"><?= getCurrentLang() === 'en' ? 'Hardware' : 'Linh kiện' ?></span>
                    <h5 class="card-title fw-bold">
                        <?= getCurrentLang() === 'en' ? 'RTX 5090: First Look' : 'RTX 5090: Đánh giá đầu tiên' ?>
                    </h5>
                    <p class="text-muted small">
                        <?= getCurrentLang() === 'en' 
                            ? 'NVIDIA\'s next-gen GPU promises 2x performance improvement over 4090...' 
                            : 'GPU thế hệ mới của NVIDIA hứa hẹn hiệu năng gấp 2 lần 4090...' ?>
                    </p>
                    <a href="#" class="text-primary text-decoration-none fw-bold">
                        <?= getCurrentLang() === 'en' ? 'Read More' : 'Đọc thêm' ?> <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== SECTION 8: BRAND PARTNERS ========== -->
<div class="mb-4">
    <h3 class="fw-bold text-center mb-4">
        <i class="fa-solid fa-handshake text-primary me-2"></i>
        <?= getCurrentLang() === 'en' ? 'Official Brand Partners' : 'Đối tác thương hiệu chính hãng' ?>
    </h3>
    <div class="card border-0 shadow-sm">
        <div class="card-body py-4">
            <div class="row align-items-center justify-content-center g-4">
                <div class="col-4 col-md-2 text-center">
                    <div class="brand-logo p-3 rounded">
                        <i class="fa-brands fa-apple fa-3x text-dark"></i>
                    </div>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <div class="brand-logo p-3 rounded">
                        <i class="fa-brands fa-samsung fa-3x" style="color: #1428a0;"></i>
                    </div>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <div class="brand-logo p-3 rounded">
                        <i class="fa-brands fa-google fa-3x" style="color: #4285f4;"></i>
                    </div>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <div class="brand-logo p-3 rounded">
                        <i class="fa-brands fa-microsoft fa-3x" style="color: #00a4ef;"></i>
                    </div>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <div class="brand-logo p-3 rounded">
                        <i class="fa-brands fa-windows fa-3x" style="color: #0078d6;"></i>
                    </div>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <div class="brand-logo p-3 rounded">
                        <i class="fa-solid fa-a fa-3x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Countdown Timer Script -->
<script>
function updateCountdown() {
    const now = new Date();
    const endOfDay = new Date();
    endOfDay.setHours(23, 59, 59, 999);
    
    const diff = endOfDay - now;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
}

setInterval(updateCountdown, 1000);
updateCountdown();
</script>