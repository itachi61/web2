<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Tổng sản phẩm</h6>
                        <h3 class="fw-bold mb-0">
                            <?php
                            try {
                                $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                                $stmt = $db->query("SELECT COUNT(*) as total FROM products");
                                echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            } catch(Exception $e) { echo '0'; }
                            ?>
                        </h3>
                    </div>
                    <i class="fa-solid fa-box fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Tổng đơn hàng</h6>
                        <h3 class="fw-bold mb-0">
                            <?php
                            try {
                                $stmt = $db->query("SELECT COUNT(*) as total FROM orders");
                                echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            } catch(Exception $e) { echo '0'; }
                            ?>
                        </h3>
                    </div>
                    <i class="fa-solid fa-cart-flatbed fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning bg-gradient text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Khách hàng</h6>
                        <h3 class="fw-bold mb-0">
                            <?php
                            try {
                                $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
                                echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            } catch(Exception $e) { echo '0'; }
                            ?>
                        </h3>
                    </div>
                    <i class="fa-solid fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Cảnh báo sắp hết hàng</h5>
        <form method="GET" action="<?= BASE_URL ?>admin" class="row g-2 mb-3 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-bold mb-1">Ngưỡng cảnh báo (≤)</label>
                <input type="number" name="threshold" class="form-control form-control-sm" style="width:100px" 
                       value="<?= intval($_GET['threshold'] ?? 5) ?>" min="1">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-filter me-1"></i>Lọc</button>
            </div>
        </form>
        <?php
        $threshold = intval($_GET['threshold'] ?? 5);
        try {
            $lowStock = $db->prepare("SELECT id, name, stock, image FROM products WHERE stock <= ? AND is_hidden = 0 ORDER BY stock ASC");
            $lowStock->execute([$threshold]);
            $lowProducts = $lowStock->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) { $lowProducts = []; }
        ?>
        <?php if (!empty($lowProducts)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-danger">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowProducts as $lp): ?>
                        <tr>
                            <td>
                                <img src="<?= BASE_URL ?>images/<?= $lp['image'] ?>" class="rounded" style="width:40px;height:40px;object-fit:cover"
                                     onerror="this.src='https://via.placeholder.com/40?text=No'">
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($lp['name']) ?></td>
                            <td>
                                <span class="badge bg-danger fs-6"><?= $lp['stock'] ?></span>
                            </td>
                            <td>
                                <?php if ($lp['stock'] == 0): ?>
                                    <span class="badge bg-dark"><i class="fa-solid fa-xmark me-1"></i>Hết hàng</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Sắp hết</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-2 text-end">
                <small class="text-muted">Tổng: <strong class="text-danger"><?= count($lowProducts) ?></strong> sản phẩm cần nhập thêm</small>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fa-solid fa-check-circle fa-3x text-success mb-2"></i>
                <p class="text-muted">Không có sản phẩm nào sắp hết hàng (ngưỡng ≤ <?= $threshold ?>)</p>
            </div>
        <?php endif; ?>
    </div>
</div>
