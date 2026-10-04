<?php
// =====================================================
// config.php — Cấu hình chung của website ShopViet
// - Kết nối MySQL (XAMPP: host=localhost, user=root, pass='')
// - Khởi động session, múi giờ Việt Nam
// - Các hàm tiện ích dùng chung
// =====================================================
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP mặc định tài khoản root không có mật khẩu
define('DB_NAME', 'shopviet');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Lỗi kết nối CSDL: ' . $conn->connect_error
        . '<br>Hãy bật MySQL trong XAMPP và import file <b>database.sql</b> vào phpMyAdmin.');
}
$conn->set_charset('utf8mb4');

// Định dạng tiền VNĐ: 7990000 -> 7.990.000đ
function format_price($number) {
    return number_format((float)$number, 0, ',', '.') . 'đ';
}

// Chống XSS khi in dữ liệu ra HTML
function esc($str) {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Tổng số lượng sản phẩm trong giỏ hàng (lưu trong session)
function cart_count() {
    $count = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $count += (int)$qty;
        }
    }
    return $count;
}

// Tổng tiền giỏ hàng — luôn lấy GIÁ HIỆN TẠI từ CSDL (không tin giá trong session)
function cart_total($conn) {
    $total = 0;
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $ids = array_map('intval', array_keys($_SESSION['cart']));
        $rs = $conn->query('SELECT id, price FROM products WHERE id IN (' . implode(',', $ids) . ')');
        $prices = [];
        while ($row = $rs->fetch_assoc()) {
            $prices[$row['id']] = $row['price'];
        }
        foreach ($_SESSION['cart'] as $id => $qty) {
            if (isset($prices[$id])) {
                $total += $prices[$id] * (int)$qty;
            }
        }
    }
    return $total;
}

// Chi tiết từng dòng trong giỏ (kèm thông tin sản phẩm từ DB)
function cart_items($conn) {
    $items = [];
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $ids = array_map('intval', array_keys($_SESSION['cart']));
        $rs = $conn->query('SELECT id, name, price, image, stock FROM products WHERE id IN (' . implode(',', $ids) . ')');
        while ($row = $rs->fetch_assoc()) {
            $row['qty'] = (int)$_SESSION['cart'][$row['id']];
            $row['subtotal'] = $row['price'] * $row['qty'];
            $items[] = $row;
        }
    }
    return $items;
}

function current_customer() {
    return $_SESSION['customer'] ?? null;
}

function require_login() {
    if (!current_customer()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
}

function is_admin() {
    return !empty($_SESSION['admin']);
}

function require_admin() {
    if (!is_admin()) {
        redirect('login.php');
    }
}

// Lấy 1 dòng dữ liệu bằng prepared statement (chống SQL Injection)
function db_one($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

// Lấy nhiều dòng dữ liệu bằng prepared statement
function db_all($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// Tên trạng thái đơn hàng hiển thị cho khách
function order_status_label($status) {
    $map = [
        'moi'        => 'Chờ xác nhận',
        'dang_giao'  => 'Đang giao hàng',
        'hoan_thanh' => 'Hoàn thành',
        'da_huy'     => 'Đã hủy',
    ];
    return $map[$status] ?? $status;
}
