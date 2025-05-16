<?php
// Xu ly loc san pham
$danh_muc_id = isset($_GET['danh-muc']) ? (int) $_GET['danh-muc'] : null;
$tu_khoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
$sap_xep = isset($_GET['sap-xep']) ? $_GET['sap-xep'] : 'moi-nhat';
$trang = isset($_GET['trang']) ? (int) $_GET['trang'] : 1;
$gioi_han = 12;

// Kiểm tra thư mục uploads
$upload_dir = __DIR__ . "/../uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Debug: Kiểm tra kết nối database
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Debug: Kiểm tra số lượng sản phẩm trong database
$sql_check = "SELECT COUNT(*) as total FROM san_pham";
$result_check = $conn->query($sql_check);
$row_check = $result_check->fetch_assoc();
$total_products = $row_check['total'];

// Nếu không có sản phẩm nào, thêm sản phẩm mẫu
if ($total_products == 0) {
    $sql_insert = "INSERT INTO san_pham (ten_san_pham, danh_muc_id, mo_ta_ngan, mo_ta, gia, hinh_anh, so_luong, noi_bat, trang_thai) 
                VALUES ('Đồ chơi mẫu', 1, 'Mô tả ngắn', 'Mô tả chi tiết', 100000, 'placeholder.jpg', 10, 1, 1)";
    $conn->query($sql_insert);
}

// Sap xep
$order_by = "ORDER BY id DESC"; // Mac dinh: moi nhat
if ($sap_xep == 'gia-thap-den-cao') {
    $order_by = "ORDER BY gia ASC";
} elseif ($sap_xep == 'gia-cao-den-thap') {
    $order_by = "ORDER BY gia DESC";
} elseif ($sap_xep == 'ten-a-z') {
    $order_by = "ORDER BY ten_san_pham ASC";
} elseif ($sap_xep == 'ten-z-a') {
    $order_by = "ORDER BY ten_san_pham DESC";
}

// Tính toán phân trang - đảm bảo không âm
$trang = max(1, $trang); // Đảm bảo trang luôn >= 1
$bat_dau = ($trang - 1) * $gioi_han;

// Sử dụng truy vấn đơn giản thay vì prepared statement để debug
// Sửa truy vấn SQL để chỉ lấy sản phẩm có trạng thái hiển thị
$where_clause = " WHERE trang_thai = 1";
if ($danh_muc_id) {
    $where_clause .= " AND danh_muc_id = $danh_muc_id";
}

if (!empty($tu_khoa)) {
    $tu_khoa_escaped = $conn->real_escape_string($tu_khoa);
    $where_clause .= " AND (ten_san_pham LIKE '%$tu_khoa_escaped%' OR mo_ta_ngan LIKE '%$tu_khoa_escaped%')";
}

// Đếm tổng số sản phẩm với điều kiện lọc
$sql_count = "SELECT COUNT(*) as total FROM san_pham $where_clause";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$tong_so_san_pham = $row_count['total'];
$tong_so_trang = ceil($tong_so_san_pham / $gioi_han);

// Lấy danh sách sản phẩm
$sql = "SELECT * FROM san_pham $where_clause $order_by LIMIT $bat_dau, $gioi_han";
$ket_qua = $conn->query($sql);

// Lay thong tin danh muc neu co
$ten_danh_muc = '';
if ($danh_muc_id) {
    $sql_dm = "SELECT ten_danh_muc FROM danh_muc WHERE id = $danh_muc_id";
    $result_dm = $conn->query($sql_dm);
    if ($row_dm = $result_dm->fetch_assoc()) {
        $ten_danh_muc = $row_dm['ten_danh_muc'];
    }
}
?>

<div class="tieu-de-trang">
  <h1 class="page-title">
      <?php
      if (!empty($ten_danh_muc)) {
          echo $ten_danh_muc;
      } elseif (!empty($tu_khoa)) {
          echo 'Kết quả tìm kiếm: <span class="highlight">' . $tu_khoa . '</span>';
      } else {
          echo 'Tất cả sản phẩm';
      }
      ?>
  </h1>
</div>

<div class="bo-loc">
  <form action="" method="GET">
      <input type="hidden" name="trang" value="san-pham">
      <?php if ($danh_muc_id): ?>
          <input type="hidden" name="danh-muc" value="<?php echo $danh_muc_id; ?>">
      <?php endif; ?>
      <?php if (!empty($tu_khoa)): ?>
          <input type="hidden" name="tu_khoa" value="<?php echo $tu_khoa; ?>">
      <?php endif; ?>
      
      <div class="sap-xep">
          <label for="sap-xep"><i class="fas fa-sort"></i> Sắp xếp:</label>
          <div class="select-wrapper">
              <select name="sap-xep" id="sap-xep" onchange="this.form.submit()">
                  <option value="moi-nhat" <?php echo $sap_xep == 'moi-nhat' ? 'selected' : ''; ?>>Mới nhất</option>
                  <option value="gia-thap-den-cao" <?php echo $sap_xep == 'gia-thap-den-cao' ? 'selected' : ''; ?>>Giá: Thấp đến cao</option>
                  <option value="gia-cao-den-thap" <?php echo $sap_xep == 'gia-cao-den-thap' ? 'selected' : ''; ?>>Giá: Cao đến thấp</option>
                  <option value="ten-a-z" <?php echo $sap_xep == 'ten-a-z' ? 'selected' : ''; ?>>Tên: A-Z</option>
                  <option value="ten-z-a" <?php echo $sap_xep == 'ten-z-a' ? 'selected' : ''; ?>>Tên: Z-A</option>
              </select>
          </div>
      </div>
  </form>
</div>

<div class="danh-sach-san-pham">
<?php
if ($ket_qua && $ket_qua->num_rows > 0) {
    while ($san_pham = $ket_qua->fetch_assoc()) {
        // Kiểm tra hình ảnh tồn tại
        $hinh_anh_path = "../uploads/" . $san_pham['hinh_anh'];
        $hinh_anh = file_exists($hinh_anh_path) ? $san_pham['hinh_anh'] : 'placeholder.jpg';

        // Lấy số lượng đã bán
        $da_ban = lay_so_luong_da_ban($conn, $san_pham['id']);

        // Tính phần trăm giảm giá nếu có
        $phan_tram_giam = 0;
        if (!empty($san_pham['gia_khuyen_mai']) && $san_pham['gia_khuyen_mai'] > 0 && $san_pham['gia_khuyen_mai'] < $san_pham['gia']) {
            $phan_tram_giam = round(($san_pham['gia'] - $san_pham['gia_khuyen_mai']) / $san_pham['gia'] * 100);
        }
        ?>
        <div class="san-pham">
              <div class="hinh-anh">
                  <a href="index.php?trang=chi-tiet-san-pham&id=<?php echo $san_pham['id']; ?>">
                      <img src="uploads/<?php echo $san_pham['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
                  </a>
                  <?php if ($phan_tram_giam > 0): ?>
                      <div class="giam-gia-badge">-<?php echo $phan_tram_giam; ?>%</div>
                  <?php endif; ?>
              </div>
            <div class="thong-tin">
                <h3>
                    <a href="index.php?trang=chi-tiet-san-pham&id=<?php echo $san_pham['id']; ?>">
                        <?php echo $san_pham['ten_san_pham']; ?>
                    </a>
                </h3>
                <div class="gia">
                    <?php if (!empty($san_pham['gia_khuyen_mai']) && $san_pham['gia_khuyen_mai'] > 0 && $san_pham['gia_khuyen_mai'] < $san_pham['gia']): ?>
                        <span class="gia-goc"><?php echo dinh_dang_tien($san_pham['gia']); ?></span>
                        <span class="gia-khuyen-mai"><?php echo dinh_dang_tien($san_pham['gia_khuyen_mai']); ?></span>
                    <?php else: ?>
                        <?php echo dinh_dang_tien($san_pham['gia']); ?>
                    <?php endif; ?>
                </div>
                <div class="hanh-dong">
                    <a href="xu-ly/them-vao-gio-ajax.php?id=<?php echo $san_pham['id']; ?>" class="them-vao-gio">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </a>
                </div>
                <?php if ($da_ban > 0): ?>
                    <div class="da-ban">Đã bán: <span><?php echo $da_ban; ?></span></div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
} else {
    // Thêm debug info
    echo '<p class="khong-co-san-pham">Không tìm thấy sản phẩm nào.</p>';

    // Kiểm tra số lượng sản phẩm trong database
    $sql_check = "SELECT COUNT(*) as total FROM san_pham";
    $result_check = $conn->query($sql_check);
    if ($result_check) {
        $row_check = $result_check->fetch_assoc();
        echo '<p>Tổng số sản phẩm trong database: ' . $row_check['total'] . '</p>';
    }

    // Hiển thị câu truy vấn SQL để debug
    echo '<p>Câu truy vấn: ' . $sql . '</p>';
}
?>
</div>

<?php
// Tao URL co so cho phan trang
$url_co_so = "index.php?trang=san-pham";
if ($danh_muc_id) {
    $url_co_so .= "&danh-muc=$danh_muc_id";
}
if (!empty($tu_khoa)) {
    $url_co_so .= "&tu_khoa=$tu_khoa";
}
if ($sap_xep != 'moi-nhat') {
    $url_co_so .= "&sap-xep=$sap_xep";
}

// Hien thi phan trang
if ($tong_so_trang > 1) {
    echo tao_phan_trang($trang, $tong_so_trang, $url_co_so);
}
?>

