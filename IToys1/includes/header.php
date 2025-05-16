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

// Khởi tạo mảng thông báo đã đọc trong session nếu chưa có
if (!isset($_SESSION['thong_bao_da_doc'])) {
    $_SESSION['thong_bao_da_doc'] = array(
        'don_hang' => array(),
        'san_pham_moi' => array(),
        'san_pham_giam_gia' => array()
    );
}

// Xử lý đánh dấu đã đọc khi có tham số trong URL
if (isset($_GET['danh-dau-da-doc']) && isset($_GET['loai']) && isset($_GET['id'])) {
    $loai = $_GET['loai'];
    $id = (int) $_GET['id'];

    if (in_array($loai, array('don_hang', 'san_pham_moi', 'san_pham_giam_gia'))) {
        if (!in_array($id, $_SESSION['thong_bao_da_doc'][$loai])) {
            $_SESSION['thong_bao_da_doc'][$loai][] = $id;
        }
    }

    // Chuyển hướng đến trang đích tương ứng
    $redirect_url = '';
    switch ($loai) {
        case 'don_hang':
            $redirect_url = "index.php?trang=tai-khoan&hanh-dong=don-hang&id={$id}";
            break;
        case 'san_pham_moi':
        case 'san_pham_giam_gia':
            $redirect_url = "index.php?trang=chi-tiet-san-pham&id={$id}";
            break;
    }

    if (!empty($redirect_url)) {
        header("Location: {$redirect_url}");
        exit;
    }
}

// Lấy thông báo cho người dùng
if (da_dang_nhap()) {
    $nguoi_dung_id = $_SESSION['nguoi_dung_id'];

    // 1. Thông báo cập nhật trạng thái đơn hàng
    $sql_don_hang = "SELECT dh.id, dh.trang_thai, dh.ngay_tao, dh.tong_tien 
                     FROM don_hang dh 
                     WHERE dh.khach_hang_id = ? AND dh.trang_thai > 0 
                     ORDER BY dh.ngay_tao DESC LIMIT 5";
    $stmt_don_hang = $conn->prepare($sql_don_hang);
    $stmt_don_hang->bind_param("i", $nguoi_dung_id);
    $stmt_don_hang->execute();
    $result_don_hang = $stmt_don_hang->get_result();
    $thong_bao_don_hang = array();
    if ($result_don_hang->num_rows > 0) {
        while ($row = $result_don_hang->fetch_assoc()) {
            $trang_thai_text = '';
            switch ($row['trang_thai']) {
                case 0:
                    $trang_thai_text = 'đang chờ xử lý';
                    break;
                case 1:
                    $trang_thai_text = 'đã xác nhận';
                    break;
                case 2:
                    $trang_thai_text = 'đang giao hàng';
                    break;
                case 3:
                    $trang_thai_text = 'đã hoàn thành';
                    break;
                case 4:
                    $trang_thai_text = 'đã hủy';
                    break;
            }
            $row['trang_thai_text'] = $trang_thai_text;
            $row['da_doc'] = in_array($row['id'], $_SESSION['thong_bao_da_doc']['don_hang']);
            $thong_bao_don_hang[] = $row;
        }
    }

    // 2. Thông báo sản phẩm mới (trong 7 ngày qua)
    $sql_san_pham_moi = "SELECT id, ten_san_pham, gia, ngay_tao 
                         FROM san_pham 
                         WHERE ngay_tao >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                         ORDER BY ngay_tao DESC LIMIT 5";
    $result_san_pham_moi = $conn->query($sql_san_pham_moi);
    $thong_bao_san_pham_moi = array();
    if ($result_san_pham_moi->num_rows > 0) {
        while ($row = $result_san_pham_moi->fetch_assoc()) {
            $row['da_doc'] = in_array($row['id'], $_SESSION['thong_bao_da_doc']['san_pham_moi']);
            $thong_bao_san_pham_moi[] = $row;
        }
    }

    // 3. Thông báo sản phẩm giảm giá
    $sql_san_pham_giam_gia = "SELECT id, ten_san_pham, gia, gia_khuyen_mai, ngay_tao 
                              FROM san_pham 
                              WHERE gia_khuyen_mai < gia AND gia_khuyen_mai > 0 
                              AND ngay_tao >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                              ORDER BY ngay_tao DESC LIMIT 5";
    $result_san_pham_giam_gia = $conn->query($sql_san_pham_giam_gia);
    $thong_bao_san_pham_giam_gia = array();
    if ($result_san_pham_giam_gia->num_rows > 0) {
        while ($row = $result_san_pham_giam_gia->fetch_assoc()) {
            $row['da_doc'] = in_array($row['id'], $_SESSION['thong_bao_da_doc']['san_pham_giam_gia']);
            $thong_bao_san_pham_giam_gia[] = $row;
        }
    }

    // Đếm số thông báo chưa đọc
    $so_thong_bao_chua_doc = 0;
    foreach ($thong_bao_don_hang as $dh) {
        if (!$dh['da_doc'])
            $so_thong_bao_chua_doc++;
    }
    foreach ($thong_bao_san_pham_moi as $sp) {
        if (!$sp['da_doc'])
            $so_thong_bao_chua_doc++;
    }
    foreach ($thong_bao_san_pham_giam_gia as $sp) {
        if (!$sp['da_doc'])
            $so_thong_bao_chua_doc++;
    }

    // Tổng số thông báo
    $tong_so_thong_bao = count($thong_bao_don_hang) + count($thong_bao_san_pham_moi) + count($thong_bao_san_pham_giam_gia);
}
?>

<header class="tieu-de">
    <div class="logo">
        <a href="index.php">
            <h1>iToys: Thế giới đồ chơi</h1>
        </a>
    </div>
  
    <div class="tim-kiem">
        <form action="index.php" method="GET">
            <input type="hidden" name="trang" value="san-pham">
            <input type="text" name="tu_khoa" placeholder="Tìm kiếm đồ chơi...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
  
    <div class="menu-chinh">
        <nav>
            <ul>
                <li><a href="index.php">Trang Chủ</a></li>
                <li><a href="index.php?trang=san-pham">Sản Phẩm</a></li>
                <li><a href="index.php?trang=lien-he">Liên Hệ</a></li>
            </ul>
        </nav>
    </div>
  
    <div class="tai-khoan-gio-hang">
        <?php if (da_dang_nhap()): ?>
            <div class="user-actions">
                <!-- Thêm phần thông báo -->
                <div class="user-notifications">
                    <div class="notification-icon" id="user-notification-toggle">
                        <i class="fas fa-bell"></i>
                        <?php if (isset($so_thong_bao_chua_doc) && $so_thong_bao_chua_doc > 0): ?>
                            <span class="notification-badge"><?php echo $so_thong_bao_chua_doc; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="notification-dropdown" id="user-notification-dropdown">
                        <div class="notification-header">
                            <h3>Thông báo</h3>
                            <?php if (isset($so_thong_bao_chua_doc) && $so_thong_bao_chua_doc > 0): ?>
                                <span class="notification-count"><?php echo $so_thong_bao_chua_doc; ?> chưa đọc</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notification-content">
                            <?php if (!isset($tong_so_thong_bao) || $tong_so_thong_bao == 0): ?>
                                <div class="no-notifications">
                                    <i class="fas fa-check-circle"></i>
                                    <p>Không có thông báo mới</p>
                                </div>
                            <?php else: ?>
                                <!-- Thông báo đơn hàng -->
                                <?php if (!empty($thong_bao_don_hang)): ?>
                                <!-- Thông báo đặt hàng thành công -->
                                <?php
                                if (isset($_SESSION['dat_hang_thanh_cong']) && $_SESSION['dat_hang_thanh_cong'] === true):
                                    // Xóa session sau khi hiển thị
                                    unset($_SESSION['dat_hang_thanh_cong']);
                                    ?>
                                    <div class="notification-success-message">
                                        <i class="fas fa-check-circle"></i>
                                        <div>
                                            <p>Cảm ơn bạn đã đặt hàng!</p>
                                            <p>Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                    <div class="notification-group">
                                        <div class="notification-group-header">
                                            <i class="fas fa-shopping-bag"></i>
                                            <span>Cập nhật đơn hàng</span>
                                        </div>
                                        
                                        <?php foreach ($thong_bao_don_hang as $dh): ?>
                                            <a href="index.php?danh-dau-da-doc=1&loai=don_hang&id=<?php echo $dh['id']; ?>" 
                                               class="notification-item <?php echo (!$dh['da_doc']) ? 'unread' : ''; ?>">
                                                <div class="notification-icon order">
                                                    <i class="fas fa-clipboard-list"></i>
                                                </div>
                                                <div class="notification-details">
                                                    <div class="notification-title">Đơn hàng #<?php echo $dh['id']; ?></div>
                                                    <div class="notification-description">
                                                    <?php
                                                    $status_class = '';
                                                    switch ($dh['trang_thai']) {
                                                        case 0:
                                                            $status_class = 'status-pending';
                                                            break;
                                                        case 1:
                                                            $status_class = 'status-confirmed';
                                                            break;
                                                        case 2:
                                                            $status_class = 'status-shipping';
                                                            break;
                                                        case 3:
                                                            $status_class = 'status-delivered';
                                                            break;
                                                        case 4:
                                                            $status_class = 'status-cancelled';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="order-status <?php echo $status_class; ?>">
                                                        Đơn hàng của bạn <?php echo $dh['trang_thai_text']; ?>
                                                    </span>
                                                </div>
                                                    <div class="notification-time">
                                                        <?php echo time_elapsed_string($dh['ngay_tao']); ?>
                                                    </div>
                                                </div>
                                                <?php if (!$dh['da_doc']): ?>
                                                    <div class="notification-unread-marker"></div>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Thông báo sản phẩm mới -->
                                <?php if (!empty($thong_bao_san_pham_moi)): ?>
                                    <div class="notification-group">
                                        <div class="notification-group-header">
                                            <i class="fas fa-star"></i>
                                            <span>Sản phẩm mới</span>
                                        </div>
                                        
                                        <?php foreach ($thong_bao_san_pham_moi as $sp): ?>
                                            <a href="index.php?danh-dau-da-doc=1&loai=san_pham_moi&id=<?php echo $sp['id']; ?>" 
                                               class="notification-item <?php echo (!$sp['da_doc']) ? 'unread' : ''; ?>">
                                                <div class="notification-icon new-product">
                                                    <i class="fas fa-gift"></i>
                                                </div>
                                                <div class="notification-details">
                                                    <div class="notification-title"><?php echo $sp['ten_san_pham']; ?></div>
                                                    <div class="notification-description">
                                                        Sản phẩm mới với giá <?php echo dinh_dang_tien($sp['gia']); ?>
                                                    </div>
                                                    <div class="notification-time">
                                                        <?php echo time_elapsed_string($sp['ngay_tao']); ?>
                                                    </div>
                                                </div>
                                                <?php if (!$sp['da_doc']): ?>
                                                    <div class="notification-unread-marker"></div>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Thông báo sản phẩm giảm giá -->
                                <?php if (!empty($thong_bao_san_pham_giam_gia)): ?>
                                    <div class="notification-group">
                                        <div class="notification-group-header">
                                            <i class="fas fa-tags"></i>
                                            <span>Khuyến mãi mới</span>
                                        </div>
                                        
                                        <?php foreach ($thong_bao_san_pham_giam_gia as $sp): ?>
                                            <a href="index.php?danh-dau-da-doc=1&loai=san_pham_giam_gia&id=<?php echo $sp['id']; ?>" 
                                               class="notification-item <?php echo (!$sp['da_doc']) ? 'unread' : ''; ?>">
                                                <div class="notification-icon discount">
                                                    <i class="fas fa-percent"></i>
                                                </div>
                                                <div class="notification-details">
                                                    <div class="notification-title"><?php echo $sp['ten_san_pham']; ?></div>
                                                    <div class="notification-description">
                                                        Giảm giá từ <?php echo dinh_dang_tien($sp['gia']); ?> còn <?php echo dinh_dang_tien($sp['gia_khuyen_mai']); ?>
                                                    </div>
                                                    <div class="notification-time">
                                                        <?php echo time_elapsed_string($sp['ngay_tao']); ?>
                                                    </div>
                                                </div>
                                                <?php if (!$sp['da_doc']): ?>
                                                    <div class="notification-unread-marker"></div>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Nút đánh dấu tất cả đã đọc -->
                                <?php if ($so_thong_bao_chua_doc > 0): ?>
                                    <div class="notification-mark-all-read">
                                        <a href="javascript:void(0)" id="mark-all-read">Đánh dấu tất cả đã đọc</a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="tai-khoan">
                    <?php
                    // Lấy thông tin avatar từ database
                    $nguoi_dung_id = $_SESSION['nguoi_dung_id'];
                    $sql = "SELECT avatar FROM nguoi_dung WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $nguoi_dung_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $avatar = '';
                    if ($row = $result->fetch_assoc()) {
                        $avatar = $row['avatar'];
                    }
                    ?>
                    <a href="index.php?trang=tai-khoan" class="user-account-link">
                        <?php if (!empty($avatar) && file_exists("uploads/" . $avatar)): ?>
                            <img src="uploads/<?php echo $avatar; ?>" alt="Avatar" class="avatar-header">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                        <span><?php echo $_SESSION['nguoi_dung_ten']; ?></span>
                    </a>
                    <div class="menu-tai-khoan">
                        <ul>
                            <li><a href="index.php?trang=tai-khoan">Tài khoản của tôi</a></li>
                            <li><a href="index.php?trang=tai-khoan&hanh-dong=don-hang">Đơn hàng của tôi</a></li>
                            <li><a href="xu-ly/dang-xuat.php">Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="tai-khoan">
                <a href="index.php?trang=dang-nhap">
                    <i class="fas fa-user"></i>
                    <span>Đăng nhập</span>
                </a>
            </div>
        <?php endif; ?>
      
        <div class="gio-hang">
            <a href="index.php?trang=gio-hang">
                <i class="fas fa-shopping-cart"></i>
                <span class="so-luong"><?php echo tong_so_san_pham_gio_hang(); ?></span>
            </a>
        </div>
    </div>
</header>

<div class="danh-muc">
    <nav>
        <ul>
            <?php
            $danh_muc = lay_danh_sach_danh_muc($conn);
            foreach ($danh_muc as $dm) {
                echo '<li><a href="index.php?trang=san-pham&danh-muc=' . $dm['id'] . '">' . $dm['ten_danh_muc'] . '</a></li>';
            }
            ?>
        </ul>
    </nav>
</div>

<style>
.avatar-header {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 5px;
}

.user-account-link {
    display: flex;
    align-items: center;
    gap: 5px;
}

.tai-khoan {
    position: relative;
}

.menu-tai-khoan {
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

.tai-khoan:hover .menu-tai-khoan {
    display: block;
}

.menu-tai-khoan ul {
    padding: 10px 0;
    margin: 0;
    list-style: none;
}

.menu-tai-khoan ul li a {
    display: block;
    padding: 8px 15px;
    transition: background-color 0.3s;
}

.menu-tai-khoan ul li a:hover {
    background-color: #f8f9fa;
}

/* Sửa lại CSS cho header */
.tieu-de {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: #fff;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 100%;
}

/* Sửa lại CSS cho danh mục */
.danh-muc {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    width: 100%;
    max-width: 100%;
    padding: 0 20px;
}

.danh-muc ul {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 10px 0;
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
}

/* CSS cho phần thông báo người dùng */
.user-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.tai-khoan-gio-hang {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-notifications {
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
    position: relative;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-unread-marker {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background-color: #4e73df;
    border-radius: 50%;
}

.notification-icon.order {
    background-color: #cce5ff;
    color: #004085;
}

.notification-icon.new-product {
    background-color: #d4edda;
    color: #155724;
}

.notification-icon.discount {
    background-color: #fff3cd;
    color: #856404;
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

.notification-mark-all-read {
    padding: 10px 15px;
    text-align: center;
    border-top: 1px solid #e9ecef;
}

.notification-mark-all-read a {
    color: #4e73df;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
}

.notification-mark-all-read a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .notification-dropdown {
        right: -100px;
        width: 300px;
    }
    
    .tieu-de {
        padding: 10px;
    }
    
    .menu-chinh {
        order: 3;
        width: 100%;
        margin-top: 10px;
    }
    
    .menu-chinh ul {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .notification-dropdown {
        right: -50px;
        width: 280px;
    }
    
    .tim-kiem {
        order: 3;
        width: 100%;
        margin-top: 10px;
    }
    
    .logo h1 {
        font-size: 18px;
    }
}

.notification-success-message {
    display: flex;
    align-items: center;
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 4px;
    margin: 10px 15px;
    border-left: 4px solid #28a745;
}

.notification-success-message i {
    font-size: 24px;
    margin-right: 15px;
    color: #28a745;
}

.notification-success-message p {
    margin: 0;
    line-height: 1.4;
}

.notification-success-message p:first-child {
    font-weight: bold;
    margin-bottom: 5px;
}

/* Thêm màu sắc cho các trạng thái đơn hàng */
.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background-color: #cce5ff;
    color: #004085;
}

.status-shipping {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-delivered {
    background-color: #d4edda;
    color: #155724;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

.order-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý thông báo người dùng
    const userNotificationToggle = document.getElementById('user-notification-toggle');
    const userNotificationDropdown = document.getElementById('user-notification-dropdown');
    
    if (userNotificationToggle && userNotificationDropdown) {
        userNotificationToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userNotificationDropdown.style.display = userNotificationDropdown.style.display === 'block' ? 'none' : 'block';
        });
        
        // Đóng dropdown khi click bên ngoài
        document.addEventListener('click', function(e) {
            if (!userNotificationToggle.contains(e.target) && !userNotificationDropdown.contains(e.target)) {
                userNotificationDropdown.style.display = 'none';
            }
        });
        
        // Ngăn sự kiện click trong dropdown lan ra ngoài
        userNotificationDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Xử lý nút đánh dấu tất cả đã đọc
        const markAllReadBtn = document.getElementById('mark-all-read');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                // Gửi AJAX request để đánh dấu tất cả đã đọc
                fetch('xu-ly/danh-dau-tat-ca-da-doc.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật giao diện
                        const unreadItems = document.querySelectorAll('.notification-item.unread');
                        unreadItems.forEach(item => {
                            item.classList.remove('unread');
                            const marker = item.querySelector('.notification-unread-marker');
                            if (marker) marker.remove();
                        });
                        
                        // Ẩn badge thông báo
                        const badge = document.querySelector('.notification-badge');
                        if (badge) badge.style.display = 'none';
                        
                        // Cập nhật số lượng thông báo chưa đọc
                        const countElement = document.querySelector('.notification-count');
                        if (countElement) countElement.textContent = '0 chưa đọc';
                        
                        // Ẩn nút đánh dấu tất cả đã đọc
                        markAllReadBtn.parentElement.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    }
});
</script>
