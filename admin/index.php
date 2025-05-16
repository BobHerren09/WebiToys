<?php
session_start();
ob_start(); // Thêm dòng này để bắt đầu output buffering
include_once("../config/database.php");
include_once("../includes/functions.php");

// Kiem tra dang nhap admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

// Tạo thư mục uploads nếu chưa tồn tại
$upload_dir = "../uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản Trị Website: iToys</title>
  <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="admin-container">
      <?php include_once("includes/sidebar.php"); ?>
      
      <main class="admin-noi-dung">
          <div class="admin-header">
              <?php include_once("includes/header.php"); ?>
          </div>
          
          <div class="admin-main">
              <?php
              $trang = isset($_GET['trang']) ? $_GET['trang'] : 'tong-quan';

              switch ($trang) {
                  case 'tong-quan':
                      include_once("pages/tong-quan.php");
                      break;
                  case 'san-pham':
                      include_once("pages/san-pham.php");
                      break;
                  case 'them-san-pham':
                      include_once("pages/them-san-pham.php");
                      break;
                  case 'sua-san-pham':
                      include_once("pages/sua-san-pham.php");
                      break;
                  case 'danh-muc':
                      include_once("pages/danh-muc.php");
                      break;
                  case 'don-hang':
                      include_once("pages/don-hang.php");
                      break;
                  case 'chi-tiet-don-hang':
                      include_once("pages/chi-tiet-don-hang.php");
                      break;
                  case 'nguoi-dung':
                      include_once("pages/nguoi-dung.php");
                      break;
                  case 'cai-dat':
                      include_once("pages/cai-dat.php");
                      break;
                  case 'banner':
                      include_once("pages/banner.php");
                      break;
                  default:
                      include_once("pages/tong-quan.php");
                      break;
              }
              ?>
          </div>
      </main>
  </div>

  <script src="../assets/js/admin.js"></script>
</body>
</html>

