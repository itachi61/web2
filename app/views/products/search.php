<div class="container my-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm: "<strong><?= htmlspecialchars($data['keyword'] ?? '') ?></strong>"</li>
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
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cat[]" value="1" id="cat1" <?= (isset($_GET['cat']) && in_array(1, $_GET['cat'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat1">Laptop</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cat[]" value="2" id="cat2" <?= (isset($_GET['cat']) && in_array(2, $_GET['cat'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat2">Điện thoại</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cat[]" value="3" id="cat3" <?= (isset($_GET['cat']) && in_array(3, $_GET['cat'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat3">Linh kiện</label>
                            </div>
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
                        <?php if(isset($_GET['cat'])): foreach($_GET['cat'] as $c): ?>
                            <input type="hidden" name="cat[]" value="<?= $c ?>">
                        <?php endforeach; endif; ?>
                        <?php if(isset($_GET['min_price'])): ?><input type="hidden" name="min_price" value="<?= $_GET['min_price'] ?>"><?php endif; ?>
                        <?php if(isset($_GET['max_price'])): ?><input type="hidden" name="max_price" value="<?= $_GET['max_price'] ?>"><?php endif; ?>

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
                <div class="row g-3">
                    <?php foreach($data['products'] as $item): ?>
                        <div class="col-6 col-md-4">
                            <div class="card product-card h-100 border-0 shadow-sm">
                                <div class="position-relative text-center p-3 bg-white rounded-top" style="height: 200px;">
                                    <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>">
                                        <img src="<?= BASE_URL ?>public/images/<?= $item['image'] ?>" 
                                             class="img-fluid h-100" 
                                             style="object-fit: contain;"
                                             onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                                    </a>
                                </div>
                                <div class="card-body border-top">
                                    <h6 class="card-title text-truncate">
                                        <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>" class="text-decoration-none text-dark fw-bold"><?= $item['name'] ?></a>
                                    </h6>
                                    <span class="text-danger fw-bold"><?= number_format($item['price'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>