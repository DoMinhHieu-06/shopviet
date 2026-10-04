-- =====================================================
-- ShopViet — CSDL website bán hàng trực tuyến
--
-- Cách import:
--   1. Mở XAMPP, Start Apache + MySQL
--   2. Mở http://localhost/phpmyadmin
--   3. Tab "Import" -> chọn file database.sql -> Go
--   (File tự tạo database `shopviet`, không cần tạo tay)
-- =====================================================

CREATE DATABASE IF NOT EXISTS shopviet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopviet;

-- 1. DANH MỤC SẢN PHẨM
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. SẢN PHẨM
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(200) NOT NULL,
  description TEXT NULL,
  price DECIMAL(12,0) NOT NULL DEFAULT 0,
  old_price DECIMAL(12,0) NULL,
  image VARCHAR(255) NOT NULL DEFAULT 'assets/img/no-image.png',
  stock INT NOT NULL DEFAULT 0,
  sold INT NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. KHÁCH HÀNG
CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NULL,
  address VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. HÓA ĐƠN (ĐƠN HÀNG)
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NULL,
  customer_name VARCHAR(100) NOT NULL,
  customer_phone VARCHAR(20) NOT NULL,
  customer_address VARCHAR(255) NOT NULL,
  note TEXT NULL,
  total DECIMAL(12,0) NOT NULL DEFAULT 0,
  status ENUM('moi','dang_giao','hoan_thanh','da_huy') NOT NULL DEFAULT 'moi',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. CHI TIẾT HÓA ĐƠN
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  product_name VARCHAR(200) NOT NULL,
  price DECIMAL(12,0) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. QUẢN TRỊ VIÊN
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. LIÊN HỆ
CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(20) NULL,
  subject VARCHAR(200) NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ================= DỮ LIỆU MẪU =================

INSERT INTO categories (name, description) VALUES
('Điện thoại', 'Smartphone chính hãng các thương hiệu lớn'),
('Laptop & Máy tính', 'Laptop học tập, làm việc và gaming'),
('Thời trang nam', 'Quần áo, phụ kiện thời trang cho nam'),
('Thời trang nữ', 'Quần áo, phụ kiện thời trang cho nữ'),
('Đồ gia dụng', 'Thiết bị gia dụng tiện ích cho gia đình'),
('Phụ kiện', 'Tai nghe, sạc dự phòng và phụ kiện công nghệ');

INSERT INTO products (category_id, name, description, price, old_price, image, stock, sold, is_featured) VALUES
(1, 'Điện thoại Samsung Galaxy A55 5G (8GB/128GB)', 'Màn hình Super AMOLED 120Hz, chip Exynos 1480, pin 5000mAh, kháng nước IP67.', 7990000, 9490000, 'assets/img/sp1.png', 50, 120, 1),
(1, 'Điện thoại iPhone 13 128GB chính hãng', 'Chip A15 Bionic mạnh mẽ, camera kép 12MP, màn hình Super Retina XDR 6.1 inch.', 13990000, 16990000, 'assets/img/sp2.png', 30, 85, 1),
(2, 'Laptop Asus VivoBook 15 (i5/8GB/512GB)', 'CPU Intel Core i5 thế hệ 13, RAM 8GB, SSD 512GB, màn hình 15.6 inch Full HD.', 14490000, NULL, 'assets/img/sp3.png', 20, 40, 1),
(2, 'Laptop HP Pavilion 15 (Ryzen 5/16GB/512GB)', 'Chip AMD Ryzen 5 7530U, RAM 16GB, SSD 512GB, vỏ nhôm cao cấp.', 15990000, 17990000, 'assets/img/sp4.png', 15, 25, 0),
(3, 'Áo thun nam cotton basic', 'Chất liệu cotton 100% thoáng mát, form regular fit, nhiều màu sắc.', 149000, 249000, 'assets/img/sp5.png', 200, 500, 1),
(3, 'Quần jean nam slimfit co giãn', 'Jean co giãn 4 chiều, form slimfit tôn dáng, bền màu.', 349000, NULL, 'assets/img/sp6.png', 150, 300, 0),
(4, 'Váy hoa nữ mùa hè', 'Vải voan mềm mại, họa tiết hoa xinh xắn, phù hợp đi chơi, dạo phố.', 259000, 399000, 'assets/img/sp7.png', 120, 260, 1),
(4, 'Áo khoác nữ chống nắng UV', 'Vải chống tia UV, thoáng khí, có mũ rộng vành, túi khóa kéo.', 199000, NULL, 'assets/img/sp8.png', 180, 340, 0),
(5, 'Nồi chiên không dầu 5L', 'Dung tích 5L cho gia đình 4-6 người, 8 chế độ nấu, lòng nồi chống dính.', 1290000, 1990000, 'assets/img/sp9.png', 40, 150, 1),
(5, 'Máy xay sinh tố đa năng', 'Công suất 500W, 2 cối xay, lưỡi dao inox 6 cánh sắc bén.', 549000, NULL, 'assets/img/sp10.png', 60, 90, 0),
(6, 'Tai nghe Bluetooth TWS', 'Bluetooth 5.3, chống ồn ENC, pin 36 giờ kèm hộp sạc, chống nước IPX5.', 399000, 699000, 'assets/img/sp11.png', 100, 420, 1),
(6, 'Sạc dự phòng 20000mAh sạc nhanh', 'Dung lượng 20000mAh, sạc nhanh 22.5W, màn hình LED hiển thị pin.', 459000, NULL, 'assets/img/sp12.png', 90, 210, 0);

-- Tài khoản quản trị: username = admin | mật khẩu = admin123
INSERT INTO admins (username, password) VALUES
('admin', '$2b$12$LHdUF6/QQ9W7YQMPIvL1cePNUlQBvRX7h.MnB/2GtZAqBOHsL6SK2');

-- Khách hàng mẫu: mật khẩu = 123456
INSERT INTO customers (name, email, password, phone, address) VALUES
('Nguyễn Văn An', 'an.nguyen@gmail.com', '$2b$12$qlXRP8OTwSWjbgHGMNKWDuqZ8emtMuyUS383gGR.MBmYc7neklci6', '0901234567', '123 Nguyễn Huệ, Quận 1, TP.HCM'),
('Trần Thị Bình', 'binh.tran@gmail.com', '$2b$12$qlXRP8OTwSWjbgHGMNKWDuqZ8emtMuyUS383gGR.MBmYc7neklci6', '0912345678', '45 Lê Lợi, Hải Châu, Đà Nẵng');

-- Đơn hàng mẫu
INSERT INTO orders (customer_id, customer_name, customer_phone, customer_address, note, total, status) VALUES
(1, 'Nguyễn Văn An', '0901234567', '123 Nguyễn Huệ, Quận 1, TP.HCM', 'Giao giờ hành chính', 8389000, 'dang_giao'),
(2, 'Trần Thị Bình', '0912345678', '45 Lê Lợi, Hải Châu, Đà Nẵng', '', 497000, 'moi');

INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES
(1, 1, 'Điện thoại Samsung Galaxy A55 5G (8GB/128GB)', 7990000, 1),
(1, 11, 'Tai nghe Bluetooth TWS', 399000, 1),
(2, 5, 'Áo thun nam cotton basic', 149000, 2),
(2, 8, 'Áo khoác nữ chống nắng UV', 199000, 1);
