<footer class="chan-trang">
    <div class="thong-tin-cua-hang">
        <h3>Cửa Hàng Đồ Chơi iToys</h3>
        <p><i class="fas fa-map-marker-alt"></i> QL21, TT. Xuân Mai, Chương Mỹ, Hà Nội</p>
        <p><i class="fas fa-phone"></i> Điện thoại: 0566191650</p>
        <p><i class="fas fa-envelope"></i> Email: lienhe@iToys.com</p>
    </div>
    
    <div class="danh-muc-chan-trang">
        <h3>Danh Mục</h3>
        <ul>
            <?php
            $danh_muc = lay_danh_sach_danh_muc($conn);
            foreach ($danh_muc as $dm) {
                echo '<li><a href="index.php?trang=san-pham&danh-muc=' . $dm['id'] . '">' . $dm['ten_danh_muc'] . '</a></li>';
            }
            ?>
        </ul>
    </div>
    
    <div class="ho-tro-khach-hang">
        <h3>Hỗ Trợ Khách Hàng</h3>
        <ul>
            <li><a href="http://localhost/Chinhsachnguoidung/huong-dan-mua-hang.php">Hướng dẫn mua hàng</a></li>
            <li><a href="http://localhost/Chinhsachnguoidung/chinh-sach-doi-tra.php">Chính sách đổi trả</a></li>
            <li><a href="http://localhost/Chinhsachnguoidung/chinh-sach-bao-hanh.php">Chính sách bảo hành</a></li>
            <li><a href="http://localhost/Chinhsachnguoidung/phuong-thuc-thanh-toan.php">Phương thức thanh toán</a></li>
            <li><a href="http://localhost/Chinhsachnguoidung/phuong-thuc-van-chuyen.php">Phương thức vận chuyển</a></li>
        </ul>
    </div>
    
    <div class="ket-noi">
        <h3>Kết Nối Với Chúng Tôi</h3>
        <div class="mang-xa-hoi">
            <a href="https://www.facebook.com/photo/?fbid=311878018469894&set=a.284250297899333"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/p/C1pQRODutfq/"><i class="fab fa-instagram"></i></a>
            <a href="https://www.youtube.com/watch?v=QwLvrnlfdNo"><i class="fab fa-youtube"></i></a>
            <a href="https://www.tiktok.com/@igorektv_/video/7306295151108869382?is_from_webapp=1&sender_device=pc"><i class="fab fa-tiktok"></i></a>
        </div>
    </div>
</footer>

<div class="ban-quyen">
    <p>&copy; <?php echo date('Y'); ?> Sinh viên Ngô Thành Đạt. Khóa luận tốt nghiệp VNUF.</p>
</div>

<style>
/* Sửa lại CSS cho footer */
.chan-trang {
    background-color: #343a40;
    color: white;
    padding: 25px 20px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    width: 100%;
    max-width: 100%;
}

.ban-quyen {
    background-color: #212529;
    color: #adb5bd;
    text-align: center;
    padding: 15px 0;
    font-size: 14px;
    width: 100%;
    max-width: 100%;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .chan-trang {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .chan-trang {
        grid-template-columns: 1fr;
    }
}
</style>
