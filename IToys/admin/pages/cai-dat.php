<?php
// Kiem tra quyen truy cap
if (!isset($_SESSION['admin_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

// Lay thong tin quan tri vien
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM quan_tri_vien WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Cập nhật session avatar nếu chưa có
if (!isset($_SESSION['admin_avatar']) && !empty($admin['avatar'])) {
    $_SESSION['admin_avatar'] = $admin['avatar'];
}

// Xu ly cap nhat thong tin ca nhan
$thong_bao = '';
$loi = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cap_nhat_thong_tin'])) {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];

    // Kiem tra thong tin
    if (empty($ho_ten)) {
        $loi = "Vui lòng nhập họ tên!";
    } elseif (empty($email)) {
        $loi = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = "Email không hợp lệ!";
    } else {
        // Kiem tra email da ton tai chua (ngoai tru admin hien tai)
        $sql = "SELECT * FROM quan_tri_vien WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $loi = "Email đã được sử dụng bởi quản trị viên khác!";
        } else {
            // Xử lý upload avatar nếu có
            $avatar = $admin['avatar']; // Giữ nguyên avatar cũ nếu không có avatar mới

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $file_name = $_FILES['avatar']['name'];
                $file_tmp = $_FILES['avatar']['tmp_name'];
                $file_size = $_FILES['avatar']['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Kiểm tra định dạng file
                $allowed_exts = array('jpg', 'jpeg', 'png', 'gif');
                if (!in_array($file_ext, $allowed_exts)) {
                    $loi = "Chỉ cho phép tải lên các file hình ảnh (jpg, jpeg, png, gif)!";
                } elseif ($file_size > 10 * 1024 * 1024) { // 10MB
                    $loi = "Kích thước file không được vượt quá 10MB!";
                } else {
                    // Tạo tên file mới
                    $avatar_moi = 'admin-avatar-' . $admin_id . '-' . time() . '.' . $file_ext;
                    $upload_dir = "../uploads/";

                    // Kiểm tra thư mục uploads tồn tại
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $upload_path = $upload_dir . $avatar_moi;

                    // Upload file
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Xóa avatar cũ nếu có
                        if (!empty($admin['avatar']) && file_exists($upload_dir . $admin['avatar'])) {
                            unlink($upload_dir . $admin['avatar']);
                        }

                        $avatar = $avatar_moi;
                    } else {
                        $loi = "Có lỗi xảy ra khi tải lên avatar!";
                    }
                }
            }

            if (empty($loi)) {
                // Cập nhật thông tin
                $sql = "UPDATE quan_tri_vien SET ho_ten = ?, email = ?, avatar = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $ho_ten, $email, $avatar, $admin_id);

                if ($stmt->execute()) {
                    $thong_bao = "Cập nhật thông tin thành công!";

                    // Cập nhật lại thông tin admin
                    $sql = "SELECT * FROM quan_tri_vien WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $admin_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $admin = $result->fetch_assoc();

                    // Cập nhật tên admin trong session
                    $_SESSION['admin_ten'] = $admin['ho_ten'];

                    // Cập nhật avatar trong session
                    $_SESSION['admin_avatar'] = $admin['avatar'];
                } else {
                    $loi = "Có lỗi xảy ra khi cập nhật thông tin!";
                }
            }
        }
    }
}

// Xu ly doi mat khau
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['doi_mat_khau'])) {
    $mat_khau_cu = $_POST['mat_khau_cu'];
    $mat_khau_moi = $_POST['mat_khau_moi'];
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'];

    // Kiem tra thong tin
    if (empty($mat_khau_cu)) {
        $loi = "Vui lòng nhập mật khẩu hiện tại!";
    } elseif (empty($mat_khau_moi)) {
        $loi = "Vui lòng nhập mật khẩu mới!";
    } elseif (strlen($mat_khau_moi) < 6) {
        $loi = "Mật khẩu mới phải có ít nhất 6 ký tự!";
    } elseif ($mat_khau_moi != $xac_nhan_mat_khau) {
        $loi = "Xác nhận mật khẩu không khớp!";
    } else {
        // Kiem tra mat khau cu
        if (password_verify($mat_khau_cu, $admin['mat_khau'])) {
            // Ma hoa mat khau moi
            $mat_khau_hash = password_hash($mat_khau_moi, PASSWORD_DEFAULT);

            // Cap nhat mat khau
            $sql = "UPDATE quan_tri_vien SET mat_khau = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $mat_khau_hash, $admin_id);

            if ($stmt->execute()) {
                $thong_bao = "Đổi mật khẩu thành công!";
            } else {
                $loi = "Có lỗi xảy ra khi đổi mật khẩu!";
            }
        } else {
            $loi = "Mật khẩu hiện tại không chính xác!";
        }
    }
}
?>

<div class="cai-dat-page">
  <div class="page-header">
      <h1>Cài Đặt Tài Khoản</h1>
  </div>
  
  <?php if (!empty($thong_bao)): ?>
      <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
  <?php endif; ?>
  
  <?php if (!empty($loi)): ?>
      <div class="thong-bao-loi"><?php echo $loi; ?></div>
  <?php endif; ?>
  
  <div class="cai-dat-container">
      <div class="thong-tin-ca-nhan">
          <h2>Thông Tin Cá Nhân</h2>
          <form method="POST" action="" enctype="multipart/form-data">
              <div class="avatar-container">
                  <div class="avatar-preview">
                      <?php if (!empty($admin['avatar']) && file_exists("../uploads/" . $admin['avatar'])): ?>
                          <img src="../uploads/<?php echo $admin['avatar']; ?>" alt="Avatar" id="avatar-preview">
                      <?php else: ?>
                          <img src="../assets/images/admin-avatar.png" alt="Avatar" id="avatar-preview">
                      <?php endif; ?>
                  </div>
                  <div class="avatar-upload">
                      <label for="avatar" class="btn-upload">
                          <i class="fas fa-camera"></i> Thay đổi avatar
                      </label>
                      <input type="file" id="avatar" name="avatar" accept="image/*">
                  </div>
              </div>
              
              <div class="form-group">
                  <label for="ho_ten">Họ tên</label>
                  <input type="text" id="ho_ten" name="ho_ten" value="<?php echo $admin['ho_ten']; ?>" required>
              </div>
              
              <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
              </div>
              
              <div class="form-group">
                  <label for="ten_dang_nhap">Tên đăng nhập</label>
                  <input type="text" id="ten_dang_nhap" value="<?php echo $admin['ten_dang_nhap']; ?>" readonly>
                  <small>Tên đăng nhập không thể thay đổi</small>
              </div>
              
              <div class="form-group">
                  <label for="quyen_han">Quyền hạn</label>
                  <input type="text" id="quyen_han" value="<?php echo ucfirst($admin['quyen_han']); ?>" readonly>
                  <small>Quyền hạn không thể thay đổi</small>
              </div>
              
              <div class="form-actions">
                  <button type="submit" name="cap_nhat_thong_tin" class="btn-luu">
                      <i class="fas fa-save"></i> Lưu thay đổi
                  </button>
              </div>
          </form>
      </div>
      
      <div class="doi-mat-khau">
          <h2>Đổi Mật Khẩu</h2>
          <form method="POST" action="">
              <div class="form-group">
                  <label for="mat_khau_cu">Mật khẩu hiện tại</label>
                  <input type="password" id="mat_khau_cu" name="mat_khau_cu" required>
              </div>
              
              <div class="form-group">
                  <label for="mat_khau_moi">Mật khẩu mới</label>
                  <input type="password" id="mat_khau_moi" name="mat_khau_moi" required>
                  <small>Mật khẩu phải có ít nhất 6 ký tự</small>
              </div>
              
              <div class="form-group">
                  <label for="xac_nhan_mat_khau">Xác nhận mật khẩu mới</label>
                  <input type="password" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau" required>
              </div>
              
              <div class="form-actions">
                  <button type="submit" name="doi_mat_khau" class="btn-luu">
                      <i class="fas fa-key"></i> Đổi mật khẩu
                  </button>
              </div>
          </form>
      </div>
  </div>
</div>

<style>
.cai-dat-page {
  padding: 20px;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.cai-dat-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.thong-tin-ca-nhan, .doi-mat-khau {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
}

.thong-tin-ca-nhan h2, .doi-mat-khau h2 {
  margin-top: 0;
  margin-bottom: 20px;
  font-size: 18px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e9ecef;
}

.avatar-container {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  gap: 20px;
}

.avatar-preview {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid #e9ecef;
}

.avatar-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-upload {
  display: flex;
  flex-direction: column;
}

.btn-upload {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 8px 15px;
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-upload:hover {
  background-color: #e9ecef;
}

input[type="file"] {
  display: none;
}

@media (max-width: 768px) {
  .cai-dat-container {
      grid-template-columns: 1fr;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Preview avatar khi chọn file
  const avatarInput = document.getElementById('avatar');
  const avatarPreview = document.getElementById('avatar-preview');
  
  if (avatarInput && avatarPreview) {
      avatarInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
              const reader = new FileReader();
              
              reader.onload = function(e) {
                  avatarPreview.src = e.target.result;
              }
              
              reader.readAsDataURL(this.files[0]);
          }
      });
  }
});
</script>
