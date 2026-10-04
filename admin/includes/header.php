<?php
// admin/includes/header.php — Khung trang quản trị (sidebar).
// Tự động yêu cầu đăng nhập admin.
require_once __DIR__ . '/../../config.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($admin_title) ? esc($admin_title) . ' | ' : '' ?>Quản trị ShopViet</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
  <aside class="sidebar">
    <h3>ShopViet Admin</h3>
    <a href="index.php">Dashboard</a>
    <a href="products.php">Sản phẩm</a>
    <a href="categories.php">Danh mục</a>
    <a href="orders.php">Đơn hàng</a>
    <a href="customers.php">Khách hàng</a>
    <a href="contacts.php">Liên hệ</a>
    <a href="../index.php" target="_blank">Xem website</a>
    <a href="logout.php">Đăng xuất (<?= esc($_SESSION['admin']['username']) ?>)</a>
  </aside>
  <div class="admin-main">
