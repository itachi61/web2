<?php
// Lấy thông tin user đang đăng nhập
$user = $data['user'] ?? null;
$cart = $data['cart'] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>cart" class="text-decoration-none">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-4 text-uppercase">
        <i class="fa-solid fa-credit-card me-2 text-primary"></i>Thanh Toán Đơn Hàng
    </h2>

    <?php if(isset($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= $data['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>checkout/process" method="POST" id="checkoutForm">
        <div class="row g-4">
            <!-- Cột trái: Thông tin giao hàng + Thanh toán -->
            <div class="col-lg-8">
                
                <!-- Section 1: Thông tin giao hàng -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <span class="badge bg-primary rounded-circle me-2">1</span>
                            Thông tin giao hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Chọn sử dụng địa chỉ từ tài khoản hoặc nhập mới -->
                        <div class="mb-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="address_type" id="useAccountAddress" value="account" checked>
                                <label class="form-check-label fw-semibold" for="useAccountAddress">
                                    <i class="fa-solid fa-user me-1"></i>Sử dụng địa chỉ tài khoản
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="address_type" id="useNewAddress" value="new">
                                <label class="form-check-label fw-semibold" for="useNewAddress">
                                    <i class="fa-solid fa-plus me-1"></i>Nhập địa chỉ mới
                                </label>
                            </div>
                        </div>

                        <!-- Địa chỉ từ tài khoản -->
                        <div id="accountAddressSection">
                            <?php if ($user && !empty($user['address'])): ?>
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small">Họ và tên</label>
                                                <p class="fw-bold mb-0"><?= htmlspecialchars($user['fullname']) ?></p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted small">Điện thoại</label>
                                                <p class="fw-bold mb-0"><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></p>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label text-muted small">Địa chỉ</label>
                                                <p class="fw-bold mb-0">
                                                    <?= htmlspecialchars($user['address'] ?? '') ?>, 
                                                    <?= htmlspecialchars($user['ward'] ?? '') ?>, 
                                                    <?= htmlspecialchars($user['district'] ?? '') ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden fields for account address -->
                                <input type="hidden" name="account_fullname" value="<?= htmlspecialchars($user['fullname']) ?>">
                                <input type="hidden" name="account_phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                <input type="hidden" name="account_address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                                <input type="hidden" name="account_ward" value="<?= htmlspecialchars($user['ward'] ?? '') ?>">
                                <input type="hidden" name="account_district" value="<?= htmlspecialchars($user['district'] ?? '') ?>">
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                    Bạn chưa cập nhật địa chỉ giao hàng trong tài khoản. Vui lòng nhập địa chỉ mới bên dưới.
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        document.getElementById('useNewAddress').checked = true;
                                        document.getElementById('accountAddressSection').style.display = 'none';
                                        document.getElementById('newAddressSection').style.display = 'block';
                                    });
                                </script>
                            <?php endif; ?>
                        </div>

                        <!-- Địa chỉ mới -->
                        <div id="newAddressSection" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="new_fullname" placeholder="Nguyễn Văn A">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="new_phone" placeholder="0901234567">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select class="form-select" name="new_district">
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                        <option value="Quận 1">Quận 1</option>
                                        <option value="Quận 2">Quận 2</option>
                                        <option value="Quận 3">Quận 3</option>
                                        <option value="Quận 4">Quận 4</option>
                                        <option value="Quận 5">Quận 5</option>
                                        <option value="Quận 6">Quận 6</option>
                                        <option value="Quận 7">Quận 7</option>
                                        <option value="Quận 8">Quận 8</option>
                                        <option value="Quận 9">Quận 9</option>
                                        <option value="Quận 10">Quận 10</option>
                                        <option value="Quận 11">Quận 11</option>
                                        <option value="Quận 12">Quận 12</option>
                                        <option value="Quận Bình Thạnh">Quận Bình Thạnh</option>
                                        <option value="Quận Gò Vấp">Quận Gò Vấp</option>
                                        <option value="Quận Phú Nhuận">Quận Phú Nhuận</option>
                                        <option value="Quận Tân Bình">Quận Tân Bình</option>
                                        <option value="Quận Tân Phú">Quận Tân Phú</option>
                                        <option value="Quận Thủ Đức">Quận Thủ Đức</option>
                                        <option value="Huyện Bình Chánh">Huyện Bình Chánh</option>
                                        <option value="Huyện Củ Chi">Huyện Củ Chi</option>
                                        <option value="Huyện Hóc Môn">Huyện Hóc Môn</option>
                                        <option value="Huyện Nhà Bè">Huyện Nhà Bè</option>
                                        <option value="Huyện Cần Giờ">Huyện Cần Giờ</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phường/Xã <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="new_ward" placeholder="VD: Phường Bến Nghé">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="new_address" placeholder="Số nhà, tên đường...">
                                </div>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mt-4">
                            <label class="form-label fw-semibold">Ghi chú đơn hàng (không bắt buộc)</label>
                            <textarea class="form-control" name="note" rows="2" placeholder="VD: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Phương thức thanh toán -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <span class="badge bg-primary rounded-circle me-2">2</span>
                            Phương thức thanh toán
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Tiền mặt -->
                            <div class="col-12">
                                <div class="form-check payment-option p-3 border rounded-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payCash" value="cash" checked>
                                    <label class="form-check-label w-100 d-flex align-items-center" for="payCash">
                                        <i class="fa-solid fa-money-bill-wave fa-2x text-success me-3"></i>
                                        <div>
                                            <span class="fw-bold d-block">Thanh toán khi nhận hàng (COD)</span>
                                            <small class="text-muted">Thanh toán bằng tiền mặt khi nhận hàng</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Chuyển khoản -->
                            <div class="col-12">
                                <div class="form-check payment-option p-3 border rounded-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payBank" value="bank_transfer">
                                    <label class="form-check-label w-100 d-flex align-items-center" for="payBank">
                                        <i class="fa-solid fa-building-columns fa-2x text-primary me-3"></i>
                                        <div>
                                            <span class="fw-bold d-block">Chuyển khoản ngân hàng</span>
                                            <small class="text-muted">Chuyển khoản trước khi giao hàng</small>
                                        </div>
                                    </label>
                                </div>
                                <!-- Chi tiết chuyển khoản -->
                                <div id="bankDetails" class="mt-3 p-3 bg-light rounded-3" style="display: none;">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-circle-info me-2"></i>Thông tin chuyển khoản</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Ngân hàng:</strong> Vietcombank</p>
                                            <p class="mb-1"><strong>Số tài khoản:</strong> <span class="text-primary">1234567890</span></p>
                                            <p class="mb-1"><strong>Chủ tài khoản:</strong> TECHSMART JSC</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Chi nhánh:</strong> TP. Hồ Chí Minh</p>
                                            <p class="mb-1"><strong>Nội dung CK:</strong> <code>DONHANG [Mã đơn]</code></p>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-2 mb-0">
                                        <small><i class="fa-solid fa-lightbulb me-1"></i>Sau khi đặt hàng, bạn sẽ nhận được mã đơn để ghi vào nội dung chuyển khoản.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Thanh toán online -->
                            <div class="col-12">
                                <div class="form-check payment-option p-3 border rounded-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payOnline" value="online">
                                    <label class="form-check-label w-100 d-flex align-items-center" for="payOnline">
                                        <i class="fa-solid fa-credit-card fa-2x text-warning me-3"></i>
                                        <div>
                                            <span class="fw-bold d-block">Thanh toán trực tuyến</span>
                                            <small class="text-muted">VISA, MasterCard, Momo, ZaloPay...</small>
                                        </div>
                                        <span class="badge bg-secondary ms-auto">Sắp ra mắt</span>
                                    </label>
                                </div>
                                <div id="onlinePaymentNotice" class="mt-2 p-3 bg-warning bg-opacity-10 rounded-3" style="display: none;">
                                    <p class="mb-0 text-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>
                                        Chức năng thanh toán trực tuyến đang được phát triển. Vui lòng chọn phương thức khác.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-receipt me-2 text-primary"></i>Đơn hàng của bạn
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Danh sách sản phẩm -->
                        <div class="order-items mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($cart as $item): ?>
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <div class="position-relative me-3">
                                        <img src="<?= BASE_URL ?>img/<?= $item['image'] ?>" 
                                             class="rounded border" width="60" height="60" 
                                             style="object-fit: contain;"
                                             onerror="this.src='https://via.placeholder.com/60'">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                            <?= $item['quantity'] ?>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 small fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                        <span class="text-muted small"><?= number_format($item['price'], 0, ',', '.') ?>đ x <?= $item['quantity'] ?></span>
                                    </div>
                                    <span class="fw-bold text-primary">
                                        <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Tính tiền -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính:</span>
                                <span><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 fw-bold mb-0">Tổng cộng:</span>
                                <span class="h4 fw-bold text-danger mb-0"><?= number_format($total, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>

                        <!-- Nút đặt hàng -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg text-white fw-bold py-3" id="submitBtn"
                                    style="background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);">
                                <i class="fa-solid fa-check-circle me-2"></i>Đặt hàng
                            </button>
                            <a href="<?= BASE_URL ?>cart" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại giỏ hàng
                            </a>
                        </div>

                        <p class="text-muted small text-center mt-3 mb-0">
                            <i class="fa-solid fa-lock me-1"></i>
                            Thông tin của bạn được bảo mật
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.payment-option {
    cursor: pointer;
    transition: all 0.3s ease;
}
.payment-option:hover {
    border-color: #0072ff !important;
    background: rgba(0, 114, 255, 0.05);
}
.payment-option:has(input:checked) {
    border-color: #0072ff !important;
    background: rgba(0, 114, 255, 0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle địa chỉ mới / tài khoản
    const useAccountAddress = document.getElementById('useAccountAddress');
    const useNewAddress = document.getElementById('useNewAddress');
    const accountSection = document.getElementById('accountAddressSection');
    const newSection = document.getElementById('newAddressSection');

    if (useAccountAddress && useNewAddress) {
        useAccountAddress.addEventListener('change', function() {
            if (this.checked) {
                accountSection.style.display = 'block';
                newSection.style.display = 'none';
            }
        });

        useNewAddress.addEventListener('change', function() {
            if (this.checked) {
                accountSection.style.display = 'none';
                newSection.style.display = 'block';
            }
        });
    }

    // Toggle thông tin chuyển khoản
    const payBank = document.getElementById('payBank');
    const payOnline = document.getElementById('payOnline');
    const bankDetails = document.getElementById('bankDetails');
    const onlineNotice = document.getElementById('onlinePaymentNotice');

    document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            bankDetails.style.display = this.value === 'bank_transfer' ? 'block' : 'none';
            onlineNotice.style.display = this.value === 'online' ? 'block' : 'none';
        });
    });

    // Form validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const addressType = document.querySelector('input[name="address_type"]:checked').value;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

        // Kiểm tra thanh toán online
        if (paymentMethod === 'online') {
            e.preventDefault();
            alert('Chức năng thanh toán trực tuyến chưa được hỗ trợ. Vui lòng chọn phương thức khác.');
            return;
        }

        // Kiểm tra địa chỉ mới
        if (addressType === 'new') {
            const fullname = document.querySelector('input[name="new_fullname"]').value.trim();
            const phone = document.querySelector('input[name="new_phone"]').value.trim();
            const district = document.querySelector('select[name="new_district"]').value;
            const ward = document.querySelector('input[name="new_ward"]').value.trim();
            const address = document.querySelector('input[name="new_address"]').value.trim();

            if (!fullname || !phone || !district || !ward || !address) {
                e.preventDefault();
                alert('Vui lòng nhập đầy đủ thông tin giao hàng!');
                return;
            }

            if (!/^[0-9]{10,11}$/.test(phone)) {
                e.preventDefault();
                alert('Số điện thoại không hợp lệ!');
                return;
            }
        }
    });
});
</script>
