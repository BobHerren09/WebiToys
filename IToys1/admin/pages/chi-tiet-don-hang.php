<?php
// Kiem tra quyen truy cap
if (!isset($_SESSION['admin_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

// Lay ID don hang
$don_hang_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($don_hang_id <= 0) {
    header("Location: index.php?trang=don-hang");
    exit();
}

// Lay thong tin don hang
$sql = "SELECT * FROM don_hang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $don_hang_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?trang=don-hang");
    exit();
}

$don_hang = $result->fetch_assoc();

// Lay chi tiet san pham trong don hang
$chi_tiet_san_pham = lay_chi_tiet_don_hang($conn, $don_hang_id);

// Xu ly cap nhat trang thai don hang
$thong_bao = '';
$loi = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trang_thai'])) {
    $trang_thai_moi = (int) $_POST['trang_thai'];

    // Cap nhat trang thai don hang
    if (cap_nhat_trang_thai_don_hang($conn, $don_hang_id, $trang_thai_moi)) {
        $thong_bao = "Cập nhật trạng thái đơn hàng thành công!";

        // Cap nhat lai thong tin don hang
        $sql = "SELECT * FROM don_hang WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $don_hang_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $don_hang = $result->fetch_assoc();
    } else {
        $loi = "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!";
    }
}
?>

<div class="chi-tiet-don-hang-page">
  <div class="page-header">
      <h1>Chi Tiết Đơn Hàng #<?php echo $don_hang_id; ?></h1>
      <a href="index.php?trang=don-hang" class="btn-quay-lai">
          <i class="fas fa-arrow-left"></i> Quay lại danh sách
      </a>
  </div>
  
  <?php if (!empty($thong_bao)): ?>
      <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
  <?php endif; ?>
  
  <?php if (!empty($loi)): ?>
      <div class="thong-bao-loi"><?php echo $loi; ?></div>
  <?php endif; ?>
  
  <div class="chi-tiet-don-hang-container">
      <div class="chi-tiet-don-hang-content">
          <div class="thong-tin-don-hang">
              <h3>Thông Tin Đơn Hàng</h3>
              <table class="bang-thong-tin">
                  <tr>
                      <th>Mã đơn hàng:</th>
                      <td>#<?php echo $don_hang['id']; ?></td>
                  </tr>
                  <tr>
                      <th>Ngày đặt:</th>
                      <td><?php echo date('d/m/Y H:i', strtotime($don_hang['ngay_tao'])); ?></td>
                  </tr>
                  <tr>
                      <th>Trạng thái:</th>
                      <td>
                          <form method="POST" action="" class="form-trang-thai">
                              <select name="trang_thai" id="trang_thai" onchange="this.form.submit()">
                                  <option value="0" <?php echo $don_hang['trang_thai'] == 0 ? 'selected' : ''; ?>>Mới</option>
                                  <option value="1" <?php echo $don_hang['trang_thai'] == 1 ? 'selected' : ''; ?>>Đang xử lý</option>
                                  <option value="2" <?php echo $don_hang['trang_thai'] == 2 ? 'selected' : ''; ?>>Đang giao</option>
                                  <option value="3" <?php echo $don_hang['trang_thai'] == 3 ? 'selected' : ''; ?>>Hoàn thành</option>
                                  <option value="4" <?php echo $don_hang['trang_thai'] == 4 ? 'selected' : ''; ?>>Đã hủy</option>
                              </select>
                          </form>
                      </td>
                  </tr>
                  <tr>
                      <th>Tổng tiền:</th>
                      <td><strong><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></strong></td>
                  </tr>
              </table>
          </div>
          
          <div class="thong-tin-khach-hang">
              <h3>Thông Tin Khách Hàng</h3>
              <table class="bang-thong-tin">
                  <tr>
                      <th>Họ tên:</th>
                      <td><?php echo $don_hang['ho_ten']; ?></td>
                  </tr>
                  <tr>
                      <th>Email:</th>
                      <td><?php echo $don_hang['email']; ?></td>
                  </tr>
                  <tr>
                      <th>Điện thoại:</th>
                      <td><?php echo $don_hang['dien_thoai']; ?></td>
                  </tr>
                  <tr>
                      <th>Địa chỉ:</th>
                      <td><?php echo $don_hang['dia_chi']; ?></td>
                  </tr>
                  <tr>
                      <th>Ghi chú:</th>
                      <td><?php echo !empty($don_hang['ghi_chu']) ? $don_hang['ghi_chu'] : 'Không có'; ?></td>
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
                  <?php if (count($chi_tiet_san_pham) > 0): ?>
                      <?php foreach ($chi_tiet_san_pham as $item): ?>
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
                  <?php else: ?>
                      <tr>
                          <td colspan="4" class="khong-co-du-lieu">Không có sản phẩm nào trong đơn hàng này.</td>
                      </tr>
                  <?php endif; ?>
              </tbody>
              <tfoot>
                  <tr>
                      <td colspan="3" class="tong-cong">Tổng cộng:</td>
                      <td><strong><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></strong></td>
                  </tr>
              </tfoot>
          </table>
      </div>
      
      <div class="hanh-dong-don-hang">
          <a href="index.php?trang=don-hang&hanh-dong=xoa&id=<?php echo $don_hang['id']; ?>" class="btn-xoa" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
              <i class="fas fa-trash"></i> Xóa đơn hàng
          </a>
          
          <button type="button" class="btn-in" onclick="window.print()">
              <i class="fas fa-print"></i> In đơn hàng
          </button>
      </div>
  </div>
</div>

