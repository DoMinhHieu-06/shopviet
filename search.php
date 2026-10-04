<?php
// search.php — Tìm kiếm sản phẩm theo tên / mô tả
require_once 'config.php';

$q = trim($_GET['q'] ?? '');
$page_title = $q !== '' ? 'Tìm kiếm: ' . $q : 'Tất cả sản phẩm';

if ($q !== '') {
    $like = '%' . $q . '%';
    $products = db_all(
        $conn,
        'SELECT p.* FROM products p WHERE p.name LIKE ? OR p.description LIKE ? ORDER BY p.id DESC',
        'ss',
        [$like, $like]
    );
} else {
    $products = db_all($conn, 'SELECT p.* FROM products p ORDER BY p.id DESC');
}

include 'includes/header.php';
?>

<div class="page-head">
  <h2><?= $q !== '' ? 'Kết quả tìm kiếm cho "' . esc($q) . '"' : 'Tất cả sản phẩm' ?></h2>
  <p class="muted">Tìm thấy <?= count($products) ?> sản phẩm</p>
</div>

<?php if ($products): ?>
<div class="product-grid">
  <?php foreach ($products as $p): ?>
    <?php include 'includes/product_card.php'; ?>
  <?php endforeach; ?>
</div>
<?php else: ?>
<p class="empty">Không tìm thấy sản phẩm nào phù hợp. Hãy thử từ khóa khác.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
