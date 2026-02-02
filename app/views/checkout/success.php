<?php
$order = $data['order'] ?? null;
$orderItems = $data['orderItems'] ?? [];

$paymentLabels = [
    'cash' => ['icon' => 'fa-money-bill-wave', 'text' => __('cash_on_delivery'), 'color' => 'success'],
    'bank_transfer' => ['icon' => 'fa-building-columns', 'text' => __('bank_transfer'), 'color' => 'primary'],
    'online' => ['icon' => 'fa-credit-card', 'text' => __('online_payment'), 'color' => 'warning']
];
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Header -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <div class="success-checkmark">
                        <i class="fa-solid fa-circle-check fa-5x text-success"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-success mb-3"><?= __('order_success') ?></h2>
                <p class="text-muted lead">
                    <?= __('thank_you') ?> <strong class="text-primary">TechSmart</strong>
                </p>
                <?php if ($order): ?>
                    <div class="d-inline-block bg-light rounded-pill px-4 py-2">
                        <span class="text-muted"><?= __('order_id') ?>:</span>
                        <span class="fw-bold text-primary fs-5">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($order): 
                $payment = $paymentLabels[$order['payment_method'] ?? 'cash'] ?? $paymentLabels['cash'];
            ?>
                <!-- Order Details Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-box me-2 text-primary"></i><?= __('order_detail') ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6 class="text-muted mb-2"><i class="fa-solid fa-location-dot me-1"></i><?= __('shipping_address') ?></h6>
                                <p class="fw-bold mb-1"><?= htmlspecialchars($order['fullname']) ?></p>
                                <p class="mb-1"><?= htmlspecialchars($order['phone']) ?></p>
                                <p class="mb-0 text-muted">
                                    <?= htmlspecialchars($order['address']) ?>, 
                                    <?= htmlspecialchars($order['ward'] ?? '') ?>, 
                                    <?= htmlspecialchars($order['district'] ?? '') ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2"><i class="fa-solid fa-credit-card me-1"></i><?= __('payment_method') ?></h6>
                                <span class="badge bg-<?= $payment['color'] ?> bg-opacity-10 text-<?= $payment['color'] ?> fs-6 px-3 py-2">
                                    <i class="fa-solid <?= $payment['icon'] ?> me-1"></i>
                                    <?= $payment['text'] ?>
                                </span>

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

                        <?php if (!empty($order['note'])): ?>
                            <div class="mb-4">
                                <h6 class="text-muted mb-2"><i class="fa-solid fa-note-sticky me-1"></i><?= __('order_note') ?></h6>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($order['note'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Order Items -->
                        <h6 class="text-muted mb-3"><i class="fa-solid fa-list me-1"></i><?= __('products') ?></h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead class="table-light">
                                    <tr>
                                        <th><?= __('product') ?></th>
                                        <th class="text-center"><?= __('quantity') ?></th>
                                        <th class="text-end"><?= __('subtotal') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= BASE_URL ?>public/images/<?= $item['image'] ?? 'placeholder.jpg' ?>" 
                                                         class="rounded me-2" width="50" height="50" 
                                                         style="object-fit: contain;"
                                                         onerror="this.src='https://via.placeholder.com/50'">
                                                    <span class="fw-bold"><?= htmlspecialchars($item['name'] ?? __('product')) ?></span>
                                                </div>
                                            </td>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td class="text-end fw-bold text-primary">
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold"><?= getCurrentLang() === 'en' ? 'Shipping' : 'Phí vận chuyển' ?>:</td>
                                        <td class="text-end text-success"><?= __('free_shipping') ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end h5 fw-bold mb-0"><?= __('total') ?>:</td>
                                        <td class="text-end h4 fw-bold text-danger mb-0">
                                            <?= number_format($order['total_money'], 0, ',', '.') ?>đ
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Status -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1"><?= __('order_status') ?></h6>
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="fa-solid fa-clock me-1"></i><?= __('status_pending') ?>
                                </span>
                            </div>
                            <div class="text-muted small">
                                <i class="fa-solid fa-calendar me-1"></i>
                                <?= __('order_date') ?>: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                <a href="<?= BASE_URL ?>orders/history" class="btn btn-primary btn-lg px-4">
                    <i class="fa-solid fa-list me-2"></i><?= __('my_orders') ?>
                </a>
                <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fa-solid fa-home me-2"></i><?= __('continue_shopping') ?>
                </a>
            </div>

            <!-- Additional Info -->
            <div class="text-center mt-5 pt-4 border-top">
                <p class="text-muted mb-2">
                    <i class="fa-solid fa-headset me-1"></i>
                    <?= getCurrentLang() === 'en' ? 'Questions? Call our hotline' : 'Có thắc mắc? Liên hệ hotline' ?>: <strong>1900 xxxx</strong>
                </p>
                <p class="text-muted small mb-0">
                    <?= getCurrentLang() === 'en' 
                        ? 'Order confirmation email has been sent to your email address' 
                        : 'Email xác nhận đơn hàng đã được gửi đến địa chỉ email của bạn' ?>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.success-checkmark {
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
