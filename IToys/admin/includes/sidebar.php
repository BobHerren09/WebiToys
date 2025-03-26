<div class="sidebar">
    <div class="logo">
        <h2>Quản Trị</h2>
    </div>
    
    <nav class="menu">
        <ul>
            <li>
                <a href="index.php?trang=tong-quan" class="<?php echo $trang == 'tong-quan' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Tổng quan
                </a>
            </li>
            <li>
                <a href="index.php?trang=san-pham" class="<?php echo $trang == 'san-pham' || $trang == 'them-san-pham' || $trang == 'sua-san-pham' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Sản phẩm
                </a>
            </li>
            <li>
                <a href="index.php?trang=danh-muc" class="<?php echo $trang == 'danh-muc' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> Danh mục
                </a>
            </li>
            <li>
                <a href="index.php?trang=don-hang" class="<?php echo $trang == 'don-hang' || $trang == 'chi-tiet-don-hang' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Đơn hàng
                </a>
            </li>
            <li>
                <a href="index.php?trang=nguoi-dung" class="<?php echo $trang == 'nguoi-dung' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Người dùng
                </a>
            </li>
            <li>
                <a href="index.php?trang=cai-dat" class="<?php echo $trang == 'cai-dat' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Cài đặt
                </a>
            </li>
            <li>
                <a href="../index.php" target="_blank">
                    <i class="fas fa-home"></i> Xem trang chủ
                </a>
            </li>
            <li>
                <a href="xu-ly/dang-xuat.php">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </nav>
</div>


