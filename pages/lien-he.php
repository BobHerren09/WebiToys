<div class="trang-lien-he">
    <div class="container">
        <h1>Liên Hệ Với Chúng Tôi</h1>
        
        <div class="lien-he-container">
            <div class="thong-tin-lien-he">
                <h2>Thông Tin Liên Hệ</h2>
                
                <div class="thong-tin-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Địa chỉ</h3>
                        <p>QL21, TT. Xuân Mai, Chương Mỹ, Hà Nội</p>
                    </div>
                </div>
                
                <div class="thong-tin-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Điện thoại</h3>
                        <p>0566191650</p>
                    </div>
                </div>
                
                <div class="thong-tin-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>lienhe@iToys.com</p>
                    </div>
                </div>
                
                <div class="thong-tin-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Giờ làm việc</h3>
                        <p>Thứ 2 - Thứ 6: 8:00 - 17:30</p>
                        <p>Thứ 7 - Chủ nhật: 8:00 - 12:00</p>
                    </div>
                </div>
                
                <div class="mang-xa-hoi">
                    <h3>Kết nối với chúng tôi</h3>
                    <div class="mang-xa-hoi-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="form-lien-he">
                <h2>Gửi Tin Nhắn Cho Chúng Tôi</h2>
                
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ho_ten">Họ tên</label>
                            <input type="text" id="ho_ten" name="ho_ten" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dien_thoai">Điện thoại</label>
                        <input type="tel" id="dien_thoai" name="dien_thoai">
                    </div>
                    
                    <div class="form-group">
                        <label for="chu_de">Chủ đề</label>
                        <select id="chu_de" name="chu_de">
                            <option value="hoi-dap">Hỏi đáp sản phẩm</option>
                            <option value="bao-gia">Yêu cầu báo giá</option>
                            <option value="khieu-nai">Khiếu nại</option>
                            <option value="hop-tac">Hợp tác kinh doanh</option>
                            <option value="khac">Khác</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="noi_dung">Nội dung</label>
                        <textarea id="noi_dung" name="noi_dung" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-gui">Gửi tin nhắn</button>
                </form>
            </div>
        </div>
        
        <div class="ban-do">
            <h2>Bản Đồ</h2>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1863.0365834266296!2d105.57598582801228!3d20.911066699999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3134456eadc4a13b%3A0xdb2599dbaa038ea4!2zxJDhu5FpIERp4buHbiDEkOG6oWkgSOG7jWMgTMOibSBOZ2hp4buHcCAtIFF14buRYyBM4buZIDIx!5e0!3m2!1svi!2s!4v1711022456789!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<style>
.trang-lien-he {
    padding: 30px 0;
}

.trang-lien-he h1 {
    text-align: center;
    margin-bottom: 30px;
}

.lien-he-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

.thong-tin-lien-he, .form-lien-he {
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.thong-tin-lien-he h2, .form-lien-he h2, .ban-do h2 {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.thong-tin-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.thong-tin-item i {
    font-size: 24px;
    color: #ff6b6b;
    margin-top: 5px;
}

.thong-tin-item h3 {
    font-size: 16px;
    margin-bottom: 5px;
}

.thong-tin-item p {
    color: #6c757d;
}

.mang-xa-hoi h3 {
    font-size: 16px;
    margin-bottom: 10px;
}

.mang-xa-hoi-icons {
    display: flex;
    gap: 10px;
}

.mang-xa-hoi-icons a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: #f8f9fa;
    border-radius: 50%;
    color: #6c757d;
    transition: all 0.3s;
}

.mang-xa-hoi-icons a:hover {
    background-color: #ff6b6b;
    color: white;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
}

.btn-gui {
    padding: 12px 24px;
    background-color: #ff6b6b;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-gui:hover {
    background-color: #ff5252;
}

.ban-do {
    background-color: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.ban-do iframe {
    border-radius: 8px;
}

@media (max-width: 768px) {
    .lien-he-container {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

