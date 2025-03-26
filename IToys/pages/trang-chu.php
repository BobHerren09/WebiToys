<?php
// Kiểm tra thư mục uploads
$upload_dir = __DIR__ . "/../uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Kiểm tra kết nối database
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra số lượng sản phẩm trong database
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

// Kiểm tra bảng banner
$sql_check_banner = "SHOW TABLES LIKE 'banner'";
$result_check_banner = $conn->query($sql_check_banner);
if ($result_check_banner->num_rows == 0) {
    // Tạo bảng banner nếu chưa tồn tại
    $sql_create_banner = "CREATE TABLE IF NOT EXISTS banner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tieu_de VARCHAR(255) NOT NULL,
    mo_ta TEXT,
    hinh_anh VARCHAR(255) NOT NULL,
    lien_ket VARCHAR(255),
    thu_tu INT DEFAULT 0,
    hien_thi TINYINT(1) DEFAULT 1,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
  )";
    $conn->query($sql_create_banner);

    // Thêm dữ liệu mẫu cho bảng banner
    $sql_insert_banner = "INSERT INTO banner (tieu_de, mo_ta, hinh_anh, lien_ket, thu_tu, hien_thi) VALUES
  ('Khuyến mãi mùa hè', 'Giảm giá đến 50% cho tất cả đồ chơi', 'banner1.jpg', 'index.php?trang=san-pham', 1, 1),
  ('Đồ chơi giáo dục', 'Phát triển trí tuệ cho bé yêu', 'banner2.jpg', 'index.php?trang=san-pham&danh-muc=2', 2, 1),
  ('Đồ chơi mới nhất', 'Những sản phẩm mới nhất của chúng tôi', 'banner3.jpg', 'index.php?trang=san-pham', 3, 1)";
    $conn->query($sql_insert_banner);
}
?>

<div class="banner">
  <div class="container">
      <!-- Slider mới với cấu trúc đơn giản hơn -->
      <div class="slider">
          <div class="slider-track">
              <?php
              // Lấy banner từ database
              $sql = "SELECT * FROM banner WHERE hien_thi = 1 ORDER BY thu_tu ASC";
              $result = $conn->query($sql);
              $banner_count = 0;

              if ($result && $result->num_rows > 0) {
                  while ($banner = $result->fetch_assoc()) {
                      $banner_count++;
                      // Sửa đường dẫn hình ảnh
                      $hinh_anh_path = "uploads/" . $banner['hinh_anh'];
                      $hinh_anh = file_exists($hinh_anh_path) ? $banner['hinh_anh'] : 'banner' . $banner_count . '.jpg';

                      // Kiểm tra nếu không tìm thấy trong uploads, thử tìm trong assets/images
                      if (!file_exists($hinh_anh_path) && file_exists("assets/images/" . $banner['hinh_anh'])) {
                          $hinh_anh_path = "assets/images/" . $banner['hinh_anh'];
                      } elseif (!file_exists($hinh_anh_path) && file_exists("assets/images/banner" . $banner_count . ".jpg")) {
                          $hinh_anh_path = "assets/images/banner" . $banner_count . ".jpg";
                          $hinh_anh = "banner" . $banner_count . ".jpg";
                      }
                      ?>
                      <div class="slide" data-index="<?php echo $banner_count - 1; ?>">
                          <a href="<?php echo $banner['lien_ket']; ?>" class="slide-link">
                              <!-- Sửa đường dẫn hình ảnh -->
                              <?php if (file_exists($hinh_anh_path)): ?>
                                  <img src="<?php echo $hinh_anh_path; ?>" alt="<?php echo $banner['tieu_de']; ?>" class="slide-image">
                              <?php elseif (file_exists("assets/images/" . $hinh_anh)): ?>
                                  <img src="assets/images/<?php echo $hinh_anh; ?>" alt="<?php echo $banner['tieu_de']; ?>" class="slide-image">
                              <?php else: ?>
                                  <img src="assets/images/banner<?php echo $banner_count; ?>.jpg" alt="<?php echo $banner['tieu_de']; ?>" class="slide-image">
                              <?php endif; ?>
                          </a>
                          <div class="slide-content">
                              <h3><?php echo $banner['tieu_de']; ?></h3>
                              <?php if (!empty($banner['mo_ta'])): ?>
                                  <p><?php echo $banner['mo_ta']; ?></p>
                              <?php endif; ?>
                              <a href="<?php echo $banner['lien_ket']; ?>" class="btn">Xem ngay</a>
                          </div>
                      </div>
                      <?php
                  }
              } else {
                  // Banner mặc định nếu không có trong database
                  $banner_count = 3;
                  for ($i = 1; $i <= 3; $i++) {
                      ?>
                      <div class="slide" data-index="<?php echo $i - 1; ?>">
                          <a href="index.php?trang=san-pham" class="slide-link">
                              <img src="assets/images/banner<?php echo $i; ?>.jpg" alt="Banner <?php echo $i; ?>" class="slide-image">
                          </a>
                          <div class="slide-content">
                              <h3><?php echo $i == 1 ? 'Khuyến mãi mùa hè' : ($i == 2 ? 'Đồ chơi giáo dục' : 'Đồ chơi mới nhất'); ?></h3>
                              <p><?php echo $i == 1 ? 'Giảm giá đến 50% cho tất cả đồ chơi' : ($i == 2 ? 'Phát triển trí tuệ cho bé yêu' : 'Những sản phẩm mới nhất của chúng tôi'); ?></p>
                              <a href="index.php?trang=san-pham" class="btn">Xem ngay</a>
                          </div>
                      </div>
                      <?php
                  }
              }
              ?>
          </div>
          
          <div class="slider-nav">
              <button class="prev-slide" aria-label="Slide trước"><i class="fas fa-chevron-left"></i></button>
              <button class="next-slide" aria-label="Slide tiếp theo"><i class="fas fa-chevron-right"></i></button>
          </div>
          
          <div class="slider-dots">
              <?php
              // Tạo dots dựa trên số lượng banner
              for ($i = 0; $i < $banner_count; $i++) {
                  echo '<button class="slider-dot' . ($i == 0 ? ' active' : '') . '" data-index="' . $i . '" aria-label="Chuyển đến slide ' . ($i + 1) . '"></button>';
              }
              ?>
          </div>
      </div>
  </div>
</div>

<div class="san-pham-noi-bat">
  <div class="container">
      <h2>Sản Phẩm Nổi Bật</h2>

      <div class="danh-sach-san-pham">
          <?php
          // Lấy sản phẩm nổi bật
          $sql = "SELECT * FROM san_pham WHERE noi_bat = 1 AND trang_thai = 1 ORDER BY id DESC LIMIT 8";
          $san_pham_noi_bat = $conn->query($sql);

          if ($san_pham_noi_bat && $san_pham_noi_bat->num_rows > 0) {
              while ($san_pham = $san_pham_noi_bat->fetch_assoc()) {
                  $hinh_anh_path = "uploads/" . $san_pham['hinh_anh'];
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
                              <img src="uploads/<?php echo $hinh_anh; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
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
              echo '<p class="khong-co-san-pham">Không có sản phẩm nổi bật.</p>';
          }
          ?>
      </div>
  </div>
</div>

<div class="danh-muc-noi-bat">
  <div class="container">
      <h2>Danh Mục Nổi Bật</h2>

      <div class="danh-sach-danh-muc">
          <?php
          // Lấy danh sách danh mục nổi bật
          $sql = "SELECT * FROM danh_muc WHERE hien_thi = 1 ORDER BY thu_tu ASC LIMIT 4";
          $ket_qua = $conn->query($sql);

          if ($ket_qua && $ket_qua->num_rows > 0) {
              while ($danh_muc = $ket_qua->fetch_assoc()) {
                  $hinh_anh = !empty($danh_muc['hinh_anh']) && file_exists("uploads/" . $danh_muc['hinh_anh'])
                      ? $danh_muc['hinh_anh']
                      : 'placeholder.jpg';
                  ?>
                  <div class="danh-muc-item">
                      <a href="index.php?trang=san-pham&danh-muc=<?php echo $danh_muc['id']; ?>">
                          <img src="uploads/<?php echo $hinh_anh; ?>" alt="<?php echo $danh_muc['ten_danh_muc']; ?>">
                          <h3><?php echo $danh_muc['ten_danh_muc']; ?></h3>
                      </a>
                  </div>
                  <?php
              }
          } else {
              echo '<p class="khong-co-danh-muc">Không có danh mục nổi bật.</p>';
          }
          ?>
      </div>
  </div>
</div>

<div class="san-pham-moi">
  <div class="container">
      <h2>Sản Phẩm Mới</h2>

      <div class="danh-sach-san-pham">
          <?php
          // Lấy sản phẩm mới
          $sql = "SELECT * FROM san_pham WHERE trang_thai = 1 ORDER BY id DESC LIMIT 8";
          $san_pham_moi = $conn->query($sql);

          if ($san_pham_moi && $san_pham_moi->num_rows > 0) {
              while ($san_pham = $san_pham_moi->fetch_assoc()) {
                  $hinh_anh_path = "uploads/" . $san_pham['hinh_anh'];
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
                              <img src="uploads/<?php echo $hinh_anh; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
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
              echo '<p class="khong-co-san-pham">Không có sản phẩm mới.</p>';
          }
          ?>
      </div>
  </div>
</div>

