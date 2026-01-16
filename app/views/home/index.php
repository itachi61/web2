<div class="rounded-3 p-4 p-md-5 mb-5 text-white text-center shadow" 
     style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
    <h1 class="display-4 fw-bold mb-3">Công Nghệ Trong Tầm Tay</h1>
    <p class="lead mb-4">Khám phá những sản phẩm mới nhất với mức giá ưu đãi chưa từng có.</p>
    <a href="#products-list" class="btn btn-light text-primary fw-bold btn-lg px-4 shadow-sm">
        <i class="fa-solid fa-bag-shopping me-2"></i>Mua sắm ngay
    </a>
</div>

<div id="products-list" class="d-flex flex-wrap justify-content-between align-items-center mb-4 border-bottom pb-2">
    <h3 class="fw-bold text-primary m-0">
        <i class="fa-solid fa-fire text-danger me-2"></i>Sản phẩm nổi bật
    </h3>
    <form action="<?= BASE_URL ?>product/search" method="GET" class="d-flex mt-2 mt-md-0">
        <input class="form-control me-2 rounded-pill" type="search" name="keyword" placeholder="Bạn tìm gì..." style="width: 250px;">
        <button class="btn btn-outline-primary rounded-circle" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>
</div>

<div class="row g-4">
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
                                 alt="<?= $product['name'] ?>"
                                 onmouseover="this.style.transform='scale(1.05)'"
                                 onmouseout="this.style.transform='scale(1)'"
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-danger fw-bold fs-5"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                <small class="text-decoration-line-through text-muted small">
                                    <?= number_format($product['price'] * 1.1, 0, ',', '.') ?>đ
                                </small>
                            </div>
                            <div class="d-grid">
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
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
            <p class="h5 text-muted">Chưa có sản phẩm nào.</p>
        </div>
    <?php endif; ?>
</div>