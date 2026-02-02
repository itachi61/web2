<?php
$orders = $data['orders'] ?? [];
$filter = $data['filter'] ?? 'all';

// Status labels
$statusLabels = [
    'pending' => ['text' => __('status_pending'), 'class' => 'warning', 'icon' => 'clock'],
    'processing' => ['text' => __('status_processing'), 'class' => 'info', 'icon' => 'spinner fa-spin'],
    'completed' => ['text' => __('status_completed'), 'class' => 'success', 'icon' => 'circle-check'],
    'cancelled' => ['text' => __('status_cancelled'), 'class' => 'danger', 'icon' => 'circle-xmark']
];

// Payment labels
$paymentLabels = [
    'cash' => __('cash_on_delivery'),
    'bank_transfer' => __('bank_transfer'),
    'online' => __('online_payment')
];

// Time filters
$timeFilters = [
    '7days' => __('last_7_days'),
    '30days' => __('last_30_days'),
    '3months' => __('last_3_months'),
    'all' => __('all_time')
];
?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none"><?= __('home') ?></a></li>
            <li class="breadcrumb-item active"><?= __('order_history') ?></li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h2 class="fw-bold mb-0">
            <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i><?= __('order_history') ?>
        </h2>
        <a href="<?= BASE_URL ?>" class="btn btn-outline-primary">
            <i class="fa-solid fa-shopping-bag me-1"></i><?= __('continue_shopping') ?>
        </a>
    </div>

    <!-- Time Filter Buttons -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="text-muted me-2">
                    <i class="fa-solid fa-filter me-1"></i><?= __('filter_time') ?>:
                </span>
                <?php foreach ($timeFilters as $key => $label): ?>
                    <a href="<?= BASE_URL ?>orders/history?filter=<?= $key ?>" 
                       class="btn btn-sm <?= $filter === $key ? 'btn-primary' : 'btn-outline-secondary' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <!-- Empty State -->
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="120" alt="No Orders" class="mb-3 opacity-50">
            <h4 class="text-muted"><?= __('no_orders') ?></h4>
            <p class="text-muted mb-4">
                <?php if (getCurrentLang() === 'en'): ?>
                    Start shopping for your favorite tech products!
                <?php else: ?>
                    Hãy bắt đầu mua sắm những sản phẩm công nghệ yêu thích!
                <?php endif; ?>
            </p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary px-4 py-2">
                <i class="fa-solid fa-shopping-cart me-2"></i><?= __('continue_shopping') ?>
            </a>
        </div>
    <?php else: ?>
        <!-- Order Stats -->
        <div class="row g-3 mb-4">
            <?php
            $stats = ['pending' => 0, 'processing' => 0, 'completed' => 0, 'cancelled' => 0];
            foreach ($orders as $o) {
                if (isset($stats[$o['status']])) $stats[$o['status']]++;
            }
            ?>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="h4 mb-0 text-warning"><?= $stats['pending'] ?></div>
                    <small class="text-muted"><?= __('status_pending') ?></small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="h4 mb-0 text-info"><?= $stats['processing'] ?></div>
                    <small class="text-muted"><?= __('status_processing') ?></small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="h4 mb-0 text-success"><?= $stats['completed'] ?></div>
                    <small class="text-muted"><?= __('status_completed') ?></small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="h4 mb-0 text-danger"><?= $stats['cancelled'] ?></div>
                    <small class="text-muted"><?= __('status_cancelled') ?></small>
                </div>
            </div>
        </div>

        <!-- Order List -->
        <div class="row g-4">
            <?php foreach ($orders as $order): 
                $status = $statusLabels[$order['status']] ?? $statusLabels['pending'];
                $payment = $paymentLabels[$order['payment_method'] ?? 'cash'] ?? __('cash_on_delivery');
            ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm order-card">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <span class="fw-bold text-primary fs-5">
                                        #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </span>
                                    <span class="text-muted ms-2">
                                        <i class="fa-regular fa-calendar me-1"></i>
                                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                    </span>
                                    <!-- Time ago badge -->
                                    <?php
                                    $orderTime = strtotime($order['created_at']);
                                    $diff = time() - $orderTime;
                                    $days = floor($diff / 86400);
                                    if ($days == 0) {
                                        $timeAgo = getCurrentLang() === 'en' ? 'Today' : 'Hôm nay';
                                    } elseif ($days == 1) {
                                        $timeAgo = getCurrentLang() === 'en' ? 'Yesterday' : 'Hôm qua';
                                    } elseif ($days < 7) {
                                        $timeAgo = $days . (getCurrentLang() === 'en' ? ' days ago' : ' ngày trước');
                                    } elseif ($days < 30) {
                                        $weeks = floor($days / 7);
                                        $timeAgo = $weeks . (getCurrentLang() === 'en' ? ' week(s) ago' : ' tuần trước');
                                    } else {
                                        $months = floor($days / 30);
                                        $timeAgo = $months . (getCurrentLang() === 'en' ? ' month(s) ago' : ' tháng trước');
                                    }
                                    ?>
                                    <span class="badge bg-light text-dark ms-2"><?= $timeAgo ?></span>
                                </div>
                                <span class="badge bg-<?= $status['class'] ?> fs-6 px-3 py-2">
                                    <i class="fa-solid fa-<?= $status['icon'] ?> me-1"></i>
                                    <?= $status['text'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h6 class="text-muted mb-2"><i class="fa-solid fa-location-dot me-1"></i><?= getCurrentLang() === 'en' ? 'Ship to' : 'Giao đến' ?></h6>
                                    <p class="fw-bold mb-1"><?= htmlspecialchars($order['fullname']) ?></p>
                                    <p class="small text-muted mb-0">
                                        <?= htmlspecialchars($order['address']) ?>, 
                                        <?= htmlspecialchars($order['ward'] ?? '') ?>, 
                                        <?= htmlspecialchars($order['district'] ?? '') ?>
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <h6 class="text-muted mb-2"><i class="fa-solid fa-phone me-1"></i><?= __('phone') ?></h6>
                                    <p class="mb-0"><?= htmlspecialchars($order['phone']) ?></p>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <h6 class="text-muted mb-2"><i class="fa-solid fa-credit-card me-1"></i><?= __('payment_method') ?></h6>
                                    <p class="mb-0"><?= $payment ?></p>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <h6 class="text-muted mb-2"><?= __('total') ?></h6>
                                    <p class="h5 fw-bold text-danger mb-0">
                                        <?= number_format($order['total_money'], 0, ',', '.') ?>đ
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <a href="<?= BASE_URL ?>orders/cancel/<?= $order['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('<?= getCurrentLang() === 'en' ? 'Are you sure you want to cancel this order?' : 'Bạn có chắc muốn hủy đơn hàng này?' ?>');">
                                            <i class="fa-solid fa-ban me-1"></i><?= __('cancel_order') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= BASE_URL ?>orders/detail/<?= $order['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye me-1"></i><?= __('view_detail') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.order-card {
    transition: all 0.3s ease;
}
.order-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
</style>
