<?php
session_start();
require_once '../config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['nguoi_dung_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

// Lấy tất cả ID đơn hàng
$sql_don_hang = "SELECT id FROM don_hang WHERE khach_hang_id = ? AND trang_thai > 0";
$stmt_don_hang = $conn->prepare($sql_don_hang);
$stmt_don_hang->bind_param("i", $_SESSION['nguoi_dung_id']);
$stmt_don_hang->execute();
$result_don_hang = $stmt_don_hang->get_result();
$don_hang_ids = [];
while ($row = $result_don_hang->fetch_assoc()) {
    $don_hang_ids[] = $row['id'];
}

// Lấy tất cả ID sản phẩm mới
$sql_san_pham_moi = "SELECT id FROM san_pham WHERE ngay_tao >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$result_san_pham_moi = $conn->query($sql_san_pham_moi);
$san_pham_moi_ids = [];
while ($row = $result_san_pham_moi->fetch_assoc()) {
    $san_pham_moi_ids[] = $row['id'];
}

// Lấy tất cả ID sản phẩm giảm giá
$sql_san_pham_giam_gia = "SELECT id FROM san_pham WHERE gia_khuyen_mai < gia AND gia_khuyen_mai > 0 AND ngay_tao >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$result_san_pham_giam_gia = $conn->query($sql_san_pham_giam_gia);
$san_pham_giam_gia_ids = [];
while ($row = $result_san_pham_giam_gia->fetch_assoc()) {
    $san_pham_giam_gia_ids[] = $row['id'];
}

// Cập nhật session
$_SESSION['thong_bao_da_doc'] = [
    'don_hang' => $don_hang_ids,
    'san_pham_moi' => $san_pham_moi_ids,
    'san_pham_giam_gia' => $san_pham_giam_gia_ids
];

echo json_encode(['success' => true]);
?>
