<?php
// admin/customers.php — Danh sách khách hàng
$admin_title = 'Quản lý khách hàng';
require_once 'includes/header.php';

$customers = db_all($conn, 'SELECT c.*, (SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id) AS total_orders
                            FROM customers c ORDER BY c.id DESC');
?>

<h2>Quản lý khách hàng</h2>

<div class="panel">
<table class="table">
  <tr><th>ID</th><th>Họ tên</th><th>Email</th><th>Điện thoại</th><th>Địa chỉ</th><th>Số đơn hàng</th><th>Ngày đăng ký</th></tr>
  <?php foreach ($customers as $c): ?>
  <tr>
    <td><?= (int)$c['id'] ?></td>
    <td><?= esc($c['name']) ?></td>
    <td><?= esc($c['email']) ?></td>
    <td><?= esc($c['phone'] ?: '—') ?></td>
    <td><?= esc($c['address'] ?: '—') ?></td>
    <td><?= (int)$c['total_orders'] ?></td>
    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php if (!$customers): ?><p class="muted">Chưa có khách hàng nào.</p><?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
