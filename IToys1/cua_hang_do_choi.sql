-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 15, 2025 lúc 07:15 PM
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
-- Cơ sở dữ liệu: `chat_system`
--
CREATE DATABASE IF NOT EXISTS `chat_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `chat_system`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `file_uploads`
--

CREATE TABLE `file_uploads` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `file_name`, `file_path`, `file_type`, `read_at`, `edited_at`, `deleted_at`, `created_at`) VALUES
(1, 1, 2, 'Hello', NULL, NULL, NULL, '2025-03-05 08:04:59', NULL, NULL, '2025-03-05 08:04:57'),
(2, 2, 1, 'Chao ni ma', NULL, NULL, NULL, '2025-03-05 08:05:07', '2025-03-05 08:10:14', NULL, '2025-03-05 08:05:06'),
(3, 1, 2, 'Aa', NULL, NULL, NULL, '2025-03-05 08:06:09', NULL, '2025-03-05 08:08:33', '2025-03-05 08:06:06'),
(4, 2, 1, 'hello', NULL, NULL, NULL, '2025-03-05 08:39:43', NULL, NULL, '2025-03-05 08:39:41'),
(5, 1, 2, '💰', NULL, NULL, NULL, '2025-03-05 08:42:05', NULL, NULL, '2025-03-05 08:42:02'),
(6, 1, 2, 'Aaaa', NULL, NULL, NULL, '2025-03-05 09:00:42', NULL, NULL, '2025-03-05 09:00:20'),
(7, 2, 1, '👍👍', NULL, NULL, NULL, '2025-03-05 09:00:54', NULL, NULL, '2025-03-05 09:00:51'),
(8, 1, 2, '123', NULL, NULL, NULL, '2025-03-05 09:01:04', '2025-03-05 09:01:18', NULL, '2025-03-05 09:01:02'),
(9, 1, 2, '👩‍🦲', NULL, NULL, NULL, '2025-03-05 09:01:27', NULL, NULL, '2025-03-05 09:01:25'),
(10, 4, 1, 'uiaiu', NULL, NULL, NULL, '2025-03-05 10:25:33', '2025-03-05 10:26:37', NULL, '2025-03-05 10:25:01'),
(11, 1, 4, '😉', NULL, NULL, NULL, '2025-03-05 10:25:59', NULL, '2025-03-05 10:26:11', '2025-03-05 10:25:56'),
(14, 4, 1, 'Hello', NULL, NULL, NULL, '2025-03-08 02:12:23', NULL, NULL, '2025-03-06 02:19:48'),
(15, 4, 1, '😏', NULL, NULL, NULL, '2025-03-08 02:12:23', NULL, NULL, '2025-03-06 02:19:55'),
(16, 2, 4, 'hello', NULL, NULL, NULL, '2025-03-06 02:20:26', NULL, NULL, '2025-03-06 02:20:23'),
(17, 4, 2, '😜', NULL, NULL, NULL, '2025-03-06 02:20:45', NULL, '2025-03-06 02:20:47', '2025-03-06 02:20:44'),
(18, 2, 1, '', 'BM01. PHIẾU ĐĂNG KÝ KHÓA LUẬN TỐT NGHIỆP.docx', 'uploads/1741229023_BM01. PHIẾU ĐĂNG KÝ KHÓA LUẬN TỐT NGHIỆP.docx', 'application/vnd.openxmlformats-officedocument.word', '2025-03-08 02:11:48', NULL, '2025-03-06 02:43:52', '2025-03-06 02:43:43'),
(19, 2, 1, '', 'Đề cương khóa luận (1).pdf', 'uploads/1741229038_Đề cương khóa luận (1).pdf', 'application/pdf', '2025-03-08 02:11:48', NULL, '2025-03-06 02:44:02', '2025-03-06 02:43:58'),
(20, 1, 4, 'ab', NULL, NULL, NULL, '2025-03-08 02:18:22', '2025-03-08 02:13:14', '2025-03-08 02:13:18', '2025-03-08 02:12:47'),
(21, 1, 4, 'ádda', NULL, NULL, NULL, '2025-03-08 02:18:22', NULL, '2025-03-08 02:15:10', '2025-03-08 02:15:07'),
(22, 1, 4, '456', NULL, NULL, NULL, '2025-03-08 02:18:22', NULL, '2025-03-08 02:15:21', '2025-03-08 02:15:19'),
(23, 1, 4, '☹️', NULL, NULL, NULL, '2025-03-08 02:18:22', NULL, NULL, '2025-03-08 02:17:35'),
(24, 1, 4, 'Hihi', NULL, NULL, NULL, '2025-03-08 02:18:22', '2025-03-07 03:56:24', NULL, '2025-03-08 02:17:39'),
(25, 1, 4, '', 'Đề cương khóa luận.pdf', 'uploads/1741400273_Đề cương khóa luận.pdf', 'application/pdf', '2025-03-08 02:18:22', NULL, NULL, '2025-03-08 02:17:53'),
(26, 4, 1, '', 'chiem-nguong-nhung-buc-anh-thien-nhien-dep-nhat-the-gioi-18524012511141175.jpg', 'uploads/1741400391_chiem-nguong-nhung-buc-anh-thien-nhien-dep-nhat-the-gioi-18524012511141175.jpg', 'image/jpeg', '2025-03-08 02:19:58', NULL, NULL, '2025-03-08 02:19:51'),
(27, 1, 4, '😅', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-07 03:56:54'),
(28, 1, 4, '😅', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-07 03:56:58'),
(29, 1, 4, '😅', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-07 03:57:02'),
(30, 1, 4, '🧄', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-07 03:57:07'),
(31, 1, 4, 'ytty', NULL, NULL, NULL, NULL, NULL, '2025-03-07 03:57:48', '2025-03-07 03:57:11'),
(32, 1, 4, 'Hello', NULL, NULL, NULL, NULL, NULL, NULL, '2025-03-07 03:57:21'),
(33, 1, 2, 'yy', NULL, NULL, NULL, '2025-03-22 14:27:03', NULL, NULL, '2025-03-07 03:57:26'),
(34, 1, 4, 'ytfy', NULL, NULL, NULL, NULL, NULL, '2025-03-07 03:57:42', '2025-03-07 03:57:31'),
(35, 2, 1, '', 'Do-choi-giao-duc-Bang-tinh-toan-MT03-Educational-Toys-Math-Table-MT03-e1729989757829.jpg', 'uploads/1742653637_Do-choi-giao-duc-Bang-tinh-toan-MT03-Educational-Toys-Math-Table-MT03-e1729989757829.jpg', 'image/jpeg', NULL, NULL, NULL, '2025-03-22 14:27:17');

--
-- Bẫy `messages`
--
DELIMITER $$
CREATE TRIGGER `before_message_update` BEFORE UPDATE ON `messages` FOR EACH ROW BEGIN
    IF NEW.message != OLD.message AND NEW.edited_at IS NOT NULL AND OLD.edited_at IS NULL THEN
        INSERT INTO message_edits (message_id, previous_content)
        VALUES (OLD.id, OLD.message);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `message_edits`
--

CREATE TABLE `message_edits` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `previous_content` text NOT NULL,
  `edited_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `message_edits`
--

INSERT INTO `message_edits` (`id`, `message_id`, `previous_content`, `edited_at`) VALUES
(1, 2, 'Cac', '2025-03-05 08:05:42'),
(2, 8, 'rrrr', '2025-03-05 09:01:18'),
(3, 10, '1', '2025-03-05 10:26:34'),
(4, 20, 'abc', '2025-03-08 02:13:14'),
(5, 24, '😭', '2025-03-07 03:56:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `message_reactions`
--

CREATE TABLE `message_reactions` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_active` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `last_active`, `created_at`) VALUES
(1, 'Td200403', '$2y$10$YeeF4fb1v2JKEMRBS5cEd.xL6rn.ffv222EuadMsgIh9ypNwZJKRy', '2025-05-13 09:52:35', '2025-03-05 08:02:26'),
(2, 'Admin', '$2y$10$lpr2q1UTBFdnR9XPpv5LCOzn7RGYKgTEeuubZ/9D7096cjn..5PpS', '2025-03-22 14:27:28', '2025-03-05 08:03:41'),
(3, 'Admin2', '$2y$10$hC9hDg4sSN99g886z4wQKugUOlkRNkXH4crnqoz7oVtABC7JyRCCu', '2025-03-05 08:53:00', '2025-03-05 08:52:55'),
(4, 'Dung', '$2y$10$t7uomiLoLwYRGVHgKYTDkOX7g/AfUxpc2vphdj2C7fUaKlDIc/Pm.', '2025-03-08 02:20:21', '2025-03-05 10:23:58');

--
-- Bẫy `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_activity_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.last_active IS NOT NULL AND (OLD.last_active IS NULL OR NEW.last_active > OLD.last_active) THEN
        -- Đóng trạng thái offline cũ nếu có
        UPDATE user_status 
        SET ended_at = NOW() 
        WHERE user_id = NEW.id AND status = 'offline' AND ended_at IS NULL;
        
        -- Thêm trạng thái online mới
        INSERT INTO user_status (user_id, status)
        VALUES (NEW.id, 'online');
    END IF;
    
    -- Nếu không hoạt động trong 5 phút, đánh dấu là offline
    IF OLD.last_active IS NOT NULL AND 
       NEW.last_active IS NOT NULL AND 
       TIMESTAMPDIFF(MINUTE, NEW.last_active, NOW()) > 5 THEN
        
        -- Đóng trạng thái online cũ
        UPDATE user_status 
        SET ended_at = NOW() 
        WHERE user_id = NEW.id AND status = 'online' AND ended_at IS NULL;
        
        -- Thêm trạng thái offline mới
        INSERT INTO user_status (user_id, status)
        VALUES (NEW.id, 'offline');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_status`
--

CREATE TABLE `user_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('online','offline','away') NOT NULL DEFAULT 'offline',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_status`
--

INSERT INTO `user_status` (`id`, `user_id`, `status`, `started_at`, `ended_at`) VALUES
(1, 1, 'online', '2025-03-05 08:02:34', NULL),
(2, 2, 'online', '2025-03-05 08:04:24', NULL),
(3, 1, 'online', '2025-03-05 08:04:53', NULL),
(4, 2, 'online', '2025-03-05 08:09:39', NULL),
(5, 2, 'online', '2025-03-05 08:12:23', NULL),
(6, 2, 'online', '2025-03-05 08:12:26', NULL),
(7, 2, 'online', '2025-03-05 08:14:43', NULL),
(8, 2, 'online', '2025-03-05 08:14:45', NULL),
(9, 1, 'online', '2025-03-05 08:14:48', NULL),
(10, 1, 'online', '2025-03-05 08:14:49', NULL),
(11, 1, 'online', '2025-03-05 08:14:50', NULL),
(12, 2, 'online', '2025-03-05 08:17:31', NULL),
(13, 2, 'online', '2025-03-05 08:19:19', NULL),
(14, 2, 'online', '2025-03-05 08:19:33', NULL),
(15, 1, 'online', '2025-03-05 08:32:22', NULL),
(16, 2, 'online', '2025-03-05 08:39:10', NULL),
(17, 1, 'online', '2025-03-05 08:39:11', NULL),
(18, 1, 'online', '2025-03-05 08:39:24', NULL),
(19, 1, 'online', '2025-03-05 08:39:27', NULL),
(20, 1, 'online', '2025-03-05 08:40:02', NULL),
(21, 2, 'online', '2025-03-05 08:40:30', NULL),
(22, 1, 'online', '2025-03-05 08:40:32', NULL),
(23, 1, 'online', '2025-03-05 08:40:43', NULL),
(24, 1, 'online', '2025-03-05 08:40:44', NULL),
(25, 2, 'online', '2025-03-05 08:40:52', NULL),
(26, 1, 'online', '2025-03-05 08:41:16', NULL),
(27, 2, 'online', '2025-03-05 08:41:25', NULL),
(28, 1, 'online', '2025-03-05 08:41:46', NULL),
(29, 2, 'online', '2025-03-05 08:41:56', NULL),
(30, 2, 'online', '2025-03-05 08:42:26', NULL),
(31, 1, 'online', '2025-03-05 08:42:33', NULL),
(32, 2, 'online', '2025-03-05 08:42:56', NULL),
(33, 1, 'online', '2025-03-05 08:43:03', NULL),
(34, 2, 'online', '2025-03-05 08:43:26', NULL),
(35, 1, 'online', '2025-03-05 08:43:33', NULL),
(36, 1, 'online', '2025-03-05 08:44:03', NULL),
(37, 2, 'online', '2025-03-05 08:44:11', NULL),
(38, 2, 'online', '2025-03-05 08:45:11', NULL),
(39, 1, 'online', '2025-03-05 08:45:11', NULL),
(40, 1, 'online', '2025-03-05 08:45:32', NULL),
(41, 1, 'online', '2025-03-05 08:46:03', NULL),
(42, 2, 'online', '2025-03-05 08:46:11', NULL),
(43, 1, 'online', '2025-03-05 08:46:33', NULL),
(44, 2, 'online', '2025-03-05 08:47:11', NULL),
(45, 1, 'online', '2025-03-05 08:47:11', NULL),
(46, 1, 'online', '2025-03-05 08:47:33', NULL),
(47, 1, 'online', '2025-03-05 08:48:03', NULL),
(48, 2, 'online', '2025-03-05 08:48:11', NULL),
(49, 1, 'online', '2025-03-05 08:49:11', NULL),
(50, 2, 'online', '2025-03-05 08:49:11', NULL),
(51, 1, 'online', '2025-03-05 08:50:11', NULL),
(52, 2, 'online', '2025-03-05 08:50:11', NULL),
(53, 1, 'online', '2025-03-05 08:51:11', NULL),
(54, 2, 'online', '2025-03-05 08:51:11', NULL),
(55, 2, 'online', '2025-03-05 08:52:11', NULL),
(56, 1, 'online', '2025-03-05 08:52:11', NULL),
(57, 1, 'online', '2025-03-05 08:52:33', NULL),
(58, 1, 'online', '2025-03-05 08:52:35', NULL),
(59, 3, 'online', '2025-03-05 08:53:00', NULL),
(60, 2, 'online', '2025-03-05 08:53:11', NULL),
(61, 2, 'online', '2025-03-05 08:53:13', NULL),
(62, 2, 'online', '2025-03-05 08:53:14', NULL),
(63, 2, 'online', '2025-03-05 08:53:49', NULL),
(64, 1, 'online', '2025-03-05 08:54:00', NULL),
(65, 1, 'online', '2025-03-05 08:54:01', NULL),
(66, 1, 'online', '2025-03-05 08:54:08', NULL),
(67, 2, 'online', '2025-03-05 08:54:37', NULL),
(68, 1, 'online', '2025-03-05 08:54:49', NULL),
(69, 1, 'online', '2025-03-05 08:54:55', NULL),
(70, 2, 'online', '2025-03-05 08:55:18', NULL),
(71, 1, 'online', '2025-03-05 08:55:25', NULL),
(72, 1, 'online', '2025-03-05 08:55:30', NULL),
(73, 2, 'online', '2025-03-05 08:55:48', NULL),
(74, 1, 'online', '2025-03-05 08:55:55', NULL),
(75, 1, 'online', '2025-03-05 08:56:01', NULL),
(76, 2, 'online', '2025-03-05 08:56:18', NULL),
(77, 1, 'online', '2025-03-05 08:56:25', NULL),
(78, 1, 'online', '2025-03-05 08:56:31', NULL),
(79, 2, 'online', '2025-03-05 08:56:48', NULL),
(80, 1, 'online', '2025-03-05 08:57:01', NULL),
(81, 1, 'online', '2025-03-05 08:57:11', NULL),
(82, 2, 'online', '2025-03-05 08:57:18', NULL),
(83, 1, 'online', '2025-03-05 08:57:35', NULL),
(84, 1, 'online', '2025-03-05 08:57:38', NULL),
(85, 1, 'online', '2025-03-05 08:57:55', NULL),
(86, 2, 'online', '2025-03-05 08:58:11', NULL),
(87, 1, 'online', '2025-03-05 08:58:25', NULL),
(88, 1, 'online', '2025-03-05 08:59:11', NULL),
(89, 2, 'online', '2025-03-05 08:59:11', NULL),
(90, 1, 'online', '2025-03-05 08:59:33', NULL),
(91, 1, 'online', '2025-03-05 09:00:10', NULL),
(92, 1, 'online', '2025-03-05 09:00:11', NULL),
(93, 2, 'online', '2025-03-05 09:00:11', NULL),
(94, 2, 'online', '2025-03-05 09:00:42', NULL),
(95, 2, 'online', '2025-03-05 09:00:48', NULL),
(96, 2, 'online', '2025-03-05 09:01:22', NULL),
(97, 2, 'online', '2025-03-05 09:01:52', NULL),
(98, 1, 'online', '2025-03-05 09:01:56', NULL),
(99, 2, 'online', '2025-03-05 09:02:21', NULL),
(100, 1, 'online', '2025-03-05 09:02:26', NULL),
(101, 2, 'online', '2025-03-05 09:02:51', NULL),
(102, 1, 'online', '2025-03-05 09:02:56', NULL),
(103, 2, 'online', '2025-03-05 09:03:22', NULL),
(104, 1, 'online', '2025-03-05 09:03:26', NULL),
(105, 2, 'online', '2025-03-05 09:03:52', NULL),
(106, 1, 'online', '2025-03-05 09:03:56', NULL),
(107, 2, 'online', '2025-03-05 09:05:11', NULL),
(108, 1, 'online', '2025-03-05 09:05:11', NULL),
(109, 4, 'online', '2025-03-05 10:24:13', NULL),
(110, 4, 'online', '2025-03-05 10:24:14', NULL),
(111, 4, 'online', '2025-03-05 10:24:55', NULL),
(112, 1, 'online', '2025-03-05 10:25:28', NULL),
(113, 1, 'online', '2025-03-05 10:25:29', NULL),
(114, 4, 'online', '2025-03-05 10:25:32', NULL),
(115, 4, 'online', '2025-03-05 10:26:01', NULL),
(116, 1, 'online', '2025-03-05 10:26:27', NULL),
(117, 4, 'online', '2025-03-05 10:26:31', NULL),
(118, 1, 'online', '2025-03-06 02:03:27', NULL),
(119, 1, 'online', '2025-03-06 02:05:32', NULL),
(120, 1, 'online', '2025-03-06 02:06:05', NULL),
(121, 4, 'online', '2025-03-06 02:06:14', NULL),
(122, 4, 'online', '2025-03-06 02:06:26', NULL),
(123, 2, 'online', '2025-03-06 02:06:51', NULL),
(124, 2, 'online', '2025-03-06 02:11:33', NULL),
(125, 2, 'online', '2025-03-06 02:11:34', NULL),
(126, 2, 'online', '2025-03-06 02:11:51', NULL),
(127, 2, 'online', '2025-03-06 02:11:52', NULL),
(128, 2, 'online', '2025-03-06 02:11:53', NULL),
(129, 2, 'online', '2025-03-06 02:12:43', NULL),
(130, 2, 'online', '2025-03-06 02:12:52', NULL),
(131, 2, 'online', '2025-03-06 02:12:54', NULL),
(132, 2, 'online', '2025-03-06 02:12:55', NULL),
(133, 2, 'online', '2025-03-06 02:12:56', NULL),
(134, 2, 'online', '2025-03-06 02:12:57', NULL),
(135, 2, 'online', '2025-03-06 02:12:58', NULL),
(136, 2, 'online', '2025-03-06 02:12:59', NULL),
(137, 2, 'online', '2025-03-06 02:13:00', NULL),
(138, 2, 'online', '2025-03-06 02:13:01', NULL),
(139, 2, 'online', '2025-03-06 02:13:05', NULL),
(140, 2, 'online', '2025-03-06 02:13:07', NULL),
(141, 2, 'online', '2025-03-06 02:13:08', NULL),
(142, 2, 'online', '2025-03-06 02:13:09', NULL),
(143, 2, 'online', '2025-03-06 02:13:10', NULL),
(144, 2, 'online', '2025-03-06 02:13:11', NULL),
(145, 2, 'online', '2025-03-06 02:13:16', NULL),
(146, 2, 'online', '2025-03-06 02:13:17', NULL),
(147, 2, 'online', '2025-03-06 02:13:18', NULL),
(148, 2, 'online', '2025-03-06 02:13:23', NULL),
(149, 2, 'online', '2025-03-06 02:13:25', NULL),
(150, 2, 'online', '2025-03-06 02:13:26', NULL),
(151, 2, 'online', '2025-03-06 02:13:27', NULL),
(152, 2, 'online', '2025-03-06 02:13:28', NULL),
(153, 2, 'online', '2025-03-06 02:13:31', NULL),
(154, 2, 'online', '2025-03-06 02:13:32', NULL),
(155, 2, 'online', '2025-03-06 02:13:33', NULL),
(156, 2, 'online', '2025-03-06 02:13:42', NULL),
(157, 2, 'online', '2025-03-06 02:13:43', NULL),
(158, 2, 'online', '2025-03-06 02:13:53', NULL),
(159, 2, 'online', '2025-03-06 02:14:15', NULL),
(160, 2, 'online', '2025-03-06 02:14:16', NULL),
(161, 2, 'online', '2025-03-06 02:14:24', NULL),
(162, 2, 'online', '2025-03-06 02:14:54', NULL),
(163, 2, 'online', '2025-03-06 02:15:23', NULL),
(164, 2, 'online', '2025-03-06 02:15:46', NULL),
(165, 2, 'online', '2025-03-06 02:15:47', NULL),
(166, 2, 'online', '2025-03-06 02:15:48', NULL),
(167, 2, 'online', '2025-03-06 02:15:49', NULL),
(168, 2, 'online', '2025-03-06 02:15:51', NULL),
(169, 2, 'online', '2025-03-06 02:15:52', NULL),
(170, 2, 'online', '2025-03-06 02:15:53', NULL),
(171, 2, 'online', '2025-03-06 02:15:54', NULL),
(172, 4, 'online', '2025-03-06 02:16:09', NULL),
(173, 4, 'online', '2025-03-06 02:16:10', NULL),
(174, 4, 'online', '2025-03-06 02:16:16', NULL),
(175, 4, 'online', '2025-03-06 02:16:17', NULL),
(176, 4, 'online', '2025-03-06 02:16:18', NULL),
(177, 4, 'online', '2025-03-06 02:16:20', NULL),
(178, 2, 'online', '2025-03-06 02:16:24', NULL),
(179, 4, 'online', '2025-03-06 02:16:40', NULL),
(180, 2, 'online', '2025-03-06 02:16:54', NULL),
(181, 4, 'online', '2025-03-06 02:17:10', NULL),
(182, 2, 'online', '2025-03-06 02:17:24', NULL),
(183, 4, 'online', '2025-03-06 02:17:40', NULL),
(184, 4, 'online', '2025-03-06 02:18:07', NULL),
(185, 4, 'online', '2025-03-06 02:18:08', NULL),
(186, 4, 'online', '2025-03-06 02:18:09', NULL),
(187, 4, 'online', '2025-03-06 02:18:10', NULL),
(188, 4, 'online', '2025-03-06 02:18:11', NULL),
(189, 2, 'online', '2025-03-06 02:18:32', NULL),
(190, 4, 'online', '2025-03-06 02:18:38', NULL),
(191, 2, 'online', '2025-03-06 02:18:47', NULL),
(192, 2, 'online', '2025-03-06 02:18:48', NULL),
(193, 2, 'online', '2025-03-06 02:18:49', NULL),
(194, 2, 'online', '2025-03-06 02:18:50', NULL),
(195, 2, 'online', '2025-03-06 02:18:51', NULL),
(196, 2, 'online', '2025-03-06 02:18:52', NULL),
(197, 2, 'online', '2025-03-06 02:18:53', NULL),
(198, 2, 'online', '2025-03-06 02:18:54', NULL),
(199, 2, 'online', '2025-03-06 02:18:55', NULL),
(200, 2, 'online', '2025-03-06 02:18:56', NULL),
(201, 4, 'online', '2025-03-06 02:19:08', NULL),
(202, 2, 'online', '2025-03-06 02:19:18', NULL),
(203, 4, 'online', '2025-03-06 02:19:25', NULL),
(204, 4, 'online', '2025-03-06 02:19:34', NULL),
(205, 4, 'online', '2025-03-06 02:19:35', NULL),
(206, 2, 'online', '2025-03-06 02:19:48', NULL),
(207, 2, 'online', '2025-03-06 02:20:06', NULL),
(208, 4, 'online', '2025-03-06 02:20:25', NULL),
(209, 2, 'online', '2025-03-06 02:20:54', NULL),
(210, 4, 'online', '2025-03-06 02:21:14', NULL),
(211, 2, 'online', '2025-03-06 02:21:24', NULL),
(212, 4, 'online', '2025-03-06 02:21:44', NULL),
(213, 2, 'online', '2025-03-06 02:21:54', NULL),
(214, 4, 'online', '2025-03-06 02:22:14', NULL),
(215, 4, 'online', '2025-03-06 02:22:44', NULL),
(216, 2, 'online', '2025-03-06 02:22:56', NULL),
(217, 4, 'online', '2025-03-06 02:23:14', NULL),
(218, 2, 'online', '2025-03-06 02:23:26', NULL),
(219, 4, 'online', '2025-03-06 02:23:44', NULL),
(220, 2, 'online', '2025-03-06 02:23:56', NULL),
(221, 4, 'online', '2025-03-06 02:24:14', NULL),
(222, 2, 'online', '2025-03-06 02:24:26', NULL),
(223, 4, 'online', '2025-03-06 02:25:33', NULL),
(224, 2, 'online', '2025-03-06 02:25:33', NULL),
(225, 2, 'online', '2025-03-06 02:26:33', NULL),
(226, 4, 'online', '2025-03-06 02:26:33', NULL),
(227, 2, 'online', '2025-03-06 02:27:18', NULL),
(228, 2, 'online', '2025-03-06 02:27:19', NULL),
(229, 2, 'online', '2025-03-06 02:27:20', NULL),
(230, 2, 'online', '2025-03-06 02:27:21', NULL),
(231, 2, 'online', '2025-03-06 02:27:22', NULL),
(232, 2, 'online', '2025-03-06 02:27:23', NULL),
(233, 2, 'online', '2025-03-06 02:27:31', NULL),
(234, 2, 'online', '2025-03-06 02:27:32', NULL),
(235, 2, 'online', '2025-03-06 02:27:33', NULL),
(236, 4, 'online', '2025-03-06 02:27:33', NULL),
(237, 2, 'online', '2025-03-06 02:27:34', NULL),
(238, 2, 'online', '2025-03-06 02:27:35', NULL),
(239, 4, 'online', '2025-03-06 02:28:33', NULL),
(240, 4, 'online', '2025-03-06 02:28:58', NULL),
(241, 4, 'online', '2025-03-06 02:29:14', NULL),
(242, 4, 'online', '2025-03-06 02:29:44', NULL),
(243, 2, 'online', '2025-03-06 02:30:09', NULL),
(244, 2, 'online', '2025-03-06 02:30:10', NULL),
(245, 2, 'online', '2025-03-06 02:30:13', NULL),
(246, 2, 'online', '2025-03-06 02:30:14', NULL),
(247, 2, 'online', '2025-03-06 02:30:15', NULL),
(248, 4, 'online', '2025-03-06 02:30:27', NULL),
(249, 2, 'online', '2025-03-06 02:30:40', NULL),
(250, 2, 'online', '2025-03-06 02:30:41', NULL),
(251, 2, 'online', '2025-03-06 02:30:42', NULL),
(252, 4, 'online', '2025-03-06 02:31:15', NULL),
(253, 4, 'online', '2025-03-06 02:31:16', NULL),
(254, 4, 'online', '2025-03-06 02:31:46', NULL),
(255, 4, 'online', '2025-03-06 02:32:16', NULL),
(256, 4, 'online', '2025-03-06 02:32:46', NULL),
(257, 4, 'online', '2025-03-06 02:33:16', NULL),
(258, 4, 'online', '2025-03-06 02:33:46', NULL),
(259, 4, 'online', '2025-03-06 02:34:33', NULL),
(260, 4, 'online', '2025-03-06 02:35:33', NULL),
(261, 4, 'online', '2025-03-06 02:35:54', NULL),
(262, 4, 'online', '2025-03-06 02:36:16', NULL),
(263, 4, 'online', '2025-03-06 02:36:46', NULL),
(264, 4, 'online', '2025-03-06 02:37:33', NULL),
(265, 4, 'online', '2025-03-06 02:38:33', NULL),
(266, 4, 'online', '2025-03-06 02:39:33', NULL),
(267, 4, 'online', '2025-03-06 02:40:33', NULL),
(268, 4, 'online', '2025-03-06 02:41:33', NULL),
(269, 4, 'online', '2025-03-06 02:41:46', NULL),
(270, 4, 'online', '2025-03-06 02:42:16', NULL),
(271, 2, 'online', '2025-03-06 02:42:58', NULL),
(272, 2, 'online', '2025-03-06 02:42:59', NULL),
(273, 2, 'online', '2025-03-06 02:43:00', NULL),
(274, 2, 'online', '2025-03-06 02:43:05', NULL),
(275, 2, 'online', '2025-03-06 02:43:29', NULL),
(276, 4, 'online', '2025-03-06 02:43:33', NULL),
(277, 2, 'online', '2025-03-06 02:43:43', NULL),
(278, 2, 'online', '2025-03-06 02:43:58', NULL),
(279, 2, 'online', '2025-03-06 02:43:59', NULL),
(280, 2, 'online', '2025-03-06 02:44:18', NULL),
(281, 2, 'online', '2025-03-06 02:44:24', NULL),
(282, 2, 'online', '2025-03-06 02:44:29', NULL),
(283, 4, 'online', '2025-03-06 02:44:33', NULL),
(284, 2, 'online', '2025-03-06 02:44:59', NULL),
(285, 4, 'online', '2025-03-06 02:45:04', NULL),
(286, 4, 'online', '2025-03-06 02:45:20', NULL),
(287, 1, 'online', '2025-03-08 02:08:55', NULL),
(288, 1, 'online', '2025-03-08 02:09:25', NULL),
(289, 1, 'online', '2025-03-08 02:09:55', NULL),
(290, 1, 'online', '2025-03-08 02:10:25', NULL),
(291, 1, 'online', '2025-03-08 02:10:46', NULL),
(292, 1, 'online', '2025-03-08 02:11:16', NULL),
(293, 1, 'online', '2025-03-08 02:11:22', NULL),
(294, 1, 'online', '2025-03-08 02:11:24', NULL),
(295, 1, 'online', '2025-03-08 02:11:48', NULL),
(296, 1, 'online', '2025-03-08 02:11:54', NULL),
(297, 1, 'online', '2025-03-08 02:12:23', NULL),
(298, 1, 'online', '2025-03-08 02:12:24', NULL),
(299, 1, 'online', '2025-03-08 02:12:25', NULL),
(300, 1, 'online', '2025-03-08 02:12:47', NULL),
(301, 1, 'online', '2025-03-08 02:12:54', NULL),
(302, 1, 'online', '2025-03-08 02:13:24', NULL),
(303, 1, 'online', '2025-03-08 02:13:54', NULL),
(304, 1, 'online', '2025-03-08 02:14:24', NULL),
(305, 1, 'online', '2025-03-08 02:14:54', NULL),
(306, 1, 'online', '2025-03-08 02:15:07', NULL),
(307, 1, 'online', '2025-03-08 02:15:13', NULL),
(308, 1, 'online', '2025-03-08 02:15:14', NULL),
(309, 1, 'online', '2025-03-08 02:15:15', NULL),
(310, 1, 'online', '2025-03-08 02:15:19', NULL),
(311, 1, 'online', '2025-03-08 02:15:44', NULL),
(312, 1, 'online', '2025-03-08 02:16:14', NULL),
(313, 1, 'online', '2025-03-08 02:16:44', NULL),
(314, 1, 'online', '2025-03-08 02:17:14', NULL),
(315, 1, 'online', '2025-03-08 02:17:35', NULL),
(316, 1, 'online', '2025-03-08 02:17:39', NULL),
(317, 1, 'online', '2025-03-08 02:17:44', NULL),
(318, 1, 'online', '2025-03-08 02:17:53', NULL),
(319, 1, 'online', '2025-03-08 02:18:14', NULL),
(320, 4, 'online', '2025-03-08 02:18:20', NULL),
(321, 4, 'online', '2025-03-08 02:18:21', NULL),
(322, 4, 'online', '2025-03-08 02:18:22', NULL),
(323, 1, 'online', '2025-03-08 02:18:44', NULL),
(324, 4, 'online', '2025-03-08 02:18:51', NULL),
(325, 4, 'online', '2025-03-08 02:19:21', NULL),
(326, 4, 'online', '2025-03-08 02:19:51', NULL),
(327, 1, 'online', '2025-03-08 02:19:58', NULL),
(328, 1, 'online', '2025-03-08 02:20:14', NULL),
(329, 4, 'online', '2025-03-08 02:20:21', NULL),
(330, 1, 'online', '2025-03-08 02:20:44', NULL),
(331, 1, 'online', '2025-03-08 02:21:14', NULL),
(332, 1, 'online', '2025-03-08 02:21:58', NULL),
(333, 1, 'online', '2025-03-08 02:22:58', NULL),
(334, 1, 'online', '2025-03-08 02:23:58', NULL),
(335, 1, 'online', '2025-03-08 02:24:58', NULL),
(336, 1, 'online', '2025-03-08 02:25:58', NULL),
(337, 1, 'online', '2025-03-08 02:26:58', NULL),
(338, 1, 'online', '2025-03-08 02:27:58', NULL),
(339, 1, 'online', '2025-03-08 02:28:58', NULL),
(340, 1, 'online', '2025-03-08 02:29:58', NULL),
(341, 1, 'online', '2025-03-08 02:30:58', NULL),
(342, 1, 'online', '2025-03-08 02:31:58', NULL),
(343, 1, 'online', '2025-03-08 02:32:58', NULL),
(344, 1, 'online', '2025-03-08 02:33:58', NULL),
(345, 1, 'online', '2025-03-08 02:34:58', NULL),
(346, 1, 'online', '2025-03-08 02:35:58', NULL),
(347, 1, 'online', '2025-03-08 02:36:58', NULL),
(348, 1, 'online', '2025-03-08 02:37:58', NULL),
(349, 1, 'online', '2025-03-08 02:38:58', NULL),
(350, 1, 'online', '2025-03-08 02:39:58', NULL),
(351, 1, 'online', '2025-03-08 02:40:14', NULL),
(352, 1, 'online', '2025-03-08 02:40:38', NULL),
(353, 1, 'online', '2025-03-08 02:40:39', NULL),
(354, 1, 'online', '2025-03-08 02:40:42', NULL),
(355, 1, 'online', '2025-03-08 02:40:43', NULL),
(356, 1, 'online', '2025-03-08 02:40:44', NULL),
(357, 1, 'online', '2025-03-08 03:54:23', NULL),
(358, 1, 'online', '2025-03-08 03:54:24', NULL),
(359, 1, 'online', '2025-03-08 03:54:54', NULL),
(360, 1, 'online', '2025-03-07 03:55:45', NULL),
(361, 1, 'online', '2025-03-07 03:55:49', NULL),
(362, 1, 'online', '2025-03-07 03:56:19', NULL),
(363, 1, 'online', '2025-03-07 03:56:50', NULL),
(364, 1, 'online', '2025-03-07 03:56:54', NULL),
(365, 1, 'online', '2025-03-07 03:56:58', NULL),
(366, 1, 'online', '2025-03-07 03:57:02', NULL),
(367, 1, 'online', '2025-03-07 03:57:07', NULL),
(368, 1, 'online', '2025-03-07 03:57:11', NULL),
(369, 1, 'online', '2025-03-07 03:57:15', NULL),
(370, 1, 'online', '2025-03-07 03:57:16', NULL),
(371, 1, 'online', '2025-03-07 03:57:18', NULL),
(372, 1, 'online', '2025-03-07 03:57:21', NULL),
(373, 1, 'online', '2025-03-07 03:57:24', NULL),
(374, 1, 'online', '2025-03-07 03:57:26', NULL),
(375, 1, 'online', '2025-03-07 03:57:28', NULL),
(376, 1, 'online', '2025-03-07 03:57:31', NULL),
(377, 1, 'online', '2025-03-07 03:57:46', NULL),
(378, 2, 'online', '2025-03-22 14:26:57', NULL),
(379, 2, 'online', '2025-03-22 14:26:58', NULL),
(380, 2, 'online', '2025-03-22 14:27:03', NULL),
(381, 2, 'online', '2025-03-22 14:27:17', NULL),
(382, 2, 'online', '2025-03-22 14:27:28', NULL),
(383, 1, 'online', '2025-05-13 09:52:02', NULL),
(384, 1, 'online', '2025-05-13 09:52:05', NULL),
(385, 1, 'online', '2025-05-13 09:52:35', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `file_uploads`
--
ALTER TABLE `file_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message_id` (`message_id`),
  ADD KEY `idx_file_type` (`file_type`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender_receiver` (`sender_id`,`receiver_id`),
  ADD KEY `idx_receiver_sender` (`receiver_id`,`sender_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `message_edits`
--
ALTER TABLE `message_edits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message_id` (`message_id`),
  ADD KEY `idx_edited_at` (`edited_at`);

--
-- Chỉ mục cho bảng `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reaction` (`message_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_message_id` (`message_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_last_active` (`last_active`);

--
-- Chỉ mục cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_session_id` (`session_id`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- Chỉ mục cho bảng `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_started_at` (`started_at`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `file_uploads`
--
ALTER TABLE `file_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `message_edits`
--
ALTER TABLE `message_edits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `message_reactions`
--
ALTER TABLE `message_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `user_status`
--
ALTER TABLE `user_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=386;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `file_uploads`
--
ALTER TABLE `file_uploads`
  ADD CONSTRAINT `file_uploads_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `message_edits`
--
ALTER TABLE `message_edits`
  ADD CONSTRAINT `message_edits_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `message_reactions`
--
ALTER TABLE `message_reactions`
  ADD CONSTRAINT `message_reactions_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_status`
--
ALTER TABLE `user_status`
  ADD CONSTRAINT `user_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Cơ sở dữ liệu: `cua_hang_do_choi`
--
CREATE DATABASE IF NOT EXISTS `cua_hang_do_choi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cua_hang_do_choi`;

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
(10, 10, 5, 3, 499000.00, 1497000.00),
(11, 11, 5, 2, 499000.00, 998000.00),
(12, 12, 5, 6, 499000.00, 2994000.00),
(13, 13, 5, 6, 499000.00, 2994000.00),
(14, 14, 8, 1, 350000.00, 350000.00),
(15, 15, 5, 1, 499000.00, 499000.00),
(16, 16, 7, 1, 99000.00, 99000.00),
(17, 17, 8, 1, 350000.00, 350000.00);

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
(10, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 1497000.00, 3, '2025-05-13 09:53:57'),
(11, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 998000.00, 3, '2025-05-14 09:59:54'),
(12, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 2994000.00, 0, '2025-05-14 10:00:39'),
(13, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 2994000.00, 4, '2025-05-14 10:01:12'),
(14, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 350000.00, 0, '2025-05-14 11:02:27'),
(15, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 499000.00, 1, '2025-05-14 11:18:10'),
(16, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 99000.00, 3, '2025-05-14 11:20:59'),
(17, 3, 'Ngô Thành Đạt', 'bobherren09@gmail.com', '0566191650', '199, Hà Nội\r\n', '', 350000.00, 2, '2025-05-14 18:14:19');

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
(5, 6, 'san-pham-6-phu-1746888790-1.jpg', 2),
(6, 4, 'san-pham-4-phu-1747239531-0.jpg', 1),
(7, 4, 'san-pham-4-phu-1747239531-1.jpg', 2),
(8, 4, 'san-pham-4-phu-1747239531-2.jpg', 3);

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
(1, 'Xe ô tô điều khiển từ xa', 4, 'Xe ô tô điều khiển từ xa tốc độ cao', 'Xe ô tô điều khiển từ xa tốc độ cao, pin sạc, chạy được 30 phút liên tục', 350000.00, 299000.00, 'san-pham-1.jpg', 5, 1, 1, '2025-05-09 15:59:33', 0),
(2, 'Bộ xếp hình 1000 chi tiết', 2, 'Bộ xếp hình 1000 chi tiết nhiều màu sắc', 'Bộ xếp hình 1000 chi tiết nhiều màu sắc, phát triển tư duy không gian và sáng tạo', 250000.00, NULL, 'san-pham-2.jpg', 30, 1, 1, '2025-05-09 15:59:33', 0),
(3, 'Búp bê thời trang', 1, 'Búp bê thời trang có thể thay đổi trang phục', 'Búp bê thời trang có thể thay đổi trang phục, phát triển óc sáng tạo và thẩm mỹ', 180000.00, 150000.00, 'san-pham-3.jpg', 40, 0, 1, '2025-05-09 15:59:33', 0),
(4, 'Máy bay mô hình Boeing 747', 3, 'Máy bay mô hình Boeing 747 tỉ lệ 1:100', 'Máy bay mô hình Boeing 747 tỉ lệ 1:100, làm từ nhựa cao cấp, sơn tỉ mỉ', 450000.00, NULL, 'san-pham-4-1747239531.jpg', 20, 1, 1, '2025-05-09 15:59:33', 0),
(5, 'Cầu trượt mini', 5, 'Cầu trượt mini cho bé từ 2-5 tuổi', 'Cầu trượt mini cho bé từ 2-5 tuổi, nhựa an toàn, dễ dàng lắp đặt', 550000.00, 499000.00, 'san-pham-5-1747239452.jpg', 14, 1, 1, '2025-05-09 15:59:33', 0),
(6, 'Bộ đồ chơi nấu ăn 84 chi tiết', 1, 'Bộ đồ chơi nấu ăn 30 món', 'Bộ đồ chơi nấu ăn 30 món, nhựa an toàn, phát triển kỹ năng xã hội', 200000.00, NULL, 'san-pham-6-1746888790.jpg', 25, 0, 1, '2025-05-09 15:59:33', 0),
(7, 'Rubik 3x3', 2, 'Rubik 3x3 xoay trơn', 'Rubik 3x3 xoay trơn, phát triển tư duy logic và trí nhớ', 120000.00, 99000.00, 'san-pham-7-1746884358.jpg', 54, 1, 1, '2025-05-09 15:59:33', 0),
(8, 'Xe tăng điều khiển', 4, 'Xe tăng điều khiển từ xa có thể bắn đạn', 'Xe tăng điều khiển từ xa có thể bắn đạn, âm thanh sống động', 400000.00, 350000.00, 'san-pham-8-1746782048.jpg', 32, 1, 1, '2025-05-09 15:59:33', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_bao`
--

CREATE TABLE `thong_bao` (
  `id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) NOT NULL,
  `loai` enum('don_hang','san_pham_moi','khuyen_mai','he_thong') NOT NULL,
  `noi_dung` text NOT NULL,
  `tham_chieu_id` int(11) DEFAULT NULL,
  `da_doc` tinyint(1) NOT NULL DEFAULT 0,
  `ngay_tao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Chỉ mục cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `hinh_anh_san_pham`
--
ALTER TABLE `hinh_anh_san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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

--
-- Các ràng buộc cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;
--
-- Cơ sở dữ liệu: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

--
-- Đang đổ dữ liệu cho bảng `pma__designer_settings`
--

INSERT INTO `pma__designer_settings` (`username`, `settings_data`) VALUES
('root', '{\"relation_lines\":\"true\",\"snap_to_grid\":\"off\",\"angular_direct\":\"direct\"}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Đang đổ dữ liệu cho bảng `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"cua_hang_do_choi\",\"table\":\"thong_bao\"},{\"db\":\"cua_hang_do_choi\",\"table\":\"nguoi_dung\"},{\"db\":\"cua_hang_do_choi\",\"table\":\"chi_tiet_don_hang\"},{\"db\":\"cua_hang_do_choi\",\"table\":\"banner\"},{\"db\":\"cua_hang_do_choi\",\"table\":\"quan_tri_vien\"}]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Đang đổ dữ liệu cho bảng `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-05-15 17:14:34', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"vi\"}');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Chỉ mục cho bảng `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Chỉ mục cho bảng `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Chỉ mục cho bảng `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Chỉ mục cho bảng `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Chỉ mục cho bảng `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Chỉ mục cho bảng `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Chỉ mục cho bảng `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Chỉ mục cho bảng `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Chỉ mục cho bảng `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Chỉ mục cho bảng `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Chỉ mục cho bảng `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Chỉ mục cho bảng `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Chỉ mục cho bảng `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Chỉ mục cho bảng `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Cơ sở dữ liệu: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
