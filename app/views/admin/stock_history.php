<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-warehouse me-2"></i>Tra cứu Tồn kho</h2>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="fa-solid fa-magnifying-glass me-2"></i>Tra cứu</h5>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="<?= BASE_URL ?>admin/stockHistory">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn sản phẩm <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select form-select-lg" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php foreach ($data['products'] as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= ($data['selectedProductId'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tra cứu tồn kho tại ngày</label>
                        <input type="date" name="date" class="form-control form-control-lg" value="<?= htmlspecialchars($data['selectedDate']) ?>">
                        <small class="text-muted">Để trống = chỉ xem lịch sử</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fa-solid fa-search me-2"></i>Tra cứu
                    </button>
                </form>
            </div>
        </div>

        <?php if ($data['selectedProduct'] && $data['stockAtDate'] !== null): ?>
        <div class="card shadow-sm border-0 rounded-3 mt-4">
            <div class="card-body text-center py-4">
                <h6 class="text-muted mb-1">Tồn kho của</h6>
                <h5 class="fw-bold text-primary"><?= htmlspecialchars($data['selectedProduct']['name']) ?></h5>
                <p class="text-muted small mb-2">Tại ngày <?= date('d/m/Y', strtotime($data['selectedDate'])) ?></p>
                <div class="display-3 fw-bold text-success"><?= $data['stockAtDate'] ?></div>
                <small class="text-muted">sản phẩm</small>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted d-block">Tồn hiện tại</small>
                        <strong class="text-primary fs-4"><?= $data['selectedProduct']['stock'] ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Chênh lệch</small>
                        <?php $diff = intval($data['selectedProduct']['stock']) - $data['stockAtDate']; ?>
                        <strong class="fs-4 <?= $diff >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $diff >= 0 ? '+' : '' ?><?= $diff ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-7">
        <?php if (!empty($data['history'])): ?>
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử thay đổi kho</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Thời gian</th>
                                <th>Loại</th>
                                <th>SL</th>
                                <th>Trước</th>
                                <th>Sau</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['history'] as $h): ?>
                            <tr>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                                <td>
                                    <?php if ($h['change_type'] == 'import'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-arrow-down me-1"></i>Nhập</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-arrow-up me-1"></i>Bán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="<?= $h['change_type'] == 'import' ? 'text-success' : 'text-danger' ?>">
                                        <?= $h['change_type'] == 'import' ? '+' : '-' ?><?= $h['change_qty'] ?>
                                    </strong>
                                </td>
                                <td><?= $h['stock_before'] ?></td>
                                <td class="fw-bold"><?= $h['stock_after'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php elseif ($data['selectedProductId'] > 0): ?>
        <!-- Hiển thị thông tin tồn kho hiện tại dù chưa có lịch sử -->
        <?php if ($data['selectedProduct']): ?>
        <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="fa-solid fa-box me-2"></i>Thông tin tồn kho hiện tại</h5>
            </div>
            <div class="card-body">
                <h5 class="fw-bold text-primary mb-3"><?= htmlspecialchars($data['selectedProduct']['name']) ?></h5>
                <div class="row text-center g-3">
                    <div class="col-3">
                        <div class="border rounded-3 py-3">
                            <small class="text-muted d-block">Tồn kho</small>
                            <strong class="fs-3 text-success"><?= $data['selectedProduct']['stock'] ?></strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded-3 py-3">
                            <small class="text-muted d-block">Giá vốn</small>
                            <strong class="text-info"><?= number_format($data['selectedProduct']['cost_price'], 0, ',', '.') ?>đ</strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded-3 py-3">
                            <small class="text-muted d-block">Giá bán</small>
                            <strong class="text-primary"><?= number_format($data['selectedProduct']['price'], 0, ',', '.') ?>đ</strong>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded-3 py-3">
                            <small class="text-muted d-block">% LN</small>
                            <strong class="text-warning"><?= $data['selectedProduct']['profit_margin'] ?? 0 ?>%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Chưa có lịch sử thay đổi kho cho sản phẩm này.</p>
                <small class="text-muted">Hãy nhập hàng qua phiếu nhập để có lịch sử.</small>
            </div>
        </div>
        <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-warehouse fa-3x text-muted mb-3"></i>
                <p class="text-muted">Chọn sản phẩm để xem lịch sử thay đổi kho</p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($data['import_batches'])): ?>
        <div class="card shadow-sm border-0 rounded-3 mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-money-bill-trend-up me-2 text-success"></i>Giá vốn & Giá bán theo lô nhập</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Lần nhập</th>
                                <th>Ngày nhập</th>
                                <th>SL</th>
                                <th>Giá vốn (nhập)</th>
                                <th>% Lợi nhuận</th>
                                <th>Giá bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['import_batches'] as $idx => $batch): ?>
                            <tr>
                                <td><span class="badge bg-primary-subtle text-primary">#<?= count($data['import_batches']) - $idx ?></span></td>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($batch['created_at'] ?? $batch['import_date'] ?? '')) ?></td>
                                <td class="fw-bold"><?= $batch['quantity'] ?></td>
                                <td class="text-info fw-bold"><?= number_format($batch['import_price'], 0, ',', '.') ?>đ</td>
                                <td><span class="badge bg-success-subtle text-success"><?= $batch['profit_margin'] ?? 0 ?>%</span></td>
                                <td class="text-primary fw-bold">
                                    <?php
                                    $margin = floatval($batch['profit_margin'] ?? 0);
                                    $sellPrice = $batch['import_price'] * (1 + $margin / 100);
                                    echo number_format($sellPrice, 0, ',', '.') . 'đ';
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
