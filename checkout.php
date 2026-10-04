<?php
// checkout.php — Đặt hàng: nhập thông tin nhận hàng -> tạo hóa đơn
require_once 'config.php';

$items = cart_items($conn);
if (!$items) {
    redirect('cart.php');
}

$c = current_customer();
$name    = $c['name'] ?? '';
$phone   = $c['phone'] ?? '';
$address = $c['address'] ?? '';
$note    = '';
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $note    = trim($_POST['note'] ?? '');

    if ($name === '')    $errors[] = 'Vui lòng nhập họ tên người nhận.';
    if (!preg_match('/^[0-9]{9,11}$/', $phone)) $errors[] = 'Số điện thoại không hợp lệ (9–11 chữ số).';
    if ($address === '') $errors[] = 'Vui lòng nhập địa chỉ nhận hàng.';

    // Kiểm tra tồn kho lần cuối trước khi chốt đơn
    foreach ($items as $it) {
        if ($it['qty'] > $it['stock']) {
            $errors[] = 'Sản phẩm "' . $it['name'] . '" chỉ còn ' . $it['stock'] . ' cái.';
        }
    }

    if (!$errors) {
        $total = cart_total($conn);
        $conn->begin_transaction();
        try {
            // 1. Tạo hóa đơn
            $stmt = $conn->prepare('INSERT INTO orders (customer_id, customer_name, customer_phone, customer_address, note, total)
                                    VALUES (?, ?, ?, ?, ?, ?)');
            $customer_id = $c ? (int)$c['id'] : null;
            $stmt->bind_param('issssd', $customer_id, $name, $phone, $address, $note, $total);
            $stmt->execute();
            $order_id = $conn->insert_id;
            $stmt->close();

            // 2. Chi tiết hóa đơn + trừ kho, tăng lượt bán
            $stmtItem = $conn->prepare('INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
                                        VALUES (?, ?, ?, ?, ?)');
            $stmtStock = $conn->prepare('UPDATE products SET stock = stock - ?, sold = sold + ? WHERE id = ?');
            foreach ($items as $it) {
                $stmtItem->bind_param('iisdi', $order_id, $it['id'], $it['name'], $it['price'], $it['qty']);
                $stmtItem->execute();
                $stmtStock->bind_param('iii', $it['qty'], $it['qty'], $it['id']);
                $stmtStock->execute();
            }
            $stmtItem->close();
            $stmtStock->close();

            $conn->commit();
            unset($_SESSION['cart']);
            redirect('order_success.php?id=' . $order_id);
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = 'Đặt hàng thất bại, vui lòng thử lại.';
        }
    }
}

$total = cart_total($conn);
$page_title = 'Đặt hàng';
include 'includes/header.php';
?>

<h2 class="page-head">Đặt hàng</h2>

<?php if ($errors): ?>
<div class="alert alert-error">
  <ul><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="checkout-grid">
  <form method="post" class="form-card">
    <h3>Thông tin nhận hàng</h3>
    <label>Họ tên người nhận *
      <input type="text" name="name" value="<?= esc($name) ?>" required>
    </label>
    <label>Số điện thoại *
      <input type="text" name="phone" value="<?= esc($phone) ?>" required>
    </label>
    <label>Địa chỉ nhận hàng *
      <input type="text" name="address" value="<?= esc($address) ?>" required>
    </label>
    <label>Ghi chú
      <textarea name="note" rows="3"><?= esc($note) ?></textarea>
    </label>
    <p class="muted">Phương thức thanh toán: <b>Thanh toán khi nhận hàng (COD)</b></p>
    <button type="submit" class="btn btn-primary">Xác nhận đặt hàng</button>
  </form>

  <div class="form-card">
    <h3>Đơn hàng của bạn</h3>
    <table class="table">
      <?php foreach ($items as $it): ?>
      <tr>
        <td><?= esc($it['name']) ?> &times; <?= (int)$it['qty'] ?></td>
        <td class="text-right"><?= format_price($it['subtotal']) ?></td>
      </tr>
      <?php endforeach; ?>
      <tr class="total-row">
        <td><b>Tổng cộng</b></td>
        <td class="text-right"><b class="price"><?= format_price($total) ?></b></td>
      </tr>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
