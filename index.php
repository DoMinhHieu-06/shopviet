<?php
// index.php — Trang chủ
require_once 'config.php';
$page_title = 'Trang chủ';

$featured = db_all($conn, 'SELECT p.* FROM products p WHERE p.is_featured = 1 ORDER BY p.id DESC LIMIT 8');
$newest   = db_all($conn, 'SELECT p.* FROM products p ORDER BY p.id DESC LIMIT 8');
$categories = db_all($conn, 'SELECT * FROM categories ORDER BY name');

include 'includes/header.php';
?>

<div class="banner">
  <div>
    <h2>Siêu Sale mỗi ngày</h2>
    <p>Giảm giá đến 50% &middot; Freeship mọi đơn hàng &middot; Hàng chính hãng</p>
    <a class="btn btn-primary" href="category.php?id=1">Mua sắm ngay</a>
  </div>
</div>

<h3 class="section-title">Danh mục sản phẩm</h3>
<div class="cat-grid">
  <?php foreach ($categories as $c): ?>
    <a class="cat-item" href="category.php?id=<?= (int)$c['id'] ?>">
      <?= esc($c['name']) ?>
    </a>
  <?php endforeach; ?>
</div>

<h3 class="section-title">Sản phẩm nổi bật</h3>
<div class="product-grid">
  <?php foreach ($featured as $p): ?>
    <?php include 'includes/product_card.php'; ?>
  <?php endforeach; ?>
</div>

<h3 class="section-title">Sản phẩm mới nhất</h3>
<div class="product-grid">
  <?php foreach ($newest as $p): ?>
    <?php include 'includes/product_card.php'; ?>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
