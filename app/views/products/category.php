<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $title ?? 'Sản phẩm' ?>
            </li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <div>
            <h2 class="fw-bold text-uppercase text-primary m-0">
                <?= $title ?? 'Danh sách sản phẩm' ?>
            </h2>
            <small class="text-muted">Tìm thấy <?= $totalProducts ?? count($products) ?> sản phẩm</small>
        </div>
        
        <div class="d-flex align-items-center">
            <label class="me-2 small text-muted text-nowrap">Sắp xếp:</label>
            <form id="sortFormCategory" method="GET" action="">
                <?php 
                // Giữ nguyên page param nếu có
                if (isset($_GET['page'])): ?>
                    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page']) ?>">
                <?php endif; ?>
                <select class="form-select form-select-sm border-0 bg-light fw-bold text-primary" name="sort" onchange="document.getElementById('sortFormCategory').submit()">
                    <option value="newest" <?= (isset($sort) && $sort == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="price_asc" <?= (isset($sort) && $sort == 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                    <option value="price_desc" <?= (isset($sort) && $sort == 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                </select>
            </form>
        </div>
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
                                    alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>"
                                    onmouseover="this.style.transform='scale(1.1)'"
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
                                <div class="mb-2">
                                    <span class="text-danger fw-bold fs-5"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php if ($disc > 0): ?>
                                        <br>
                                        <small class="text-decoration-line-through text-muted small">
                                            <?= number_format($product['price'] / (1 - $disc/100), 0, ',', '.') ?>đ
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($product['sold_count']) && $product['sold_count'] > 0): ?>
                                    <small class="text-muted d-block mb-2"><i class="fa-solid fa-fire-flame-curved text-warning me-1"></i>Đã bán <?= $product['sold_count'] ?></small>
                                <?php endif; ?>
                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-primary btn-sm rounded-pill flex-grow-1 btn-add-cart" data-product-id="<?= $product['id'] ?>" data-base-url="<?= BASE_URL ?>">
                                        <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
                                    </a>
                                    <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>?redirect=checkout" class="btn btn-danger btn-sm rounded-pill" title="Mua ngay">
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
                    <i class="fa-solid fa-folder-open fa-4x text-muted"></i>
                </div>
                <p class="h5 text-muted">Không tìm thấy sản phẩm nào trong danh mục "<?= $title ?? 'này' ?>"</p>
                <a href="<?= BASE_URL ?>" class="btn btn-outline-primary mt-3">Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php 
    // Include pagination component if we have pagination data
    if (isset($currentPage) && isset($totalPages) && isset($baseUrl)) {
        include __DIR__ . '/../layouts/pagination.php';
    }
    ?>
</div>