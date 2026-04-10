<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="container my-4">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-box me-2 text-primary"></i>Đơn hàng của tôi</h3>

    <?php if (!empty($_SESSION['order_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i><?= $_SESSION['order_success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['order_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['order_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i><?= $_SESSION['order_error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['order_error']); ?>
    <?php endif; ?>

    <?php if (empty($data['orders'])): ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-box-open fa-4x text-muted opacity-50 mb-3"></i>
            <p class="h5 text-muted">Bạn chưa có đơn hàng nào.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary mt-3"><i class="fa-solid fa-bag-shopping me-2"></i>Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <?php foreach ($data['orders'] as $order): ?>
        <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold">Đơn hàng #<?= $order['id'] ?></span>
                    <small class="text-muted ms-2"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                </div>
                <?php if ($order['status'] === 'pending'): ?>
                    <form method="POST" action="<?= BASE_URL ?>auth/cancelOrder/<?= $order['id'] ?>" 
                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng #<?= $order['id'] ?>?')">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-xmark me-1"></i>Hủy đơn
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <?php
                $statusMap = [
                    'pending' => ['Chờ xử lý', 'warning', 'fa-clock'],
                    'processing' => ['Đang giao hàng', 'info', 'fa-truck'],
                    'completed' => ['Hoàn thành', 'success', 'fa-circle-check'],
                    'cancelled' => ['Đã hủy', 'danger', 'fa-circle-xmark']
                ];
                $s = $statusMap[$order['status']] ?? ['N/A', 'secondary', 'fa-question'];
                ?>
                <div class="d-flex align-items-center mb-3 p-2 rounded-3 bg-<?= $s[1] ?> bg-opacity-10 border border-<?= $s[1] ?>">
                    <i class="fa-solid <?= $s[2] ?> text-<?= $s[1] ?> fs-4 me-3"></i>
                    <div>
                        <span class="fw-bold text-<?= $s[1] ?>"><?= $s[0] ?></span>
                        <small class="text-muted d-block">Trạng thái đơn hàng</small>
                    </div>
                    <?php if ($order['status'] === 'processing'): ?>
                        <small class="ms-auto text-muted fst-italic">
                            <i class="fa-solid fa-info-circle me-1"></i>Đơn hàng đang được giao, không thể hủy
                        </small>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted"><i class="fa-solid fa-user me-1"></i> <?= htmlspecialchars($order['fullname']) ?></small><br>
                        <small class="text-muted"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($order['phone']) ?></small><br>
                        <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($order['address']) ?></small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="fw-bold fs-5 text-danger"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
                    </div>
                </div>

                <?php if (!empty($order['items'])): ?>
                <hr>
                <div class="d-flex flex-wrap gap-3">
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="d-flex align-items-center gap-2 border rounded-3 p-2 bg-light">
                        <img src="<?= BASE_URL ?>images/<?= $item['image'] ?>" class="rounded" style="width: 40px; height: 40px; object-fit: contain;" onerror="this.src='https://via.placeholder.com/40?text=?'">
                        <div>
                            <small class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></small>
                            <small class="text-muted d-block">x<?= $item['quantity'] ?> • <?= number_format($item['price'], 0, ',', '.') ?>đ</small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
