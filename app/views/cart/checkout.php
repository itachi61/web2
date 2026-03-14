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
                            <?php if ($savedAddr): ?>
                            <div class="border rounded bg-light py-2 px-3 mb-2 d-flex justify-content-between align-items-center" id="savedAddrBox">
                                <div>
                                    <i class="fa-solid fa-location-dot text-primary me-1"></i>
                                    <span class="small"><?= htmlspecialchars($savedAddr) ?></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnChangeAddr">
                                    <i class="fa-solid fa-pen me-1"></i>Đổi
                                </button>
                            </div>
                            <?php endif; ?>

                            <div id="newAddrForm" style="<?= $savedAddr ? 'display:none' : '' ?>">
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <select id="province" class="form-select">
                                            <option value="">Tỉnh/Thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="district" class="form-select" disabled>
                                            <option value="">Quận/Huyện</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select id="ward" class="form-select" disabled>
                                            <option value="">Phường/Xã</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" id="streetAddr" class="form-control" placeholder="Số nhà, tên đường (VD: 123 Nguyễn Huệ)">
                            </div>

                            <input type="hidden" name="address" id="addressField" value="<?= htmlspecialchars($savedAddr) ?>" required>
                            <small class="text-muted"><i class="fa-solid fa-info-circle me-1"></i>Bạn có thể dùng địa chỉ khác cho đơn hàng này</small>
                        </div>

                        <script>
                        (function() {
                            const API = 'https://provinces.open-api.vn/api/';
                            const pSel = document.getElementById('province');
                            const dSel = document.getElementById('district');
                            const wSel = document.getElementById('ward');
                            const street = document.getElementById('streetAddr');
                            const addrField = document.getElementById('addressField');
                            const savedBox = document.getElementById('savedAddrBox');
                            const btnChange = document.getElementById('btnChangeAddr');
                            const newForm = document.getElementById('newAddrForm');

                            function buildAddr() {
                                const parts = [];
                                if (street && street.value) parts.push(street.value);
                                if (wSel && wSel.value && wSel.selectedOptions[0]) parts.push(wSel.selectedOptions[0].text);
                                if (dSel && dSel.value && dSel.selectedOptions[0]) parts.push(dSel.selectedOptions[0].text);
                                if (pSel && pSel.value && pSel.selectedOptions[0]) parts.push(pSel.selectedOptions[0].text);
                                if (parts.length > 0) addrField.value = parts.join(', ');
                            }

                            // Nút Đổi địa chỉ
                            if (btnChange) {
                                btnChange.addEventListener('click', function() {
                                    savedBox.style.display = 'none';
                                    newForm.style.display = 'block';
                                    addrField.value = '';
                                });
                            }

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
                                buildAddr();
                            };

                            dSel.onchange = function() {
                                wSel.innerHTML = '<option value="">Phường/Xã</option>';
                                wSel.disabled = true;
                                if (!this.value) return;
                                fetch(API + 'd/' + this.value + '?depth=2').then(r => r.json()).then(data => {
                                    data.wards.forEach(w => wSel.add(new Option(w.name, w.code)));
                                    wSel.disabled = false;
                                }).catch(() => {});
                                buildAddr();
                            };

                            wSel.onchange = buildAddr;
                            if (street) street.addEventListener('input', buildAddr);

                            // Validate: phải có địa chỉ trước khi submit
                            document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                                if (!addrField.value.trim()) {
                                    e.preventDefault();
                                    alert('Vui lòng nhập địa chỉ giao hàng!');
                                }
                            });
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
