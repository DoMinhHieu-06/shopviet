<?php
// includes/header.php — Phần đầu trang (dùng SAU khi đã require 'config.php').
// Biến tùy chọn: $page_title
$categories_nav = db_all($conn, 'SELECT id, name FROM categories ORDER BY name');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? esc($page_title) . ' | ' : '' ?>ShopViet - Mua sắm trực tuyến</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div class="container topbar-inner">
    <a href="index.php" class="logo">Shop<span>Viet</span></a>
    <form class="search" action="search.php" method="get">
      <input type="text" name="q" placeholder="Tìm sản phẩm..." value="<?= esc($_GET['q'] ?? '') ?>">
      <button type="submit">Tìm</button>
    </form>
    <nav class="topnav">
      <a href="cart.php" class="cart-link">&#128722; Giỏ hàng (<?= cart_count() ?>)</a>
      <?php if (current_customer()): ?>
        <a href="account.php">&#128100; <?= esc(current_customer()['name']) ?></a>
        <a href="logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="register.php">Đăng ký</a>
        <a href="login.php">Đăng nhập</a>
      <?php endif; ?>
    </nav>
  </div>
  <div class="catbar">
    <div class="container">
      <a href="index.php">Trang chủ</a>
      <?php foreach ($categories_nav as $c): ?>
        <a href="category.php?id=<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></a>
      <?php endforeach; ?>
      <a href="contact.php">Liên hệ</a>
    </div>
  </div>
</header>
<main class="container">
