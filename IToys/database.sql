
-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS cua_hang_do_choi DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cua_hang_do_choi;

-- Tạo bảng danh mục
CREATE TABLE IF NOT EXISTS danh_muc (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_danh_muc VARCHAR(255) NOT NULL,
  mo_ta TEXT,
  hinh_anh VARCHAR(255),
  thu_tu INT DEFAULT 0,
  trang_thai TINYINT(1) DEFAULT 1,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng sản phẩm
CREATE TABLE IF NOT EXISTS san_pham (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_san_pham VARCHAR(255) NOT NULL,
  danh_muc_id INT,
  mo_ta TEXT,
  mo_ta_chi_tiet TEXT,
  hinh_anh VARCHAR(255),
  gia DECIMAL(15,0) NOT NULL DEFAULT 0,
  gia_khuyen_mai DECIMAL(15,0) DEFAULT NULL,
  so_luong INT DEFAULT 0,
  luot_xem INT DEFAULT 0,
  trang_thai TINYINT(1) DEFAULT 1,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (danh_muc_id) REFERENCES danh_muc(id) ON DELETE SET NULL
);

-- Tạo bảng người dùng
CREATE TABLE IF NOT EXISTS nguoi_dung (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ho_ten VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  mat_khau VARCHAR(255) NOT NULL,
  dien_thoai VARCHAR(20),
  dia_chi TEXT,
  avatar VARCHAR(255) DEFAULT NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng quản trị viên
CREATE TABLE IF NOT EXISTS quan_tri_vien (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_dang_nhap VARCHAR(50) NOT NULL UNIQUE,
  mat_khau VARCHAR(255) NOT NULL,
  ho_ten VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  quyen_han VARCHAR(20) DEFAULT 'admin',
  avatar VARCHAR(255) DEFAULT NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng đơn hàng
CREATE TABLE IF NOT EXISTS don_hang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  khach_hang_id INT,
  ho_ten VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  dien_thoai VARCHAR(20) NOT NULL,
  dia_chi TEXT NOT NULL,
  ghi_chu TEXT,
  tong_tien DECIMAL(15,0) NOT NULL DEFAULT 0,
  trang_thai TINYINT DEFAULT 0,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (khach_hang_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
);

-- Tạo bảng chi tiết đơn hàng
CREATE TABLE IF NOT EXISTS chi_tiet_don_hang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  don_hang_id INT NOT NULL,
  san_pham_id INT,
  so_luong INT NOT NULL DEFAULT 1,
  gia DECIMAL(15,0) NOT NULL DEFAULT 0,
  thanh_tien DECIMAL(15,0) NOT NULL DEFAULT 0,
  FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
  FOREIGN KEY (san_pham_id) REFERENCES san_pham(id) ON DELETE SET NULL
);

-- Tạo bảng banner
CREATE TABLE IF NOT EXISTS banner (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tieu_de VARCHAR(255) NOT NULL,
  mo_ta TEXT,
  hinh_anh VARCHAR(255) NOT NULL,
  lien_ket VARCHAR(255),
  thu_tu INT DEFAULT 0,
  hien_thi TINYINT(1) DEFAULT 1,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Thêm quản trị viên mặc định
INSERT INTO quan_tri_vien (ten_dang_nhap, mat_khau, ho_ten, email, quyen_han) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'admin@example.com', 'admin');

