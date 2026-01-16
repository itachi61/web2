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
            <small class="text-muted">Tìm thấy <?= count($products) ?> sản phẩm</small>
        </div>
        
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                Sắp xếp theo
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Mới nhất</a></li>
                <li><a class="dropdown-item" href="#">Giá thấp đến cao</a></li>
                <li><a class="dropdown-item" href="#">Giá cao đến thấp</a></li>
            </ul>
        </div>
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
                                     onmouseover="this.style.transform='scale(1.1)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     onerror="this.src='https://via.placeholder.com/300?text=TechSmart'">
                            </a>
                        </div>
                        
                        <div class="card-body d-flex flex-column border-top bg-light bg-opacity-10">
                            <h6 class="card-title mb-2" style="height: 40px; overflow: hidden; line-height: 1.4;">
                                <a href="<?= BASE_URL ?>product/detail/<?= $product['id'] ?>" class="text-dark text-decoration-none fw-bold">
                                    <?= $product['name'] ?>
                                </a>
                            </h6>
                            
                            <div class="mt-auto">
                                <div class="mb-3">
                                    <span class="text-danger fw-bold fs-5"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <br>
                                    <small class="text-decoration-line-through text-muted small">
                                        <?= number_format($product['price'] * 1.1, 0, ',', '.') ?>đ
                                    </small>
                                </div>
                                <div class="d-grid">
                                    <a href="<?= BASE_URL ?>cart/add/<?= $product['id'] ?>" class="btn btn-primary btn-sm rounded-pill">
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
                    <i class="fa-solid fa-folder-open fa-4x text-muted"></i>
                </div>
                <p class="h5 text-muted">Không tìm thấy sản phẩm nào trong danh mục "<?= $title ?? 'này' ?>"</p>
                <a href="<?= BASE_URL ?>" class="btn btn-outline-primary mt-3">Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>