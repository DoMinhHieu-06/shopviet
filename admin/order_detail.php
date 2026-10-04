<?php
// admin/order_detail.php — Chi tiết đơn hàng + cập nhật trạng thái
$admin_title = 'Chi tiết đơn hàng';
require_once 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$order = db_one($conn, 'SELECT * FROM orders WHERE id = ?', 'i', [$id]);
if (!$order) {
    redirect('orders.php');
}

// Cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';
    if (in_array($new_status, ['moi', 'dang_giao', 'hoan_thanh', 'da_huy'])) {
        $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $new_status, $id);
        $stmt->execute();
        $stmt->close();
        $order['status'] = $new_status;
    }
}

$items = db_all($conn, 'SELECT * FROM order_items WHERE order_id = ?', 'i', [$id]);
?>

<h2>Đơn hàng #<?= (int)$order['id'] ?></h2>

<div class="panel">
  <table class="table">
    <tr><th style="width:220px">Khách hàng</th><td><?= esc($order['customer_name']) ?></td></tr>
    <tr><th>Điện thoại</th><td><?= esc($order['customer_phone']) ?></td></tr>
    <tr><th>Địa chỉ</th><td><?= esc($order['customer_address']) ?></td></tr>
    <tr><th>Ghi chú</th><td><?= esc($order['note'] ?: '—') ?></td></tr>
    <tr><th>Ngày đặt</th><td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td></tr>
    <tr><th>Tổng tiền</th><td><b class="price"><?= format_price($order['total']) ?></b></td></tr>
    <tr><th>Trạng thái</th>
      <td>
        <form method="post" style="display:flex;gap:8px;align-items:center">
          <select name="status" style="padding:8px">
            <?php foreach (['moi', 'dang_giao', 'hoan_thanh', 'da_huy'] as $s): ?>
            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= esc(order_status_label($s)) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-primary" type="submit">Cập nhật</button>
        </form>
      </td>
    </tr>
  </table>
</div>

<div class="panel">
  <h3>Sản phẩm trong đơn</h3>
  <table class="table">
    <tr><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th></tr>
    <?php foreach ($items as $it): ?>
    <tr>
      <td><?= esc($it['product_name']) ?></td>
      <td><?= format_price($it['price']) ?></td>
      <td><?= (int)$it['quantity'] ?></td>
      <td><?= format_price($it['price'] * $it['quantity']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<p><a class="btn" href="orders.php">&laquo; Về danh sách đơn hàng</a></p>

<?php require_once 'includes/footer.php'; ?>
