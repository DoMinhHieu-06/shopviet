<?php
// cart.php — Giỏ hàng (lưu trong $_SESSION['cart'] dạng [product_id => qty])
require_once 'config.php';

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// Thêm vào giỏ
if ($action === 'add' && $id > 0) {
    $qty = max(1, (int)($_GET['qty'] ?? 1));
    $p = db_one($conn, 'SELECT id, stock FROM products WHERE id = ?', 'i', [$id]);
    if ($p && $p['stock'] > 0) {
        $cur = (int)($_SESSION['cart'][$id] ?? 0);
        $_SESSION['cart'][$id] = min($cur + $qty, (int)$p['stock']); // không vượt quá tồn kho
    }
    redirect('cart.php');
}

// Xóa 1 dòng
if ($action === 'remove' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    redirect('cart.php');
}

// Xóa toàn bộ giỏ
if ($action === 'clear') {
    unset($_SESSION['cart']);
    redirect('cart.php');
}

// Cập nhật số lượng (gửi từ form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $pid => $q) {
        $pid = (int)$pid;
        $q = (int)$q;
        if ($q <= 0) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $p = db_one($conn, 'SELECT stock FROM products WHERE id = ?', 'i', [$pid]);
            if ($p) {
                $_SESSION['cart'][$pid] = min($q, (int)$p['stock']);
            }
        }
    }
    redirect('cart.php');
}

$items = cart_items($conn);
$total = cart_total($conn);
$page_title = 'Giỏ hàng';
include 'includes/header.php';
?>

<h2 class="page-head">Giỏ hàng của bạn</h2>

<?php if (!$items): ?>
  <p class="empty">Giỏ hàng đang trống. <a href="index.php">Tiếp tục mua sắm</a></p>
<?php else: ?>
<form method="post" action="cart.php">
<table class="table cart-table">
  <tr>
    <th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th><th></th>
  </tr>
  <?php foreach ($items as $it): ?>
  <tr>
    <td>
      <div class="cart-product">
        <img src="<?= esc($it['image']) ?>" alt="">
        <a href="product.php?id=<?= (int)$it['id'] ?>"><?= esc($it['name']) ?></a>
      </div>
    </td>
    <td><?= format_price($it['price']) ?></td>
    <td><input type="number" name="qty[<?= (int)$it['id'] ?>]" value="<?= (int)$it['qty'] ?>" min="0" max="<?= (int)$it['stock'] ?>"></td>
    <td><b><?= format_price($it['subtotal']) ?></b></td>
    <td><a class="link-danger" href="cart.php?action=remove&amp;id=<?= (int)$it['id'] ?>" onclick="return confirm('Xóa sản phẩm này khỏi giỏ?')">Xóa</a></td>
  </tr>
  <?php endforeach; ?>
</table>
<div class="cart-actions">
  <button type="submit" class="btn">Cập nhật giỏ hàng</button>
  <a class="link-danger" href="cart.php?action=clear" onclick="return confirm('Xóa toàn bộ giỏ hàng?')">Xóa giỏ hàng</a>
</div>
</form>

<div class="cart-summary">
  <p>Tổng cộng: <b class="price-lg"><?= format_price($total) ?></b></p>
  <a class="btn btn-primary" href="checkout.php">Tiến hành đặt hàng</a>
  <a class="btn" href="index.php">Tiếp tục mua sắm</a>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
