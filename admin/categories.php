<?php
// admin/categories.php — Quản lý danh mục: thêm / sửa / xóa trong 1 trang
$admin_title = 'Quản lý danh mục';
require_once 'includes/header.php';

$msg = '';

// Thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'add') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name !== '') {
        $stmt = $conn->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
        $stmt->bind_param('ss', $name, $desc);
        $stmt->execute();
        $stmt->close();
        $msg = 'Đã thêm danh mục.';
    }
}

// Sửa danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'edit') {
    $cid = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if ($cid > 0 && $name !== '') {
        $stmt = $conn->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->bind_param('si', $name, $cid);
        $stmt->execute();
        $stmt->close();
        $msg = 'Đã cập nhật danh mục.';
    }
}

// Xóa danh mục (sản phẩm thuộc danh mục sẽ về "không danh mục" nhờ ON DELETE SET NULL)
if (isset($_GET['delete'])) {
    $cid = (int)$_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->bind_param('i', $cid);
    $stmt->execute();
    $stmt->close();
    redirect('categories.php');
}

$categories = db_all($conn, 'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS total
                             FROM categories c ORDER BY c.name');
?>

<h2>Quản lý danh mục</h2>
<?php if ($msg): ?><div class="alert alert-success"><?= esc($msg) ?></div><?php endif; ?>

<div class="panel">
  <h3>Thêm danh mục mới</h3>
  <form method="post" style="display:flex;gap:8px;flex-wrap:wrap">
    <input type="hidden" name="do" value="add">
    <input type="text" name="name" placeholder="Tên danh mục *" required style="padding:8px;flex:1;min-width:200px">
    <input type="text" name="description" placeholder="Mô tả" style="padding:8px;flex:2;min-width:200px">
    <button class="btn btn-primary" type="submit">Thêm</button>
  </form>
</div>

<div class="panel">
<table class="table">
  <tr><th>ID</th><th>Tên danh mục</th><th>Số sản phẩm</th><th>Thao tác</th></tr>
  <?php foreach ($categories as $c): ?>
  <tr>
    <td><?= (int)$c['id'] ?></td>
    <td>
      <form method="post" style="display:flex;gap:6px">
        <input type="hidden" name="do" value="edit">
        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
        <input type="text" name="name" value="<?= esc($c['name']) ?>" style="padding:6px">
        <button class="btn" type="submit">Lưu</button>
      </form>
    </td>
    <td><?= (int)$c['total'] ?></td>
    <td><a class="link-danger" href="categories.php?delete=<?= (int)$c['id'] ?>"
           data-confirm="Xóa danh mục này? Sản phẩm bên trong sẽ không bị xóa.">Xóa</a></td>
  </tr>
  <?php endforeach; ?>
</table>
</div>

<?php require_once 'includes/footer.php'; ?>
