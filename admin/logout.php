<?php
// admin/logout.php — Đăng xuất quản trị
require_once '../config.php';
unset($_SESSION['admin']);
redirect('login.php');
