<?php
// Kiem tra don hang
$don_hang_id = isset($_GET['don-hang']) ? (int) $_GET['don-hang'] : 0;

if ($don_hang_id <= 0) {
    header("Location: index.php");
    exit();
}

// Lay thong tin don hang
$sql = "SELECT * FROM don_hang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $don_hang_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$don_hang = $result->fetch_assoc();
?>

<div class="trang-cam-on">
    <div class="container">
        <div class="khung-cam-on">
            <div class="icon-thanh-cong">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Cảm ơn bạn đã đặt hàng!</h1>
            
            <p class="ma-don-hang">Mã đơn hàng: <strong>#<?php echo $don_hang_id; ?></strong></p>
            
            <p class="thong-bao">
                Đơn hàng của bạn đã được tiếp nhận và đang được xử lý. 
                Chúng tôi sẽ gửi thông báo cho bạn khi đơn hàng được giao đi.
            </p>
            
            <div class="thong-tin-don-hang">
                <h2>Thông tin đơn hàng</h2>
                
                <div class="thong-tin-item">
                    <span class="nhan">Người nhận:</span>
                    <span class="gia-tri"><?php echo $don_hang['ho_ten']; ?></span>
                </div>
                
                <div class="thong-tin-item">
                    <span class="nhan">Điện thoại:</span>
                    <span class="gia-tri"><?php echo $don_hang['dien_thoai']; ?></span>
                </div>
                
                <div class="thong-tin-item">
                    <span class="nhan">Địa chỉ:</span>
                    <span class="gia-tri"><?php echo $don_hang['dia_chi']; ?></span>
                </div>
                
                <div class="thong-tin-item">
                    <span class="nhan">Tổng tiền:</span>
                    <span class="gia-tri"><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></span>
                </div>
                
                <div class="thong-tin-item">
                    <span class="nhan">Phương thức thanh toán:</span>
                    <span class="gia-tri">Thanh toán khi nhận hàng (COD)</span>
                </div>
            </div>
            
            <div class="hanh-dong">
                <a href="index.php?trang=tai-khoan&hanh-dong=don-hang" class="btn-xem-don-hang">
                    <i class="fas fa-eye"></i> Xem đơn hàng
                </a>
                
                <a href="index.php" class="btn-tiep-tuc-mua">
                    <i class="fas fa-shopping-cart"></i> Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.trang-cam-on {
    padding: 50px 0;
}

.khung-cam-on {
    max-width: 600px;
    margin: 0 auto;
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    text-align: center;
}

.icon-thanh-cong {
    font-size: 60px;
    color: #28a745;
    margin-bottom: 20px;
}

.khung-cam-on h1 {
    margin-bottom: 15px;
    color: #333;
}

.ma-don-hang {
    font-size: 18px;
    margin-bottom: 20px;
}

.thong-bao {
    margin-bottom: 30px;
    color: #6c757d;
    line-height: 1.6;
}

.thong-tin-don-hang {
    text-align: left;
    margin-bottom: 30px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.thong-tin-don-hang h2 {
    font-size: 18px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.thong-tin-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.thong-tin-item .nhan {
    font-weight: 500;
}

.hanh-dong {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.btn-xem-don-hang, .btn-tiep-tuc-mua {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 10px 20px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.btn-xem-don-hang {
    background-color: #f8f9fa;
    color: #495057;
}

.btn-xem-don-hang:hover {
    background-color: #e9ecef;
}

.btn-tiep-tuc-mua {
    background-color: #ff6b6b;
    color: white;
}

.btn-tiep-tuc-mua:hover {
    background-color: #ff5252;
}

@media (max-width: 576px) {
    .hanh-dong {
        flex-direction: column;
    }
    
    .btn-xem-don-hang, .btn-tiep-tuc-mua {
        width: 100%;
        justify-content: center;
    }
}
</style>


