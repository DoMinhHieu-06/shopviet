<?php
// admin/login.php — Đăng nhập quản trị (tài khoản mẫu: admin / admin123)
require_once '../config.php';

if (is_admin()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $a = db_one($conn, 'SELECT * FROM admins WHERE username = ?', 's', [$username]);
    if ($a && password_verify($password, $a['password'])) {
        $_SESSION['admin'] = ['id' => (int)$a['id'], 'username' => $a['username']];
        redirect('index.php');
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng nhập quản trị | ShopViet</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="auth-box" style="margin-top:60px">
  <h2>Quản trị ShopViet</h2>
  <?php if ($error): ?>
  <div class="alert alert-error"><?= esc($error) ?></div>
  <?php endif; ?>
  <form method="post" class="form-card">
    <label>Tên đăng nhập
      <input type="text" name="username" required autofocus>
    </label>
    <label>Mật khẩu
      <input type="password" name="password" required>
    </label>
    <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
    <p class="muted text-center">Tài khoản mẫu: <b>admin</b> / <b>admin123</b></p>
    <p class="muted text-center"><a href="../index.php">Về trang chủ website</a></p>
  </form>
</div>
</body>
</html>
