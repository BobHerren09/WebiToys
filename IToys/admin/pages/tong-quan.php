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
  
  <!-- Biểu đồ doanh thu -->
  <div class="bieu-do-doanh-thu">
      <h2>Biểu Đồ Doanh Thu Theo Tháng</h2>
      <div class="bieu-do-container">
          <canvas id="bieu-do-doanh-thu"></canvas>
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

@media (max-width: 768px) {
    .banner-list {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Biểu đồ doanh thu
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
});
</script>

