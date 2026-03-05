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
        <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-line text-primary me-2"></i>Chào mừng đến bảng điều khiển</h5>
        <p class="text-muted">Sử dụng menu bên trái để quản lý sản phẩm, đơn hàng, và khách hàng.</p>
    </div>
</div>
