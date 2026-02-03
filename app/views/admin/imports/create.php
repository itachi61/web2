<!-- Create Import Form (Tạo phiếu nhập) -->
<div class="mb-4">
    <a href="<?= BASE_URL ?>admin/imports" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
    </a>
</div>

<form action="<?= BASE_URL ?>admin/storeImport" method="POST" id="importForm">
    <div class="row g-4">
        <!-- Import Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-file-invoice text-primary me-2"></i>Thông tin phiếu
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Mã phiếu <span class="text-danger">*</span></label>
                        <input type="text" name="import_code" class="form-control" 
                               value="IMP-<?= date('Ymd') ?>-<?= str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) ?>" 
                               required readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhà cung cấp</label>
                        <input type="text" name="supplier" class="form-control" placeholder="Tên nhà cung cấp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú thêm..."></textarea>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng số sản phẩm:</span>
                        <strong id="totalItems">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tổng tiền nhập:</span>
                        <strong class="text-primary fs-5" id="totalAmount">0đ</strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-warning">
                            <i class="fa-solid fa-file-pen me-2"></i>Lưu nháp
                        </button>
                        <button type="submit" name="action" value="complete" class="btn btn-success">
                            <i class="fa-solid fa-check me-2"></i>Hoàn thành nhập
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Selection -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Chọn sản phẩm
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i class="fa-solid fa-plus me-2"></i>Thêm SP
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="importItemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th style="width: 120px;">Số lượng</th>
                                    <th style="width: 150px;">Giá nhập</th>
                                    <th style="width: 150px;">Thành tiền</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody id="importItems">
                                <tr id="emptyRow">
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fa-solid fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                                        Chưa có sản phẩm nào. Nhấn "Thêm SP" để bắt đầu.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-box text-primary me-2"></i>Chọn sản phẩm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchProduct" placeholder="Tìm kiếm sản phẩm..." 
                           onkeyup="searchProductFilter()" oninput="searchProductFilter()">
                </div>
                <div class="list-group" id="productList" style="max-height: 400px; overflow-y: auto;">
                    <?php if (!empty($data['products'])): ?>
                        <?php foreach ($data['products'] as $product): ?>
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center product-item"
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                data-price="<?= $product['cost_price'] ?? ($product['price'] * 0.8) ?>">
                            <div>
                                <strong class="product-name"><?= htmlspecialchars($product['name']) ?></strong>
                                <br><small class="text-muted">Tồn kho: <?= $product['stock'] ?? 0 ?></small>
                            </div>
                            <span class="badge bg-primary">Chọn</span>
                        </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">Không có sản phẩm nào</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Search product filter function - must be global
function searchProductFilter() {
    var input = document.getElementById('searchProduct');
    var filter = input.value.toLowerCase();
    var productList = document.getElementById('productList');
    var items = productList.getElementsByClassName('product-item');
    
    for (var i = 0; i < items.length; i++) {
        var productName = items[i].getAttribute('data-name');
        if (productName) {
            if (productName.toLowerCase().indexOf(filter) > -1) {
                items[i].classList.remove('d-none');
            } else {
                items[i].classList.add('d-none');
            }
        }
    }
}
</script>

<script>
let items = [];
let itemIndex = 0;

// Add product to import list
document.querySelectorAll('.product-item').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const price = parseFloat(this.dataset.price);
        
        // Check if already added
        if (items.find(i => i.id === id)) {
            alert('Sản phẩm đã được thêm!');
            return;
        }
        
        addItem(id, name, price);
        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
    });
});

function addItem(id, name, price) {
    items.push({ id, name, price, quantity: 1 });
    renderItems();
}

function removeItem(index) {
    items.splice(index, 1);
    renderItems();
}

function updateQuantity(index, qty) {
    items[index].quantity = parseInt(qty) || 1;
    calculateTotal();
}

function updatePrice(index, price) {
    items[index].price = parseFloat(price) || 0;
    calculateTotal();
}

function renderItems() {
    const tbody = document.getElementById('importItems');
    
    if (items.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fa-solid fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                    Chưa có sản phẩm nào. Nhấn "Thêm SP" để bắt đầu.
                </td>
            </tr>
        `;
        calculateTotal();
        return;
    }
    
    tbody.innerHTML = items.map((item, index) => `
        <tr>
            <td>
                <strong>${item.name}</strong>
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
            </td>
            <td>
                <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm" 
                       value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)">
            </td>
            <td>
                <input type="number" name="items[${index}][price]" class="form-control form-control-sm" 
                       value="${item.price}" min="0" step="1000" onchange="updatePrice(${index}, this.value)">
            </td>
            <td class="fw-bold text-primary item-total">${formatNumber(item.quantity * item.price)}đ</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">
                    <i class="fa-solid fa-times"></i>
                </button>
            </td>
        </tr>
    `).join('');
    
    calculateTotal();
}

function calculateTotal() {
    const totalItems = items.reduce((sum, i) => sum + i.quantity, 0);
    const totalAmount = items.reduce((sum, i) => sum + (i.quantity * i.price), 0);
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalAmount').textContent = formatNumber(totalAmount) + 'đ';
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>
