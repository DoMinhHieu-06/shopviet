<?php
// admin/product_form.php — Thêm / sửa sản phẩm (chung 1 form)
$admin_title = 'Thêm / sửa sản phẩm';
require_once 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$p = $id > 0 ? db_one($conn, 'SELECT * FROM products WHERE id = ?', 'i', [$id]) : null;
if ($id > 0 && !$p) {
    redirect('products.php');
}

$categories = db_all($conn, 'SELECT * FROM categories ORDER BY name');
$errors = [];

// Giữ lại giá trị đã nhập khi form lỗi
$val = [
    'name'        => $p['name'] ?? '',
    'category_id' => $p['category_id'] ?? 0,
    'price'       => $p['price'] ?? '',
    'old_price'   => $p['old_price'] ?? '',
    'stock'       => $p['stock'] ?? 0,
    'is_featured' => $p['is_featured'] ?? 0,
    'description' => $p['description'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $val['name']        = trim($_POST['name'] ?? '');
    $val['category_id'] = (int)($_POST['category_id'] ?? 0);
    $val['price']       = trim($_POST['price'] ?? '');
    $val['old_price']   = trim($_POST['old_price'] ?? '');
    $val['stock']       = (int)($_POST['stock'] ?? 0);
    $val['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $val['description'] = trim($_POST['description'] ?? '');

    if ($val['name'] === '') $errors[] = 'Vui lòng nhập tên sản phẩm.';
    if (!is_numeric($val['price']) || $val['price'] < 0) $errors[] = 'Giá bán không hợp lệ.';
    if ($val['old_price'] !== '' && (!is_numeric($val['old_price']) || $val['old_price'] < 0)) {
        $errors[] = 'Giá gốc không hợp lệ.';
    }
    if ($val['stock'] < 0) $errors[] = 'Tồn kho không hợp lệ.';

    $image = $p['image'] ?? 'assets/img/no-image.png';

    // Xử lý upload ảnh mới (nếu có)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $errors[] = 'Chỉ chấp nhận file ảnh jpg/png/webp.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ảnh quá lớn (tối đa 2MB).';
        } else {
            $fname = 'sp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destDir = __DIR__ . '/../uploads';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destDir . '/' . $fname)) {
                $image = 'uploads/' . $fname;
            } else {
                $errors[] = 'Upload ảnh thất bại.';
            }
        }
    }

    if (!$errors) {
        $price = (float)$val['price'];
        $old_price = $val['old_price'] === '' ? null : (float)$val['old_price'];
        $cat_id = $val['category_id'] > 0 ? $val['category_id'] : null;

        if ($p) {
            $stmt = $conn->prepare('UPDATE products SET category_id = ?, name = ?, description = ?,
                                    price = ?, old_price = ?, image = ?, stock = ?, is_featured = ? WHERE id = ?');
            $stmt->bind_param('issddssii', $cat_id, $val['name'], $val['description'], $price,
                              $old_price, $image, $val['stock'], $val['is_featured'], $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare('INSERT INTO products (category_id, name, description, price, old_price, image, stock, is_featured)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('issddssi', $cat_id, $val['name'], $val['description'], $price,
                              $old_price, $image, $val['stock'], $val['is_featured']);
            $stmt->execute();
            $stmt->close();
        }
        redirect('products.php');
    }
}
?>

<h2><?= $p ? 'Sửa sản phẩm #' . (int)$p['id'] : 'Thêm sản phẩm mới' ?></h2>

<?php if ($errors): ?>
<div class="alert alert-error"><ul><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="form-card" style="max-width:640px">
  <label>Tên sản phẩm *
    <input type="text" name="name" value="<?= esc($val['name']) ?>" required>
  </label>
  <label>Danh mục
    <select name="category_id">
      <option value="0">— Không thuộc danh mục —</option>
      <?php foreach ($categories as $c): ?>
      <option value="<?= (int)$c['id'] ?>" <?= (int)$val['category_id'] === (int)$c['id'] ? 'selected' : '' ?>>
        <?= esc($c['name']) ?>
      </option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Giá bán (VNĐ) *
    <input type="number" name="price" value="<?= esc($val['price']) ?>" min="0" step="1000" required>
  </label>
  <label>Giá gốc (VNĐ, để trống nếu không giảm giá)
    <input type="number" name="old_price" value="<?= esc($val['old_price']) ?>" min="0" step="1000">
  </label>
  <label>Tồn kho
    <input type="number" name="stock" value="<?= (int)$val['stock'] ?>" min="0">
  </label>
  <label>Mô tả
    <textarea name="description" rows="4"><?= esc($val['description']) ?></textarea>
  </label>
  <label>Ảnh sản phẩm
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
    <?php if ($p): ?><br><img class="thumb" style="margin-top:6px" src="../<?= esc($p['image']) ?>" alt=""><?php endif; ?>
  </label>
  <label>
    <input type="checkbox" name="is_featured" value="1" <?= $val['is_featured'] ? 'checked' : '' ?>>
    Sản phẩm nổi bật (hiện ở trang chủ)
  </label>
  <button type="submit" class="btn btn-primary"><?= $p ? 'Cập nhật' : 'Thêm sản phẩm' ?></button>
  <a class="btn" href="products.php">Hủy</a>
</form>

<?php require_once 'includes/footer.php'; ?>
