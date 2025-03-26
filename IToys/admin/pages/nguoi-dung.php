<?php
// Kiem tra quyen truy cap
if (!isset($_SESSION['admin_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

// Xu ly hanh dong
$hanh_dong = isset($_GET['hanh-dong']) ? $_GET['hanh-dong'] : '';
$thong_bao = '';
$loi = '';

// Xu ly them nguoi dung
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $hanh_dong == 'them') {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'];
    $dien_thoai = $_POST['dien_thoai'];
    $dia_chi = $_POST['dia_chi'];
    $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;

    // Kiem tra thong tin
    if (empty($ho_ten)) {
        $loi = "Vui lòng nhập họ tên!";
    } elseif (empty($email)) {
        $loi = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = "Email không hợp lệ!";
    } elseif (empty($mat_khau)) {
        $loi = "Vui lòng nhập mật khẩu!";
    } elseif (strlen($mat_khau) < 6) {
        $loi = "Mật khẩu phải có ít nhất 6 ký tự!";
    } elseif ($mat_khau != $xac_nhan_mat_khau) {
        $loi = "Xác nhận mật khẩu không khớp!";
    } else {
        // Kiem tra email da ton tai chua
        $sql = "SELECT * FROM nguoi_dung WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $loi = "Email đã được sử dụng!";
        } else {
            // Ma hoa mat khau
            $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);
            $ngay_tao = date('Y-m-d H:i:s');

            // Them nguoi dung vao database
            $sql = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, dien_thoai, dia_chi, trang_thai, ngay_tao) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $ho_ten, $email, $mat_khau_hash, $dien_thoai, $dia_chi, $trang_thai, $ngay_tao);

            if ($stmt->execute()) {
                $thong_bao = "Thêm người dùng thành công!";
                // Reset form
                $_POST = array();
            } else {
                $loi = "Có lỗi xảy ra khi thêm người dùng!";
            }
        }
    }
}

// Xu ly sua nguoi dung
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $hanh_dong == 'sua') {
    $nguoi_dung_id = (int) $_POST['nguoi_dung_id'];
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $dien_thoai = $_POST['dien_thoai'];
    $dia_chi = $_POST['dia_chi'];
    $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;
    $mat_khau_moi = $_POST['mat_khau_moi'];
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'];

    // Kiem tra thong tin
    if (empty($ho_ten)) {
        $loi = "Vui lòng nhập họ tên!";
    } elseif (empty($email)) {
        $loi = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = "Email không hợp lệ!";
    } elseif (!empty($mat_khau_moi) && strlen($mat_khau_moi) < 6) {
        $loi = "Mật khẩu mới phải có ít nhất 6 ký tự!";
    } elseif (!empty($mat_khau_moi) && $mat_khau_moi != $xac_nhan_mat_khau) {
        $loi = "Xác nhận mật khẩu không khớp!";
    } else {
        // Kiem tra email da ton tai chua (ngoai tru nguoi dung hien tai)
        $sql = "SELECT * FROM nguoi_dung WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $nguoi_dung_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $loi = "Email đã được sử dụng bởi người dùng khác!";
        } else {
            // Cap nhat thong tin nguoi dung
            if (!empty($mat_khau_moi)) {
                // Cap nhat ca mat khau
                $mat_khau_hash = password_hash($mat_khau_moi, PASSWORD_DEFAULT);
                $sql = "UPDATE nguoi_dung SET ho_ten = ?, email = ?, mat_khau = ?, dien_thoai = ?, dia_chi = ?, trang_thai = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssii", $ho_ten, $email, $mat_khau_hash, $dien_thoai, $dia_chi, $trang_thai, $nguoi_dung_id);
            } else {
                // Khong cap nhat mat khau
                $sql = "UPDATE nguoi_dung SET ho_ten = ?, email = ?, dien_thoai = ?, dia_chi = ?, trang_thai = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssii", $ho_ten, $email, $dien_thoai, $dia_chi, $trang_thai, $nguoi_dung_id);
            }

            if ($stmt->execute()) {
                $thong_bao = "Cập nhật người dùng thành công!";
                $hanh_dong = ''; // Reset hành động để hiển thị lại danh sách
            } else {
                $loi = "Có lỗi xảy ra khi cập nhật người dùng!";
            }
        }
    }
}

// Xu ly xoa nguoi dung
if ($hanh_dong == 'xoa' && isset($_GET['id'])) {
    $nguoi_dung_id = (int) $_GET['id'];

    // Kiem tra nguoi dung ton tai
    $sql = "SELECT * FROM nguoi_dung WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nguoi_dung_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Kiem tra nguoi dung co don hang khong
        $sql = "SELECT COUNT(*) as total FROM don_hang WHERE khach_hang_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $nguoi_dung_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['total'] > 0) {
            $loi = "Không thể xóa người dùng này vì có đơn hàng liên quan!";
        } else {
            // Xoa nguoi dung
            $sql = "DELETE FROM nguoi_dung WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $nguoi_dung_id);

            if ($stmt->execute()) {
                $thong_bao = "Xóa người dùng thành công!";
            } else {
                $loi = "Có lỗi xảy ra khi xóa người dùng!";
            }
        }
    } else {
        $loi = "Người dùng không tồn tại!";
    }
}

// Xu ly cap nhat trang thai nguoi dung
if ($hanh_dong == 'cap-nhat-trang-thai' && isset($_GET['id']) && isset($_GET['trang-thai'])) {
    $nguoi_dung_id = (int) $_GET['id'];
    $trang_thai = (int) $_GET['trang-thai'];

    $sql = "UPDATE nguoi_dung SET trang_thai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $trang_thai, $nguoi_dung_id);

    if ($stmt->execute()) {
        $thong_bao = "Cập nhật trạng thái người dùng thành công!";
    } else {
        $loi = "Có lỗi xảy ra khi cập nhật trạng thái người dùng!";
    }
}

// Lay thong tin nguoi dung can sua
$nguoi_dung_sua = null;
if ($hanh_dong == 'sua' && isset($_GET['id'])) {
    $nguoi_dung_id = (int) $_GET['id'];

    $sql = "SELECT * FROM nguoi_dung WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nguoi_dung_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $nguoi_dung_sua = $result->fetch_assoc();
    } else {
        $loi = "Người dùng không tồn tại!";
        $hanh_dong = '';
    }
}

// Xu ly loc va tim kiem
$tu_khoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
$trang_thai_loc = isset($_GET['trang-thai']) ? (int) $_GET['trang-thai'] : -1;
$trang = isset($_GET['trang']) ? (int) $_GET['trang'] : 1;
$gioi_han = 10;

// Tao cau truy van
$dieu_kien = array();
$params = array();
$types = '';

if (!empty($tu_khoa)) {
    $dieu_kien[] = "(ho_ten LIKE ? OR email LIKE ? OR dien_thoai LIKE ?)";
    $tu_khoa_search = "%$tu_khoa%";
    $params[] = $tu_khoa_search;
    $params[] = $tu_khoa_search;
    $params[] = $tu_khoa_search;
    $types .= 'sss';
}

if ($trang_thai_loc >= 0) {
    $dieu_kien[] = "trang_thai = ?";
    $params[] = $trang_thai_loc;
    $types .= 'i';
}

$where = '';
if (!empty($dieu_kien)) {
    $where = "WHERE " . implode(' AND ', $dieu_kien);
}

// Dem tong so nguoi dung
$sql_count = "SELECT COUNT(*) as total FROM nguoi_dung $where";
$stmt_count = $conn->prepare($sql_count);

if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$tong_so_nguoi_dung = $row_count['total'];
$tong_so_trang = ceil($tong_so_nguoi_dung / $gioi_han);

// Tính toán phân trang - đảm bảo không âm
$trang = max(1, $trang); // Đảm bảo trang luôn >= 1
$bat_dau = ($trang - 1) * $gioi_han;

// Lay danh sach nguoi dung
$sql = "SELECT * FROM nguoi_dung $where ORDER BY id DESC LIMIT ?, ?";

$params[] = $bat_dau;
$params[] = $gioi_han;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$nguoi_dung_list = array();
while ($row = $result->fetch_assoc()) {
    $nguoi_dung_list[] = $row;
}
?>

<div class="nguoi-dung-page">
  <div class="page-header">
      <h1>Quản Lý Người Dùng</h1>
      <?php if ($hanh_dong != 'them' && $hanh_dong != 'sua'): ?>
          <button type="button" class="btn-them-moi" onclick="location.href='index.php?trang=nguoi-dung&hanh-dong=them'">
              <i class="fas fa-plus"></i> Thêm người dùng mới
          </button>
      <?php endif; ?>
  </div>
  
  <?php if (!empty($thong_bao)): ?>
      <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
  <?php endif; ?>
  
  <?php if (!empty($loi)): ?>
      <div class="thong-bao-loi"><?php echo $loi; ?></div>
  <?php endif; ?>
  
  <?php if ($hanh_dong == 'them'): ?>
      <!-- Form thêm người dùng -->
      <div class="form-container">
          <h2>Thêm Người Dùng Mới</h2>
          <form method="POST" action="index.php?trang=nguoi-dung&hanh-dong=them">
              <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="ho_ten">Họ tên <span class="required">*</span></label>
                      <input type="text" id="ho_ten" name="ho_ten" value="<?php echo isset($_POST['ho_ten']) ? $_POST['ho_ten'] : ''; ?>" required>
                  </div>
                  
                  <div class="form-group col-md-6">
                      <label for="email">Email <span class="required">*</span></label>
                      <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                  </div>
              </div>
              
              <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="mat_khau">Mật khẩu <span class="required">*</span></label>
                      <input type="password" id="mat_khau" name="mat_khau" required>
                      <small>Mật khẩu phải có ít nhất 6 ký tự</small>
                  </div>
                  
                  <div class="form-group col-md-6">
                      <label for="xac_nhan_mat_khau">Xác nhận mật khẩu <span class="required">*</span></label>
                      <input type="password" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau" required>
                  </div>
              </div>
              
              <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="dien_thoai">Điện thoại</label>
                      <input type="text" id="dien_thoai" name="dien_thoai" value="<?php echo isset($_POST['dien_thoai']) ? $_POST['dien_thoai'] : ''; ?>">
                  </div>
                  
                  <div class="form-group col-md-6">
                      <div class="checkbox" style="margin-top: 32px;">
                          <input type="checkbox" id="trang_thai" name="trang_thai" value="1" <?php echo (!isset($_POST['trang_thai']) || $_POST['trang_thai'] == 1) ? 'checked' : ''; ?>>
                          <label for="trang_thai">Kích hoạt tài khoản</label>
                      </div>
                  </div>
              </div>
              
              <div class="form-group">
                  <label for="dia_chi">Địa chỉ</label>
                  <textarea id="dia_chi" name="dia_chi" rows="3"><?php echo isset($_POST['dia_chi']) ? $_POST['dia_chi'] : ''; ?></textarea>
              </div>
              
              <div class="form-actions">
                  <button type="submit" class="btn-luu">
                      <i class="fas fa-save"></i> Lưu người dùng
                  </button>
                  <a href="index.php?trang=nguoi-dung" class="btn-huy">Hủy</a>
              </div>
          </form>
      </div>
  <?php elseif ($hanh_dong == 'sua' && $nguoi_dung_sua): ?>
      <!-- Form sửa người dùng -->
      <div class="form-container">
          <h2>Sửa Thông Tin Người Dùng</h2>
          <form method="POST" action="index.php?trang=nguoi-dung&hanh-dong=sua">
              <input type="hidden" name="nguoi_dung_id" value="<?php echo $nguoi_dung_sua['id']; ?>">
              
              <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="ho_ten">Họ tên <span class="required">*</span></label>
                      <input type="text" id="ho_ten" name="ho_ten" value="<?php echo $nguoi_dung_sua['ho_ten']; ?>" required>
                  </div>
                  
                  <div class="form-group col-md-6">
                      <label for="email">Email <span class="required">*</span></label>
                      <input type="email" id="email" name="email" value="<?php echo $nguoi_dung_sua['email']; ?>" required>
                  </div>
              </div>
              
              <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="mat_khau_moi">Mật khẩu mới</label>
                      <input type="password" id="mat_khau_moi" name="mat_khau_moi">
                      <small>Để trống nếu không muốn thay đổi mật khẩu</small>
                  </div>
                  
                  <div class="form-group col-md-6">
                      <label for="xac_nhan_mat_khau">Xác nhận mật khẩu mới</label>
                      <input type="password" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau">
                  </div>
              </div>
              
              <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="dien_thoai">Điện thoại</label>
                      <input type="text" id="dien_thoai" name="dien_thoai" value="<?php echo $nguoi_dung_sua['dien_thoai']; ?>">
                  </div>
                  
                  <div class="form-group col-md-6">
                      <div class="checkbox" style="margin-top: 32px;">
                          <input type="checkbox" id="trang_thai" name="trang_thai" value="1" <?php echo $nguoi_dung_sua['trang_thai'] ? 'checked' : ''; ?>>
                          <label for="trang_thai">Kích hoạt tài khoản</label>
                      </div>
                  </div>
              </div>
              
              <div class="form-group">
                  <label for="dia_chi">Địa chỉ</label>
                  <textarea id="dia_chi" name="dia_chi" rows="3"><?php echo $nguoi_dung_sua['dia_chi']; ?></textarea>
              </div>
              
              <div class="form-actions">
                  <button type="submit" class="btn-luu">
                      <i class="fas fa-save"></i> Lưu thay đổi
                  </button>
                  <a href="index.php?trang=nguoi-dung" class="btn-huy">Hủy</a>
              </div>
          </form>
      </div>
  <?php else: ?>
      <!-- Danh sách người dùng -->
      <div class="bo-loc">
          <form action="" method="GET" class="form-loc">
              <input type="hidden" name="trang" value="nguoi-dung">
              
              <div class="form-group">
                  <label for="trang-thai">Trạng thái:</label>
                  <select name="trang-thai" id="trang-thai">
                      <option value="-1" <?php echo $trang_thai_loc == -1 ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                      <option value="1" <?php echo $trang_thai_loc == 1 ? 'selected' : ''; ?>>Đã kích hoạt</option>
                      <option value="0" <?php echo $trang_thai_loc == 0 ? 'selected' : ''; ?>>Chưa kích hoạt</option>
                  </select>
              </div>
              
              <div class="form-group">
                  <label for="tu_khoa">Tìm kiếm:</label>
                  <input type="text" name="tu_khoa" id="tu_khoa" value="<?php echo $tu_khoa; ?>" placeholder="Tên, email, điện thoại...">
              </div>
              
              <div class="form-group">
                  <button type="submit" class="btn-loc">Lọc</button>
                  <a href="index.php?trang=nguoi-dung" class="btn-dat-lai">Đặt lại</a>
              </div>
          </form>
      </div>
      
      <div class="danh-sach-nguoi-dung">
          <table>
              <thead>
                  <tr>
                      <th width="5%">ID</th>
                      <th width="20%">Họ tên</th>
                      <th width="20%">Email</th>
                      <th width="15%">Điện thoại</th>
                      <th width="15%">Ngày tạo</th>
                      <th width="10%">Trạng thái</th>
                      <th width="15%">Thao tác</th>
                  </tr>
              </thead>
              <tbody>
                  <?php if (!empty($nguoi_dung_list)): ?>
                      <?php foreach ($nguoi_dung_list as $nguoi_dung): ?>
                          <tr>
                              <td><?php echo $nguoi_dung['id']; ?></td>
                              <td><?php echo $nguoi_dung['ho_ten']; ?></td>
                              <td><?php echo $nguoi_dung['email']; ?></td>
                              <td><?php echo $nguoi_dung['dien_thoai']; ?></td>
                              <td><?php echo date('d/m/Y', strtotime($nguoi_dung['ngay_tao'])); ?></td>
                              <td>
                                  <a href="index.php?trang=nguoi-dung&hanh-dong=cap-nhat-trang-thai&id=<?php echo $nguoi_dung['id']; ?>&trang-thai=<?php echo $nguoi_dung['trang_thai'] ? 0 : 1; ?>" class="btn-trang-thai <?php echo $nguoi_dung['trang_thai'] ? 'active' : ''; ?>">
                                      <?php echo $nguoi_dung['trang_thai'] ? 'Đã kích hoạt' : 'Chưa kích hoạt'; ?>
                                  </a>
                              </td>
                              <td class="hanh-dong">
                                  <a href="index.php?trang=nguoi-dung&hanh-dong=sua&id=<?php echo $nguoi_dung['id']; ?>" class="btn-sua">
                                      <i class="fas fa-edit"></i> Sửa
                                  </a>
                                  <a href="index.php?trang=nguoi-dung&hanh-dong=xoa&id=<?php echo $nguoi_dung['id']; ?>" class="btn-xoa" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                      <i class="fas fa-trash"></i> Xóa
                                  </a>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  <?php else: ?>
                      <tr>
                          <td colspan="7" class="khong-co-du-lieu">Không có người dùng nào.</td>
                      </tr>
                  <?php endif; ?>
              </tbody>
          </table>
      </div>
      
      <?php if ($tong_so_trang > 1): ?>
          <div class="phan-trang">
              <?php
              // Tao URL co so cho phan trang
              $url_co_so = "index.php?trang=nguoi-dung";
              if (!empty($tu_khoa)) {
                  $url_co_so .= "&tu_khoa=$tu_khoa";
              }
              if ($trang_thai_loc >= 0) {
                  $url_co_so .= "&trang-thai=$trang_thai_loc";
              }

              // Nut trang truoc
              if ($trang > 1) {
                  echo '<a href="' . $url_co_so . '&trang=' . ($trang - 1) . '" class="trang-truoc">Trước</a>';
              }

              // Cac trang
              $bat_dau = max(1, $trang - 2);
              $ket_thuc = min($tong_so_trang, $trang + 2);

              for ($i = $bat_dau; $i <= $ket_thuc; $i++) {
                  if ($i == $trang) {
                      echo '<span class="trang-hien-tai">' . $i . '</span>';
                  } else {
                      echo '<a href="' . $url_co_so . '&trang=' . $i . '">' . $i . '</a>';
                  }
              }

              // Nut trang sau
              if ($trang < $tong_so_trang) {
                  echo '<a href="' . $url_co_so . '&trang=' . ($trang + 1) . '" class="trang-sau">Sau</a>';
              }
              ?>
          </div>
      <?php endif; ?>
  <?php endif; ?>
</div>

