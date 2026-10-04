<?php
// admin/product_delete.php — Xóa sản phẩm
require_once '../config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $p = db_one($conn, 'SELECT image FROM products WHERE id = ?', 'i', [$id]);
    $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Xóa file ảnh đã upload (nếu nằm trong thư mục uploads/)
    if ($p && strpos($p['image'], 'uploads/') === 0) {
        $file = __DIR__ . '/../' . $p['image'];
        if (is_file($file)) {
            unlink($file);
        }
    }
}
redirect('products.php');
