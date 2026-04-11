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
                        <?= $product['category_name'] ?? 'Sản phẩm' ?>
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

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-filter me-2 text-primary"></i>Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>product/search" method="GET">

                        <input type="hidden" name="keyword" value="<?= htmlspecialchars($data['keyword'] ?? '') ?>">

                        <div class="mb-4">
                            <h6 class="fw-bold">Danh mục</h6>
                            <?php if (!empty($data['categories'])): ?>
                                <?php foreach ($data['categories'] as $cat): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="cat[]" value="<?= $cat['id'] ?>" id="cat<?= $cat['id'] ?>" <?= (isset($_GET['cat']) && in_array($cat['id'], $_GET['cat'])) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="cat<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small">Đang cập nhật...</p>
                            <?php endif; ?>
                        </div>

                        <hr class="text-muted opacity-25">

                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Khoảng giá</h6>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ" value="<?= $_GET['min_price'] ?? '' ?>">
                                <span>-</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="<?= $_GET['max_price'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">Áp dụng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-3 shadow-sm border">
                <span class="text-muted">Tìm thấy <strong class="text-dark"><?= isset($data['products']) ? count($data['products']) : 0 ?></strong> kết quả</span>

                <div class="d-flex align-items-center">
                    <label class="me-2 small text-muted text-nowrap">Sắp xếp:</label>
                    <form id="sortForm" action="<?= BASE_URL ?>product/search" method="GET">
                        <input type="hidden" name="keyword" value="<?= htmlspecialchars($data['keyword'] ?? '') ?>">
                        <?php if (isset($_GET['cat'])): foreach ($_GET['cat'] as $c): ?>
                                <input type="hidden" name="cat[]" value="<?= $c ?>">
                        <?php endforeach;
                        endif; ?>
                        <?php if (isset($_GET['min_price'])): ?><input type="hidden" name="min_price" value="<?= $_GET['min_price'] ?>"><?php endif; ?>
                        <?php if (isset($_GET['max_price'])): ?><input type="hidden" name="max_price" value="<?= $_GET['max_price'] ?>"><?php endif; ?>

                        <select class="form-select form-select-sm border-0 bg-light fw-bold text-primary" name="sort" onchange="document.getElementById('sortForm').submit()">
                            <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                            <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                        </select>
                    </form>
                </div>
            </div>

            <?php if (empty($data['products'])): ?>
                <div class="alert alert-warning text-center">Không tìm thấy sản phẩm nào.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($data['products'] as $item): ?>
                        <div class="col-6 col-md-4 col-lg-4">
                            <div class="card product-card h-100 shadow-sm border-0">
                                <div class="position-relative p-3 text-center bg-white rounded-top" style="height: 220px;">
                                    <?php $disc = intval($item['discount'] ?? 0); ?>
                                    <?php if ($disc > 0): ?>
                                        <span class="badge bg-danger">-<?= $disc ?>%</span>
                                    <?php endif; ?>

                                    <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>">
                                        <img src="<?= BASE_URL ?>images/<?= $item['image'] ?>"
                                            class="img-fluid h-100"
                                            style="object-fit: contain; transition: transform 0.3s;"
                                            alt="<?= htmlspecialchars($item['name'] ?? 'Product') ?>"
                                            onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                                    </a>
                                </div>

                                <div class="card-body d-flex flex-column border-top bg-light bg-opacity-10">
                                    <h6 class="card-title mb-2" style="height: 40px; overflow: hidden; line-height: 1.4;">
                                        <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>" class="text-dark text-decoration-none fw-bold">
                                            <?= $item['name'] ?>
                                        </a>
                                    </h6>

                                    <div class="mt-auto">
                                        <div class="mb-2">
                                            <?php if ($disc > 0): ?>
                                                <span class="text-danger fw-bold fs-5"><?= number_format($item['price'] * (1 - $disc / 100), 0, ',', '.') ?>đ</span>
                                                <br>
                                                <small class="text-decoration-line-through text-muted small">
                                                    <?= number_format($item['price'], 0, ',', '.') ?>đ
                                                </small>
                                            <?php else: ?>
                                                <span class="text-danger fw-bold fs-5"><?= number_format($item['price'], 0, ',', '.') ?>đ</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (isset($item['sold_count']) && $item['sold_count'] > 0): ?>
                                            <small class="text-muted d-block mb-2"><i class="fa-solid fa-fire-flame-curved text-warning me-1"></i>Đã bán <?= $item['sold_count'] ?></small>
                                        <?php endif; ?>
                                        <div class="d-flex gap-2">
                                            <?php if (($item['stock'] ?? 0) > 0): ?>
                                            <a href="<?= BASE_URL ?>cart/add/<?= $item['id'] ?>" class="btn btn-primary btn-sm rounded-pill flex-grow-1 btn-add-cart" data-product-id="<?= $item['id'] ?>" data-base-url="<?= BASE_URL ?>">
                                                <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
                                            </a>
                                            <a href="<?= BASE_URL ?>cart/add/<?= $item['id'] ?>?redirect=checkout" class="btn btn-danger btn-sm rounded-pill" title="Mua ngay">
                                                <i class="fa-solid fa-bolt"></i> Mua ngay
                                            </a>
                                            <?php else: ?>
                                            <span class="btn btn-secondary btn-sm rounded-pill flex-grow-1 disabled">
                                                <i class="fa-solid fa-ban me-1"></i> Hết hàng
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>