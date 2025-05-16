<?php
// Định nghĩa hàm time_elapsed_string
function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'năm',
        'm' => 'tháng',
        'w' => 'tuần',
        'd' => 'ngày',
        'h' => 'giờ',
        'i' => 'phút',
        's' => 'giây',
    );

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full)
        $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' trước' : 'vừa xong';
}

// Lấy thông báo sản phẩm sắp hết hàng
$sql_san_pham_sap_het = "SELECT COUNT(*) as total FROM san_pham WHERE so_luong > 0 AND so_luong < 10";
$result_san_pham_sap_het = $conn->query($sql_san_pham_sap_het);
$row_san_pham_sap_het = $result_san_pham_sap_het->fetch_assoc();
$so_san_pham_sap_het = $row_san_pham_sap_het['total'];

// Lấy thông báo đơn hàng mới (trạng thái = 0)
$sql_don_hang_moi = "SELECT COUNT(*) as total FROM don_hang WHERE trang_thai = 0";
$result_don_hang_moi = $conn->query($sql_don_hang_moi);
$row_don_hang_moi = $result_don_hang_moi->fetch_assoc();
$so_don_hang_moi = $row_don_hang_moi['total'];

// Tổng số thông báo
$tong_so_thong_bao = $so_san_pham_sap_het + $so_don_hang_moi;

// Lấy chi tiết sản phẩm sắp hết hàng
$sql_chi_tiet_sp = "SELECT id, ten_san_pham, so_luong FROM san_pham WHERE so_luong > 0 AND so_luong < 10 ORDER BY so_luong ASC LIMIT 5";
$result_chi_tiet_sp = $conn->query($sql_chi_tiet_sp);
$san_pham_sap_het = array();
if ($result_chi_tiet_sp->num_rows > 0) {
    while ($row = $result_chi_tiet_sp->fetch_assoc()) {
        $san_pham_sap_het[] = $row;
    }
}

// Lấy chi tiết đơn hàng mới
$sql_chi_tiet_dh = "SELECT id, ho_ten, tong_tien, ngay_tao FROM don_hang WHERE trang_thai = 0 ORDER BY ngay_tao DESC LIMIT 5";
$result_chi_tiet_dh = $conn->query($sql_chi_tiet_dh);
$don_hang_moi = array();
if ($result_chi_tiet_dh->num_rows > 0) {
    while ($row = $result_chi_tiet_dh->fetch_assoc()) {
        $don_hang_moi[] = $row;
    }
}
?>

<div class="admin-header-content">
  <div class="admin-search">
      <form action="index.php" method="GET">
          <input type="hidden" name="trang" value="san-pham">
          <input type="text" name="tu_khoa" placeholder="Tìm kiếm...">
          <button type="submit"><i class="fas fa-search"></i></button>
      </form>
  </div>
  
  <div class="admin-user-section">
    <!-- Thêm phần thông báo -->
    <div class="admin-notifications">
        <div class="notification-icon" id="notification-toggle">
            <i class="fas fa-bell"></i>
            <?php if ($tong_so_thong_bao > 0): ?>
                <span class="notification-badge"><?php echo $tong_so_thong_bao; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="notification-dropdown" id="notification-dropdown">
            <div class="notification-header">
                <h3>Thông báo</h3>
                <?php if ($tong_so_thong_bao > 0): ?>
                    <span class="notification-count"><?php echo $tong_so_thong_bao; ?> mới</span>
                <?php endif; ?>
            </div>
            
            <div class="notification-content">
                <?php if ($tong_so_thong_bao == 0): ?>
                    <div class="no-notifications">
                        <i class="fas fa-check-circle"></i>
                        <p>Không có thông báo mới</p>
                    </div>
                <?php else: ?>
                    <!-- Thông báo sản phẩm sắp hết hàng -->
                    <?php if ($so_san_pham_sap_het > 0): ?>
                        <div class="notification-group">
                            <div class="notification-group-header">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Sản phẩm sắp hết hàng</span>
                            </div>
                            
                            <?php foreach ($san_pham_sap_het as $sp): ?>
                                <a href="index.php?trang=sua-san-pham&id=<?php echo $sp['id']; ?>" class="notification-item">
                                    <div class="notification-icon warning">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="notification-details">
                                        <div class="notification-title"><?php echo $sp['ten_san_pham']; ?></div>
                                        <div class="notification-description">
                                            Còn lại: <span class="<?php echo $sp['so_luong'] <= 5 ? 'text-danger' : 'text-warning'; ?>"><?php echo $sp['so_luong']; ?> sản phẩm</span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            
                            <?php if (count($san_pham_sap_het) < $so_san_pham_sap_het): ?>
                                <a href="index.php?trang=san-pham&sap-het=1" class="notification-view-all">
                                    Xem tất cả <?php echo $so_san_pham_sap_het; ?> sản phẩm sắp hết
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Thông báo đơn hàng mới -->
                    <?php if ($so_don_hang_moi > 0): ?>
                        <div class="notification-group">
                            <div class="notification-group-header">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Đơn hàng mới</span>
                            </div>
                            
                            <?php foreach ($don_hang_moi as $dh): ?>
                                <a href="index.php?trang=chi-tiet-don-hang&id=<?php echo $dh['id']; ?>" class="notification-item">
                                    <div class="notification-icon new">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div class="notification-details">
                                        <div class="notification-title">Đơn hàng #<?php echo $dh['id']; ?></div>
                                        <div class="notification-description">
                                            <?php echo $dh['ho_ten']; ?> - <?php echo dinh_dang_tien($dh['tong_tien']); ?>
                                        </div>
                                        <div class="notification-time">
                                            <?php echo time_elapsed_string($dh['ngay_tao']); ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            
                            <?php if (count($don_hang_moi) < $so_don_hang_moi): ?>
                                <a href="index.php?trang=don-hang&trang-thai=0" class="notification-view-all">
                                    Xem tất cả <?php echo $so_don_hang_moi; ?> đơn hàng mới
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="admin-user">
        <?php
        // Lấy thông tin avatar từ database
        $admin_id = $_SESSION['admin_id'];
        $sql = "SELECT avatar FROM quan_tri_vien WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $avatar = '';
        if ($row = $result->fetch_assoc()) {
            $avatar = $row['avatar'];
        }
        ?>
        <div class="admin-user-info">
            <span><?php echo $_SESSION['admin_ten']; ?></span>
            <?php if (!empty($avatar) && file_exists("../uploads/" . $avatar)): ?>
                <img src="../uploads/<?php echo $avatar; ?>" alt="Avatar" class="admin-avatar">
            <?php else: ?>
                <img src="../assets/images/admin-avatar.png" alt="Avatar" class="admin-avatar">
            <?php endif; ?>
        </div>
        <div class="admin-user-menu">
            <ul>
                <li><a href="index.php?trang=cai-dat"><i class="fas fa-cog"></i> Cài đặt tài khoản</a></li>
                <li><a href="xu-ly/dang-xuat.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </div>
    </div>
  </div>
</div>

<style>
.admin-header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  background-color: #fff;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.admin-search {
  flex: 1;
  max-width: 400px;
}

.admin-search form {
  display: flex;
  align-items: center;
}

.admin-search input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px 0 0 4px;
  outline: none;
}

.admin-search button {
  padding: 8px 12px;
  background-color: #4e73df;
  color: white;
  border: none;
  border-radius: 0 4px 4px 0;
  cursor: pointer;
}

/* CSS cho phần thông báo và user */
.admin-user-section {
  display: flex;
  align-items: center;
  gap: 15px;
}

.admin-notifications {
  position: relative;
}

.notification-icon {
  position: relative;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f9fa;
  border-radius: 50%;
  cursor: pointer;
  transition: background-color 0.3s;
}

.notification-icon:hover {
  background-color: #e9ecef;
}

.notification-icon i {
  font-size: 18px;
  color: #4e73df;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: #e74a3b;
  color: white;
  font-size: 10px;
  font-weight: bold;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-dropdown {
  position: absolute;
  top: 100%;
  right: -150px;
  width: 350px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  z-index: 1000;
  display: none;
  overflow: hidden;
  margin-top: 10px;
}

@media (max-width: 768px) {
  .notification-dropdown {
    right: -100px;
  }
}

.notification-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #e9ecef;
}

.notification-header h3 {
  margin: 0;
  font-size: 16px;
  color: #333;
}

.notification-count {
  background-color: #4e73df;
  color: white;
  padding: 3px 8px;
  border-radius: 10px;
  font-size: 12px;
}

.notification-content {
  max-height: 400px;
  overflow-y: auto;
}

.no-notifications {
  padding: 30px 15px;
  text-align: center;
  color: #6c757d;
}

.no-notifications i {
  font-size: 40px;
  margin-bottom: 10px;
  color: #28a745;
}

.notification-group {
  border-bottom: 1px solid #e9ecef;
}

.notification-group-header {
  padding: 10px 15px;
  background-color: #f8f9fa;
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
}

.notification-group-header i {
  margin-right: 8px;
  color: #4e73df;
}

.notification-item {
  display: flex;
  padding: 12px 15px;
  border-bottom: 1px solid #f1f1f1;
  text-decoration: none;
  color: #333;
  transition: background-color 0.3s;
}

.notification-item:hover {
  background-color: #f8f9fa;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-icon.warning {
  background-color: #fff3cd;
  color: #856404;
}

.notification-icon.new {
  background-color: #d1e7dd;
  color: #0f5132;
}

.notification-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
  flex-shrink: 0;
}

.notification-details {
  flex: 1;
}

.notification-title {
  font-weight: 600;
  margin-bottom: 3px;
  font-size: 14px;
}

.notification-description {
  font-size: 13px;
  color: #6c757d;
  margin-bottom: 3px;
}

.notification-time {
  font-size: 12px;
  color: #adb5bd;
}

.text-warning {
  color: #f6c23e;
  font-weight: 600;
}

.text-danger {
  color: #e74a3b;
  font-weight: 600;
}

.notification-view-all {
  display: block;
  padding: 10px 15px;
  text-align: center;
  background-color: #f8f9fa;
  color: #4e73df;
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  transition: background-color 0.3s;
}

.notification-view-all:hover {
  background-color: #e9ecef;
}

.admin-user {
  position: relative;
}

.admin-user-info {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 5px;
  border-radius: 4px;
}

.admin-user-info:hover {
  background-color: #f8f9fa;
}

.admin-user-info span {
  font-weight: 500;
}

.admin-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e9ecef;
}

.admin-user-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  border-radius: 4px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  width: 200px;
  z-index: 100;
  display: none;
}

.admin-user:hover .admin-user-menu {
  display: block;
}

.admin-user-menu ul {
  padding: 10px 0;
  margin: 0;
  list-style: none;
}

.admin-user-menu ul li a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 15px;
  color: #333;
  text-decoration: none;
  transition: background-color 0.3s;
}

.admin-user-menu ul li a:hover {
  background-color: #f8f9fa;
}

/* Mobile menu styles */
.mobile-menu-toggle {
  display: none;
  background: none;
  border: none;
  color: #333;
  font-size: 24px;
  cursor: pointer;
}

@media (max-width: 768px) {
  .mobile-menu-toggle {
    display: block;
  }
  
  .sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .sidebar.active {
    transform: translateX(0);
    display: block;
  }
  
  .admin-avatar {
    width: 35px;
    height: 35px;
  }
  
  .admin-user-menu.active {
    display: block;
  }
  
  .notification-dropdown {
    width: 300px;
    right: -120px;
  }
}

@media (max-width: 576px) {
  .notification-dropdown {
    width: 280px;
    right: -100px;
  }
  
  .admin-search {
    max-width: 150px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const adminUserInfo = document.querySelector('.admin-user-info');
  const adminUserMenu = document.querySelector('.admin-user-menu');
  
  // Hiển thị/ẩn menu khi click trên thiết bị di động
  if (adminUserInfo && adminUserMenu) {
    adminUserInfo.addEventListener('click', function(e) {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        adminUserMenu.classList.toggle('active');
      }
    });
    
    // Đóng menu khi click bên ngoài
    document.addEventListener('click', function(e) {
      if (!adminUserInfo.contains(e.target) && !adminUserMenu.contains(e.target)) {
        adminUserMenu.classList.remove('active');
      }
    });
  }
  
  // Xử lý thông báo
  const notificationToggle = document.getElementById('notification-toggle');
  const notificationDropdown = document.getElementById('notification-dropdown');
  
  if (notificationToggle && notificationDropdown) {
    notificationToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
    });
    
    // Đóng dropdown khi click bên ngoài
    document.addEventListener('click', function(e) {
      if (!notificationToggle.contains(e.target) && !notificationDropdown.contains(e.target)) {
        notificationDropdown.style.display = 'none';
      }
    });
    
    // Ngăn sự kiện click trong dropdown lan ra ngoài
    notificationDropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }
});
</script>
