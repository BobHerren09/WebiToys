<?php
session_start();
ob_start(); // Thêm dòng này để bắt đầu output buffering
include_once("config/database.php");
include_once("includes/functions.php");

// Tạo thư mục uploads nếu chưa tồn tại
$upload_dir = __DIR__ . "/uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iToys: Thế giới đồ chơi</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php include_once("includes/header.php"); ?>
        
        <main class="noi-dung-chinh">
            <?php
            $trang = isset($_GET['trang']) ? $_GET['trang'] : 'trang-chu';

            switch ($trang) {
                case 'trang-chu':
                    include_once("pages/trang-chu.php");
                    break;
                case 'san-pham':
                    include_once("pages/san-pham.php");
                    break;
                case 'chi-tiet-san-pham':
                    include_once("pages/chi-tiet-san-pham.php");
                    break;
                case 'gio-hang':
                    include_once("pages/gio-hang.php");
                    break;
                case 'thanh-toan':
                    include_once("pages/thanh-toan.php");
                    break;
                case 'dang-nhap':
                    include_once("pages/dang-nhap.php");
                    break;
                case 'dang-ky':
                    include_once("pages/dang-ky.php");
                    break;
                case 'tai-khoan':
                    include_once("pages/tai-khoan.php");
                    break;
                case 'lien-he':
                    include_once("pages/lien-he.php");
                    break;
                case 'cam-on':
                    include_once("pages/cam-on.php");
                    break;
                default:
                    include_once("pages/trang-chu.php");
                    break;
            }
            ?>
        </main>
        
        <?php include_once("includes/footer.php"); ?>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>

