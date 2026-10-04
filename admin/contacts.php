<?php
// admin/contacts.php — Xem tin nhắn liên hệ
$admin_title = 'Tin nhắn liên hệ';
require_once 'includes/header.php';

// Đánh dấu đã đọc
if (isset($_GET['read'])) {
    $cid = (int)$_GET['read'];
    $stmt = $conn->prepare('UPDATE contacts SET is_read = 1 WHERE id = ?');
    $stmt->bind_param('i', $cid);
    $stmt->execute();
    $stmt->close();
    redirect('contacts.php');
}

// Xóa tin nhắn
if (isset($_GET['delete'])) {
    $cid = (int)$_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->bind_param('i', $cid);
    $stmt->execute();
    $stmt->close();
    redirect('contacts.php');
}

$contacts = db_all($conn, 'SELECT * FROM contacts ORDER BY id DESC');
?>

<h2>Tin nhắn liên hệ</h2>

<div class="panel">
<table class="table">
  <tr><th>ID</th><th>Người gửi</th><th>Email</th><th>Tiêu đề</th><th>Nội dung</th><th>Ngày gửi</th><th>Thao tác</th></tr>
  <?php foreach ($contacts as $ct): ?>
  <tr <?= $ct['is_read'] ? '' : 'style="background:#fff8f0;font-weight:bold"' ?>>
    <td><?= (int)$ct['id'] ?></td>
    <td><?= esc($ct['name']) ?><br><span class="muted"><?= esc($ct['phone'] ?: '') ?></span></td>
    <td><?= esc($ct['email']) ?></td>
    <td><?= esc($ct['subject'] ?: '—') ?></td>
    <td><?= nl2br(esc(mb_substr($ct['message'], 0, 120))) ?><?= mb_strlen($ct['message']) > 120 ? '...' : '' ?></td>
    <td><?= date('d/m/Y H:i', strtotime($ct['created_at'])) ?></td>
    <td>
      <?php if (!$ct['is_read']): ?><a href="contacts.php?read=<?= (int)$ct['id'] ?>">Đánh dấu đã đọc</a> | <?php endif; ?>
      <a class="link-danger" href="contacts.php?delete=<?= (int)$ct['id'] ?>" data-confirm="Xóa tin nhắn này?">Xóa</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php if (!$contacts): ?><p class="muted">Chưa có tin nhắn nào.</p><?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
