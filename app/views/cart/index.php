<div class="container py-5">
    <h2 class="fw-bold mb-4 text-uppercase">Giỏ hàng của bạn</h2>

    <?php if (isset($_SESSION['cart_error'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <?= $_SESSION['cart_error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['cart_error']); ?>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="120" alt="Empty Cart" class="mb-3 opacity-50">
            <h4 class="text-muted">Giỏ hàng đang trống!</h4>
            <p class="mb-4">Bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary px-4 py-2">
                <i class="fa-solid fa-arrow-left me-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <form action="<?= BASE_URL ?>cart/update" method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-3 ps-4">Sản phẩm</th>
                                            <th class="py-3">Đơn giá</th>
                                            <th class="py-3 text-center">Số lượng</th>
                                            <th class="py-3">Thành tiền</th>
                                            <th class="py-3 text-end pe-4">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_bill = 0;
                                        foreach ($cart as $id => $item): 
                                            $line_total = $item['price'] * $item['quantity'];
                                            $total_bill += $line_total;
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= BASE_URL . 'images/' . $item['image'] ?>" 
                                                         class="rounded border me-3" width="60" height="60" 
                                                         style="object-fit: contain;"
                                                         onerror="this.src='https://via.placeholder.com/60'">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>" class="text-dark text-decoration-none"><?= $item['name'] ?></a></h6>
                                                        <small class="text-muted">Mã SP: #<?= $item['id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                            <td class="text-center">
                                                <?php $maxStock = isset($stockMap[$id]) ? $stockMap[$id] : 99; ?>
                                                <input type="number" name="qty[<?= $id ?>]" 
                                                       value="<?= $item['quantity'] ?>" 
                                                       class="form-control form-control-sm text-center mx-auto" 
                                                       style="width: 70px;" min="1" max="<?= $maxStock ?>"
                                                       title="Tồn kho: <?= $maxStock ?>">
                                                <small class="text-muted" style="font-size:0.7rem;">Kho: <?= $maxStock ?></small>
                                            </td>
                                            <td class="fw-bold text-primary">
                                                <?= number_format($line_total, 0, ',', '.') ?>đ
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="cart-delete-wrap d-inline-block">
                                                    <!-- Nút xóa ban đầu -->
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-cart-delete-init" title="Xóa sản phẩm">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                    <!-- Xác nhận xóa (ẩn mặc định) -->
                                                    <div class="cart-delete-confirm d-none">
                                                        <a href="<?= BASE_URL ?>cart/remove/<?= $id ?>" 
                                                           class="btn btn-sm btn-danger me-1">
                                                            <i class="fa-solid fa-check me-1"></i>Xóa
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-secondary btn-cart-delete-cancel">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-3 text-end">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-rotate me-1"></i> Cập nhật giỏ hàng
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Cộng giỏ hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tạm tính:</span>
                                <span class="fw-bold"><?= number_format($total_bill, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 fw-bold">Tổng cộng:</span>
                                <span class="h4 fw-bold text-danger"><?= number_format($total_bill, 0, ',', '.') ?>đ</span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <a href="<?= BASE_URL ?>checkout" class="btn btn-primary btn-lg fw-bold">
                                        Tiến hành thanh toán
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>auth/login" class="btn btn-warning btn-lg fw-bold text-white">
                                        Đăng nhập để thanh toán
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary">
                                    Tiếp tục xem sản phẩm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>