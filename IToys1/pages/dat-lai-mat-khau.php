<?php
$thong_bao = '';
$loai_thong_bao = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$token_hop_le = false;

// Kiểm tra token có hợp lệ không
if (!empty($token)) {
    $token_hop_le = kiem_tra_token($conn, $token);
}

// Xử lý đặt lại mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_hop_le) {
    $mat_khau = $_POST['mat_khau'];
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'];

    // Kiểm tra mật khẩu và xác nhận mật khẩu có khớp không
    if ($mat_khau !== $xac_nhan_mat_khau) {
        $loai_thong_bao = 'error';
        $thong_bao = "Mật khẩu và xác nhận mật khẩu không khớp.";
    }
    // Kiểm tra độ dài mật khẩu
    elseif (strlen($mat_khau) < 6) {
        $loai_thong_bao = 'error';
        $thong_bao = "Mật khẩu phải có ít nhất 6 ký tự.";
    } else {
        // Cập nhật mật khẩu mới
        if (cap_nhat_mat_khau($conn, $token, $mat_khau)) {
            $loai_thong_bao = 'success';
            $thong_bao = "Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập với mật khẩu mới.";
            // Vô hiệu hóa token sau khi sử dụng
            vo_hieu_hoa_token($conn, $token);
        } else {
            $loai_thong_bao = 'error';
            $thong_bao = "Đã xảy ra lỗi khi cập nhật mật khẩu. Vui lòng thử lại.";
        }
    }
}

// Hàm kiểm tra token (giả định)
function kiem_tra_token($conn, $token)
{
    // Trong thực tế, bạn sẽ kiểm tra token trong cơ sở dữ liệu
    // Ví dụ: SELECT * FROM dat_lai_mat_khau WHERE token = ? AND thoi_gian_het_han > NOW() AND da_su_dung = 0
    return true; // Giả sử token luôn hợp lệ để demo
}

// Hàm cập nhật mật khẩu (giả định)
function cap_nhat_mat_khau($conn, $token, $mat_khau)
{
    // Trong thực tế, bạn sẽ cập nhật mật khẩu trong cơ sở dữ liệu
    // Ví dụ: 
    // 1. Lấy email từ token: SELECT email FROM dat_lai_mat_khau WHERE token = ?
    // 2. Cập nhật mật khẩu: UPDATE nguoi_dung SET mat_khau = ? WHERE email = ?
    return true; // Giả sử luôn thành công để demo
}

// Hàm vô hiệu hóa token sau khi sử dụng (giả định)
function vo_hieu_hoa_token($conn, $token)
{
    // Trong thực tế, bạn sẽ đánh dấu token đã được sử dụng
    // Ví dụ: UPDATE dat_lai_mat_khau SET da_su_dung = 1 WHERE token = ?
    return true;
}
?>

<div class="trang-dat-lai-mat-khau">
    <div class="container">
        <div class="khung-dat-lai-mat-khau">
            <h1>Đặt Lại Mật Khẩu</h1>
            
            <?php if (!empty($thong_bao)): ?>
                <div class="thong-bao <?php echo $loai_thong_bao == 'success' ? 'thong-bao-success' : 'thong-bao-loi'; ?>">
                    <?php echo $thong_bao; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$token_hop_le): ?>
                <div class="thong-bao thong-bao-loi">
                    Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.
                </div>
                <div class="lien-ket-khac">
                    <p><a href="index.php?trang=quen-mat-khau">Yêu cầu liên kết mới</a></p>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="mat_khau">Mật khẩu mới</label>
                        <input type="password" id="mat_khau" name="mat_khau" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="xac_nhan_mat_khau">Xác nhận mật khẩu</label>
                        <input type="password" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-dat-lai-mat-khau">Đặt Lại Mật Khẩu</button>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="lien-ket-khac">
                <p><a href="index.php?trang=dang-nhap">Quay lại đăng nhập</a></p>
            </div>
        </div>
    </div>
</div>

<style>
.trang-dat-lai-mat-khau {
    padding: 40px 0;
}

.khung-dat-lai-mat-khau {
    max-width: 500px;
    margin: 0 auto;
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.khung-dat-lai-mat-khau h1 {
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

.btn-dat-lai-mat-khau {
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

.btn-dat-lai-mat-khau:hover {
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
