<?php
// register.php — Đăng ký tài khoản khách hàng
require_once 'config.php';

if (current_customer()) {
    redirect('index.php');
}

$errors = [];
$name = $email = $phone = $address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if ($name === '') $errors[] = 'Vui lòng nhập họ tên.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
    if (strlen($password) < 6) $errors[] = 'Mật khẩu phải từ 6 ký tự trở lên.';
    if ($password !== $confirm) $errors[] = 'Nhập lại mật khẩu không khớp.';

    if (!$errors) {
        $exists = db_one($conn, 'SELECT id FROM customers WHERE email = ?', 's', [$email]);
        if ($exists) {
            $errors[] = 'Email này đã được đăng ký. Hãy đăng nhập.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO customers (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $name, $email, $hash, $phone, $address);
            $stmt->execute();
            $new_id = $conn->insert_id;
            $stmt->close();

            // Tự động đăng nhập sau khi đăng ký
            $_SESSION['customer'] = ['id' => $new_id, 'name' => $name, 'email' => $email];
            $next = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            redirect($next);
        }
    }
}

$page_title = 'Đăng ký';
include 'includes/header.php';
?>

<div class="auth-box">
  <h2>Đăng ký tài khoản</h2>
  <?php if ($errors): ?>
  <div class="alert alert-error"><ul><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>
  <form method="post" class="form-card">
    <label>Họ tên *
      <input type="text" name="name" value="<?= esc($name) ?>" required>
    </label>
    <label>Email *
      <input type="email" name="email" value="<?= esc($email) ?>" required>
    </label>
    <label>Mật khẩu * (tối thiểu 6 ký tự)
      <input type="password" name="password" required>
    </label>
    <label>Nhập lại mật khẩu *
      <input type="password" name="confirm" required>
    </label>
    <label>Số điện thoại
      <input type="text" name="phone" value="<?= esc($phone) ?>">
    </label>
    <label>Địa chỉ
      <input type="text" name="address" value="<?= esc($address) ?>">
    </label>
    <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
    <p class="muted text-center">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
