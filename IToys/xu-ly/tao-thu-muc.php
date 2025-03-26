<?php
// Tạo thư mục uploads nếu chưa tồn tại
$upload_dir = __DIR__ . "/../uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    echo "Đã tạo thư mục uploads thành công!";
} else {
    echo "Thư mục uploads đã tồn tại!";
}

// Kiểm tra quyền ghi của thư mục
if (is_writable($upload_dir)) {
    echo "<br>Thư mục uploads có quyền ghi.";
} else {
    echo "<br>Thư mục uploads không có quyền ghi. Vui lòng cấp quyền ghi cho thư mục.";
    chmod($upload_dir, 0777);
    echo "<br>Đã thử cấp quyền ghi cho thư mục uploads.";
}
?>

