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
</div>

<div class="row g-4" id="product-grid">
    <?php if (!empty($products)): ?>
        <?php foreach($products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100 shadow-sm border-0">
                    <div class="position-relative p-3 text-center bg-white rounded-top" style="height: 220px;">
                        <?php $disc = intval($product['discount'] ?? 0); ?>
                        <?php if ($disc > 0): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">-<?= $disc ?>%</span>
                        <?php endif; ?>
                        
                        <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>">
                            <img src="<?= BASE_URL ?>images/<?= $product['image'] ?>" 
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
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-danger fw-bold fs-5"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                <?php if ($disc > 0): ?>
                                    <small class="text-decoration-line-through text-muted small">
                                        <?= number_format($product['price'] / (1 - $disc/100), 0, ',', '.') ?>đ
                                    </small>
                                <?php endif; ?>
                            </div>
                            <?php if (isset($product['sold_count']) && $product['sold_count'] > 0): ?>
                                <small class="text-muted d-block mb-2"><i class="fa-solid fa-fire-flame-curved text-warning me-1"></i>Đã bán <?= $product['sold_count'] ?></small>
                            <?php endif; ?>
                            <div class="d-flex gap-2">
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 btn-add-cart" data-product-id="<?= $product['id'] ?>" data-base-url="<?= BASE_URL ?>">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
                                </a>
                                <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>?redirect=checkout" class="btn btn-danger btn-sm rounded-pill">
                                    <i class="fa-solid fa-bolt"></i>
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

<?php 
// Include pagination component if we have pagination data
if (isset($currentPage) && isset($totalPages)) {
    $baseUrl = BASE_URL;
    include __DIR__ . '/../layouts/pagination.php';
}
?>