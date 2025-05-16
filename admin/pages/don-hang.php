<?php
// Kiem tra quyen truy cap
if (!isset($_SESSION['admin_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

// Xử lý POST form cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['don_hang_id']) && isset($_POST['trang_thai'])) {
    $don_hang_id = (int) $_POST['don_hang_id'];
    $trang_thai_moi = (int) $_POST['trang_thai'];
    $trang_thai_cu = (int) $_POST['trang_thai_cu'];

    // Cập nhật trạng thái đơn hàng
    $sql = "UPDATE don_hang SET trang_thai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $trang_thai_moi, $don_hang_id);

    if ($stmt->execute()) {
        // Cập nhật số lượng sản phẩm
        cap_nhat_so_luong_san_pham($conn, $don_hang_id, $trang_thai_moi, $trang_thai_cu);
        $thong_bao = "Cập nhật trạng thái đơn hàng thành công!";
    } else {
        $loi = "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!";
    }
}

// Xu ly hanh dong
$hanh_dong = isset($_GET['hanh-dong']) ? $_GET['hanh-dong'] : '';
$thong_bao = '';
$loi = '';


// Xu ly cap nhat trang thai don hang
if ($hanh_dong == 'cap-nhat-trang-thai' && isset($_GET['id']) && isset($_GET['trang-thai'])) {
    $don_hang_id = (int) $_GET['id'];
    $trang_thai = (int) $_GET['trang-thai'];

    // Kiem tra don hang ton tai
    $sql = "SELECT * FROM don_hang WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $don_hang_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $don_hang = $result->fetch_assoc();
        $trang_thai_cu = $don_hang['trang_thai'];

        // Cap nhat trang thai don hang
        $sql = "UPDATE don_hang SET trang_thai = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $trang_thai, $don_hang_id);

        if ($stmt->execute()) {
            // Cập nhật số lượng sản phẩm
            cap_nhat_so_luong_san_pham($conn, $don_hang_id, $trang_thai, $trang_thai_cu);
            $thong_bao = "Cập nhật trạng thái đơn hàng thành công!";
        } else {
            $loi = "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!";
        }
    } else {
        $loi = "Đơn hàng không tồn tại!";
    }
}

// Xu ly xoa don hang
if ($hanh_dong == 'xoa' && isset($_GET['id'])) {
    $don_hang_id = (int) $_GET['id'];

    // Kiem tra don hang ton tai
    $sql = "SELECT * FROM don_hang WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $don_hang_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Xoa chi tiet don hang
        $sql = "DELETE FROM chi_tiet_don_hang WHERE don_hang_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $don_hang_id);
        $stmt->execute();

        // Xoa don hang
        $sql = "DELETE FROM don_hang WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $don_hang_id);

        if ($stmt->execute()) {
            $thong_bao = "Xóa đơn hàng thành công!";
        } else {
            $loi = "Có lỗi xảy ra khi xóa đơn hàng!";
        }
    } else {
        $loi = "Đơn hàng không tồn tại!";
    }
}

// Xu ly loc va tim kiem
$tu_khoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
$trang_thai_loc = isset($_GET['trang-thai']) ? (int) $_GET['trang-thai'] : -1;
$trang = isset($_GET['page']) ? (int) $_GET['page'] : 1;
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

// Dem tong so don hang
$sql_count = "SELECT COUNT(*) as total FROM don_hang $where";
$stmt_count = $conn->prepare($sql_count);

if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$tong_so_don_hang = $row_count['total'];
$tong_so_trang = ceil($tong_so_don_hang / $gioi_han);

// Tính toán phân trang - đảm bảo không âm
$trang = max(1, $trang); // Đảm bảo trang luôn >= 1
$bat_dau = ($trang - 1) * $gioi_han;

// Lay danh sach don hang
$sql = "SELECT * FROM don_hang $where ORDER BY id DESC LIMIT ?, ?";

$params[] = $bat_dau;
$params[] = $gioi_han;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$don_hang_list = array();
while ($row = $result->fetch_assoc()) {
    $don_hang_list[] = $row;
}

// Lay chi tiet don hang
$chi_tiet_don_hang = null;
if ($hanh_dong == 'xem' && isset($_GET['id'])) {
    $don_hang_id = (int) $_GET['id'];

    // Lay thong tin don hang
    $sql = "SELECT * FROM don_hang WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $don_hang_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $chi_tiet_don_hang = $result->fetch_assoc();

        // Lay chi tiet san pham trong don hang
        $chi_tiet_don_hang['san_pham'] = lay_chi_tiet_don_hang($conn, $don_hang_id);
    } else {
        $loi = "Đơn hàng không tồn tại!";
        $hanh_dong = '';
    }
}
?>

<div class="don-hang-page">
  <div class="page-header">
      <h1>Quản Lý Đơn Hàng</h1>
  </div>
  
  <?php if (!empty($thong_bao)): ?>
      <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
  <?php endif; ?>
  
  <?php if (!empty($loi)): ?>
      <div class="thong-bao-loi"><?php echo $loi; ?></div>
  <?php endif; ?>
  
  <?php if ($hanh_dong == 'xem' && $chi_tiet_don_hang): ?>
      <!-- Chi tiết đơn hàng -->
      <div class="chi-tiet-don-hang-container">
          <div class="page-header">
              <h2>Chi Tiết Đơn Hàng #<?php echo $chi_tiet_don_hang['id']; ?></h2>
              <a href="index.php?trang=don-hang" class="btn-quay-lai">
                  <i class="fas fa-arrow-left"></i> Quay lại danh sách
              </a>
          </div>
          
          <div class="chi-tiet-don-hang-content">
              <div class="thong-tin-don-hang">
                  <h3>Thông Tin Đơn Hàng</h3>
                  <table class="bang-thong-tin">
                      <tr>
                          <th>Mã đơn hàng:</th>
                          <td>#<?php echo $chi_tiet_don_hang['id']; ?></td>
                      </tr>
                      <tr>
                          <th>Ngày đặt:</th>
                          <td><?php echo date('d/m/Y H:i', strtotime($chi_tiet_don_hang['ngay_tao'])); ?></td>
                      </tr>
                      <tr>
                          <th>Trạng thái:</th>
                          <td>
                              <form method="POST" action="" class="form-trang-thai">
                                  <input type="hidden" name="don_hang_id" value="<?php echo $chi_tiet_don_hang['id']; ?>">
                                  <input type="hidden" name="trang_thai_cu" value="<?php echo $chi_tiet_don_hang['trang_thai']; ?>">
                                  <select name="trang_thai" id="trang_thai" onchange="this.form.submit()">
                                      <option value="0" <?php echo $chi_tiet_don_hang['trang_thai'] == 0 ? 'selected' : ''; ?>>Mới</option>
                                      <option value="1" <?php echo $chi_tiet_don_hang['trang_thai'] == 1 ? 'selected' : ''; ?>>Đang xử lý</option>
                                      <option value="2" <?php echo $chi_tiet_don_hang['trang_thai'] == 2 ? 'selected' : ''; ?>>Đang giao</option>
                                      <option value="3" <?php echo $chi_tiet_don_hang['trang_thai'] == 3 ? 'selected' : ''; ?>>Hoàn thành</option>
                                      <option value="4" <?php echo $chi_tiet_don_hang['trang_thai'] == 4 ? 'selected' : ''; ?>>Đã hủy</option>
                                  </select>
                              </form>
                          </td>
                      </tr>
                      <tr>
                          <th>Tổng tiền:</th>
                          <td><strong><?php echo dinh_dang_tien($chi_tiet_don_hang['tong_tien']); ?></strong></td>
                      </tr>
                  </table>
              </div>
              
              <div class="thong-tin-khach-hang">
                  <h3>Thông Tin Khách Hàng</h3>
                  <table class="bang-thong-tin">
                      <tr>
                          <th>Họ tên:</th>
                          <td><?php echo $chi_tiet_don_hang['ho_ten']; ?></td>
                      </tr>
                      <tr>
                          <th>Email:</th>
                          <td><?php echo $chi_tiet_don_hang['email']; ?></td>
                      </tr>
                      <tr>
                          <th>Điện thoại:</th>
                          <td><?php echo $chi_tiet_don_hang['dien_thoai']; ?></td>
                      </tr>
                      <tr>
                          <th>Địa chỉ:</th>
                          <td><?php echo $chi_tiet_don_hang['dia_chi']; ?></td>
                      </tr>
                      <tr>
                          <th>Ghi chú:</th>
                          <td><?php echo !empty($chi_tiet_don_hang['ghi_chu']) ? $chi_tiet_don_hang['ghi_chu'] : 'Không có'; ?></td>
                      </tr>
                  </table>
              </div>
          </div>
          
          <div class="san-pham-don-hang">
              <h3>Sản Phẩm Đã Đặt</h3>
              <table>
                  <thead>
                      <tr>
                          <th>Sản phẩm</th>
                          <th>Giá</th>
                          <th>Số lượng</th>
                          <th>Thành tiền</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($chi_tiet_don_hang['san_pham'] as $item): ?>
                          <tr>
                              <td class="san-pham-info">
                                  <img src="../uploads/<?php echo $item['hinh_anh']; ?>" alt="<?php echo $item['ten_san_pham']; ?>" class="hinh-anh-nho">
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
                          <td><strong><?php echo dinh_dang_tien($chi_tiet_don_hang['tong_tien']); ?></strong></td>
                      </tr>
                  </tfoot>
              </table>
          </div>
          
          <div class="hanh-dong-don-hang">
              <a href="index.php?trang=don-hang&hanh-dong=xoa&id=<?php echo $chi_tiet_don_hang['id']; ?>" class="btn-xoa" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                  <i class="fas fa-trash"></i> Xóa đơn hàng
              </a>
              
              <button type="button" class="btn-in" onclick="window.print()">
                  <i class="fas fa-print"></i> In đơn hàng
              </button>
          </div>
      </div>
  <?php else: ?>
      <!-- Danh sách đơn hàng -->
      <div class="bo-loc">
          <form action="" method="GET" class="form-loc">
              <input type="hidden" name="trang" value="don-hang">
              
              <div class="form-group">
                  <label for="trang-thai">Trạng thái:</label>
                  <select name="trang-thai" id="trang-thai">
                      <option value="-1" <?php echo $trang_thai_loc == -1 ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                      <option value="0" <?php echo $trang_thai_loc == 0 ? 'selected' : ''; ?>>Mới</option>
                      <option value="1" <?php echo $trang_thai_loc == 1 ? 'selected' : ''; ?>>Đang xử lý</option>
                      <option value="2" <?php echo $trang_thai_loc == 2 ? 'selected' : ''; ?>>Đang giao</option>
                      <option value="3" <?php echo $trang_thai_loc == 3 ? 'selected' : ''; ?>>Hoàn thành</option>
                      <option value="4" <?php echo $trang_thai_loc == 4 ? 'selected' : ''; ?>>Đã hủy</option>
                  </select>
              </div>
              
              <div class="form-group">
                  <label for="tu_khoa">Tìm kiếm:</label>
                  <input type="text" name="tu_khoa" id="tu_khoa" value="<?php echo $tu_khoa; ?>" placeholder="Tên, email, điện thoại...">
              </div>
              
              <div class="form-group">
                  <button type="submit" class="btn-loc">Lọc</button>
                  <a href="index.php?trang=don-hang" class="btn-dat-lai">Đặt lại</a>
              </div>
          </form>
      </div>
      
      <div class="danh-sach-don-hang">
          <table>
              <thead>
                  <tr>
                      <th width="5%">ID</th>
                      <th width="20%">Khách hàng</th>
                      <th width="15%">Điện thoại</th>
                      <th width="15%">Ngày đặt</th>
                      <th width="15%">Tổng tiền</th>
                      <th width="15%">Trạng thái</th>
                      <th width="15%">Thao tác</th>
                  </tr>
              </thead>
              <tbody>
                  <?php if (!empty($don_hang_list)): ?>
                      <?php foreach ($don_hang_list as $don_hang): ?>
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
                              <td><?php echo $don_hang['dien_thoai']; ?></td>
                              <td><?php echo date('d/m/Y', strtotime($don_hang['ngay_tao'])); ?></td>
                              <td><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></td>
                              <td><span class="trang-thai <?php echo $trang_thai_class; ?>"><?php echo $trang_thai_text; ?></span></td>
                              <td class="hanh-dong">
                                  <a href="index.php?trang=chi-tiet-don-hang&id=<?php echo $don_hang['id']; ?>" class="btn-xem">
                                      <i class="fas fa-eye"></i> Xem
                                  </a>
                                  <a href="index.php?trang=don-hang&hanh-dong=xoa&id=<?php echo $don_hang['id']; ?>" class="btn-xoa" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                                      <i class="fas fa-trash"></i> Xóa
                                  </a>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  <?php else: ?>
                      <tr>
                          <td colspan="7" class="khong-co-du-lieu">Không có đơn hàng nào.</td>
                      </tr>
                  <?php endif; ?>
              </tbody>
          </table>
      </div>
      
      <?php if ($tong_so_trang > 1): ?>
          <div class="phan-trang">
              <?php
              // Tao URL co so cho phan trang
              $url_co_so = "index.php?trang=don-hang";
              if (!empty($tu_khoa)) {
                  $url_co_so .= "&tu_khoa=$tu_khoa";
              }
              if ($trang_thai_loc >= 0) {
                  $url_co_so .= "&trang-thai=$trang_thai_loc";
              }

              // Nut trang truoc
              if ($trang > 1) {
                  echo '<a href="' . $url_co_so . '&page=' . ($trang - 1) . '" class="trang-truoc">Trước</a>';
              }

              // Cac trang
              $bat_dau = max(1, $trang - 2);
              $ket_thuc = min($tong_so_trang, $trang + 2);

              for ($i = $bat_dau; $i <= $ket_thuc; $i++) {
                  if ($i == $trang) {
                      echo '<span class="trang-hien-tai">' . $i . '</span>';
                  } else {
                      echo '<a href="' . $url_co_so . '&page=' . $i . '">' . $i . '</a>';
                  }
              }

              // Nut trang sau
              if ($trang < $tong_so_trang) {
                  echo '<a href="' . $url_co_so . '&page=' . ($trang + 1) . '" class="trang-sau">Sau</a>';
              }
              ?>
          </div>
      <?php endif; ?>
  <?php endif; ?>
</div>
