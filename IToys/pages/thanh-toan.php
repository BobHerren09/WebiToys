<?php


// Kiem tra gio hang co san pham khong
if (!isset($_SESSION['gio_hang']) || count($_SESSION['gio_hang']) == 0) {
    header("Location: index.php?trang=gio-hang");
    exit();
}

// Kiem tra nguoi dung da dang nhap chua
if (!da_dang_nhap()) {
    // Luu URL hien tai de chuyen huong sau khi dang nhap
    $_SESSION['redirect_after_login'] = "index.php?trang=thanh-toan";
    header("Location: index.php?trang=dang-nhap");
    exit();
}

// Hoặc nếu bạn muốn cho phép thanh toán không cần đăng nhập,
// nhưng cần thông tin người dùng, bạn có thể kiểm tra và khởi tạo các biến:
$ho_ten = isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$dien_thoai = isset($_SESSION['dien_thoai']) ? $_SESSION['dien_thoai'] : '';
$dia_chi = isset($_SESSION['dia_chi']) ? $_SESSION['dia_chi'] : '';


// Lay thong tin nguoi dung
$nguoi_dung_id = $_SESSION['nguoi_dung_id'];
$sql = "SELECT * FROM nguoi_dung WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $nguoi_dung_id);
$stmt->execute();
$result = $stmt->get_result();
$nguoi_dung = $result->fetch_assoc();

// Xu ly dat hang
$thong_bao = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $dien_thoai = $_POST['dien_thoai'];
    $dia_chi = $_POST['dia_chi'];
    $ghi_chu = isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : '';

    // Kiem tra thong tin
    if (empty($ho_ten) || empty($email) || empty($dien_thoai) || empty($dia_chi)) {
        $thong_bao = "Vui lòng điền đầy đủ thông tin!";
    } else {
        // Tao don hang
        $don_hang_id = tao_don_hang($conn, $nguoi_dung_id, $ho_ten, $email, $dien_thoai, $dia_chi, $ghi_chu);

        if ($don_hang_id) {
            // Chuyen huong den trang cam on
            header("Location: index.php?trang=cam-on&don-hang=" . $don_hang_id);
            exit();
        } else {
            $thong_bao = "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!";
        }
    }
}

// Lay danh sach san pham trong gio hang
$san_pham_gio_hang = array();
$tong_tien = 0;

foreach ($_SESSION['gio_hang'] as $san_pham_id => $so_luong) {
    $san_pham = lay_san_pham_theo_id($conn, $san_pham_id);

    if ($san_pham) {
        $san_pham['so_luong'] = $so_luong;
        $san_pham['thanh_tien'] = $san_pham['gia'] * $so_luong;
        $san_pham_gio_hang[] = $san_pham;
        $tong_tien += $san_pham['thanh_tien'];
    }
}
?>

<div class="trang-thanh-toan">
    <div class="container">
        <h1>Thanh Toán</h1>
        
        <?php if (!empty($thong_bao)): ?>
            <div class="thong-bao-loi"><?php echo $thong_bao; ?></div>
        <?php endif; ?>
        
        <div class="thanh-toan-container">
            <div class="thong-tin-thanh-toan">
                <h2>Thông Tin Thanh Toán</h2>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="ho_ten">Họ tên</label>
                        <input type="text" id="ho_ten" name="ho_ten" value="<?php echo $nguoi_dung['ho_ten']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $nguoi_dung['email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dien_thoai">Điện thoại</label>
                        <input type="tel" id="dien_thoai" name="dien_thoai" value="<?php echo $nguoi_dung['dien_thoai']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dia_chi">Địa chỉ giao hàng</label>
                        <textarea id="dia_chi" name="dia_chi" rows="3" required><?php echo $nguoi_dung['dia_chi']; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="ghi_chu">Ghi chú (tùy chọn)</label>
                        <textarea id="ghi_chu" name="ghi_chu" rows="3"></textarea>
                    </div>
                    
                    <div class="phuong-thuc-thanh-toan">
                        <h3>Phương thức thanh toán</h3>
                        
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="cod" name="phuong_thuc" value="cod" checked>
                                <label for="cod">Thanh toán khi nhận hàng (COD)</label>
                            </div>
                            
                            <div class="radio-item">
                                <input type="radio" id="bank_transfer" name="phuong_thuc" value="bank_transfer">
                                <label for="bank_transfer">Chuyển khoản ngân hàng</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-dat-hang">Đặt hàng</button>
                </form>
            </div>
            
            <div class="don-hang-cua-ban">
                <h2>Đơn Hàng Của Bạn</h2>
                
                <div class="san-pham-list">
                    <?php foreach ($san_pham_gio_hang as $san_pham): ?>
                        <div class="san-pham-item">
                            <div class="san-pham-info">
                                <img src="uploads/<?php echo $san_pham['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
                                <div>
                                    <h4><?php echo $san_pham['ten_san_pham']; ?></h4>
                                    <p>Số lượng: <?php echo $san_pham['so_luong']; ?></p>
                                </div>
                            </div>
                            <div class="san-pham-gia">
                                <?php echo dinh_dang_tien($san_pham['thanh_tien']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="tong-cong">
                    <div class="dong-tong">
                        <span>Tổng cộng:</span>
                        <span class="gia-tri"><?php echo dinh_dang_tien($tong_tien); ?></span>
                    </div>
                    <div class="dong-tong">
                        <span>Phí vận chuyển:</span>
                        <span class="gia-tri">Miễn phí</span>
                    </div>
                    <div class="dong-tong tong-thanh-toan">
                        <span>Tổng thanh toán:</span>
                        <span class="gia-tri"><?php echo dinh_dang_tien($tong_tien); ?></span>
                    </div>
                </div>
                
                <div class="quay-lai-gio-hang">
                    <a href="index.php?trang=gio-hang">
                        <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.trang-thanh-toan {
    padding: 30px 0;
}

.trang-thanh-toan h1 {
    margin-bottom: 30px;
    text-align: center;
}

.thong-bao-loi {
    background-color: #f8d7da;
    color: #842029;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
    text-align: center;
}

.thanh-toan-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.thong-tin-thanh-toan, .don-hang-cua-ban {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.thong-tin-thanh-toan h2, .don-hang-cua-ban h2 {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
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

.phuong-thuc-thanh-toan {
    margin-bottom: 20px;
}

.phuong-thuc-thanh-toan h3 {
    margin-bottom: 15px;
    font-size: 16px;
}

.radio-group {
    border: 1px solid #ddd;
    border-radius: 4px;
}

.radio-item {
    padding: 12px 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #ddd;
}

.radio-item:last-child {
    border-bottom: none;
}

.btn-dat-hang {
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

.btn-dat-hang:hover {
    background-color: #ff5252;
}

.san-pham-list {
    margin-bottom: 20px;
}

.san-pham-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.san-pham-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.san-pham-info img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

.san-pham-info h4 {
    font-size: 14px;
    margin-bottom: 5px;
}

.san-pham-info p {
    font-size: 12px;
    color: #6c757d;
}

.san-pham-gia {
    font-weight: 500;
}

.tong-cong {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.dong-tong {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.tong-thanh-toan {
    font-size: 18px;
    font-weight: bold;
    color: #ff6b6b;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
}

.quay-lai-gio-hang {
    margin-top: 20px;
    text-align: center;
}

.quay-lai-gio-hang a {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #6c757d;
}

@media (max-width: 768px) {
    .thanh-toan-container {
        grid-template-columns: 1fr;
    }
    
    .don-hang-cua-ban {
        order: -1;
    }
}
</style>


