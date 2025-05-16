<?php
$thong_bao = '';
$loai_thong_bao = '';
$email = '';

// Xu ly gui yeu cau dat lai mat khau
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Kiem tra email co ton tai trong he thong
    if (email_ton_tai($conn, $email)) {
        // Tao ma token ngau nhien
        $token = bin2hex(random_bytes(32));
        $thoi_gian_het_han = date('Y-m-d H:i:s', time() + 3600); // 1 giờ

        // Luu token vao co so du lieu
        if (luu_token_dat_lai_mat_khau($conn, $email, $token, $thoi_gian_het_han)) {
            // Gui email voi link dat lai mat khau
            $link_dat_lai = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?trang=dat-lai-mat-khau&token=" . $token;

            // Trong thuc te, ban se gui email tai day
            // gui_email($email, "Đặt lại mật khẩu", "Nhấp vào liên kết sau để đặt lại mật khẩu: " . $link_dat_lai);

            $loai_thong_bao = 'success';
            $thong_bao = "Hướng dẫn đặt lại mật khẩu đã được gửi đến email của bạn.";
        } else {
            $loai_thong_bao = 'error';
            $thong_bao = "Đã xảy ra lỗi. Vui lòng thử lại sau.";
        }
    } else {
        $loai_thong_bao = 'error';
        $thong_bao = "Email không tồn tại trong hệ thống.";
    }
}

// Hàm kiểm tra email tồn tại (giả định)
function email_ton_tai($conn, $email)
{
    // Trong thực tế, bạn sẽ kiểm tra email trong cơ sở dữ liệu
    // Ví dụ: SELECT COUNT(*) FROM nguoi_dung WHERE email = ?
    return true; // Giả sử email luôn tồn tại để demo
}

// Hàm lưu token đặt lại mật khẩu (giả định)
function luu_token_dat_lai_mat_khau($conn, $email, $token, $thoi_gian_het_han)
{
    // Trong thực tế, bạn sẽ lưu token vào cơ sở dữ liệu
    // Ví dụ: INSERT INTO dat_lai_mat_khau (email, token, thoi_gian_het_han) VALUES (?, ?, ?)
    return true; // Giả sử luôn thành công để demo
}
?>

<div class="trang-quen-mat-khau">
    <div class="container">
        <div class="khung-quen-mat-khau">
            <h1>Quên Mật Khẩu</h1>
            
            <?php if (!empty($thong_bao)): ?>
                <div class="thong-bao <?php echo $loai_thong_bao == 'success' ? 'thong-bao-success' : 'thong-bao-loi'; ?>">
                    <?php echo $thong_bao; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="Nhập email đã đăng ký" required>
                </div>
                
                <div class="form-group huong-dan">
                    <p>Nhập email đã đăng ký của bạn. Chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu qua email.</p>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-gui-yeu-cau">Gửi Yêu Cầu</button>
                </div>
            </form>
            
            <div class="lien-ket-khac">
                <p><a href="index.php?trang=dang-nhap">Quay lại đăng nhập</a></p>
            </div>
        </div>
    </div>
</div>

<style>
.trang-quen-mat-khau {
    padding: 40px 0;
}

.khung-quen-mat-khau {
    max-width: 500px;
    margin: 0 auto;
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.khung-quen-mat-khau h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

.thong-bao {
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
    text-align: center;
}

.thong-bao-loi {
    background-color: #f8d7da;
    color: #842029;
}

.thong-bao-success {
    background-color: #d4edda;
    color: #155724;
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

.huong-dan {
    font-size: 14px;
    color: #666;
}

.btn-gui-yeu-cau {
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

.btn-gui-yeu-cau:hover {
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
