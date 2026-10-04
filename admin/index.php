<?php
// admin/index.php — Dashboard: thống kê tổng quan
$admin_title = 'Dashboard';
require_once 'includes/header.php';

$total_products  = db_one($conn, 'SELECT COUNT(*) AS c FROM products')['c'];
$total_orders    = db_one($conn, 'SELECT COUNT(*) AS c FROM orders')['c'];
$total_customers = db_one($conn, 'SELECT COUNT(*) AS c FROM customers')['c'];
$revenue         = db_one($conn, "SELECT COALESCE(SUM(total),0) AS s FROM orders WHERE status <> 'da_huy'")['s'];
$pending_orders  = db_one($conn, "SELECT COUNT(*) AS c FROM orders WHERE status = 'moi'")['c'];

$recent_orders = db_all($conn, 'SELECT * FROM orders ORDER BY id DESC LIMIT 5');
$low_stock     = db_all($conn, 'SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5');
?>

<h2>Dashboard</h2>

<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= (int)$total_products ?></div><div class="lbl">Sản phẩm</div></div>
  <div class="stat-card"><div class="num"><?= (int)$total_orders ?></div><div class="lbl">Đơn hàng</div></div>
  <div class="stat-card"><div class="num"><?= (int)$pending_orders ?></div><div class="lbl">Đơn chờ xác nhận</div></div>
  <div class="stat-card"><div class="num"><?= (int)$total_customers ?></div><div class="lbl">Khách hàng</div></div>
  <div class="stat-card"><div class="num"><?= format_price($revenue) ?></div><div class="lbl">Doanh thu</div></div>
</div>

<div class="panel">
  <h3>Đơn hàng mới nhất</h3>
  <table class="table">
    <tr><th>Mã</th><th>Khách hàng</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày đặt</th><th></th></tr>
    <?php foreach ($recent_orders as $o): ?>
    <tr>
      <td>#<?= (int)$o['id'] ?></td>
      <td><?= esc($o['customer_name']) ?></td>
      <td><?= format_price($o['total']) ?></td>
      <td><span class="status status-<?= esc($o['status']) ?>"><?= esc(order_status_label($o['status'])) ?></span></td>
      <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
      <td><a href="order_detail.php?id=<?= (int)$o['id'] ?>">Chi tiết</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<div class="panel">
  <h3>Sản phẩm sắp hết hàng (tồn kho &lt; 10)</h3>
  <?php if ($low_stock): ?>
  <table class="table">
    <tr><th>Sản phẩm</th><th>Tồn kho</th><th></th></tr>
    <?php foreach ($low_stock as $p): ?>
    <tr>
      <td><?= esc($p['name']) ?></td>
      <td><b class="link-danger"><?= (int)$p['stock'] ?></b></td>
      <td><a href="product_form.php?id=<?= (int)$p['id'] ?>">Sửa</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
  <p class="muted">Không có sản phẩm nào sắp hết hàng.</p>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
