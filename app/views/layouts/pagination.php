<?php
/**
 * Reusable Pagination Component
 * Required variables:
 * - $currentPage
 * - $totalPages
 * - $baseUrl
 */

if (!isset($totalPages) || (int)$totalPages <= 1) {
    return;
}

$currentPage = max(1, (int)($currentPage ?? 1));
$totalPages  = (int)$totalPages;

$range = 2;
$startPage = max(1, $currentPage - $range);
$endPage   = min($totalPages, $currentPage + $range);

$prev = max(1, $currentPage - 1);
$next = min($totalPages, $currentPage + 1);
?>

<nav aria-label="Product pagination" class="mt-5">
  <ul class="pagination justify-content-center">

    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $baseUrl ?>?page=<?= $prev ?>" aria-label="Previous">
        <i class="fa-solid fa-chevron-left"></i>
      </a>
    </li>

    <?php if ($startPage > 1): ?>
      <li class="page-item">
        <a class="page-link" href="<?= $baseUrl ?>?page=1">1</a>
      </li>
      <?php if ($startPage > 2): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
      <?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
      <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
        <a class="page-link" href="<?= $baseUrl ?>?page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <?php if ($endPage < $totalPages): ?>
      <?php if ($endPage < $totalPages - 1): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
      <?php endif; ?>
      <li class="page-item">
        <a class="page-link" href="<?= $baseUrl ?>?page=<?= $totalPages ?>"><?= $totalPages ?></a>
      </li>
    <?php endif; ?>

    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= $baseUrl ?>?page=<?= $next ?>" aria-label="Next">
        <i class="fa-solid fa-chevron-right"></i>
      </a>
    </li>

  </ul>
</nav>
