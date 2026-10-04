<?php
// order_success.php — Thông báo đặt hàng thành công
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
$order = db_one($conn, 'SELECT * FROM orders WHERE id = ?', 'i', [$id]);
if (!$order) {
    redirect('index.php');
}
$page_title = 'Đặt hàng thành công';
include 'includes/header.php';
?>

<div class="success-box">
  <div class="success-icon">&#10004;</div>
  <h2>Đặt hàng thành công!</h2>
  <p>Cảm ơn <b><?= esc($order['customer_name']) ?></b> đã mua sắm tại ShopViet.</p>
  <table class="table order-info">
    <tr><th>Mã đơn hàng:</th><td><b>#<?= (int)$order['id'] ?></b></td></tr>
    <tr><th>Tổng tiền:</th><td><b class="price"><?= format_price($order['total']) ?></b></td></tr>
    <tr><th>Người nhận:</th><td><?= esc($order['customer_name']) ?> — <?= esc($order['customer_phone']) ?></td></tr>
    <tr><th>Địa chỉ:</th><td><?= esc($order['customer_address']) ?></td></tr>
    <tr><th>Trạng thái:</th><td><?= esc(order_status_label($order['status'])) ?></td></tr>
  </table>
  <p class="muted">Chúng tôi sẽ liên hệ xác nhận và giao hàng trong thời gian sớm nhất.</p>
  <a class="btn btn-primary" href="index.php">Tiếp tục mua sắm</a>
  <?php if (current_customer()): ?>
    <a class="btn" href="account.php">Xem đơn hàng của tôi</a>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
