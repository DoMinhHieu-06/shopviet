<?php
// product.php — Chi tiết sản phẩm
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
$p = db_one(
    $conn,
    'SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?',
    'i',
    [$id]
);
if (!$p) {
    redirect('index.php');
}
$page_title = $p['name'];

// Sản phẩm liên quan (cùng danh mục, trừ chính nó)
$related = db_all(
    $conn,
    'SELECT * FROM products WHERE category_id = ? AND id <> ? ORDER BY id DESC LIMIT 4',
    'ii',
    [(int)$p['category_id'], (int)$p['id']]
);

include 'includes/header.php';
?>

<div class="product-detail">
  <div class="pd-image">
    <img src="<?= esc($p['image']) ?>" alt="<?= esc($p['name']) ?>">
  </div>
  <div class="pd-info">
    <h2><?= esc($p['name']) ?></h2>
    <p class="muted">Danh mục: <a href="category.php?id=<?= (int)$p['category_id'] ?>"><?= esc($p['cat_name'] ?? '—') ?></a>
      &middot; Đã bán <?= (int)$p['sold'] ?></p>
    <div class="pd-price">
      <span class="price-lg"><?= format_price($p['price']) ?></span>
      <?php if (!empty($p['old_price']) && $p['old_price'] > $p['price']): ?>
        <span class="old-price"><?= format_price($p['old_price']) ?></span>
        <span class="discount">-<?= (int)(100 - $p['price'] / $p['old_price'] * 100) ?>%</span>
      <?php endif; ?>
    </div>
    <p>Tình trạng:
      <?php if ($p['stock'] > 0): ?>
        <span class="stock-ok">Còn hàng (<?= (int)$p['stock'] ?>)</span>
      <?php else: ?>
        <span class="stock-out">Hết hàng</span>
      <?php endif; ?>
    </p>
    <?php if ($p['stock'] > 0): ?>
    <form class="pd-cart" action="cart.php" method="get">
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
      <label>Số lượng:
        <input type="number" name="qty" value="1" min="1" max="<?= (int)$p['stock'] ?>">
      </label>
      <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
    </form>
    <?php endif; ?>
  </div>
</div>

<div class="pd-desc">
  <h3>Mô tả sản phẩm</h3>
  <p><?= nl2br(esc($p['description'])) ?></p>
</div>

<?php if ($related): ?>
<h3 class="section-title">Sản phẩm liên quan</h3>
<div class="product-grid">
  <?php foreach ($related as $p): ?>
    <?php include 'includes/product_card.php'; ?>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
