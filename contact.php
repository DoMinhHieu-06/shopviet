<?php
// contact.php — Form liên hệ (lưu vào bảng contacts để admin xem)
require_once 'config.php';

$success = false;
$name = $email = $phone = $subject = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $message !== '') {
        $stmt = $conn->prepare('INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
        $stmt->execute();
        $stmt->close();
        $success = true;
        $name = $email = $phone = $subject = $message = '';
    }
}

$page_title = 'Liên hệ';
include 'includes/header.php';
?>

<h2 class="page-head">Liên hệ với ShopViet</h2>

<div class="checkout-grid">
  <form method="post" class="form-card">
    <h3>Gửi tin nhắn cho chúng tôi</h3>
    <?php if ($success): ?>
      <div class="alert alert-success">Cảm ơn bạn! Tin nhắn đã được gửi, chúng tôi sẽ phản hồi sớm nhất.</div>
    <?php endif; ?>
    <label>Họ tên *
      <input type="text" name="name" value="<?= esc($name) ?>" required>
    </label>
    <label>Email *
      <input type="email" name="email" value="<?= esc($email) ?>" required>
    </label>
    <label>Số điện thoại
      <input type="text" name="phone" value="<?= esc($phone) ?>">
    </label>
    <label>Tiêu đề
      <input type="text" name="subject" value="<?= esc($subject) ?>">
    </label>
    <label>Nội dung *
      <textarea name="message" rows="5" required><?= esc($message) ?></textarea>
    </label>
    <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
  </form>

  <div class="form-card">
    <h3>Thông tin liên hệ</h3>
    <p><b>Địa chỉ:</b> 123 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh</p>
    <p><b>Hotline:</b> 1900 1234 (8h – 22h mỗi ngày)</p>
    <p><b>Email:</b> hotro@shopviet.vn</p>
    <p class="muted">ShopViet luôn sẵn sàng hỗ trợ bạn về đơn hàng, sản phẩm và chính sách đổi trả.</p>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
