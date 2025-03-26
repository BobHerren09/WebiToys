<div class="admin-header-content">
  <div class="admin-search">
      <form action="index.php" method="GET">
          <input type="hidden" name="trang" value="san-pham">
          <input type="text" name="tu_khoa" placeholder="Tìm kiếm...">
          <button type="submit"><i class="fas fa-search"></i></button>
      </form>
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
});
</script>