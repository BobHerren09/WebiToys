<?php
// Lay ID san pham
$san_pham_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Kiem tra san pham ton tai
if ($san_pham_id <= 0) {
    header("Location: index.php");
    exit();
}

// Lay thong tin san pham
$san_pham = lay_san_pham_theo_id($conn, $san_pham_id);

// Kiểm tra sản phẩm tồn tại và đang hiển thị
if (!$san_pham || $san_pham['trang_thai'] != 1) {
    header("Location: index.php");
    exit();
}

// Lay danh muc cua san pham
$danh_muc = null;
if ($san_pham['danh_muc_id']) {
    $sql = "SELECT * FROM danh_muc WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $san_pham['danh_muc_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $danh_muc = $result->fetch_assoc();
}

// Lay hinh anh san pham
$hinh_anh_san_pham = array();
$sql = "SELECT * FROM hinh_anh_san_pham WHERE san_pham_id = ? ORDER BY thu_tu ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $san_pham_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $hinh_anh_san_pham[] = $row;
}

// Lay san pham lien quan
$san_pham_lien_quan = array();
if ($san_pham['danh_muc_id']) {
    $sql = "SELECT * FROM san_pham WHERE danh_muc_id = ? AND id != ? ORDER BY id DESC LIMIT 4";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $san_pham['danh_muc_id'], $san_pham_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $san_pham_lien_quan[] = $row;
    }
}

// Lấy số lượng đã bán
$da_ban = lay_so_luong_da_ban($conn, $san_pham_id);

// Tính phần trăm giảm giá nếu có
$phan_tram_giam = 0;
if (!empty($san_pham['gia_khuyen_mai']) && $san_pham['gia_khuyen_mai'] > 0 && $san_pham['gia_khuyen_mai'] < $san_pham['gia']) {
    $phan_tram_giam = round(($san_pham['gia'] - $san_pham['gia_khuyen_mai']) / $san_pham['gia'] * 100);
}

// Xu ly them vao gio hang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['them_vao_gio'])) {
    $so_luong = isset($_POST['so_luong']) ? (int) $_POST['so_luong'] : 1;

    if ($so_luong > 0) {
        them_vao_gio_hang($san_pham_id, $so_luong);

        // Đặt thông báo vào session
        $_SESSION['thong_bao_gio_hang'] = "Sản phẩm đã được thêm vào giỏ hàng!";

       
    }
}

// Xử lý nút mua ngay
if (isset($_GET['mua-ngay']) && $_GET['mua-ngay'] == '1') {
    $so_luong = isset($_GET['so_luong']) ? (int) $_GET['so_luong'] : 1;

    if ($so_luong > 0) {
        them_vao_gio_hang($san_pham_id, $so_luong);

        // Chuyển hướng đến trang thanh toán
        header("Location: index.php?trang=thanh-toan");
        exit();
    }
}
?>

<div class="trang-chi-tiet-san-pham">
    <div class="container">
        <div class="duong-dan">
            <a href="index.php">Trang chủ</a> &gt;
            <?php if ($danh_muc): ?>
                <a href="index.php?trang=san-pham&danh-muc=<?php echo $danh_muc['id']; ?>"><?php echo $danh_muc['ten_danh_muc']; ?></a> &gt;
            <?php else: ?>
                <a href="index.php?trang=san-pham">Sản phẩm</a> &gt;
            <?php endif; ?>
            <span><?php echo $san_pham['ten_san_pham']; ?></span>
        </div>
        
        <div class="chi-tiet-san-pham">
            <div class="hinh-anh-san-pham">
    <div class="hinh-anh-chinh">
        <img src="uploads/<?php echo $san_pham['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>" id="hinh-anh-chinh" style="transition: opacity 0.3s ease;">
        <?php if ($phan_tram_giam > 0): ?>
            <div class="giam-gia-badge">-<?php echo $phan_tram_giam; ?>%</div>
        <?php endif; ?>
    </div>
    
    <?php if (count($hinh_anh_san_pham) > 0): ?>
        <div class="hinh-anh-phu">
            <div class="hinh-anh-item active" data-src="uploads/<?php echo $san_pham['hinh_anh']; ?>">
                <img src="uploads/<?php echo $san_pham['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
            </div>
            
            <?php foreach ($hinh_anh_san_pham as $hinh_anh): ?>
                <div class="hinh-anh-item" data-src="uploads/<?php echo $hinh_anh['hinh_anh']; ?>">
                    <img src="uploads/<?php echo $hinh_anh['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
            
            <div class="thong-tin-san-pham">
                <h1><?php echo $san_pham['ten_san_pham']; ?></h1>
                
                <div class="gia-san-pham">
                    <?php if ($san_pham['gia_khuyen_mai'] && $san_pham['gia_khuyen_mai'] < $san_pham['gia']): ?>
                        <span class="gia-goc"><?php echo dinh_dang_tien($san_pham['gia']); ?></span>
                        <span class="gia-khuyen-mai"><?php echo dinh_dang_tien($san_pham['gia_khuyen_mai']); ?></span>
                    <?php else: ?>
                        <span class="gia"><?php echo dinh_dang_tien($san_pham['gia']); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($da_ban > 0): ?>
                    <div class="da-ban">Đã bán: <span><?php echo $da_ban; ?></span></div>
                <?php endif; ?>
                
                <div class="mo-ta-ngan">
                    <?php echo $san_pham['mo_ta_ngan']; ?>
                </div>
                
                <form method="POST" action="" class="form-mua-hang">
                    <div class="so-luong-mua">
                        <label for="so_luong">Số lượng:</label>
                        <div class="so-luong-control">
                            <button type="button" class="giam-so-luong">-</button>
                            <input type="number" id="so_luong" name="so_luong" value="1" min="1" max="99">
                            <button type="button" class="tang-so-luong">+</button>
                        </div>
                    </div>
                    
                    <div class="nut-mua-hang">
                        <button type="submit" name="them_vao_gio" class="btn-them-vao-gio">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                        </button>
                        <button type="button" class="btn-mua-ngay">Mua ngay</button>
                    </div>
                </form>
                
                <div class="thong-tin-khac">
                    <div class="thong-tin-item">
                        <i class="fas fa-truck"></i>
                        <span>Giao hàng miễn phí toàn quốc</span>
                    </div>
                    <div class="thong-tin-item">
                        <i class="fas fa-undo"></i>
                        <span>Đổi trả trong vòng 7 ngày</span>
                    </div>
                    <div class="thong-tin-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Bảo hành chính hãng 12 tháng</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mo-ta-san-pham">
            <h2>Mô tả sản phẩm</h2>
            <div class="noi-dung-mo-ta">
                <?php echo $san_pham['mo_ta']; ?>
            </div>
        </div>
        
        <?php if (count($san_pham_lien_quan) > 0): ?>
            <div class="san-pham-lien-quan">
                <h2>Sản phẩm liên quan</h2>
                
                <div class="danh-sach-san-pham">
                    <?php foreach ($san_pham_lien_quan as $sp): ?>
                        <div class="san-pham">
                            <div class="hinh-anh">
                                <a href="index.php?trang=chi-tiet-san-pham&id=<?php echo $sp['id']; ?>">
                                    <img src="uploads/<?php echo $sp['hinh_anh']; ?>" alt="<?php echo $sp['ten_san_pham']; ?>">
                                </a>
                                <?php
                                // Tính phần trăm giảm giá cho sản phẩm liên quan
                                $sp_phan_tram_giam = 0;
                                if (!empty($sp['gia_khuyen_mai']) && $sp['gia_khuyen_mai'] > 0 && $sp['gia_khuyen_mai'] < $sp['gia']) {
                                    $sp_phan_tram_giam = round(($sp['gia'] - $sp['gia_khuyen_mai']) / $sp['gia'] * 100);
                                }
                                if ($sp_phan_tram_giam > 0):
                                    ?>
                                    <div class="giam-gia-badge">-<?php echo $sp_phan_tram_giam; ?>%</div>
                                <?php endif; ?>
                            </div>
                            <div class="thong-tin">
                                <h3>
                                    <a href="index.php?trang=chi-tiet-san-pham&id=<?php echo $sp['id']; ?>">
                                        <?php echo $sp['ten_san_pham']; ?>
                                    </a>
                                </h3>
                                <div class="gia">
                                    <?php
                                    if ($sp['gia_khuyen_mai'] && $sp['gia_khuyen_mai'] < $sp['gia']):
                                        ?>
                                        <span class="gia-goc"><?php echo dinh_dang_tien($sp['gia']); ?></span>
                                        <span class="gia-khuyen-mai"><?php echo dinh_dang_tien($sp['gia_khuyen_mai']); ?></span>
                                    <?php else: ?>
                                        <?php echo dinh_dang_tien($sp['gia']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="hanh-dong">
                                    <a href="xu-ly/gio-hang.php?hanh-dong=them&id=<?php echo $sp['id']; ?>" class="them-vao-gio">
                                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                    </a>
                                </div>
                                <?php
                                // Lấy số lượng đã bán cho sản phẩm liên quan
                                $sp_da_ban = lay_so_luong_da_ban($conn, $sp['id']);
                                if ($sp_da_ban > 0):
                                    ?>
                                    <div class="da-ban">Đã bán: <span><?php echo $sp_da_ban; ?></span></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.trang-chi-tiet-san-pham {
    padding: 30px 0;
}

.duong-dan {
    margin-bottom: 20px;
    font-size: 14px;
}

.duong-dan a {
    color: #6c757d;
}

.duong-dan a:hover {
    color: #ff6b6b;
}

.chi-tiet-san-pham {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
}

.hinh-anh-san-pham {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.hinh-anh-chinh {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    position: relative; /* Để định vị badge giảm giá */
}

.hinh-anh-chinh img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.hinh-anh-phu {
    display: flex;
    gap: 10px;
    overflow-x: auto;
}

.hinh-anh-item {
    width: 80px;
    height: 80px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
}

.hinh-anh-item.active {
    border-color: #ff6b6b;
}

.hinh-anh-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thong-tin-san-pham h1 {
    font-size: 24px;
    margin-bottom: 15px;
}

.gia-san-pham {
    margin-bottom: 15px;
}

.gia-goc {
    text-decoration: line-through;
    color: #6c757d;
    margin-right: 10px;
}

.gia-khuyen-mai, .gia {
    font-size: 24px;
    font-weight: bold;
    color: #ff6b6b;
}

.da-ban {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 15px;
}

.da-ban span {
    font-weight: bold;
}

.mo-ta-ngan {
    margin-bottom: 20px;
    line-height: 1.6;
}

.form-mua-hang {
    margin-bottom: 20px;
}

.so-luong-mua {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.so-luong-mua label {
    font-weight: 500;
}

.so-luong-control {
    display: flex;
    align-items: center;
}

.so-luong-control input {
    width: 50px;
    text-align: center;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 0;
}

.giam-so-luong, .tang-so-luong {
    width: 36px;
    height: 36px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    cursor: pointer;
}

.giam-so-luong {
    border-radius: 4px 0 0 4px;
}

.tang-so-luong {
    border-radius: 0 4px 4px 0;
}

.nut-mua-hang {
    display: flex;
    gap: 10px;
}

.btn-them-vao-gio, .btn-mua-ngay {
    padding: 12px 20px;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-them-vao-gio {
    background-color: #ff6b6b;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    gap: 5px;
}

.btn-them-vao-gio:hover {
    background-color: #ff5252;
}

.btn-mua-ngay {
    background-color: #343a40;
    color: white;
    border: none;
}

.btn-mua-ngay:hover {
    background-color: #212529;
}

.thong-tin-khac {
    margin-top: 30px;
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
}

.thong-tin-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.thong-tin-item i {
    color: #ff6b6b;
}

.mo-ta-san-pham {
    margin-bottom: 40px;
}

.mo-ta-san-pham h2 {
    font-size: 20px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.noi-dung-mo-ta {
    line-height: 1.8;
}

.san-pham-lien-quan h2 {
    font-size: 20px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.giam-gia-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #ff6b6b;
    color: white;
    padding: 5px 8px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: bold;
}

@media (max-width: 768px) {
    .chi-tiet-san-pham {
        grid-template-columns: 1fr;
    }
    
    .nut-mua-hang {
        flex-direction: column;
    }
    
    .btn-them-vao-gio, .btn-mua-ngay {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Xu ly nut tang giam so luong
  const giamButton = document.querySelector('.giam-so-luong');
  const tangButton = document.querySelector('.tang-so-luong');
  const soLuongInput = document.getElementById('so_luong');
  
  giamButton.addEventListener('click', function() {
      let value = parseInt(soLuongInput.value);
      if (value > 1) {
          soLuongInput.value = value - 1;
      }
  });
  
  tangButton.addEventListener('click', function() {
      let value = parseInt(soLuongInput.value);
      if (value < 99) {
          soLuongInput.value = value + 1;
      }
  });
  
  // Xu ly chuyen doi hinh anh
  const hinhAnhItems = document.querySelectorAll('.hinh-anh-item');
  const hinhAnhChinh = document.getElementById('hinh-anh-chinh');
  
  // Preload images for smoother transitions
  const preloadImages = () => {
    hinhAnhItems.forEach(item => {
      const src = item.getAttribute('data-src');
      const img = new Image();
      img.src = src;
    });
  };
  
  // Preload images when page loads
  preloadImages();
  
  hinhAnhItems.forEach(item => {
      item.addEventListener('click', function() {
          // Xoa class active
          hinhAnhItems.forEach(i => i.classList.remove('active'));
          
          // Them class active cho item duoc chon
          this.classList.add('active');
          
          // Cap nhat hinh anh chinh
          const src = this.getAttribute('data-src');
          
          // Add fade effect
          hinhAnhChinh.style.opacity = '0.5';
          
          // Change image and restore opacity when loaded
          setTimeout(() => {
            hinhAnhChinh.src = src;
            hinhAnhChinh.onload = function() {
              hinhAnhChinh.style.opacity = '1';
            };
          }, 200);
      });
  });
  
  // Xu ly nut mua ngay
  const muaNgayButton = document.querySelector('.btn-mua-ngay');

  muaNgayButton.addEventListener('click', function() {
      // Lấy số lượng sản phẩm
      const soLuong = document.getElementById('so_luong').value;
      
      // Chuyển hướng đến trang chi tiết sản phẩm với tham số mua ngay
      window.location.href = `index.php?trang=chi-tiet-san-pham&id=<?php echo $san_pham_id; ?>&mua-ngay=1&so_luong=${soLuong}`;
  });
});
</script>