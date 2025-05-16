<?php
// Kiem tra quyen truy cap
if (!isset($_SESSION['admin_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

// Lấy dữ liệu thống kê
// Tổng số sản phẩm
$sql = "SELECT COUNT(*) as total FROM san_pham";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$tong_san_pham = $row['total'];

// Tổng số đơn hàng
$sql = "SELECT COUNT(*) as total FROM don_hang";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$tong_don_hang = $row['total'];

// Tổng số người dùng
$sql = "SELECT COUNT(*) as total FROM nguoi_dung";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$tong_nguoi_dung = $row['total'];

// Tổng doanh thu
$sql = "SELECT SUM(tong_tien) as total FROM don_hang WHERE trang_thai = 3"; // Đơn hàng đã hoàn thành
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$tong_doanh_thu = $row['total'] ? $row['total'] : 0;

// Tổng doanh thu tháng hiện tại
$sql = "SELECT SUM(tong_tien) as total FROM don_hang 
        WHERE trang_thai = 3 
        AND MONTH(ngay_tao) = MONTH(CURRENT_DATE) 
        AND YEAR(ngay_tao) = YEAR(CURRENT_DATE)";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$tong_doanh_thu_thang = $row['total'] ? $row['total'] : 0;

// Tổng doanh thu ngày hôm nay
$sql = "SELECT SUM(tong_tien) as total FROM don_hang 
        WHERE trang_thai = 3 
        AND DATE(ngay_tao) = CURRENT_DATE";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$tong_doanh_thu_ngay = $row['total'] ? $row['total'] : 0;

// Lấy dữ liệu doanh thu theo tháng cho biểu đồ
$sql = "SELECT MONTH(ngay_tao) as thang, SUM(tong_tien) as doanh_thu 
        FROM don_hang 
        WHERE trang_thai = 3 AND YEAR(ngay_tao) = YEAR(CURRENT_DATE) 
        GROUP BY MONTH(ngay_tao) 
        ORDER BY MONTH(ngay_tao)";
$result = $conn->query($sql);
$doanh_thu_theo_thang = array();
$labels = array();
$data = array();

// Khởi tạo mảng với 12 tháng, giá trị mặc định là 0
for ($i = 1; $i <= 12; $i++) {
    $doanh_thu_theo_thang[$i] = 0;
    $labels[] = 'Tháng ' . $i;
}

// Cập nhật dữ liệu từ database
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doanh_thu_theo_thang[$row['thang']] = (int) $row['doanh_thu'];
    }
}

// Chuyển đổi mảng thành định dạng cho biểu đồ
foreach ($doanh_thu_theo_thang as $doanh_thu) {
    $data[] = $doanh_thu;
}

// Lấy dữ liệu doanh thu theo ngày trong tháng hiện tại cho biểu đồ
$sql = "SELECT DAY(ngay_tao) as ngay, SUM(tong_tien) as doanh_thu 
        FROM don_hang 
        WHERE trang_thai = 3 
        AND MONTH(ngay_tao) = MONTH(CURRENT_DATE) 
        AND YEAR(ngay_tao) = YEAR(CURRENT_DATE) 
        GROUP BY DAY(ngay_tao) 
        ORDER BY DAY(ngay_tao)";
$result = $conn->query($sql);
$doanh_thu_theo_ngay = array();
$labels_ngay = array();
$data_ngay = array();

// Lấy số ngày trong tháng hiện tại
$so_ngay_trong_thang = date('t');

// Khởi tạo mảng với số ngày trong tháng, giá trị mặc định là 0
for ($i = 1; $i <= $so_ngay_trong_thang; $i++) {
    $doanh_thu_theo_ngay[$i] = 0;
    $labels_ngay[] = 'Ngày ' . $i;
}

// Cập nhật dữ liệu từ database
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doanh_thu_theo_ngay[$row['ngay']] = (int) $row['doanh_thu'];
    }
}

// Chuyển đổi mảng thành định dạng cho biểu đồ
foreach ($doanh_thu_theo_ngay as $doanh_thu) {
    $data_ngay[] = $doanh_thu;
}

// Lấy dữ liệu chi tiết doanh thu theo ngày cho báo cáo
$sql = "SELECT DATE(ngay_tao) as ngay, COUNT(*) as so_don_hang, SUM(tong_tien) as doanh_thu 
        FROM don_hang 
        WHERE trang_thai = 3 
        AND MONTH(ngay_tao) = MONTH(CURRENT_DATE) 
        AND YEAR(ngay_tao) = YEAR(CURRENT_DATE) 
        GROUP BY DATE(ngay_tao) 
        ORDER BY DATE(ngay_tao) DESC";
$result = $conn->query($sql);
$bao_cao_doanh_thu = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bao_cao_doanh_thu[] = $row;
    }
}

// Lấy dữ liệu top 10 sản phẩm bán chạy trong tháng
$sql = "SELECT sp.id, sp.ten_san_pham, sp.gia, COUNT(ct.id) as so_luong_ban, SUM(ct.so_luong * ct.gia) as doanh_thu
        FROM chi_tiet_don_hang ct
        JOIN san_pham sp ON ct.san_pham_id = sp.id
        JOIN don_hang dh ON ct.don_hang_id = dh.id
        WHERE dh.trang_thai = 3
        AND MONTH(dh.ngay_tao) = MONTH(CURRENT_DATE)
        AND YEAR(dh.ngay_tao) = YEAR(CURRENT_DATE)
        GROUP BY sp.id
        ORDER BY so_luong_ban DESC
        LIMIT 10";
$result = $conn->query($sql);
$san_pham_ban_chay = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $san_pham_ban_chay[] = $row;
    }
}

// Lấy đơn hàng gần đây
$sql = "SELECT * FROM don_hang ORDER BY id DESC LIMIT 5";
$result = $conn->query($sql);
$don_hang_gan_day = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $don_hang_gan_day[] = $row;
    }
}

// Lấy sản phẩm mới nhất
$sql = "SELECT sp.*, dm.ten_danh_muc 
        FROM san_pham sp 
        LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
        ORDER BY sp.id DESC LIMIT 5";
$result = $conn->query($sql);
$san_pham_moi_nhat = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $san_pham_moi_nhat[] = $row;
    }
}

// Lấy banner
$sql = "SELECT * FROM banner ORDER BY thu_tu ASC";
$result = $conn->query($sql);
$banners = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $banners[] = $row;
    }
}
?>

<div class="tong-quan-page">
  <h1>Tổng Quan</h1>
  
  <div class="thong-ke-tong-hop">
      <div class="thong-ke-item">
          <div class="thong-ke-icon">
              <i class="fas fa-box"></i>
          </div>
          <div class="thong-ke-content">
              <div class="thong-ke-so"><?php echo $tong_san_pham; ?></div>
              <div class="thong-ke-ten">Sản phẩm</div>
          </div>
      </div>
      
      <div class="thong-ke-item">
          <div class="thong-ke-icon">
              <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="thong-ke-content">
              <div class="thong-ke-so"><?php echo $tong_don_hang; ?></div>
              <div class="thong-ke-ten">Đơn hàng</div>
          </div>
      </div>
      
      <div class="thong-ke-item">
          <div class="thong-ke-icon">
              <i class="fas fa-users"></i>
          </div>
          <div class="thong-ke-content">
              <div class="thong-ke-so"><?php echo $tong_nguoi_dung; ?></div>
              <div class="thong-ke-ten">Người dùng</div>
          </div>
      </div>
      
      <div class="thong-ke-item">
          <div class="thong-ke-icon">
              <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="thong-ke-content">
              <div class="thong-ke-so"><?php echo dinh_dang_tien($tong_doanh_thu); ?></div>
              <div class="thong-ke-ten">Doanh thu</div>
          </div>
      </div>
  </div>
  
  <!-- Thêm thống kê doanh thu tháng/ngày -->
  <div class="doanh-thu-chi-tiet">
      <div class="doanh-thu-item">
          <div class="doanh-thu-icon">
              <i class="fas fa-calendar-alt"></i>
          </div>
          <div class="doanh-thu-content">
              <div class="doanh-thu-so"><?php echo dinh_dang_tien($tong_doanh_thu_thang); ?></div>
              <div class="doanh-thu-ten">Doanh thu tháng <?php echo date('m/Y'); ?></div>
          </div>
      </div>
      
      <div class="doanh-thu-item">
          <div class="doanh-thu-icon">
              <i class="fas fa-calendar-day"></i>
          </div>
          <div class="doanh-thu-content">
              <div class="doanh-thu-so"><?php echo dinh_dang_tien($tong_doanh_thu_ngay); ?></div>
              <div class="doanh-thu-ten">Doanh thu ngày <?php echo date('d/m/Y'); ?></div>
          </div>
      </div>
  </div>
  
  <!-- Nút in báo cáo doanh thu -->
<div class="in-bao-cao">
    <button id="btn-in-bao-cao" class="btn-in">
        <i class="fas fa-print"></i> In báo cáo doanh thu chi tiết
    </button>
</div>
  
  <!-- Biểu đồ doanh thu -->
  <div class="bieu-do-doanh-thu">
      <h2>Biểu Đồ Doanh Thu Theo Tháng</h2>
      <div class="bieu-do-container">
          <canvas id="bieu-do-doanh-thu"></canvas>
      </div>
  </div>
  
  <!-- Biểu đồ doanh thu theo ngày -->
  <div class="bieu-do-doanh-thu">
      <h2>Biểu Đồ Doanh Thu Theo Ngày (Tháng <?php echo date('m/Y'); ?>)</h2>
      <div class="bieu-do-container">
          <canvas id="bieu-do-doanh-thu-ngay"></canvas>
      </div>
  </div>
  
  <!-- Quản lý banner -->
  <div class="quan-ly-banner">
      <div class="banner-header">
          <h2>Quản Lý Banner</h2>
          <a href="index.php?trang=banner" class="btn-them-moi">
              <i class="fas fa-plus"></i> Quản lý banner
          </a>
      </div>
      
      <div class="banner-list">
          <?php if (!empty($banners)): ?>
              <?php foreach ($banners as $banner): ?>
                  <div class="banner-item">
                      <img src="../uploads/<?php echo $banner['hinh_anh']; ?>" alt="<?php echo $banner['tieu_de']; ?>">
                      <div class="banner-info">
                          <h3><?php echo $banner['tieu_de']; ?></h3>
                          <p><?php echo $banner['mo_ta']; ?></p>
                          <div class="banner-status">
                              <span class="<?php echo $banner['hien_thi'] ? 'active' : 'inactive'; ?>">
                                  <?php echo $banner['hien_thi'] ? 'Đang hiển thị' : 'Đã ẩn'; ?>
                              </span>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          <?php else: ?>
              <p class="khong-co-du-lieu">Chưa có banner nào. <a href="index.php?trang=banner">Thêm banner mới</a></p>
          <?php endif; ?>
      </div>
  </div>
  
  <div class="don-hang-gan-day">
      <h2>Đơn Hàng Gần Đây</h2>
      
      <div class="bang-du-lieu">
          <table>
              <thead>
                  <tr>
                      <th>Mã đơn</th>
                      <th>Khách hàng</th>
                      <th>Ngày đặt</th>
                      <th>Tổng tiền</th>
                      <th>Trạng thái</th>
                      <th>Thao tác</th>
                  </tr>
              </thead>
              <tbody>
                  <?php if (!empty($don_hang_gan_day)): ?>
                      <?php foreach ($don_hang_gan_day as $don_hang): ?>
                          <?php
                          // Xác định trạng thái đơn hàng
                          $trang_thai_text = '';
                          $trang_thai_class = '';

                          switch ($don_hang['trang_thai']) {
                              case 0:
                                  $trang_thai_text = 'Mới';
                                  $trang_thai_class = 'moi';
                                  break;
                              case 1:
                                  $trang_thai_text = 'Đang xử lý';
                                  $trang_thai_class = 'dang-xu-ly';
                                  break;
                              case 2:
                                  $trang_thai_text = 'Đang giao';
                                  $trang_thai_class = 'dang-giao';
                                  break;
                              case 3:
                                  $trang_thai_text = 'Hoàn thành';
                                  $trang_thai_class = 'hoan-thanh';
                                  break;
                              case 4:
                                  $trang_thai_text = 'Đã hủy';
                                  $trang_thai_class = 'da-huy';
                                  break;
                          }
                          ?>
                          <tr>
                              <td>#<?php echo $don_hang['id']; ?></td>
                              <td><?php echo $don_hang['ho_ten']; ?></td>
                              <td><?php echo date('d/m/Y', strtotime($don_hang['ngay_tao'])); ?></td>
                              <td><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></td>
                              <td><span class="trang-thai <?php echo $trang_thai_class; ?>"><?php echo $trang_thai_text; ?></span></td>
                              <td>
                                  <a href="index.php?trang=chi-tiet-don-hang&id=<?php echo $don_hang['id']; ?>" class="btn-xem">
                                      <i class="fas fa-eye"></i> Xem
                                  </a>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  <?php else: ?>
                      <tr>
                          <td colspan="6" class="khong-co-du-lieu">Không có đơn hàng nào.</td>
                      </tr>
                  <?php endif; ?>
              </tbody>
          </table>
      </div>
      
      <div class="xem-tat-ca">
          <a href="index.php?trang=don-hang" class="btn-xem-tat-ca">Xem tất cả đơn hàng</a>
      </div>
  </div>
  
  <div class="san-pham-moi-nhat">
      <h2>Sản Phẩm Mới Nhất</h2>
      
      <div class="bang-du-lieu">
          <table>
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Hình ảnh</th>
                      <th>Tên sản phẩm</th>
                      <th>Danh mục</th>
                      <th>Giá</th>
                      <th>Thao tác</th>
                  </tr>
              </thead>
              <tbody>
                  <?php if (!empty($san_pham_moi_nhat)): ?>
                      <?php foreach ($san_pham_moi_nhat as $san_pham): ?>
                          <tr>
                              <td><?php echo $san_pham['id']; ?></td>
                              <td>
                                  <img src="../uploads/<?php echo $san_pham['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>" class="hinh-anh-nho">
                              </td>
                              <td><?php echo $san_pham['ten_san_pham']; ?></td>
                              <td><?php echo $san_pham['ten_danh_muc']; ?></td>
                              <td><?php echo dinh_dang_tien($san_pham['gia']); ?></td>
                              <td>
                                  <a href="index.php?trang=sua-san-pham&id=<?php echo $san_pham['id']; ?>" class="btn-sua">
                                      <i class="fas fa-edit"></i> Sửa
                                  </a>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  <?php else: ?>
                      <tr>
                          <td colspan="6" class="khong-co-du-lieu">Không có sản phẩm nào.</td>
                      </tr>
                  <?php endif; ?>
              </tbody>
          </table>
      </div>
      
      <div class="xem-tat-ca">
          <a href="index.php?trang=san-pham" class="btn-xem-tat-ca">Xem tất cả sản phẩm</a>
      </div>
  </div>
  
  <!-- Phần báo cáo doanh thu chi tiết (ẩn, chỉ hiển thị khi in) -->
<div id="bao-cao-doanh-thu" class="bao-cao-doanh-thu">
    <div class="bao-cao-header">
        <h1>BÁO CÁO DOANH THU </h1>
        <p>Thời gian: Tháng <?php echo date('m/Y'); ?></p>
        <p>Ngày xuất báo cáo: <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>
    
    <div class="bao-cao-tong-hop">
        <h2>THỐNG KÊ TỔNG HỢP</h2>
        <div class="bao-cao-thong-ke">
            <div class="bao-cao-item">
                <div class="bao-cao-label">Tổng doanh thu tháng:</div>
                <div class="bao-cao-value"><?php echo dinh_dang_tien($tong_doanh_thu_thang); ?></div>
            </div>
            <div class="bao-cao-item">
                <div class="bao-cao-label">Tổng số đơn hàng:</div>
                <div class="bao-cao-value"><?php echo $tong_don_hang; ?></div>
            </div>
            <div class="bao-cao-item">
                <div class="bao-cao-label">Doanh thu trung bình mỗi ngày:</div>
                <div class="bao-cao-value">
                    <?php
                    $so_ngay = date('t');
                    $doanh_thu_trung_binh = $tong_doanh_thu_thang / $so_ngay;
                    echo dinh_dang_tien($doanh_thu_trung_binh);
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bao-cao-chi-tiet">
        <h2>DOANH THU CHI TIẾT THEO NGÀY</h2>
        <table class="bang-bao-cao">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Số đơn hàng</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bao_cao_doanh_thu)): ?>
                    <?php foreach ($bao_cao_doanh_thu as $item): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($item['ngay'])); ?></td>
                            <td><?php echo $item['so_don_hang']; ?></td>
                            <td><?php echo dinh_dang_tien($item['doanh_thu']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="khong-co-du-lieu">Không có dữ liệu doanh thu.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="bao-cao-san-pham">
        <h2>TOP SẢN PHẨM BÁN CHẠY</h2>
        <table class="bang-bao-cao">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng bán</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($san_pham_ban_chay)): ?>
                    <?php $stt = 1; ?>
                    <?php foreach ($san_pham_ban_chay as $item): ?>
                        <tr>
                            <td><?php echo $stt++; ?></td>
                            <td><?php echo $item['ten_san_pham']; ?></td>
                            <td><?php echo dinh_dang_tien($item['gia']); ?></td>
                            <td><?php echo $item['so_luong_ban']; ?></td>
                            <td><?php echo dinh_dang_tien($item['doanh_thu']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="khong-co-du-lieu">Không có dữ liệu sản phẩm bán chạy.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="bao-cao-bieu-do">
        <h2>BIỂU ĐỒ DOANH THU</h2>
        <div class="bao-cao-bieu-do-container">
            <canvas id="bieu-do-bao-cao"></canvas>
        </div>
    </div>
    
    <div class="bao-cao-footer">
        <p>© <?php echo date('Y'); ?> - Hệ thống quản trị iToys</p>
    </div>
</div>
</div>

<style>
.bieu-do-doanh-thu {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

.bieu-do-doanh-thu h2 {
    margin-bottom: 20px;
    font-size: 18px;
    color: #333;
}

.bieu-do-container {
    height: 300px;
    position: relative;
}

.quan-ly-banner {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

.banner-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.banner-header h2 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.banner-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.banner-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.banner-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.banner-info {
    padding: 15px;
}

.banner-info h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.banner-info p {
    margin: 0 0 10px 0;
    color: #6c757d;
    font-size: 14px;
}

.banner-status {
    font-size: 12px;
}

.banner-status .active {
    color: #28a745;
}

.banner-status .inactive {
    color: #dc3545;
}

/* Thêm CSS cho phần doanh thu chi tiết */
.doanh-thu-chi-tiet {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.doanh-thu-item {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
}

.doanh-thu-icon {
    width: 50px;
    height: 50px;
    background-color: rgba(255, 107, 107, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.doanh-thu-icon i {
    font-size: 20px;
    color: #ff6b6b;
}

.doanh-thu-content {
    flex: 1;
}

.doanh-thu-so {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.doanh-thu-ten {
    font-size: 14px;
    color: #6c757d;
}

@media (max-width: 768px) {
    .banner-list {
        grid-template-columns: 1fr;
    }
    
    .doanh-thu-chi-tiet {
        grid-template-columns: 1fr;
    }
}

/* CSS cho nút in báo cáo */
.in-bao-cao {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.btn-in {
    background-color: #4e73df;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px 15px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background-color 0.3s;
}

.btn-in i {
    margin-right: 8px;
}

.btn-in:hover {
    background-color: #2e59d9;
}

/* CSS cho báo cáo doanh thu chi tiết */
.bao-cao-doanh-thu {
    display: none;
    background-color: white;
    padding: 30px;
    font-family: Arial, sans-serif;
}

.bao-cao-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 20px;
}

.bao-cao-header h1 {
    margin: 0 0 10px 0;
    font-size: 24px;
}

.bao-cao-header p {
    margin: 5px 0;
    font-size: 14px;
    color: #555;
}

.bao-cao-tong-hop {
    margin-bottom: 30px;
}

.bao-cao-tong-hop h2 {
    font-size: 18px;
    margin-bottom: 15px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.bao-cao-thong-ke {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.bao-cao-item {
    background-color: #f8f9fc;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #4e73df;
}

.bao-cao-label {
    font-size: 14px;
    color: #555;
    margin-bottom: 5px;
}

.bao-cao-value {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.bao-cao-chi-tiet, .bao-cao-san-pham {
    margin-bottom: 30px;
}

.bao-cao-chi-tiet h2, .bao-cao-san-pham h2, .bao-cao-bieu-do h2 {
    font-size: 18px;
    margin-bottom: 15px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.bang-bao-cao {
    width: 100%;
    border-collapse: collapse;
}

.bang-bao-cao th, .bang-bao-cao td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

.bang-bao-cao th {
    background-color: #f8f9fc;
    font-weight: bold;
}

.bang-bao-cao tr:nth-child(even) {
    background-color: #f8f9fc;
}

.bao-cao-bieu-do-container {
    height: 300px;
    margin-bottom: 30px;
}

.bao-cao-footer {
    text-align: center;
    margin-top: 50px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    font-size: 12px;
    color: #777;
}

/* CSS cho in ấn */
@media print {
    body * {
        visibility: hidden;
    }
    
    #bao-cao-doanh-thu, #bao-cao-doanh-thu * {
        visibility: visible;
    }
    
    #bao-cao-doanh-thu {
        display: block;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .bao-cao-bieu-do-container {
        height: 400px;
        page-break-inside: avoid;
    }
    
    .bao-cao-chi-tiet, .bao-cao-san-pham {
        page-break-inside: avoid;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Biểu đồ doanh thu theo tháng
    const ctx = document.getElementById('bieu-do-doanh-thu').getContext('2d');
    const bieuDoDoanhThu = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: 'rgba(255, 107, 107, 0.5)',
                borderColor: 'rgba(255, 107, 107, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            }
        }
    });
    
    // Biểu đồ doanh thu theo ngày
    const ctxNgay = document.getElementById('bieu-do-doanh-thu-ngay').getContext('2d');
    const bieuDoDoanhThuNgay = new Chart(ctxNgay, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels_ngay); ?>,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?php echo json_encode($data_ngay); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            }
        }
    });

    // Xử lý in báo cáo doanh thu
    document.getElementById('btn-in-bao-cao').addEventListener('click', function() {
        // Tạo biểu đồ cho báo cáo in
        const ctxBaoCao = document.getElementById('bieu-do-bao-cao').getContext('2d');
        const bieuDoBaoCao = new Chart(ctxBaoCao, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_ngay); ?>,
                datasets: [{
                    label: 'Doanh thu ngày (VNĐ)',
                    data: <?php echo json_encode($data_ngay); ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.5)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Doanh thu theo ngày - Tháng <?php echo date('m/Y'); ?>',
                        font: {
                            size: 16
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
        
        // Đợi biểu đồ render xong
        setTimeout(function() {
            window.print();
        }, 500);
    });
});
</script>
