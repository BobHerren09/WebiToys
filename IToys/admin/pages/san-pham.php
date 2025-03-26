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

// Xu ly xoa san pham
if ($hanh_dong == 'xoa' && isset($_GET['id'])) {
    $san_pham_id = (int) $_GET['id'];

    // Kiem tra san pham ton tai
    $sql = "SELECT * FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $san_pham_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $san_pham = $result->fetch_assoc();
        $hinh_anh = $san_pham['hinh_anh'];

        // Xoa hinh anh san pham
        $sql = "DELETE FROM hinh_anh_san_pham WHERE san_pham_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $san_pham_id);
        $stmt->execute();

        // Xoa san pham
        $sql = "DELETE FROM san_pham WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $san_pham_id);

        if ($stmt->execute()) {
            // Xoa file hinh anh
            if (!empty($hinh_anh) && file_exists("../uploads/$hinh_anh")) {
                unlink("../uploads/$hinh_anh");
            }

            $thong_bao = "Xóa sản phẩm thành công!";
        } else {
            $loi = "Có lỗi xảy ra khi xóa sản phẩm!";
        }
    } else {
        $loi = "Sản phẩm không tồn tại!";
    }
}

// Xu ly cap nhat trang thai san pham
if ($hanh_dong == 'cap-nhat-trang-thai' && isset($_GET['id']) && isset($_GET['trang-thai'])) {
    $san_pham_id = (int) $_GET['id'];
    $trang_thai = (int) $_GET['trang-thai'];

    $sql = "UPDATE san_pham SET trang_thai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $trang_thai, $san_pham_id);

    if ($stmt->execute()) {
        $thong_bao = "Cập nhật trạng thái sản phẩm thành công!";
    } else {
        $loi = "Có lỗi xảy ra khi cập nhật trạng thái sản phẩm!";
    }
}

// Xu ly cap nhat san pham noi bat
if ($hanh_dong == 'cap-nhat-noi-bat' && isset($_GET['id']) && isset($_GET['noi-bat'])) {
    $san_pham_id = (int) $_GET['id'];
    $noi_bat = (int) $_GET['noi-bat'];

    $sql = "UPDATE san_pham SET noi_bat = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $noi_bat, $san_pham_id);

    if ($stmt->execute()) {
        $thong_bao = "Cập nhật sản phẩm nổi bật thành công!";
    } else {
        $loi = "Có lỗi xảy ra khi cập nhật sản phẩm nổi bật!";
    }
}

// Xu ly loc va tim kiem
$danh_muc_id = isset($_GET['danh-muc']) ? (int) $_GET['danh-muc'] : 0;
$tu_khoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
$trang_thai_loc = isset($_GET['trang-thai']) ? (int) $_GET['trang-thai'] : -1;
$trang = isset($_GET['trang']) ? (int) $_GET['trang'] : 1;
$gioi_han = 10;

// Tạo câu truy vấn đơn giản thay vì prepared statement
$where_clause = "";
$join_clause = "LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id";

if ($danh_muc_id > 0) {
    $where_clause .= " WHERE sp.danh_muc_id = $danh_muc_id";
} else {
    $where_clause .= " WHERE 1=1"; // Luôn đúng để dễ thêm điều kiện
}

if (!empty($tu_khoa)) {
    $tu_khoa_escaped = $conn->real_escape_string($tu_khoa);
    $where_clause .= " AND (sp.ten_san_pham LIKE '%$tu_khoa_escaped%' OR sp.mo_ta_ngan LIKE '%$tu_khoa_escaped%')";
}

if ($trang_thai_loc >= 0) {
    $where_clause .= " AND sp.trang_thai = $trang_thai_loc";
}

// Đếm tổng số sản phẩm
$sql_count = "SELECT COUNT(*) as total FROM san_pham sp $where_clause";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$tong_so_san_pham = $row_count['total'];
$tong_so_trang = ceil($tong_so_san_pham / $gioi_han);

// Tính toán phân trang - đảm bảo không âm
$trang = max(1, $trang); // Đảm bảo trang luôn >= 1
$bat_dau = ($trang - 1) * $gioi_han;

// Lấy danh sách sản phẩm
$sql = "SELECT sp.*, dm.ten_danh_muc 
      FROM san_pham sp 
      $join_clause
      $where_clause 
      ORDER BY sp.id DESC 
      LIMIT $bat_dau, $gioi_han";

$result = $conn->query($sql);

// Kiểm tra thư mục uploads
$upload_dir = __DIR__ . "/../../uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Lay danh sach danh muc
$sql_dm = "SELECT * FROM danh_muc ORDER BY thu_tu ASC";
$result_dm = $conn->query($sql_dm);
$danh_muc_list = array();
while ($row_dm = $result_dm->fetch_assoc()) {
    $danh_muc_list[] = $row_dm;
}
?>

<div class="san-pham-page">
    <div class="page-header">
        <h1>Quản Lý Sản Phẩm</h1>
        <a href="index.php?trang=them-san-pham" class="btn-them-moi">
            <i class="fas fa-plus"></i> Thêm sản phẩm mới
        </a>
    </div>
    
    <?php if (!empty($thong_bao)): ?>
        <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($loi)): ?>
        <div class="thong-bao-loi"><?php echo $loi; ?></div>
    <?php endif; ?>
    
    <div class="bo-loc">
        <form action="" method="GET" class="form-loc">
            <input type="hidden" name="trang" value="san-pham">
            
            <div class="form-group">
                <label for="danh-muc">Danh mục:</label>
                <select name="danh-muc" id="danh-muc">
                    <option value="0">Tất cả danh mục</option>
                    <?php foreach ($danh_muc_list as $dm): ?>
                        <option value="<?php echo $dm['id']; ?>" <?php echo $danh_muc_id == $dm['id'] ? 'selected' : ''; ?>>
                            <?php echo $dm['ten_danh_muc']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="trang-thai">Trạng thái:</label>
                <select name="trang-thai" id="trang-thai">
                    <option value="-1" <?php echo $trang_thai_loc == -1 ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                    <option value="1" <?php echo $trang_thai_loc == 1 ? 'selected' : ''; ?>>Hiển thị</option>
                    <option value="0" <?php echo $trang_thai_loc == 0 ? 'selected' : ''; ?>>Ẩn</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="tu_khoa">Tìm kiếm:</label>
                <input type="text" name="tu_khoa" id="tu_khoa" value="<?php echo $tu_khoa; ?>" placeholder="Tên sản phẩm...">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-loc">Lọc</button>
                <a href="index.php?trang=san-pham" class="btn-dat-lai">Đặt lại</a>
            </div>
        </form>
    </div>
    
    <div class="danh-sach-san-pham">
  <table>
      <thead>
          <tr>
              <th width="5%">ID</th>
              <th width="10%">Hình ảnh</th>
              <th width="25%">Tên sản phẩm</th>
              <th width="15%">Danh mục</th>
              <th width="10%">Giá</th>
              <th width="10%">Nổi bật</th>
              <th width="10%">Trạng thái</th>
              <th width="15%">Thao tác</th>
          </tr>
      </thead>
      <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($san_pham = $result->fetch_assoc()): ?>
                  <tr>
                      <td><?php echo $san_pham['id']; ?></td>
                      <td>
                          <?php
                          $hinh_anh_path = "../uploads/" . $san_pham['hinh_anh'];
                          $hinh_anh = file_exists($hinh_anh_path) ? $san_pham['hinh_anh'] : 'placeholder.jpg';
                          ?>
                          <img src="../uploads/<?php echo $hinh_anh; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>" class="hinh-anh-nho">
                      </td>
                      <td><?php echo $san_pham['ten_san_pham']; ?></td>
                      <td><?php echo $san_pham['ten_danh_muc']; ?></td>
                      <td><?php echo dinh_dang_tien($san_pham['gia']); ?></td>
                      <td>
                          <a href="index.php?trang=san-pham&hanh-dong=cap-nhat-noi-bat&id=<?php echo $san_pham['id']; ?>&noi-bat=<?php echo $san_pham['noi_bat'] ? 0 : 1; ?>" class="btn-trang-thai <?php echo $san_pham['noi_bat'] ? 'active' : ''; ?>">
                              <?php echo $san_pham['noi_bat'] ? 'Có' : 'Không'; ?>
                          </a>
                      </td>
                      <td>
                          <a href="index.php?trang=san-pham&hanh-dong=cap-nhat-trang-thai&id=<?php echo $san_pham['id']; ?>&trang-thai=<?php echo $san_pham['trang_thai'] ? 0 : 1; ?>" class="btn-trang-thai <?php echo $san_pham['trang_thai'] ? 'active' : ''; ?>">
                              <?php echo $san_pham['trang_thai'] ? 'Hiển thị' : 'Ẩn'; ?>
                          </a>
                      </td>
                      <td class="hanh-dong">
                          <a href="index.php?trang=sua-san-pham&id=<?php echo $san_pham['id']; ?>" class="btn-sua">
                              <i class="fas fa-edit"></i> Sửa
                          </a>
                          <a href="index.php?trang=san-pham&hanh-dong=xoa&id=<?php echo $san_pham['id']; ?>" class="btn-xoa" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                              <i class="fas fa-trash"></i> Xóa
                          </a>
                      </td>
                  </tr>
              <?php endwhile; ?>
          <?php else: ?>
              <tr>
                  <td colspan="8" class="khong-co-du-lieu">
                      Không có sản phẩm nào.
                      <?php
                      // Thêm debug info
                      $sql_check = "SELECT COUNT(*) as total FROM san_pham";
                      $result_check = $conn->query($sql_check);
                      if ($result_check) {
                          $row_check = $result_check->fetch_assoc();
                          echo '<br>Tổng số sản phẩm trong database: ' . $row_check['total'];
                      }

                      // Hiển thị câu truy vấn SQL để debug
                      echo '<br>Câu truy vấn: ' . $sql;
                      ?>
                  </td>
              </tr>
          <?php endif; ?>
      </tbody>
  </table>
</div>
    
    <?php if ($tong_so_trang > 1): ?>
        <div class="phan-trang">
            <?php
            // Tao URL co so cho phan trang
            $url_co_so = "index.php?trang=san-pham";
            if ($danh_muc_id > 0) {
                $url_co_so .= "&danh-muc=$danh_muc_id";
            }
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
</div>

