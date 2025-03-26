<?php
// Kiem tra neu da dang nhap thi chuyen huong ve trang chu
if (da_dang_nhap()) {
    header("Location: index.php");
    exit();
}

$thong_bao = '';
$ho_ten = '';
$email = '';
$dien_thoai = '';
$dia_chi = '';

// Xu ly dang ky
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'];
    $dien_thoai = $_POST['dien_thoai'];
    $dia_chi = $_POST['dia_chi'];

    // Kiem tra mat khau khop nhau
    if ($mat_khau !== $xac_nhan_mat_khau) {
        $thong_bao = "Mật khẩu xác nhận không khớp!";
    } else {
        // Kiem tra email da ton tai chua
        $sql = "SELECT * FROM nguoi_dung WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $ket_qua = $stmt->get_result();

        if ($ket_qua->num_rows > 0) {
            $thong_bao = "Email đã được sử dụng!";
        } else {
            // Dang ky tai khoan moi
            $nguoi_dung_id = dang_ky_tai_khoan($conn, $ho_ten, $email, $mat_khau, $dien_thoai, $dia_chi);

            if ($nguoi_dung_id) {
                // Dang nhap sau khi dang ky
                dang_nhap($conn, $email, $mat_khau);

                // Chuyen huong ve trang chu
                header("Location: index.php");
                exit();
            } else {
                $thong_bao = "Đăng ký thất bại. Vui lòng thử lại!";
            }
        }
    }
}
?>

<div class="trang-dang-ky">
    <div class="container">
        <div class="khung-dang-ky">
            <h1>Đăng Ký Tài Khoản</h1>
            
            <?php if (!empty($thong_bao)): ?>
                <div class="thong-bao-loi"><?php echo $thong_bao; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="ho_ten">Họ tên</label>
                    <input type="text" id="ho_ten" name="ho_ten" value="<?php echo $ho_ten; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mat_khau">Mật khẩu</label>
                    <input type="password" id="mat_khau" name="mat_khau" required>
                </div>
                
                <div class="form-group">
                    <label for="xac_nhan_mat_khau">Xác nhận mật khẩu</label>
                    <input type="password" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau" required>
                </div>
                
                <div class="form-group">
                    <label for="dien_thoai">Điện thoại</label>
                    <input type="tel" id="dien_thoai" name="dien_thoai" value="<?php echo $dien_thoai; ?>">
                </div>
                
                <div class="form-group">
                    <label for="dia_chi">Địa chỉ</label>
                    <textarea id="dia_chi" name="dia_chi" rows="3"><?php echo $dia_chi; ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-dang-ky">Đăng Ký</button>
                </div>
            </form>
            
            <div class="lien-ket-khac">
                <p>Đã có tài khoản? <a href="index.php?trang=dang-nhap">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</div>

<style>
.trang-dang-ky {
    padding: 40px 0;
}

.khung-dang-ky {
    max-width: 600px;
    margin: 0 auto;
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.khung-dang-ky h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

.thong-bao-loi {
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

.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
}

.btn-dang-ky {
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

.btn-dang-ky:hover {
    background-color: #ff5252;
}

.lien-ket-khac {
    margin-top: 20px;
    text-align: center;
}

.lien-ket-khac a {
    color: #ff6b6b;
    text-decoration: underline;
}
</style>


