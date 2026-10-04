# ShopViet — Website bán hàng trực tuyến

Đồ án môn học: xây dựng website bán hàng phong cách Shopee bằng **PHP thuần + MySQL**.

## 1. Tính năng

**Khách hàng (không cần đăng nhập vẫn xem/mua được):**
- Trang chủ: banner, danh mục, sản phẩm nổi bật, sản phẩm mới
- Xem sản phẩm theo danh mục, sắp xếp (mới nhất / bán chạy / giá tăng / giá giảm)
- Tìm kiếm sản phẩm, xem chi tiết + sản phẩm liên quan
- Giỏ hàng: thêm / sửa số lượng / xóa (lưu trong session)
- Đặt hàng (COD): tự động trừ tồn kho, tạo hóa đơn + chi tiết hóa đơn
- Đăng ký / đăng nhập / xem đơn hàng của tôi
- Form liên hệ (tin nhắn lưu vào CSDL cho admin xem)

**Quản trị viên (`/admin`):**
- Dashboard: thống kê sản phẩm, đơn hàng, khách hàng, doanh thu, đơn chờ xác nhận, sản phẩm sắp hết hàng
- Quản lý sản phẩm: thêm / sửa / xóa, upload ảnh
- Quản lý danh mục: thêm / sửa / xóa
- Quản lý đơn hàng: lọc theo trạng thái, xem chi tiết, cập nhật trạng thái
- Quản lý khách hàng, xem tin nhắn liên hệ

## 2. Cài đặt & chạy trên XAMPP (Windows)

**Bước 1 — Cài XAMPP:** tải tại https://www.apachefriends.org, cài đặt, mở XAMPP Control Panel,
nhấn **Start** cho 2 dòng **Apache** và **MySQL**.

**Bước 2 — Copy code:** giải nén file `shopviet.zip`, copy thư mục `shopviet` vào
`C:\xampp\htdocs\` sao cho có đường dẫn `C:\xampp\htdocs\shopviet\index.php`.

**Bước 3 — Tạo CSDL:** mở trình duyệt vào http://localhost/phpmyadmin
→ tab **Import** → **Choose File** → chọn file `database.sql` trong thư mục `shopviet`
→ nhấn **Go**. Database `shopviet` sẽ được tạo tự động kèm dữ liệu mẫu.

**Bước 4 — Chạy website:** mở http://localhost/shopviet

> Nếu MySQL của bạn user `root` có mật khẩu (mặc định XAMPP là không có),
> mở file `config.php` và sửa dòng `define('DB_PASS', '');` cho đúng.

## 3. Tài khoản mẫu

| Loại | Địa chỉ | Tài khoản | Mật khẩu |
|------|---------|-----------|----------|
| Quản trị | http://localhost/shopviet/admin | `admin` | `admin123` |
| Khách hàng | http://localhost/shopviet/login.php | `an.nguyen@gmail.com` | `123456` |

## 4. Cấu trúc thư mục

```
shopviet/
├── config.php            # Kết nối CSDL + hàm dùng chung
├── database.sql          # CSDL: 7 bảng + dữ liệu mẫu
├── index.php             # Trang chủ
├── category.php          # Sản phẩm theo danh mục
├── search.php            # Tìm kiếm
├── product.php           # Chi tiết sản phẩm
├── cart.php              # Giỏ hàng
├── checkout.php          # Đặt hàng
├── order_success.php     # Đặt hàng thành công
├── register.php / login.php / logout.php / account.php
├── contact.php           # Liên hệ
├── includes/             # header, footer, thẻ sản phẩm
├── assets/css/style.css  # Giao diện (tông cam Shopee)
├── assets/js/main.js
├── assets/img/           # Ảnh sản phẩm mẫu
├── uploads/              # Ảnh upload từ trang admin
└── admin/                # Trang quản trị
    ├── index.php         # Dashboard
    ├── products.php / product_form.php / product_delete.php
    ├── categories.php / orders.php / order_detail.php
    ├── customers.php / contacts.php
    └── login.php / logout.php
```

## 5. CSDL (7 bảng)

`categories`, `products`, `customers`, `orders` (hóa đơn),
`order_items` (chi tiết hóa đơn), `admins`, `contacts`.

## 6. Gợi ý khi demo / bảo vệ

- Mở trang chủ → tìm kiếm → xem chi tiết → thêm vào giỏ → đặt hàng → sang tab admin
  xem đơn mới ở trạng thái "Chờ xác nhận" → cập nhật "Đang giao hàng".
- Nhấn mạnh: dùng **prepared statement** chống SQL Injection, mật khẩu mã hóa
  **bcrypt**, session giỏ hàng, kiểm tra tồn kho khi đặt hàng (transaction).
