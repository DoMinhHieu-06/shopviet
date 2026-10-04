<?php
// admin/orders.php — Danh sách đơn hàng (+ lọc theo trạng thái)
$admin_title = 'Quản lý đơn hàng';
require_once 'includes/header.php';

$status = $_GET['status'] ?? '';
$allowed = ['moi', 'dang_giao', 'hoan_thanh', 'da_huy'];
if ($status !== '' && in_array($status, $allowed)) {
    $orders = db_all($conn, 'SELECT * FROM orders WHERE status = ? ORDER BY id DESC', 's', [$status]);
} else {
    $status = '';
    $orders = db_all($conn, 'SELECT * FROM orders ORDER BY id DESC');
}
?>

<h2>Quản lý đơn hàng</h2>

<div class="sort-bar">
  <span>Lọc:</span>
  <a href="orders.php" class="<?= $status === '' ? 'active' : '' ?>">Tất cả</a>
  <?php foreach ($allowed as $s): ?>
  <a href="orders.php?status=<?= $s ?>" class="<?= $status === $s ? 'active' : '' ?>"><?= esc(order_status_label($s)) ?></a>
  <?php endforeach; ?>
</div>

<div class="panel">
<table class="table">
  <tr><th>Mã</th><th>Khách hàng</th><th>Điện thoại</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày đặt</th><th></th></tr>
  <?php foreach ($orders as $o): ?>
  <tr>
    <td><b>#<?= (int)$o['id'] ?></b></td>
    <td><?= esc($o['customer_name']) ?></td>
    <td><?= esc($o['customer_phone']) ?></td>
    <td><?= format_price($o['total']) ?></td>
    <td><span class="status status-<?= esc($o['status']) ?>"><?= esc(order_status_label($o['status'])) ?></span></td>
    <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
    <td><a href="order_detail.php?id=<?= (int)$o['id'] ?>">Chi tiết</a></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php if (!$orders): ?><p class="muted">Không có đơn hàng nào.</p><?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
