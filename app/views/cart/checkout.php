<div class="container my-4">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-credit-card me-2 text-primary"></i>Thanh toán</h3>
    
    <div class="row g-4">
        <!-- Form thông tin giao hàng -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fa-solid fa-truck me-2"></i>Thông tin giao hàng</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= BASE_URL ?>checkout/placeOrder" method="POST" id="checkoutForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" class="form-control form-control-lg" 
                                   value="<?= htmlspecialchars($data['user']['fullname'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control form-control-lg" 
                                   value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>"
                                   placeholder="Ví dụ: 0901234567" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <?php $savedAddr = $_SESSION['address'] ?? ''; ?>
                            
                            <!-- Ô nhập địa chỉ - luôn hiển thị, chỉnh sửa trực tiếp -->
                            <input type="text" name="address" id="addressField" class="form-control form-control-lg mb-2" 
                                   value="<?= htmlspecialchars($savedAddr) ?>" 
                                   placeholder="Nhập địa chỉ giao hàng..." required>
                            
                            <a href="#" id="togglePicker" class="small text-primary text-decoration-none">
                                <i class="fa-solid fa-map-location-dot me-1"></i>Chọn từ danh sách tỉnh/huyện/xã
                            </a>

                            <!-- Bộ chọn tỉnh/huyện/xã (ẩn mặc định, toggle khi cần) -->
                            <div id="addrPicker" style="display:none" class="mt-2">
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <select id="province" class="form-select form-select-sm">
                                            <option value="">Tỉnh/Thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="district" class="form-select form-select-sm" disabled>
                                            <option value="">Quận/Huyện</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="ward" class="form-select form-select-sm" disabled>
                                            <option value="">Phường/Xã</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" id="streetAddr" class="form-control form-control-sm" placeholder="Số nhà, tên đường (VD: 123 Nguyễn Huệ)">
                                <button type="button" id="applyAddr" class="btn btn-sm btn-primary mt-2">
                                    <i class="fa-solid fa-check me-1"></i>Áp dụng địa chỉ này
                                </button>
                            </div>

                            <small class="text-muted d-block mt-1"><i class="fa-solid fa-info-circle me-1"></i>Bạn có thể nhập trực tiếp hoặc chọn từ danh sách</small>
                        </div>

                        <script>
                        (function() {
                            const API = 'https://provinces.open-api.vn/api/';
                            const pSel = document.getElementById('province');
                            const dSel = document.getElementById('district');
                            const wSel = document.getElementById('ward');
                            const street = document.getElementById('streetAddr');
                            const addrField = document.getElementById('addressField');
                            const picker = document.getElementById('addrPicker');
                            const toggleBtn = document.getElementById('togglePicker');
                            const applyBtn = document.getElementById('applyAddr');

                            // Toggle picker
                            toggleBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
                            });

                            // Áp dụng địa chỉ từ picker vào ô input
                            applyBtn.addEventListener('click', function() {
                                const parts = [];
                                if (street.value.trim()) parts.push(street.value.trim());
                                if (wSel.value && wSel.selectedOptions[0]) parts.push(wSel.selectedOptions[0].text);
                                if (dSel.value && dSel.selectedOptions[0]) parts.push(dSel.selectedOptions[0].text);
                                if (pSel.value && pSel.selectedOptions[0]) parts.push(pSel.selectedOptions[0].text);
                                if (parts.length > 0) {
                                    addrField.value = parts.join(', ');
                                    picker.style.display = 'none';
                                }
                            });

                            // Load tỉnh thành
                            fetch(API + '?depth=1').then(r => r.json()).then(data => {
                                data.sort((a, b) => a.name.localeCompare(b.name));
                                data.forEach(p => pSel.add(new Option(p.name, p.code)));
                            }).catch(() => {});

                            pSel.onchange = function() {
                                dSel.innerHTML = '<option value="">Quận/Huyện</option>';
                                wSel.innerHTML = '<option value="">Phường/Xã</option>';
                                dSel.disabled = true; wSel.disabled = true;
                                if (!this.value) return;
                                fetch(API + 'p/' + this.value + '?depth=2').then(r => r.json()).then(data => {
                                    data.districts.forEach(d => dSel.add(new Option(d.name, d.code)));
                                    dSel.disabled = false;
                                }).catch(() => {});
                            };

                            dSel.onchange = function() {
                                wSel.innerHTML = '<option value="">Phường/Xã</option>';
                                wSel.disabled = true;
                                if (!this.value) return;
                                fetch(API + 'd/' + this.value + '?depth=2').then(r => r.json()).then(data => {
                                    data.wards.forEach(w => wSel.add(new Option(w.name, w.code)));
                                    wSel.disabled = false;
                                }).catch(() => {});
                            };
                        })();
                        </script>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2" 
                                      placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)"></textarea>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-money-bill me-2"></i>Phương thức thanh toán</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment" value="cod" id="cod" checked>
                            <label class="form-check-label fw-medium" for="cod">
                                <i class="fa-solid fa-money-bill-wave text-success me-1"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment" value="bank" id="bank">
                            <label class="form-check-label fw-medium" for="bank">
                                <i class="fa-solid fa-building-columns text-primary me-1"></i> Chuyển khoản ngân hàng
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tổng đơn hàng -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3" style="position: sticky; top: 90px;">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-cart-shopping me-2"></i>Đơn hàng của bạn</h5>
                </div>
                <div class="card-body p-4">
                    <?php foreach ($data['cart'] as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= BASE_URL ?>images/<?= $item['image'] ?>" 
                                 class="rounded" style="width: 50px; height: 50px; object-fit: contain;"
                                 onerror="this.src='https://via.placeholder.com/50?text=?'">
                            <div>
                                <small class="fw-bold d-block" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($item['name']) ?>
                                </small>
                                <small class="text-muted">x<?= $item['quantity'] ?></small>
                            </div>
                        </div>
                        <span class="fw-bold text-danger"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</span>
                    </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-bold"><?= number_format($data['total'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Tổng cộng:</span>
                        <span class="fw-bold fs-5 text-danger"><?= number_format($data['total'], 0, ',', '.') ?>đ</span>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" form="checkoutForm" class="btn btn-danger btn-lg fw-bold shadow">
                            <i class="fa-solid fa-lock me-2"></i>Xác nhận đặt hàng
                        </button>
                        <a href="<?= BASE_URL ?>cart" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
