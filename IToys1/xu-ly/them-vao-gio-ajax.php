<?php
session_start();
include_once("../config/database.php");
include_once("../includes/functions.php");

// Đặt header để trả về JSON
header('Content-Type: application/json');

// Kiểm tra request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['san_pham_id'])) {
    $san_pham_id = (int) $_POST['san_pham_id'];

    // Kiểm tra sản phẩm tồn tại và đang hiển thị
    $sql = "SELECT * FROM san_pham WHERE id = ? AND trang_thai = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $san_pham_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Sản phẩm không tồn tại hoặc đã bị ẩn
        echo json_encode(array('success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn'));
        exit();
    }

    // Thêm sản phẩm vào giỏ hàng
    if ($san_pham_id > 0) {
        them_vao_gio_hang($san_pham_id, 1);

        // Trả về kết quả thành công và số lượng sản phẩm trong giỏ hàng
        echo json_encode([
            'success' => true,
            'cart_count' => tong_so_san_pham_gio_hang()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
}
?>

