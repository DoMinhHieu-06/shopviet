<?php
// category.php — Sản phẩm theo danh mục (+ sắp xếp)
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
$cat = db_one($conn, 'SELECT * FROM categories WHERE id = ?', 'i', [$id]);
if (!$cat) {
    redirect('index.php');
}
$page_title = $cat['name'];

// Sắp xếp: chỉ cho phép các giá trị trong danh sách trắng (tránh SQL Injection)
$sort = $_GET['sort'] ?? 'new';
if ($sort === 'price_asc') {
    $orderBy = 'p.price ASC';
} elseif ($sort === 'price_desc') {
    $orderBy = 'p.price DESC';
} elseif ($sort === 'best') {
    $orderBy = 'p.sold DESC';
} else {
    $sort = 'new';
    $orderBy = 'p.id DESC';
}

$products = db_all($conn, "SELECT p.* FROM products p WHERE p.category_id = ? ORDER BY $orderBy", 'i', [$id]);

include 'includes/header.php';
?>

<div class="page-head">
  <h2><?= esc($cat['name']) ?></h2>
  <p class="muted"><?= esc($cat['description'] ?? '') ?></p>
</div>

<div class="sort-bar">
  <span>Sắp xếp:</span>
  <a href="category.php?id=<?= $id ?>&amp;sort=new" class="<?= $sort === 'new' ? 'active' : '' ?>">Mới nhất</a>
  <a href="category.php?id=<?= $id ?>&amp;sort=best" class="<?= $sort === 'best' ? 'active' : '' ?>">Bán chạy</a>
  <a href="category.php?id=<?= $id ?>&amp;sort=price_asc" class="<?= $sort === 'price_asc' ? 'active' : '' ?>">Giá tăng dần</a>
  <a href="category.php?id=<?= $id ?>&amp;sort=price_desc" class="<?= $sort === 'price_desc' ? 'active' : '' ?>">Giá giảm dần</a>
</div>

<?php if ($products): ?>
<div class="product-grid">
  <?php foreach ($products as $p): ?>
    <?php include 'includes/product_card.php'; ?>
  <?php endforeach; ?>
</div>
<?php else: ?>
<p class="empty">Chưa có sản phẩm nào trong danh mục này.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
