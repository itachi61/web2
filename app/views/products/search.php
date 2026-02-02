<?php
$products = $data['products'] ?? [];
$keyword = $data['keyword'] ?? '';
$categories = $data['categories'] ?? [];
$selectedCategories = $data['selectedCategories'] ?? [];
$minPrice = $data['minPrice'] ?? '';
$maxPrice = $data['maxPrice'] ?? '';
$sort = $data['sort'] ?? 'newest';
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$totalProducts = $data['totalProducts'] ?? count($products);
$baseUrl = $data['baseUrl'] ?? BASE_URL . 'product/search';
$queryString = $data['queryString'] ?? '';
?>

<div class="container my-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm: "<strong><?= htmlspecialchars($keyword) ?></strong>"</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0 rounded-3 sticky-top" style="top: 100px;">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-filter me-2 text-primary"></i>Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>product/search" method="GET" id="filterForm">
                        
                        <!-- Search keyword -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Từ khóa</h6>
                            <input type="text" name="keyword" class="form-control" 
                                   value="<?= htmlspecialchars($keyword) ?>" placeholder="Nhập từ khóa...">
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Danh mục</h6>
                            <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cat[]" 
                                           value="<?= $cat['id'] ?>" id="cat<?= $cat['id'] ?>"
                                           <?= in_array($cat['id'], $selectedCategories) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="cat<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="text-muted opacity-25">

                        <!-- Price Range -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Khoảng giá</h6>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="number" name="min_price" class="form-control form-control-sm" 
                                       placeholder="Từ" value="<?= htmlspecialchars($minPrice) ?>">
                                <span>-</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" 
                                       placeholder="Đến" value="<?= htmlspecialchars($maxPrice) ?>">
                            </div>
                            <small class="text-muted">Đơn vị: VNĐ</small>
                        </div>

                        <!-- Sort (hidden, will be set by dropdown) -->
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>" id="sortInput">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-magnifying-glass me-1"></i>Áp dụng
                            </button>
                            <a href="<?= BASE_URL ?>product/search" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-rotate-left me-1"></i>Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-3 shadow-sm border">
                <span class="text-muted">
                    Tìm thấy <strong class="text-dark"><?= $totalProducts ?></strong> kết quả
                    <?php if ($totalPages > 1): ?>
                        <span class="ms-2 text-muted small">(Trang <?= $currentPage ?>/<?= $totalPages ?>)</span>
                    <?php endif; ?>
                </span>
                
                <div class="d-flex align-items-center">
                    <label class="me-2 small text-muted text-nowrap">Sắp xếp:</label>
                    <select class="form-select form-select-sm border-0 bg-light fw-bold text-primary" 
                            id="sortSelect" style="width: auto;">
                        <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                    </select>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="alert alert-warning text-center py-5">
                    <i class="fa-solid fa-search fa-3x mb-3 text-muted"></i>
                    <h5>Không tìm thấy sản phẩm nào</h5>
                    <p class="mb-0">Thử tìm kiếm với từ khóa khác hoặc điều chỉnh bộ lọc.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach($products as $item): ?>
                        <div class="col-6 col-md-4">
                            <div class="card product-card h-100 border-0 shadow-sm">
                                <div class="position-relative text-center p-3 bg-white rounded-top" style="height: 200px;">
                                    <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>">
                                        <img src="<?= BASE_URL ?>public/images/<?= $item['image'] ?>" 
                                             class="img-fluid h-100" 
                                             style="object-fit: contain; transition: transform 0.3s;"
                                             onmouseover="this.style.transform='scale(1.08)'"
                                             onmouseout="this.style.transform='scale(1)'"
                                             onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                                    </a>
                                </div>
                                <div class="card-body border-top">
                                    <h6 class="card-title mb-2" style="height: 40px; overflow: hidden;">
                                        <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>" class="text-decoration-none text-dark fw-bold">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </a>
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-danger fw-bold"><?= number_format($item['price'], 0, ',', '.') ?>đ</span>
                                        <a href="<?= BASE_URL ?>cart/add/<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <?php 
                    $this->view('components/pagination', [
                        'currentPage' => $currentPage,
                        'totalPages' => $totalPages,
                        'baseUrl' => $baseUrl,
                        'queryString' => $queryString
                    ]); 
                    ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Sort change handler
document.getElementById('sortSelect').addEventListener('change', function() {
    document.getElementById('sortInput').value = this.value;
    document.getElementById('filterForm').submit();
});
</script>