<?php
// Script để cài đặt cơ sở dữ liệu và tạo thư mục uploads

// Kết nối đến MySQL
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

// Tạo kết nối
$conn = new mysqli($db_host, $db_user, $db_pass);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

echo "<h1>Cài đặt Cửa Hàng Đồ Chơi</h1>";

// Tạo cơ sở dữ liệu
$sql = "CREATE DATABASE IF NOT EXISTS cua_hang_do_choi DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "<p>Tạo cơ sở dữ liệu thành công!</p>";
} else {
    echo "<p>Lỗi khi tạo cơ sở dữ liệu: " . $conn->error . "</p>";
}

// Chọn cơ sở dữ liệu
$conn->select_db("cua_hang_do_choi");

// Đọc file SQL
$sql_file = file_get_contents('database.sql');

// Thực thi từng câu lệnh SQL
$queries = explode(';', $sql_file);
$success = true;

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) !== TRUE) {
            echo "<p>Lỗi khi thực thi: " . $conn->error . "</p>";
            $success = false;
        }
    }
}

// Tạo bảng banner nếu chưa tồn tại
$sql = "CREATE TABLE IF NOT EXISTS banner (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tieu_de VARCHAR(255) NOT NULL,
  mo_ta TEXT,
  hinh_anh VARCHAR(255) NOT NULL,
  lien_ket VARCHAR(255),
  thu_tu INT DEFAULT 0,
  hien_thi TINYINT(1) DEFAULT 1,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Tạo bảng banner thành công!</p>";
} else {
    echo "<p>Lỗi khi tạo bảng banner: " . $conn->error . "</p>";
    $success = false;
}

// Thêm dữ liệu mẫu cho bảng banner
$sql = "INSERT INTO banner (tieu_de, mo_ta, hinh_anh, lien_ket, thu_tu, hien_thi) VALUES
('Khuyến mãi mùa hè', 'Giảm giá đến 50% cho tất cả đồ chơi', 'banner1.jpg', 'index.php?trang=san-pham', 1, 1),
('Đồ chơi giáo dục', 'Phát triển trí tuệ cho bé yêu', 'banner2.jpg', 'index.php?trang=san-pham&danh-muc=2', 2, 1),
('Đồ chơi mới nhất', 'Những sản phẩm mới nhất của chúng tôi', 'banner3.jpg', 'index.php?trang=san-pham', 3, 1)";

if ($conn->query($sql) === TRUE) {
    echo "<p>Thêm dữ liệu mẫu cho bảng banner thành công!</p>";
} else {
    echo "<p>Lỗi khi thêm dữ liệu mẫu cho bảng banner: " . $conn->error . "</p>";
}

// Tạo thư mục uploads
$upload_dir = __DIR__ . "/uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    echo "<p>Đã tạo thư mục uploads thành công!</p>";
} else {
    echo "<p>Thư mục uploads đã tồn tại!</p>";
}

// Kiểm tra quyền ghi của thư mục
if (is_writable($upload_dir)) {
    echo "<p>Thư mục uploads có quyền ghi.</p>";
} else {
    echo "<p>Thư mục uploads không có quyền ghi. Vui lòng cấp quyền ghi cho thư mục.</p>";
    chmod($upload_dir, 0777);
    echo "<p>Đã thử cấp quyền ghi cho thư mục uploads.</p>";
}

// Kết thúc
if ($success) {
    echo "<h2>Cài đặt hoàn tất!</h2>";
    echo "<p>Bạn có thể <a href='index.php'>truy cập trang chủ</a> hoặc <a href='admin/dang-nhap.php'>đăng nhập vào trang quản trị</a>.</p>";
    echo "<p>Tài khoản quản trị mặc định:</p>";
    echo "<ul>";
    echo "<li>Tên đăng nhập: admin</li>";
    echo "<li>Mật khẩu: 123456</li>";
    echo "</ul>";
} else {
    echo "<h2>Cài đặt chưa hoàn tất!</h2>";
    echo "<p>Vui lòng kiểm tra các lỗi và thử lại.</p>";
}

$conn->close();
?>


