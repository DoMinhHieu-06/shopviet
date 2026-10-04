<?php
// account.php — Đơn hàng của tôi (yêu cầu đăng nhập)
require_once 'config.php';
require_login();

$c = current_customer();
$orders = db_all($conn, 'SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC', 'i', [(int)$c['id']]);

$page_title = 'Đơn hàng của tôi';
include 'includes/header.php';
?>

<h2 class="page-head">Xin chào, <?= esc($c['name']) ?>!</h2>
<h3 class="section-title">Đơn hàng của tôi</h3>

<?php if (!$orders): ?>
  <p class="empty">Bạn chưa có đơn hàng nào. <a href="index.php">Mua sắm ngay</a></p>
<?php else: ?>
  <?php foreach ($orders as $o): ?>
  <div class="order-card">
    <div class="order-head">
      <span>Mã đơn: <b>#<?= (int)$o['id'] ?></b></span>
      <span><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></span>
      <span class="status status-<?= esc($o['status']) ?>"><?= esc(order_status_label($o['status'])) ?></span>
      <span class="price"><b><?= format_price($o['total']) ?></b></span>
    </div>
    <table class="table">
      <?php $items = db_all($conn, 'SELECT * FROM order_items WHERE order_id = ?', 'i', [(int)$o['id']]); ?>
      <?php foreach ($items as $it): ?>
      <tr>
        <td><?= esc($it['product_name']) ?> &times; <?= (int)$it['quantity'] ?></td>
        <td class="text-right"><?= format_price($it['price'] * $it['quantity']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <p class="muted">Người nhận: <?= esc($o['customer_name']) ?> — <?= esc($o['customer_phone']) ?> — <?= esc($o['customer_address']) ?></p>
  </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
