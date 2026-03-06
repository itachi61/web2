<div class="container py-5">
    <h2 class="fw-bold mb-4 text-uppercase"><?= __('your_cart') ?></h2>

    <?php if (empty($cart)): ?>
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="120" alt="Empty Cart" class="mb-3 opacity-50">
            <h4 class="text-muted"><?= __('cart_empty') ?>!</h4>
            <p class="mb-4">
                <?= getCurrentLang() === 'en' 
                    ? "You haven't added any products to your cart yet." 
                    : 'Bạn chưa thêm sản phẩm nào vào giỏ hàng.' ?>
            </p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary px-4 py-2">
                <i class="fa-solid fa-arrow-left me-2"></i><?= __('continue_shopping') ?>
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
                                            <th class="py-3 ps-4"><?= __('product') ?></th>
                                            <th class="py-3"><?= __('price') ?></th>
                                            <th class="py-3 text-center"><?= __('quantity') ?></th>
                                            <th class="py-3"><?= __('subtotal') ?></th>
                                            <th class="py-3 text-end pe-4"><?= __('remove') ?></th>
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
                                                    <img src="<?= BASE_URL ?>public/images/<?= $item['image'] ?>" 
                                                         class="rounded border me-3" width="60" height="60" 
                                                         style="object-fit: contain;"
                                                         onerror="this.src='https://via.placeholder.com/60'">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">
                                                            <a href="<?= BASE_URL ?>product/detail/<?= $item['id'] ?>" class="text-dark text-decoration-none">
                                                                <?= htmlspecialchars($item['name']) ?>
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted">ID: #<?= $item['id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                            <td class="text-center">
                                                <input type="number" name="qty[<?= $id ?>]" 
                                                       value="<?= $item['quantity'] ?>" 
                                                       class="form-control form-control-sm text-center mx-auto" 
                                                       style="width: 60px;" min="1">
                                            </td>
                                            <td class="fw-bold text-primary">
                                                <?= number_format($line_total, 0, ',', '.') ?>đ
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="<?= BASE_URL ?>cart/remove/<?= $id ?>" 
                                                   class="btn btn-sm btn-outline-danger border-0"
                                                   onclick="return confirm('<?= getCurrentLang() === 'en' ? 'Remove this product from cart?' : 'Xóa sản phẩm này khỏi giỏ hàng?' ?>');">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-3 text-end">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-rotate me-1"></i> <?= __('update') ?>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold"><?= __('order_summary') ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span><?= __('subtotal') ?>:</span>
                                <span class="fw-bold"><?= number_format($total_bill, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span><?= getCurrentLang() === 'en' ? 'Shipping' : 'Phí vận chuyển' ?>:</span>
                                <span class="text-success"><?= __('free_shipping') ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 fw-bold"><?= __('total') ?>:</span>
                                <span class="h4 fw-bold text-danger"><?= number_format($total_bill, 0, ',', '.') ?>đ</span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <a href="<?= BASE_URL ?>checkout" class="btn btn-primary btn-lg fw-bold">
                                        <?= __('proceed_checkout') ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>auth/login" class="btn btn-warning btn-lg fw-bold text-white">
                                        <?= getCurrentLang() === 'en' ? 'Login to Checkout' : 'Đăng nhập để thanh toán' ?>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary">
                                    <?= __('continue_shopping') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>