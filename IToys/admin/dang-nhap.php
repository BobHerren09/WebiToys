<?php
session_start();
ob_start(); // Thêm dòng này để bắt đầu output buffering
include_once("../config/database.php");
include_once("../includes/functions.php");

// Neu da dang nhap, chuyen huong den trang quan tri
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$thong_bao = '';

// Xu ly dang nhap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_dang_nhap = $_POST['ten_dang_nhap'];
    $mat_khau = $_POST['mat_khau'];

    // Kiem tra thong tin dang nhap
    $sql = "SELECT * FROM quan_tri_vien WHERE ten_dang_nhap = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ten_dang_nhap);
    $stmt->execute();
    $ket_qua = $stmt->get_result();

    if ($ket_qua->num_rows > 0) {
        $admin = $ket_qua->fetch_assoc();

        // Kiem tra mat khau
        if (password_verify($mat_khau, $admin['mat_khau'])) {
            // Dang nhap thanh cong
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_ten'] = $admin['ho_ten'];

            // Lưu avatar vào session nếu có
            if (!empty($admin['avatar'])) {
                $_SESSION['admin_avatar'] = $admin['avatar'];
            }


            header("Location: index.php");
            exit();
        } else {
            $thong_bao = "Mật khẩu không chính xác!";
        }
    } else {
        $thong_bao = "Tên đăng nhập không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="dang-nhap-body">
    <div class="dang-nhap-container">
        <div class="dang-nhap-form">
            <h2>Đăng Nhập Quản Trị</h2>
            
            <?php if (!empty($thong_bao)): ?>
                <div class="thong-bao"><?php echo $thong_bao; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="ten_dang_nhap">Tên đăng nhập</label>
                    <input type="text" id="ten_dang_nhap" name="ten_dang_nhap" required>
                </div>
                
                <div class="form-group">
                    <label for="mat_khau">Mật khẩu</label>
                    <input type="password" id="mat_khau" name="mat_khau" required>
                </div>
                
                <button type="submit" class="btn-dang-nhap">Đăng Nhập</button>
            </form>
            
            <div class="quay-lai">
                <a href="../index.php">Quay lại trang chủ</a>
            </div>
        </div>
    </div>
</body>
</html>

<style>
    /* Đăng nhập */
    .dang-nhap-body {
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .dang-nhap-container {
        width: 100%;
        max-width: 400px;
        padding: 20px;
    }

    .dang-nhap-form {
        background-color: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }

        .dang-nhap-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

    .thong-bao {
        background-color: #f8d7da;
        color: #842029;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

    .btn-dang-nhap {
        width: 100%;
        padding: 12px;
        background-color: #ff6b6b;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s;
    }

        .btn-dang-nhap:hover {
            background-color: #ff5252;
        }

    .quay-lai {
        text-align: center;
        margin-top: 20px;
    }

        .quay-lai a {
            color: #6c757d;
            text-decoration: underline;
        }
</style>