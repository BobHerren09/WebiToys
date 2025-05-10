<?php
// Kiem tra neu da dang nhap thi chuyen huong ve trang chu
if (da_dang_nhap()) {
    header("Location: index.php");
    exit();
}

$thong_bao = '';
$email = '';

// Xu ly dang nhap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];

    // Kiem tra dang nhap
    if (dang_nhap($conn, $email, $mat_khau)) {
        // Neu co trang can chuyen huong sau khi dang nhap
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }
    } else {
        $thong_bao = "Email hoặc mật khẩu không chính xác!";
    }
}
?>

<div class="trang-dang-nhap">
    <div class="container">
        <div class="khung-dang-nhap">
            <h1>Đăng Nhập</h1>
            
            <?php if (!empty($thong_bao)): ?>
                <div class="thong-bao-loi"><?php echo $thong_bao; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mat_khau">Mật khẩu</label>
                    <input type="password" id="mat_khau" name="mat_khau" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-dang-nhap">Đăng Nhập</button>
                </div>
            </form>
            
            <div class="lien-ket-khac">
                <p>Chưa có tài khoản? <a href="index.php?trang=dang-ky">Đăng ký ngay</a></p>
                <p><a href="#">Quên mật khẩu?</a></p>
            </div>
        </div>
    </div>
</div>

<style>
.trang-dang-nhap {
    padding: 40px 0;
}

.khung-dang-nhap {
    max-width: 500px;
    margin: 0 auto;
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.khung-dang-nhap h1 {
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

.lien-ket-khac {
    margin-top: 20px;
    text-align: center;
}

.lien-ket-khac p {
    margin-bottom: 10px;
}

.lien-ket-khac a {
    color: #ff6b6b;
    text-decoration: underline;
}
</style>

