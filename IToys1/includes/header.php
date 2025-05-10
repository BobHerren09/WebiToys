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
        <div class="tai-khoan">
            <?php if (da_dang_nhap()): ?>
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
            <?php else: ?>
                <a href="index.php?trang=dang-nhap">
                    <i class="fas fa-user"></i>
                    <span>Đăng nhập</span>
                </a>
            <?php endif; ?>
        </div>
      
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
}

.menu-tai-khoan ul li a {
    display: block;
    padding: 8px 15px;
    transition: background-color 0.3s;
}

.menu-tai-khoan ul li a:hover {
    background-color: #f8f9fa;
}
</style>