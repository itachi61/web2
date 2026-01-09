<div class="admin-header">
  <h1>Dashboard</h1>
  <p>Chào mừng quay lại, Admin!</p>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon">📦</div>
    <div class="stat-info">
      <h3><?= number_format($stats['total_products']) ?></h3>
      <p>Sản phẩm</p>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon">📋</div>
    <div class="stat-info">
      <h3><?= number_format($stats['total_orders']) ?></h3>
      <p>Đơn hàng</p>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon">💰</div>
    <div class="stat-info">
      <h3><?= number_format($stats['total_revenue']) ?>đ</h3>
      <p>Doanh thu</p>
    </div>
  </div>
  
  <div class="stat-card">
    <div class="stat-icon">👥</div>
    <div class="stat-info">
      <h3><?= number_format($stats['total_users']) ?></h3>
      <p>Khách hàng</p>
    </div>
  </div>
</div>

<div class="admin-section">
  <h2>Đơn hàng gần đây</h2>
  <div class="table-container">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Khách hàng</th>
          <th>Tổng tiền</th>
          <th>Trạng thái</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentOrders as $order): ?>
          <tr>
            <td>#<?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['customer']) ?></td>
            <td><?= number_format($order['total']) ?>đ</td>
            <td><span class="badge"><?= htmlspecialchars($order['status']) ?></span></td>
            <td>
              <a href="#" class="btn-small">Xem</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
