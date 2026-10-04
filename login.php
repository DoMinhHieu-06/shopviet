<?php
// login.php — Đăng nhập khách hàng
require_once 'config.php';

if (current_customer()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $c = db_one($conn, 'SELECT * FROM customers WHERE email = ?', 's', [$email]);
    if ($c && password_verify($password, $c['password'])) {
        $_SESSION['customer'] = ['id' => (int)$c['id'], 'name' => $c['name'], 'email' => $c['email']];
        $next = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        redirect($next);
    } else {
        $error = 'Email hoặc mật khẩu không đúng.';
    }
}

$page_title = 'Đăng nhập';
include 'includes/header.php';
?>

<div class="auth-box">
  <h2>Đăng nhập</h2>
  <?php if ($error): ?>
  <div class="alert alert-error"><?= esc($error) ?></div>
  <?php endif; ?>
  <form method="post" class="form-card">
    <label>Email *
      <input type="email" name="email" required>
    </label>
    <label>Mật khẩu *
      <input type="password" name="password" required>
    </label>
    <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
    <p class="muted text-center">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
    <p class="muted text-center">Tài khoản mẫu: <b>an.nguyen@gmail.com</b> / <b>123456</b></p>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
