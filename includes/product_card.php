<?php
// includes/product_card.php — Thẻ hiển thị 1 sản phẩm (dùng trong các trang danh sách).
// Yêu cầu biến $p: id, name, price, old_price, image, sold
?>
<div class="product-card">
  <a href="product.php?id=<?= (int)$p['id'] ?>">
    <img src="<?= esc($p['image']) ?>" alt="<?= esc($p['name']) ?>" loading="lazy">
  </a>
  <div class="product-info">
    <a class="product-name" href="product.php?id=<?= (int)$p['id'] ?>"><?= esc($p['name']) ?></a>
    <div class="product-price-row">
      <span class="price"><?= format_price($p['price']) ?></span>
      <?php if (!empty($p['old_price']) && $p['old_price'] > $p['price']): ?>
        <span class="old-price"><?= format_price($p['old_price']) ?></span>
      <?php endif; ?>
    </div>
    <div class="product-meta">Đã bán <?= (int)($p['sold'] ?? 0) ?></div>
    <a class="btn btn-add" href="cart.php?action=add&amp;id=<?= (int)$p['id'] ?>">Thêm vào giỏ</a>
  </div>
</div>
