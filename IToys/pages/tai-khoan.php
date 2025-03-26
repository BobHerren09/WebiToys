<?php
// Kiem tra dang nhap
if (!da_dang_nhap()) {
    header("Location: index.php?trang=dang-nhap");
    exit();
}

// Lay thong tin nguoi dung
$nguoi_dung_id = $_SESSION['nguoi_dung_id'];
$sql = "SELECT * FROM nguoi_dung WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $nguoi_dung_id);
$stmt->execute();
$result = $stmt->get_result();
$nguoi_dung = $result->fetch_assoc();

// Xu ly hanh dong
$hanh_dong = isset($_GET['hanh-dong']) ? $_GET['hanh-dong'] : 'thong-tin';
$thong_bao = '';
$loi = '';

// Cap nhat thong tin ca nhan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $hanh_dong == 'thong-tin') {
    $ho_ten = $_POST['ho_ten'];
    $dien_thoai = $_POST['dien_thoai'];
    $dia_chi = $_POST['dia_chi'];

    // Kiem tra thong tin
    if (empty($ho_ten)) {
        $loi = "Vui lòng nhập họ tên!";
    } else {
        // Xử lý upload avatar nếu có
        $avatar = $nguoi_dung['avatar']; // Giữ nguyên avatar cũ nếu không có avatar mới

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
                $avatar_moi = 'user-avatar-' . $nguoi_dung_id . '-' . time() . '.' . $file_ext;
                $upload_dir = "uploads/";

                // Kiểm tra thư mục uploads tồn tại
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $upload_path = $upload_dir . $avatar_moi;

                // Upload file
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Xóa avatar cũ nếu có
                    if (!empty($nguoi_dung['avatar']) && file_exists($upload_dir . $nguoi_dung['avatar'])) {
                        unlink($upload_dir . $nguoi_dung['avatar']);
                    }

                    $avatar = $avatar_moi;
                } else {
                    $loi = "Có lỗi xảy ra khi tải lên avatar!";
                }
            }
        }

        if (empty($loi)) {
            // Cap nhat thong tin
            $sql = "UPDATE nguoi_dung SET ho_ten = ?, dien_thoai = ?, dia_chi = ?, avatar = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $ho_ten, $dien_thoai, $dia_chi, $avatar, $nguoi_dung_id);

            if ($stmt->execute()) {
                $thong_bao = "Cập nhật thông tin thành công!";

                // Cap nhat lai thong tin nguoi dung
                $sql = "SELECT * FROM nguoi_dung WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $nguoi_dung_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $nguoi_dung = $result->fetch_assoc();

                // Cap nhat ten nguoi dung trong session
                $_SESSION['nguoi_dung_ten'] = $nguoi_dung['ho_ten'];
            } else {
                $loi = "Có lỗi xảy ra khi cập nhật thông tin!";
            }
        }
    }
}

// Doi mat khau
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $hanh_dong == 'doi-mat-khau') {
    $mat_khau_cu = $_POST['mat_khau_cu'];
    $mat_khau_moi = $_POST['mat_khau_moi'];
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'];

    // Kiem tra mat khau cu
    if (!password_verify($mat_khau_cu, $nguoi_dung['mat_khau'])) {
        $loi = "Mật khẩu cũ không chính xác!";
    } elseif (empty($mat_khau_moi) || strlen($mat_khau_moi) < 6) {
        $loi = "Mật khẩu mới phải có ít nhất 6 ký tự!";
    } elseif ($mat_khau_moi != $xac_nhan_mat_khau) {
        $loi = "Xác nhận mật khẩu không khớp!";
    } else {
        // Cap nhat mat khau
        $mat_khau_hash = password_hash($mat_khau_moi, PASSWORD_DEFAULT);
        $sql = "UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $mat_khau_hash, $nguoi_dung_id);

        if ($stmt->execute()) {
            $thong_bao = "Đổi mật khẩu thành công!";
        } else {
            $loi = "Có lỗi xảy ra khi đổi mật khẩu!";
        }
    }
}
?>

<div class="trang-tai-khoan">
  <div class="container">
      <h1>Tài Khoản Của Tôi</h1>
      
      <div class="tai-khoan-container">
          <div class="menu-tai-khoan">
              <div class="thong-tin-nguoi-dung">
                  <div class="avatar">
                      <?php if (!empty($nguoi_dung['avatar']) && file_exists("uploads/" . $nguoi_dung['avatar'])): ?>
                          <img src="uploads/<?php echo $nguoi_dung['avatar']; ?>" alt="<?php echo $nguoi_dung['ho_ten']; ?>">
                      <?php else: ?>
                          <i class="fas fa-user-circle"></i>
                      <?php endif; ?>
                  </div>
                  <div class="ten-nguoi-dung">
                      <h3><?php echo $nguoi_dung['ho_ten']; ?></h3>
                      <p><?php echo $nguoi_dung['email']; ?></p>
                  </div>
              </div>
              
              <ul class="danh-sach-menu">
                  <li class="<?php echo $hanh_dong == 'thong-tin' ? 'active' : ''; ?>">
                      <a href="index.php?trang=tai-khoan&hanh-dong=thong-tin">
                          <i class="fas fa-user"></i> Thông tin tài khoản
                      </a>
                  </li>
                  <li class="<?php echo $hanh_dong == 'don-hang' ? 'active' : ''; ?>">
                      <a href="index.php?trang=tai-khoan&hanh-dong=don-hang">
                          <i class="fas fa-shopping-bag"></i> Đơn hàng của tôi
                      </a>
                  </li>
                  <li class="<?php echo $hanh_dong == 'doi-mat-khau' ? 'active' : ''; ?>">
                      <a href="index.php?trang=tai-khoan&hanh-dong=doi-mat-khau">
                          <i class="fas fa-lock"></i> Đổi mật khẩu
                      </a>
                  </li>
                  <li>
                      <a href="xu-ly/dang-xuat.php">
                          <i class="fas fa-sign-out-alt"></i> Đăng xuất
                      </a>
                  </li>
              </ul>
          </div>
          
          <div class="noi-dung-tai-khoan">
              <?php if (!empty($thong_bao)): ?>
                  <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
              <?php endif; ?>
              
              <?php if (!empty($loi)): ?>
                  <div class="thong-bao-loi"><?php echo $loi; ?></div>
              <?php endif; ?>
              
              <?php if ($hanh_dong == 'thong-tin'): ?>
                  <!-- Thông tin tài khoản -->
                  <div class="khung-noi-dung">
                      <h2>Thông Tin Tài Khoản</h2>
                      
                      <form method="POST" action="index.php?trang=tai-khoan&hanh-dong=thong-tin" enctype="multipart/form-data">
                          <div class="avatar-container">
                              <div class="avatar-preview">
                                  <?php if (!empty($nguoi_dung['avatar']) && file_exists("uploads/" . $nguoi_dung['avatar'])): ?>
                                      <img src="uploads/<?php echo $nguoi_dung['avatar']; ?>" alt="Avatar" id="avatar-preview">
                                  <?php else: ?>
                                      <div class="no-avatar">
                                          <i class="fas fa-user"></i>
                                      </div>
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
                              <input type="text" id="ho_ten" name="ho_ten" value="<?php echo $nguoi_dung['ho_ten']; ?>" required>
                          </div>
                          
                          <div class="form-group">
                              <label for="email">Email</label>
                              <input type="email" id="email" value="<?php echo $nguoi_dung['email']; ?>" readonly>
                              <small>Email không thể thay đổi</small>
                          </div>
                          
                          <div class="form-group">
                              <label for="dien_thoai">Điện thoại</label>
                              <input type="tel" id="dien_thoai" name="dien_thoai" value="<?php echo $nguoi_dung['dien_thoai']; ?>">
                          </div>
                          
                          <div class="form-group">
                              <label for="dia_chi">Địa chỉ</label>
                              <textarea id="dia_chi" name="dia_chi" rows="3"><?php echo $nguoi_dung['dia_chi']; ?></textarea>
                          </div>
                          
                          <button type="submit" class="btn-cap-nhat">Cập nhật thông tin</button>
                      </form>
                  </div>
              <?php elseif ($hanh_dong == 'don-hang'): ?>
                  <!-- Đơn hàng của tôi -->
                  <div class="khung-noi-dung">
                      <h2>Đơn Hàng Của Tôi</h2>
                      
                      <?php
                      // Lay danh sach don hang
                      $sql = "SELECT * FROM don_hang WHERE khach_hang_id = ? ORDER BY id DESC";
                      $stmt = $conn->prepare($sql);
                      $stmt->bind_param("i", $nguoi_dung_id);
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result->num_rows > 0):
                          ?>
                          <div class="danh-sach-don-hang">
                              <table>
                                  <thead>
                                      <tr>
                                          <th>Mã đơn hàng</th>
                                          <th>Ngày đặt</th>
                                          <th>Tổng tiền</th>
                                          <th>Trạng thái</th>
                                          <th>Chi tiết</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php while ($don_hang = $result->fetch_assoc()): ?>
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
                                              <td><?php echo date('d/m/Y', strtotime($don_hang['ngay_tao'])); ?></td>
                                              <td><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></td>
                                              <td><span class="trang-thai <?php echo $trang_thai_class; ?>"><?php echo $trang_thai_text; ?></span></td>
                                              <td>
                                                  <button type="button" class="btn-xem-chi-tiet" data-id="<?php echo $don_hang['id']; ?>">
                                                      <i class="fas fa-eye"></i> Xem
                                                  </button>
                                              </td>
                                          </tr>
                                          
                                          <!-- Chi tiết đơn hàng -->
                                          <tr class="chi-tiet-don-hang" id="chi-tiet-<?php echo $don_hang['id']; ?>">
                                              <td colspan="5">
                                                  <div class="noi-dung-chi-tiet">
                                                      <div class="thong-tin-don-hang">
                                                          <h4>Thông tin đơn hàng #<?php echo $don_hang['id']; ?></h4>
                                                          <p><strong>Người nhận:</strong> <?php echo $don_hang['ho_ten']; ?></p>
                                                          <p><strong>Điện thoại:</strong> <?php echo $don_hang['dien_thoai']; ?></p>
                                                          <p><strong>Địa chỉ:</strong> <?php echo $don_hang['dia_chi']; ?></p>
                                                          <p><strong>Ghi chú:</strong> <?php echo $don_hang['ghi_chu'] ? $don_hang['ghi_chu'] : 'Không có'; ?></p>
                                                      </div>
                                                      
                                                      <div class="san-pham-don-hang">
                                                          <h4>Sản phẩm đã đặt</h4>
                                                          <table class="bang-san-pham">
                                                              <thead>
                                                                  <tr>
                                                                      <th>Sản phẩm</th>
                                                                      <th>Giá</th>
                                                                      <th>Số lượng</th>
                                                                      <th>Thành tiền</th>
                                                                  </tr>
                                                              </thead>
                                                              <tbody>
                                                                  <?php
                                                                  // Lấy chi tiết đơn hàng
                                                                  $chi_tiet = lay_chi_tiet_don_hang($conn, $don_hang['id']);
                                                                  foreach ($chi_tiet as $item):
                                                                      ?>
                                                                      <tr>
                                                                          <td class="san-pham-info">
                                                                              <img src="uploads/<?php echo $item['hinh_anh']; ?>" alt="<?php echo $item['ten_san_pham']; ?>">
                                                                              <span><?php echo $item['ten_san_pham']; ?></span>
                                                                          </td>
                                                                          <td><?php echo dinh_dang_tien($item['gia']); ?></td>
                                                                          <td><?php echo $item['so_luong']; ?></td>
                                                                          <td><?php echo dinh_dang_tien($item['thanh_tien']); ?></td>
                                                                      </tr>
                                                                  <?php endforeach; ?>
                                                              </tbody>
                                                              <tfoot>
                                                                  <tr>
                                                                      <td colspan="3" class="tong-cong">Tổng cộng:</td>
                                                                      <td><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></td>
                                                                  </tr>
                                                              </tfoot>
                                                          </table>
                                                      </div>
                                                  </div>
                                              </td>
                                          </tr>
                                      <?php endwhile; ?>
                                  </tbody>
                              </table>
                          </div>
                      <?php else: ?>
                          <div class="khong-co-don-hang">
                              <p>Bạn chưa có đơn hàng nào.</p>
                              <a href="index.php?trang=san-pham" class="btn-mua-ngay">Mua sắm ngay</a>
                          </div>
                      <?php endif; ?>
                  </div>
              <?php elseif ($hanh_dong == 'doi-mat-khau'): ?>
                  <!-- Đổi mật khẩu -->
                  <div class="khung-noi-dung">
                      <h2>Đổi Mật Khẩu</h2>
                      
                      <form method="POST" action="index.php?trang=tai-khoan&hanh-dong=doi-mat-khau">
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
                          
                          <button type="submit" class="btn-cap-nhat">Đổi mật khẩu</button>
                      </form>
                  </div>
              <?php endif; ?>
          </div>
      </div>
  </div>
</div>

<style>
.trang-tai-khoan {
  padding: 30px 0;
}

.trang-tai-khoan h1 {
  margin-bottom: 30px;
  text-align: center;
}

.tai-khoan-container {
  display: grid;
  grid-template-columns: 500px 1fr;
  gap: 30px;
}

.menu-tai-khoan {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  overflow: hidden;
}

.thong-tin-nguoi-dung {
  padding: 20px;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  gap: 15px;
}

.avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  overflow: hidden;
  background-color: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar i {
  font-size: 40px;
  color: #adb5bd;
}

.ten-nguoi-dung h3 {
  margin-bottom: 5px;
  font-size: 16px;
}

.ten-nguoi-dung p {
  font-size: 14px;
  color: #6c757d;
}

.danh-sach-menu {
  padding: 10px 0;
}

.danh-sach-menu li a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  transition: background-color 0.3s;
}

.danh-sach-menu li a:hover {
  background-color: #f8f9fa;
}

.danh-sach-menu li.active a {
  background-color: #f8f9fa;
  color: #ff6b6b;
  font-weight: 500;
}

.noi-dung-tai-khoan {
  min-height: 500px;
}



.khung-noi-dung h2 {
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e9ecef;
}

.thong-bao-thanh-cong {
  background-color: #d1e7dd;
  color: #0f5132;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 20px;
  text-align: center;
}

.thong-bao-loi {
  background-color: #f8d7da;
  color: #842029;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 20px;
  text-align: center;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
}

.form-group input, .form-group textarea {
  width: 100%;
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group textarea {
  resize: vertical;
}

.form-group small {
  display: block;
  margin-top: 5px;
  font-size: 12px;
  color: #6c757d;
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
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-avatar {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #e9ecef;
}

.no-avatar i {
  font-size: 50px;
  color: #adb5bd;
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

.btn-cap-nhat {
  width: 100%;
  padding: 12px;
  background-color: #ff6b6b;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-cap-nhat:hover {
  background-color: #ff5252;
}

/* Đơn hàng */



.danh-sach-don-hang table {
  width: 100%;
  border-collapse: collapse;
}

.danh-sach-don-hang th, .danh-sach-don-hang td {
  padding: 4px 78px;
  text-align: left;
  border-bottom: 1px solid #e9ecef;
}

.danh-sach-don-hang th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.trang-thai {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.moi {
  background-color: #e9ecef;
  color: #495057;
}

.dang-xu-ly {
  background-color: #cff4fc;
  color: #055160;
}

.dang-giao {
  background-color: #fff3cd;
  color: #664d03;
}

.hoan-thanh {
  background-color: #d1e7dd;
  color: #0f5132;
}

.da-huy {
  background-color: #f8d7da;
  color: #842029;
}

.btn-xem-chi-tiet {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 12px;
  background-color: #e9ecef;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-xem-chi-tiet:hover {
  background-color: #dee2e6;
}

.chi-tiet-don-hang {
  display: none;
}

.chi-tiet-don-hang.active {
  display: table-row;
}

.noi-dung-chi-tiet {
  padding: 20px;
  background-color: #f8f9fa;
}

.thong-tin-don-hang {
  margin-bottom: 20px;
}

.thong-tin-don-hang h4, .san-pham-don-hang h4 {
  margin-bottom: 10px;
  font-size: 16px;
}

.bang-san-pham {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.bang-san-pham th, .bang-san-pham td {
  padding: 10px;
  text-align: left;
  border-bottom: 1px solid #dee2e6;
}

.bang-san-pham th {
  background-color: #e9ecef;
  font-weight: 500;
}

.san-pham-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.san-pham-info img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.tong-cong {
  text-align: right;
  font-weight: bold;
}

.khong-co-don-hang {
  text-align: center;
  padding: 30px 0;
}

.khong-co-don-hang p {
  margin-bottom: 15px;
  color: #6c757d;
}

.btn-mua-ngay {
  display: inline-block;
  padding: 10px 20px;
  background-color: #ff6b6b;
  color: white;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.btn-mua-ngay:hover {
  background-color: #ff5252;
}

@media (max-width: 768px) {
  .tai-khoan-container {
      grid-template-columns: 1fr;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Xu ly xem chi tiet don hang
  const xemChiTietButtons = document.querySelectorAll('.btn-xem-chi-tiet');
  
  xemChiTietButtons.forEach(button => {
      button.addEventListener('click', function() {
          const donHangId = this.getAttribute('data-id');
          const chiTietElement = document.getElementById('chi-tiet-' + donHangId);
          
          // Dong tat ca chi tiet khac
          document.querySelectorAll('.chi-tiet-don-hang.active').forEach(el => {
              if (el.id !== 'chi-tiet-' + donHangId) {
                  el.classList.remove('active');
              }
          });
          
          // Hien thi/an chi tiet hien tai
          chiTietElement.classList.toggle('active');
      });
  });
  
  // Preview avatar khi chọn file
  const avatarInput = document.getElementById('avatar');
  const avatarPreview = document.getElementById('avatar-preview');
  
  if (avatarInput && avatarPreview) {
      avatarInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
              const reader = new FileReader();
              
              reader.onload = function(e) {
                  // Nếu chưa có avatar, tạo thẻ img mới
                  if (!avatarPreview) {
                      const noAvatar = document.querySelector('.no-avatar');
                      if (noAvatar) {
                          noAvatar.innerHTML = '';
                          const img = document.createElement('img');
                          img.id = 'avatar-preview';
                          img.src = e.target.result;
                          noAvatar.appendChild(img);
                      }
                  } else {
                      avatarPreview.src = e.target.result;
                  }
              }
              
              reader.readAsDataURL(this.files[0]);
          }
      });
  }
});
</script>

