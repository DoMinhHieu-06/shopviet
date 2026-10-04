<?php
// logout.php — Đăng xuất khách hàng
require_once 'config.php';
unset($_SESSION['customer']);
redirect('index.php');
