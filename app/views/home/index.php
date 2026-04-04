<!-- HERO BANNER CAROUSEL -->
<style>
    .hero-carousel .carousel-item {
        min-height: 280px;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
    }

    .hero-slide {
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-radius: 16px;
    }

    .hero-slide::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: heroRotate 15s linear infinite;
    }

    @keyframes heroRotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes heroFadeUp {
        0% {
            opacity: 0;
            transform: translateY(40px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes heroFloat {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-12px);
        }
    }

    @keyframes heroPulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }

        50% {
            box-shadow: 0 0 20px 10px rgba(255, 255, 255, 0.1);
        }
    }

    @keyframes sparkle {

        0%,
        100% {
            opacity: 0;
            transform: scale(0);
        }

        50% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .carousel-item.active .hero-title {
        animation: heroFadeUp 0.8s ease-out both;
    }

    .carousel-item.active .hero-desc {
        animation: heroFadeUp 0.8s ease-out 0.2s both;
    }

    .carousel-item.active .hero-btn {
        animation: heroFadeUp 0.8s ease-out 0.4s both;
    }

    .carousel-item.active .hero-icon {
        animation: heroFloat 3s ease-in-out infinite;
    }

    .hero-btn:hover {
        transform: scale(1.08) !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
    }

    .hero-particle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        animation: sparkle 4s ease-in-out infinite;
    }
</style>

<div id="heroBanner" class="carousel slide mb-5 hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="0" class="active rounded-pill" style="width:30px;height:5px;"></button>
        <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="1" class="rounded-pill" style="width:30px;height:5px;"></button>
        <button type="button" data-bs-target="#heroBanner" data-bs-slide-to="2" class="rounded-pill" style="width:30px;height:5px;"></button>
    </div>
    <div class="carousel-inner rounded-4 shadow-lg">
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <div class="hero-slide" style="background: linear-gradient(135deg, #0061ff 0%, #60efff 100%);">
                <div class="hero-particle" style="width:8px;height:8px;top:20%;left:15%;animation-delay:0s;"></div>
                <div class="hero-particle" style="width:12px;height:12px;top:60%;left:80%;animation-delay:1s;"></div>
                <div class="hero-particle" style="width:6px;height:6px;top:30%;left:70%;animation-delay:2s;"></div>
                <div class="hero-particle" style="width:10px;height:10px;top:75%;left:25%;animation-delay:0.5s;"></div>
                <div class="hero-particle" style="width:5px;height:5px;top:10%;left:90%;animation-delay:3s;"></div>
                <div class="position-relative z-1 px-4 py-5">
                    <div class="hero-icon mb-3"><i class="fa-solid fa-microchip fa-3x text-white opacity-75"></i></div>
                    <h1 class="hero-title display-5 fw-bold text-white mb-3">Công Nghệ Trong Tầm Tay</h1>
                    <p class="hero-desc lead text-white opacity-90 mb-4">Khám phá những sản phẩm mới nhất với mức giá ưu đãi chưa từng có</p>
                    <a href="#products-list" class="hero-btn btn btn-light text-primary fw-bold btn-lg px-5 rounded-pill shadow" style="transition: all 0.3s;">
                        <i class="fa-solid fa-bag-shopping me-2"></i>Mua sắm ngay
                    </a>
                </div>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item">
            <div class="hero-slide" style="background: linear-gradient(135deg, #f857a6 0%, #ff5858 50%, #ffc837 100%);">
                <div class="hero-particle" style="width:10px;height:10px;top:15%;left:85%;animation-delay:0.5s;"></div>
                <div class="hero-particle" style="width:8px;height:8px;top:70%;left:10%;animation-delay:1.5s;"></div>
                <div class="hero-particle" style="width:14px;height:14px;top:40%;left:50%;animation-delay:2.5s;"></div>
                <div class="hero-particle" style="width:6px;height:6px;top:80%;left:75%;animation-delay:0s;"></div>
                <div class="position-relative z-1 px-4 py-5">
                    <div class="hero-icon mb-3"><i class="fa-solid fa-bolt fa-3x text-white opacity-75"></i></div>
                    <h1 class="hero-title display-5 fw-bold text-white mb-3">Flash Sale Cuối Tuần</h1>
                    <p class="hero-desc lead text-white opacity-90 mb-4">Giảm đến <span class="badge bg-warning text-dark fs-5 px-3 py-2 rounded-pill">30%</span> cho laptop & điện thoại hot nhất</p>
                    <a href="#products-list" class="hero-btn btn btn-warning text-dark fw-bold btn-lg px-5 rounded-pill shadow" style="transition: all 0.3s;">
                        <i class="fa-solid fa-fire me-2"></i>Xem ngay
                    </a>
                </div>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item">
            <div class="hero-slide" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="hero-particle" style="width:9px;height:9px;top:25%;left:20%;animation-delay:1s;"></div>
                <div class="hero-particle" style="width:7px;height:7px;top:55%;left:65%;animation-delay:0s;"></div>
                <div class="hero-particle" style="width:11px;height:11px;top:80%;left:40%;animation-delay:2s;"></div>
                <div class="hero-particle" style="width:5px;height:5px;top:10%;left:55%;animation-delay:3s;"></div>
                <div class="position-relative z-1 px-4 py-5">
                    <div class="hero-icon mb-3"><i class="fa-solid fa-shield-halved fa-3x text-white opacity-75"></i></div>
                    <h1 class="hero-title display-5 fw-bold text-white mb-3">Bảo Hành Chính Hãng</h1>
                    <p class="hero-desc lead text-white opacity-90 mb-4">Cam kết 100% sản phẩm chính hãng, bảo hành toàn quốc, đổi trả 30 ngày</p>
                    <a href="#products-list" class="hero-btn btn btn-light text-success fw-bold btn-lg px-5 rounded-pill shadow" style="transition: all 0.3s;">
                        <i class="fa-solid fa-truck-fast me-2"></i>Khám phá
                    </a>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroBanner" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroBanner" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

<div id="products-list" class="d-flex flex-wrap justify-content-between align-items-center mb-4 border-bottom pb-2">
    <h3 class="fw-bold text-primary m-0">
        <i class="fa-solid fa-fire text-danger me-2"></i>Sản phẩm nổi bật
    </h3>
</div>

<div class="row g-4" id="product-grid">
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100 shadow-sm border-0">
                    <div class="position-relative p-3 text-center bg-white rounded-top" style="height: 220px;">
                        <?php $disc = intval($product['discount'] ?? 0); ?>
                        <?php if ($disc > 0): ?>
                            <span class="badge bg-danger">-<?= $disc ?>%</span>
                        <?php endif; ?>

                        <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>">
                            <img src="<?= BASE_URL ?>images/<?= $product['image'] ?>"
                                class="img-fluid h-100"
                                style="object-fit: contain; transition: transform 0.3s;"
                                alt="<?= $product['name'] ?>"
                                onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                        </a>
                    </div>

                    <div class="card-body d-flex flex-column border-top bg-light bg-opacity-10">
                        <h6 class="card-title mb-2" style="height: 40px; overflow: hidden; line-height: 1.4;">
                            <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>" class="text-dark text-decoration-none fw-bold">
                                <?= $product['name'] ?>
                            </a>
                        </h6>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <?php if ($disc > 0): ?>
                                    <span class="text-danger fw-bold fs-5"><?= number_format($product['price'] * (1 - $disc / 100), 0, ',', '.') ?>đ</span>
                                    <small class="text-decoration-line-through text-muted small">
                                        <?= number_format($product['price'], 0, ',', '.') ?>đ
                                    </small>
                                <?php else: ?>
                                    <span class="text-danger fw-bold fs-5"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                <?php endif; ?>
                            </div>
                            <?php if (isset($product['sold_count']) && $product['sold_count'] > 0): ?>
                                <small class="text-muted d-block mb-2"><i class="fa-solid fa-fire-flame-curved text-warning me-1"></i>Đã bán <?= $product['sold_count'] ?></small>
                            <?php endif; ?>
                            <div class="d-flex gap-2 w-100">
                                <?php if (($product['stock'] ?? 0) > 0): ?>
                                <!-- Nút Thêm vào giỏ -->
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>"
                                    class="btn btn-outline-primary btn-sm rounded-pill flex-fill btn-add-cart"
                                    data-product-id="<?= $product['id'] ?>"
                                    data-base-url="<?= BASE_URL ?>">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
                                </a>

                                <!-- Nút Mua ngay -->
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>?redirect=checkout"
                                    class="btn btn-danger btn-sm rounded-pill flex-fill">
                                    <i class="fa-solid fa-bolt"></i> Mua ngay
                                </a>
                                <?php else: ?>
                                <span class="btn btn-secondary btn-sm rounded-pill flex-fill disabled">
                                    <i class="fa-solid fa-ban me-1"></i> Hết hàng
                                </span>
                                <?php endif; ?>
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
            <p class="h5 text-muted">Chưa có sản phẩm nào.</p>
        </div>
    <?php endif; ?>
</div>

<?php
// Include pagination component if we have pagination data
if (isset($currentPage) && isset($totalPages)) {
    $baseUrl = BASE_URL;
    include __DIR__ . '/../layouts/pagination.php';
}
?>