<?php
/**
 * Pagination Component
 * 
 * Required data:
 * - $currentPage: Trang hiện tại
 * - $totalPages: Tổng số trang
 * - $baseUrl: URL cơ sở (không chứa ?page=)
 * - $queryString: Query string hiện tại (không chứa page)
 */

$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$baseUrl = $data['baseUrl'] ?? '';
$queryString = $data['queryString'] ?? '';

// Không hiển thị nếu chỉ có 1 trang
if ($totalPages <= 1) return;

// Xây dựng URL với query string
function buildPageUrl($baseUrl, $page, $queryString) {
    $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
    $url = $baseUrl;
    if (!empty($queryString)) {
        $url .= $separator . $queryString;
        $separator = '&';
    }
    $url .= $separator . 'page=' . $page;
    return $url;
}

// Số trang hiển thị mỗi bên của trang hiện tại
$range = 2;
$startPage = max(1, $currentPage - $range);
$endPage = min($totalPages, $currentPage + $range);
?>

<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center mb-0">
        <!-- First Page -->
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= buildPageUrl($baseUrl, 1, $queryString) ?>" aria-label="First">
                <i class="fa-solid fa-angles-left"></i>
            </a>
        </li>
        
        <!-- Previous Page -->
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= buildPageUrl($baseUrl, $currentPage - 1, $queryString) ?>" aria-label="Previous">
                <i class="fa-solid fa-angle-left"></i>
            </a>
        </li>

        <!-- Page Numbers -->
        <?php if ($startPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= buildPageUrl($baseUrl, 1, $queryString) ?>">1</a>
            </li>
            <?php if ($startPage > 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="<?= buildPageUrl($baseUrl, $i, $queryString) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="<?= buildPageUrl($baseUrl, $totalPages, $queryString) ?>"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>

        <!-- Next Page -->
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= buildPageUrl($baseUrl, $currentPage + 1, $queryString) ?>" aria-label="Next">
                <i class="fa-solid fa-angle-right"></i>
            </a>
        </li>
        
        <!-- Last Page -->
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= buildPageUrl($baseUrl, $totalPages, $queryString) ?>" aria-label="Last">
                <i class="fa-solid fa-angles-right"></i>
            </a>
        </li>
    </ul>
    
    <!-- Page Info -->
    <div class="text-center mt-2">
        <small class="text-muted">
            Trang <?= $currentPage ?> / <?= $totalPages ?>
        </small>
    </div>
</nav>

<style>
.pagination .page-link {
    border-radius: 8px !important;
    margin: 0 2px;
    border: none;
    color: #0072ff;
    transition: all 0.3s ease;
}
.pagination .page-link:hover {
    background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
    color: white;
    transform: translateY(-2px);
}
.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
    border: none;
}
.pagination .page-item.disabled .page-link {
    color: #999;
    background: #f8f9fa;
}
</style>
