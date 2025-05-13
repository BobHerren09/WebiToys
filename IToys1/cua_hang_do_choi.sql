-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 13, 2025 lúc 10:11 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `cua_hang_do_choi`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `lien_ket` varchar(255) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `hien_thi` tinyint(1) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `banner`
--

INSERT INTO `banner` (`id`, `tieu_de`, `mo_ta`, `hinh_anh`, `lien_ket`, `thu_tu`, `hien_thi`, `ngay_tao`) VALUES
(1, 'Đồ chơi giáo dục', 'Giảm giá đến 50% cho tất cả đồ chơi giáo dục', 'banner-1-1746937556.jpg', 'index.php?trang=san-pham&danh-muc=2', 1, 1, '2025-05-09 16:00:48'),
(2, 'Đồ chơi mô hình Kamen Rider chính hãng', 'Decade, và các loại model kit KR Bandai giảm giá 20%', 'banner-2-1746937692.gif', 'index.php?trang=san-pham&danh-muc=2', 2, 1, '2025-05-09 16:00:48'),
(3, 'Đồ chơi mùa hè 2025', 'Những sản phẩm mới nhất của chúng tôi', 'banner-3-1746937793.jpg', 'index.php?trang=san-pham', 3, 1, '2025-05-09 16:00:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `id` int(11) NOT NULL,
  `don_hang_id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia` decimal(10,2) NOT NULL,
  `thanh_tien` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id`, `don_hang_id`, `san_pham_id`, `so_luong`, `gia`, `thanh_tien`) VALUES
(1, 1, 7, 1, 99000.00, 99000.00),
(2, 2, 7, 5, 99000.00, 495000.00),
(3, 3, 8, 3, 350000.00, 1050000.00),
(7, 7, 7, 5, 99000.00, 495000.00),
(8, 8, 6, 7, 200000.00, 1400000.00),
(9, 9, 8, 4, 350000.00, 1400000.00),
(10, 10, 5, 3, 499000.00, 1497000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id` int(11) NOT NULL,
  `ten_danh_muc` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `hien_thi` tinyint(1) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `mo_ta`, `hinh_anh`, `thu_tu`, `hien_thi`, `ngay_tao`) VALUES
(1, 'Đồ chơi trẻ em', 'Các loại đồ chơi dành cho trẻ em', 'danh-muc-1.jpg', 1, 1, '2025-05-09 15:59:33'),
(2, 'Đồ chơi giáo dục', 'Đồ chơi phát triển trí tuệ và kỹ năng', 'danh-muc-2.jpg', 2, 1, '2025-05-09 15:59:33'),
(3, 'Đồ chơi mô hình', 'Các loại mô hình xe, máy bay, tàu thuyền', 'danh-muc-3.jpg', 3, 1, '2025-05-09 15:59:33'),
(4, 'Đồ chơi điều khiển', 'Đồ chơi điều khiển từ xa', 'danh-muc-4.jpg', 4, 1, '2025-05-09 15:59:33'),
(5, 'Đồ chơi ngoài trời', 'Đồ chơi sử dụng ngoài trời', 'danh-muc-5.jpg', 5, 1, '2025-05-09 15:59:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id` int(11) NOT NULL,
  `khach_hang_id` int(11) DEFAULT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `dien_thoai` varchar(20) NOT NULL,
  `dia_chi` text NOT NULL,
  `ghi_chu` text DEFAULT NULL,
  `tong_tien` decimal(10,2) NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id`, `khach_hang_id`, `ho_ten`, `email`, `dien_thoai`, `dia_chi`, `ghi_chu`, `tong_tien`, `trang_thai`, `ngay_tao`) VALUES
(1, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 99000.00, 3, '2025-05-10 15:40:23'),
(2, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 495000.00, 3, '2025-05-10 15:41:23'),
(3, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 1050000.00, 3, '2025-05-10 16:30:30'),
(7, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 495000.00, 3, '2025-05-10 17:20:37'),
(8, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 1400000.00, 4, '2025-05-10 17:21:14'),
(9, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 1400000.00, 4, '2025-05-10 17:23:03'),
(10, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 1497000.00, 3, '2025-05-13 09:53:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinh_anh_san_pham`
--

CREATE TABLE `hinh_anh_san_pham` (
  `id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `thu_tu` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hinh_anh_san_pham`
--

INSERT INTO `hinh_anh_san_pham` (`id`, `san_pham_id`, `hinh_anh`, `thu_tu`) VALUES
(1, 8, 'san-pham-8-phu-1746782048-0.jpg', 1),
(2, 8, 'san-pham-8-phu-1746782048-1.jpg', 2),
(3, 7, 'san-pham-7-phu-1746884358-0.jpg', 1),
(4, 6, 'san-pham-6-phu-1746888790-0.png', 1),
(5, 6, 'san-pham-6-phu-1746888790-1.jpg', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int(11) NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `dien_thoai` varchar(20) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `trang_thai` tinyint(1) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ho_ten`, `email`, `mat_khau`, `dien_thoai`, `dia_chi`, `trang_thai`, `ngay_tao`, `avatar`) VALUES
(1, 'Nguyễn Văn A', 'nguyenvana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0123456789', 'Hà Nội', 1, '2025-05-09 15:59:33', NULL),
(2, 'Trần Thị B', 'tranthib@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', 'Hồ Chí Minh', 1, '2025-05-09 15:59:33', NULL),
(3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '$2y$10$xewQW9Pln8eMLk2T52ZAi.JQLck5pRhMPcqi5pnkFqDL0JfbZLJIC', '0566191650', '199, Hà Nội\r\n', 1, '2025-05-09 11:04:30', 'user-avatar-3-1746782562.gif');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhat_ky_ton_kho`
--

CREATE TABLE `nhat_ky_ton_kho` (
  `id` int(11) NOT NULL,
  `san_pham_id` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `loai_thay_doi` enum('tang','giam') NOT NULL,
  `ghi_chu` text DEFAULT NULL,
  `ngay_tao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quan_tri_vien`
--

CREATE TABLE `quan_tri_vien` (
  `id` int(11) NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `ten_dang_nhap` varchar(255) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `quyen_han` varchar(50) DEFAULT 'admin',
  `trang_thai` tinyint(1) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `quan_tri_vien`
--

INSERT INTO `quan_tri_vien` (`id`, `ho_ten`, `ten_dang_nhap`, `mat_khau`, `email`, `quyen_han`, `trang_thai`, `ngay_tao`, `avatar`) VALUES
(1, 'Admin NTĐ', 'admin', '$2y$10$c4NSejNYByABsa0sYjhcYeXJxAR01N6EAYB6cZVAsBWRp9SsBRiKy', 'admin@example.com', 'admin', 1, '2025-05-09 15:59:33', 'admin-avatar-1-1746782429.gif');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `id` int(11) NOT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `danh_muc_id` int(11) DEFAULT NULL,
  `mo_ta_ngan` text DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `gia` decimal(10,2) NOT NULL,
  `gia_khuyen_mai` decimal(10,2) DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `so_luong` int(11) DEFAULT 0,
  `noi_bat` tinyint(1) DEFAULT 0,
  `trang_thai` tinyint(1) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ton_kho` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id`, `ten_san_pham`, `danh_muc_id`, `mo_ta_ngan`, `mo_ta`, `gia`, `gia_khuyen_mai`, `hinh_anh`, `so_luong`, `noi_bat`, `trang_thai`, `ngay_tao`, `ton_kho`) VALUES
(1, 'Xe ô tô điều khiển từ xa', 4, 'Xe ô tô điều khiển từ xa tốc độ cao', 'Xe ô tô điều khiển từ xa tốc độ cao, pin sạc, chạy được 30 phút liên tục', 350000.00, 299000.00, 'san-pham-1.jpg', 50, 1, 1, '2025-05-09 15:59:33', 0),
(2, 'Bộ xếp hình 1000 chi tiết', 2, 'Bộ xếp hình 1000 chi tiết nhiều màu sắc', 'Bộ xếp hình 1000 chi tiết nhiều màu sắc, phát triển tư duy không gian và sáng tạo', 250000.00, NULL, 'san-pham-2.jpg', 30, 1, 1, '2025-05-09 15:59:33', 0),
(3, 'Búp bê thời trang', 1, 'Búp bê thời trang có thể thay đổi trang phục', 'Búp bê thời trang có thể thay đổi trang phục, phát triển óc sáng tạo và thẩm mỹ', 180000.00, 150000.00, 'san-pham-3.jpg', 40, 0, 1, '2025-05-09 15:59:33', 0),
(4, 'Máy bay mô hình Boeing 747', 3, 'Máy bay mô hình Boeing 747 tỉ lệ 1:100', 'Máy bay mô hình Boeing 747 tỉ lệ 1:100, làm từ nhựa cao cấp, sơn tỉ mỉ', 450000.00, NULL, 'san-pham-4.jpg', 20, 1, 1, '2025-05-09 15:59:33', 0),
(5, 'Cầu trượt mini', 5, 'Cầu trượt mini cho bé từ 2-5 tuổi', 'Cầu trượt mini cho bé từ 2-5 tuổi, nhựa an toàn, dễ dàng lắp đặt', 550000.00, 499000.00, 'san-pham-5.jpg', 12, 1, 1, '2025-05-09 15:59:33', 0),
(6, 'Bộ đồ chơi nấu ăn 84 chi tiết', 1, 'Bộ đồ chơi nấu ăn 30 món', 'Bộ đồ chơi nấu ăn 30 món, nhựa an toàn, phát triển kỹ năng xã hội', 200000.00, NULL, 'san-pham-6-1746888790.jpg', 25, 0, 1, '2025-05-09 15:59:33', 0),
(7, 'Rubik 3x3', 2, 'Rubik 3x3 xoay trơn', 'Rubik 3x3 xoay trơn, phát triển tư duy logic và trí nhớ', 120000.00, 99000.00, 'san-pham-7-1746884358.jpg', 55, 1, 1, '2025-05-09 15:59:33', 0),
(8, 'Xe tăng điều khiển', 4, 'Xe tăng điều khiển từ xa có thể bắn đạn', 'Xe tăng điều khiển từ xa có thể bắn đạn, âm thanh sống động', 400000.00, 350000.00, 'san-pham-8-1746782048.jpg', 34, 1, 1, '2025-05-09 15:59:33', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `don_hang_id` (`don_hang_id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `khach_hang_id` (`khach_hang_id`);

--
-- Chỉ mục cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `nhat_ky_ton_kho`
--
ALTER TABLE `nhat_ky_ton_kho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `san_pham_id` (`san_pham_id`);

--
-- Chỉ mục cho bảng `quan_tri_vien`
--
ALTER TABLE `quan_tri_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `danh_muc_id` (`danh_muc_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `nhat_ky_ton_kho`
--
ALTER TABLE `nhat_ky_ton_kho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `quan_tri_vien`
--
ALTER TABLE `quan_tri_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`khach_hang_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  ADD CONSTRAINT `hinh_anh_san_pham_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nhat_ky_ton_kho`
--
ALTER TABLE `nhat_ky_ton_kho`
  ADD CONSTRAINT `nhat_ky_ton_kho_ibfk_1` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`);

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
