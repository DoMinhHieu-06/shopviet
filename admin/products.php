<?php
// admin/products.php — Danh sách sản phẩm (+ tìm kiếm)
$admin_title = 'Quản lý sản phẩm';
require_once 'includes/header.php';

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $like = '%' . $q . '%';
    $products = db_all(
        $conn,
        'SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.name LIKE ? ORDER BY p.id DESC',
        's',
        [$like]
    );
} else {
    $products = db_all(
        $conn,
        'SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC'
    );
}
?>

<h2>Quản lý sản phẩm</h2>

<div class="toolbar">
  <a class="btn btn-primary" href="product_form.php">+ Thêm sản phẩm</a>
  <form method="get" style="display:flex;gap:6px">
    <input type="text" name="q" placeholder="Tìm theo tên..." value="<?= esc($q) ?>" style="padding:8px">
    <button class="btn" type="submit">Tìm</button>
  </form>
</div>

<div class="panel">
<table class="table">
  <tr>
    <th>ID</th><th>Ảnh</th><th>Tên sản phẩm</th><th>Danh mục</th>
    <th>Giá</th><th>Tồn kho</th><th>Đã bán</th><th>Nổi bật</th><th>Thao tác</th>
  </tr>
  <?php foreach ($products as $p): ?>
  <tr>
    <td><?= (int)$p['id'] ?></td>
    <td><img class="thumb" src="../<?= esc($p['image']) ?>" alt=""></td>
    <td><?= esc($p['name']) ?></td>
    <td><?= esc($p['cat_name'] ?? '—') ?></td>
    <td><?= format_price($p['price']) ?></td>
    <td><?= (int)$p['stock'] ?></td>
    <td><?= (int)$p['sold'] ?></td>
    <td><?= $p['is_featured'] ? '<span class="badge">Nổi bật</span>' : '—' ?></td>
    <td>
      <a href="product_form.php?id=<?= (int)$p['id'] ?>">Sửa</a> |
      <a class="link-danger" href="product_delete.php?id=<?= (int)$p['id'] ?>"
         data-confirm="Xóa sản phẩm này?">Xóa</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php if (!$products): ?><p class="muted">Không có sản phẩm nào.</p><?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
