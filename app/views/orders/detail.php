<?php
$order = $data['order'] ?? null;
$orderItems = $data['orderItems'] ?? [];

// Status labels
$statusLabels = [
    'pending' => ['text' => __('status_pending'), 'class' => 'warning', 'icon' => 'clock'],
    'processing' => ['text' => __('status_processing'), 'class' => 'info', 'icon' => 'spinner fa-spin'],
    'completed' => ['text' => __('status_completed'), 'class' => 'success', 'icon' => 'circle-check'],
    'cancelled' => ['text' => __('status_cancelled'), 'class' => 'danger', 'icon' => 'circle-xmark']
];

// Payment labels
$paymentLabels = [
    'cash' => ['icon' => 'fa-money-bill-wave', 'text' => __('cash_on_delivery'), 'color' => 'success'],
    'bank_transfer' => ['icon' => 'fa-building-columns', 'text' => __('bank_transfer'), 'color' => 'primary'],
    'online' => ['icon' => 'fa-credit-card', 'text' => __('online_payment'), 'color' => 'warning']
];

if (!$order) {
    echo '<div class="alert alert-danger">' . (getCurrentLang() === 'en' ? 'Order not found' : 'Đơn hàng không tồn tại') . '</div>';
    return;
}

$status = $statusLabels[$order['status']] ?? $statusLabels['pending'];
$payment = $paymentLabels[$order['payment_method'] ?? 'cash'] ?? $paymentLabels['cash'];
?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none"><?= __('home') ?></a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>orders/history" class="text-decoration-none"><?= __('order_history') ?></a></li>
            <li class="breadcrumb-item active">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></li>
        </ol>
    </nav>

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

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fa-solid fa-receipt me-2 text-primary"></i>
                <?= __('order_detail') ?> #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
            </h2>
            <p class="text-muted mb-0">
                <i class="fa-regular fa-calendar me-1"></i>
                <?= __('order_date') ?>: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($order['status'] === 'pending'): ?>
                <a href="<?= BASE_URL ?>orders/cancel/<?= $order['id'] ?>" 
                   class="btn btn-outline-danger"
                   onclick="return confirm('<?= getCurrentLang() === 'en' ? 'Are you sure you want to cancel this order?' : 'Bạn có chắc muốn hủy đơn hàng này?' ?>');">
                    <i class="fa-solid fa-ban me-1"></i><?= __('cancel_order') ?>
                </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>orders/history" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i><?= __('back') ?>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Order Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2"><?= __('order_status') ?></h6>
                            <span class="badge bg-<?= $status['class'] ?> fs-5 px-4 py-2">
                                <i class="fa-solid fa-<?= $status['icon'] ?> me-2"></i>
                                <?= $status['text'] ?>
                            </span>
                        </div>
                        
                        <!-- Order Timeline -->
                        <div class="d-none d-md-block">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-center">
                                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-check fa-sm"></i>
                                    </div>
                                    <small class="d-block text-muted mt-1"><?= getCurrentLang() === 'en' ? 'Placed' : 'Đã đặt' ?></small>
                                </div>
                                <div class="border-top flex-grow-1" style="width: 40px;"></div>
                                <div class="text-center">
                                    <div class="rounded-circle <?= in_array($order['status'], ['processing', 'completed']) ? 'bg-success text-white' : 'bg-light text-muted' ?> d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-<?= in_array($order['status'], ['processing', 'completed']) ? 'check' : 'clock' ?> fa-sm"></i>
                                    </div>
                                    <small class="d-block text-muted mt-1"><?= getCurrentLang() === 'en' ? 'Processing' : 'Xử lý' ?></small>
                                </div>
                                <div class="border-top flex-grow-1" style="width: 40px;"></div>
                                <div class="text-center">
                                    <div class="rounded-circle <?= $order['status'] === 'completed' ? 'bg-success text-white' : 'bg-light text-muted' ?> d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-<?= $order['status'] === 'completed' ? 'check' : 'truck' ?> fa-sm"></i>
                                    </div>
                                    <small class="d-block text-muted mt-1"><?= getCurrentLang() === 'en' ? 'Delivered' : 'Giao' ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-box me-2 text-primary"></i><?= __('products') ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4"><?= __('product') ?></th>
                                    <th class="text-center"><?= __('price') ?></th>
                                    <th class="text-center"><?= __('quantity') ?></th>
                                    <th class="text-end pe-4"><?= __('subtotal') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= BASE_URL ?>public/images/<?= $item['image'] ?? 'placeholder.jpg' ?>" 
                                                     class="rounded border me-3" width="60" height="60" 
                                                     style="object-fit: contain;"
                                                     onerror="this.src='https://via.placeholder.com/60'">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">
                                                        <a href="<?= BASE_URL ?>product/detail/<?= $item['product_id'] ?>" class="text-dark text-decoration-none">
                                                            <?= htmlspecialchars($item['name'] ?? __('product')) ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">ID: #<?= $item['product_id'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($item['price'], 0, ',', '.') ?>đ
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark fs-6"><?= $item['quantity'] ?></span>
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-primary">
                                            <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold"><?= __('subtotal') ?>:</td>
                                    <td class="text-end pe-4"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold"><?= getCurrentLang() === 'en' ? 'Shipping' : 'Phí vận chuyển' ?>:</td>
                                    <td class="text-end pe-4 text-success"><?= __('free_shipping') ?></td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end h5 fw-bold mb-0"><?= __('total') ?>:</td>
                                    <td class="text-end pe-4 h4 fw-bold text-danger mb-0">
                                        <?= number_format($order['total_money'], 0, ',', '.') ?>đ
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Shipping Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-truck me-2 text-primary"></i><?= __('shipping_info') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small"><?= __('fullname') ?></label>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($order['fullname']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small"><?= __('phone') ?></label>
                        <p class="fw-bold mb-0">
                            <i class="fa-solid fa-phone me-1 text-success"></i>
                            <?= htmlspecialchars($order['phone']) ?>
                        </p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small"><?= __('address') ?></label>
                        <p class="mb-0">
                            <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                            <?= htmlspecialchars($order['address']) ?>,
                            <?= htmlspecialchars($order['ward'] ?? '') ?>,
                            <?= htmlspecialchars($order['district'] ?? '') ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-credit-card me-2 text-primary"></i><?= __('payment_method') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-<?= $payment['color'] ?> bg-opacity-10 p-3 me-3">
                            <i class="fa-solid <?= $payment['icon'] ?> fa-lg text-<?= $payment['color'] ?>"></i>
                        </div>
                        <div>
                            <p class="fw-bold mb-0"><?= $payment['text'] ?></p>
                        </div>
                    </div>

                    <?php if (($order['payment_method'] ?? '') === 'bank_transfer'): ?>
                        <div class="mt-3 p-3 bg-light rounded">
                            <p class="small mb-1"><strong><?= getCurrentLang() === 'en' ? 'Bank' : 'Ngân hàng' ?>:</strong> Vietcombank</p>
                            <p class="small mb-1"><strong><?= getCurrentLang() === 'en' ? 'Account No' : 'STK' ?>:</strong> 1234567890</p>
                            <p class="small mb-1"><strong><?= getCurrentLang() === 'en' ? 'Account Name' : 'Chủ TK' ?>:</strong> TECHSMART JSC</p>
                            <p class="small mb-0"><strong><?= getCurrentLang() === 'en' ? 'Reference' : 'Nội dung' ?>:</strong> <code>DONHANG <?= $order['id'] ?></code></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Note -->
            <?php if (!empty($order['note'])): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-note-sticky me-2 text-primary"></i><?= __('order_note') ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($order['note'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
