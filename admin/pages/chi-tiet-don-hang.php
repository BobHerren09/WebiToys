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
    $trang_thai_cu = (int) $_POST['trang_thai_cu'];

    // Cap nhat trang thai don hang
    $sql = "UPDATE don_hang SET trang_thai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $trang_thai_moi, $don_hang_id);

    if ($stmt->execute()) {
        // Cập nhật số lượng sản phẩm
        cap_nhat_so_luong_san_pham($conn, $don_hang_id, $trang_thai_moi, $trang_thai_cu);
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

// Lấy thông tin trạng thái đơn hàng
$trang_thai_text = '';
$trang_thai_class = '';
$trang_thai_icon = '';

switch ($don_hang['trang_thai']) {
    case 0:
        $trang_thai_text = 'Mới';
        $trang_thai_class = 'moi';
        $trang_thai_icon = 'fa-tag';
        break;
    case 1:
        $trang_thai_text = 'Đang xử lý';
        $trang_thai_class = 'dang-xu-ly';
        $trang_thai_icon = 'fa-spinner fa-spin';
        break;
    case 2:
        $trang_thai_text = 'Đang giao';
        $trang_thai_class = 'dang-giao';
        $trang_thai_icon = 'fa-truck';
        break;
    case 3:
        $trang_thai_text = 'Hoàn thành';
        $trang_thai_class = 'hoan-thanh';
        $trang_thai_icon = 'fa-check-circle';
        break;
    case 4:
        $trang_thai_text = 'Đã hủy';
        $trang_thai_class = 'da-huy';
        $trang_thai_icon = 'fa-times-circle';
        break;
}
?>

<style>
/* CSS cho trang chi tiết đơn hàng */
.chi-tiet-don-hang-page {
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.header-actions {
    display: flex;
    gap: 10px;
}

.trang-thai-don-hang-card {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.trang-thai-hien-tai {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    border-radius: 6px;
    font-weight: 600;
}

.trang-thai-hien-tai i {
    font-size: 18px;
}

.trang-thai-hien-tai.moi {
    background-color: #e9ecef;
    color: #495057;
}

.trang-thai-hien-tai.dang-xu-ly {
    background-color: #cff4fc;
    color: #055160;
}

.trang-thai-hien-tai.dang-giao {
    background-color: #fff3cd;
    color: #664d03;
}

.trang-thai-hien-tai.hoan-thanh {
    background-color: #d1e7dd;
    color: #0f5132;
}

.trang-thai-hien-tai.da-huy {
    background-color: #f8d7da;
    color: #842029;
}

.form-trang-thai {
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-trang-thai .form-group {
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-trang-thai select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    min-width: 150px;
}

.chi-tiet-don-hang-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.thong-tin-don-hang,
.thong-tin-khach-hang {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

.thong-tin-don-hang h3,
.thong-tin-khach-hang h3,
.san-pham-don-hang h3 {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.bang-thong-tin {
    width: 100%;
    border-collapse: collapse;
}

.bang-thong-tin th,
.bang-thong-tin td {
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.bang-thong-tin th {
    width: 30%;
    text-align: left;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.bang-thong-tin td a {
    color: #0d6efd;
    text-decoration: none;
}

.bang-thong-tin td a:hover {
    text-decoration: underline;
}

.tong-tien {
    color: #dc3545;
    font-size: 16px;
}

.san-pham-don-hang {
    margin-bottom: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

.table-responsive {
    overflow-x: auto;
}

.san-pham-don-hang table {
    width: 100%;
    border-collapse: collapse;
}

.san-pham-don-hang table th,
.san-pham-don-hang table td {
    padding: 12px;
    border: 1px solid #e9ecef;
}

.san-pham-don-hang table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

/* Căn giữa các cột giá, số lượng và thành tiền */
.san-pham-don-hang table th:nth-child(2),
.san-pham-don-hang table th:nth-child(3),
.san-pham-don-hang table th:nth-child(4),
.san-pham-don-hang table td:nth-child(2),
.san-pham-don-hang table td:nth-child(3),
.san-pham-don-hang table td:nth-child(4) {
    text-align: center;
}

.san-pham-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.san-pham-image {
    flex-shrink: 0;
}

.hinh-anh-nho {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.san-pham-details {
    display: flex;
    flex-direction: column;
}

.san-pham-name {
    font-weight: 500;
    margin-bottom: 5px;
}

.san-pham-sku {
    font-size: 12px;
    color: #6c757d;
}

.gia, .so-luong, .thanh-tien {
    text-align: center;
}

.tong-cong {
    text-align: right;
    font-weight: 600;
    padding-right: 15px !important;
}

.hanh-dong-don-hang {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Nút in đơn hàng */
.btn-in {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    transition: background-color 0.3s;
}

.btn-in:hover {
    background-color: #5a6268;
}

.btn-in i {
    font-size: 16px;
}

/* CSS cho phần in đơn hàng */
.print-area {
    display: none;
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.print-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 20px;
}

.company-info h1 {
    margin: 0 0 10px 0;
    color: #333;
}

.company-info p {
    margin: 5px 0;
}

.order-title {
    text-align: right;
}

.order-title h2 {
    margin: 0 0 10px 0;
    color: #333;
}

.print-customer-info {
    margin-bottom: 30px;
}

.info-section h3 {
    margin: 0 0 10px 0;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.info-section p {
    margin: 5px 0;
}

.print-products h3 {
    margin: 0 0 10px 0;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.print-products table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

.print-products th, 
.print-products td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

/* Căn giữa các cột trong bản in */
.print-products th:nth-child(3),
.print-products th:nth-child(4),
.print-products th:nth-child(5),
.print-products td:nth-child(3),
.print-products td:nth-child(4),
.print-products td:nth-child(5) {
    text-align: center;
}

.print-products th {
    background-color: #f2f2f2;
}

.print-products tfoot tr td {
    font-weight: bold;
}

.signature-section {
    display: flex;
    justify-content: space-between;
    margin-top: 50px;
}

.signature {
    text-align: center;
    width: 200px;
}

.signature p {
    margin: 5px 0;
}

.signature-line {
    margin-top: 70px;
    border-top: 1px solid #000;
}

.thank-you {
    text-align: center;
    margin-top: 30px;
    font-style: italic;
}

/* Responsive styles */
@media (max-width: 768px) {
    .chi-tiet-don-hang-content {
        grid-template-columns: 1fr;
    }
    
    .trang-thai-don-hang-card {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-trang-thai {
        width: 100%;
    }
    
    .form-trang-thai .form-group {
        width: 100%;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-trang-thai select {
        width: 100%;
    }
    
    .hanh-dong-don-hang {
        flex-direction: column;
    }
    
    .btn-xoa,
    .btn-in {
        width: 100%;
        justify-content: center;
    }
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    .print-area, .print-area * {
        visibility: visible;
    }
    
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: block !important;
    }
    
    .admin-header,
    .sidebar,
    .page-header,
    .hanh-dong-don-hang,
    .form-trang-thai,
    .btn-in,
    .btn-quay-lai,
    .chi-tiet-don-hang-page {
        display: none !important;
    }
}
</style>

<!-- Phần hiển thị bình thường -->
<div class="chi-tiet-don-hang-page" id="chi-tiet-don-hang-content">
  <div class="page-header">
      <h1>Chi Tiết Đơn Hàng #<?php echo $don_hang_id; ?></h1>
      <div class="header-actions">
          <a href="index.php?trang=don-hang" class="btn-quay-lai">
              <i class="fas fa-arrow-left"></i> Quay lại danh sách
          </a>
          <!-- Đã xóa nút in đơn hàng ở đây -->
      </div>
  </div>
  
  <?php if (!empty($thong_bao)): ?>
      <div class="thong-bao-thanh-cong">
          <i class="fas fa-check-circle"></i> <?php echo $thong_bao; ?>
      </div>
  <?php endif; ?>
  
  <?php if (!empty($loi)): ?>
      <div class="thong-bao-loi">
          <i class="fas fa-exclamation-circle"></i> <?php echo $loi; ?>
      </div>
  <?php endif; ?>
  
  <div class="chi-tiet-don-hang-container">
      <!-- Thông tin trạng thái đơn hàng -->
      <div class="trang-thai-don-hang-card">
          <div class="trang-thai-hien-tai <?php echo $trang_thai_class; ?>">
              <i class="fas <?php echo $trang_thai_icon; ?>"></i>
              <span>Trạng thái hiện tại: <?php echo $trang_thai_text; ?></span>
          </div>
          
          <form method="POST" action="" class="form-trang-thai">
              <input type="hidden" name="don_hang_id" value="<?php echo $don_hang['id']; ?>">
              <input type="hidden" name="trang_thai_cu" value="<?php echo $don_hang['trang_thai']; ?>">
              <div class="form-group">
                  <label for="trang_thai"><i class="fas fa-exchange-alt"></i> Cập nhật trạng thái:</label>
                  <select name="trang_thai" id="trang_thai" onchange="this.form.submit()">
                      <option value="0" <?php echo $don_hang['trang_thai'] == 0 ? 'selected' : ''; ?>>Mới</option>
                      <option value="1" <?php echo $don_hang['trang_thai'] == 1 ? 'selected' : ''; ?>>Đang xử lý</option>
                      <option value="2" <?php echo $don_hang['trang_thai'] == 2 ? 'selected' : ''; ?>>Đang giao</option>
                      <option value="3" <?php echo $don_hang['trang_thai'] == 3 ? 'selected' : ''; ?>>Hoàn thành</option>
                      <option value="4" <?php echo $don_hang['trang_thai'] == 4 ? 'selected' : ''; ?>>Đã hủy</option>
                  </select>
              </div>
          </form>
      </div>
      
      <div class="chi-tiet-don-hang-content">
          <div class="thong-tin-don-hang">
              <h3><i class="fas fa-info-circle"></i> Thông Tin Đơn Hàng</h3>
              <table class="bang-thong-tin">
                  <tr>
                      <th><i class="fas fa-hashtag"></i> Mã đơn hàng:</th>
                      <td>#<?php echo $don_hang['id']; ?></td>
                  </tr>
                  <tr>
                      <th><i class="fas fa-calendar-alt"></i> Ngày đặt:</th>
                      <td><?php echo date('d/m/Y H:i', strtotime($don_hang['ngay_tao'])); ?></td>
                  </tr>
                  <tr>
                      <th><i class="fas fa-money-bill-wave"></i> Tổng tiền:</th>
                      <td><strong class="tong-tien"><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></strong></td>
                  </tr>
              </table>
          </div>
          
          <div class="thong-tin-khach-hang">
              <h3><i class="fas fa-user"></i> Thông Tin Khách Hàng</h3>
              <table class="bang-thong-tin">
                  <tr>
                      <th><i class="fas fa-user-circle"></i> Họ tên:</th>
                      <td><?php echo $don_hang['ho_ten']; ?></td>
                  </tr>
                  <tr>
                      <th><i class="fas fa-envelope"></i> Email:</th>
                      <td><a href="mailto:<?php echo $don_hang['email']; ?>"><?php echo $don_hang['email']; ?></a></td>
                  </tr>
                  <tr>
                      <th><i class="fas fa-phone"></i> Điện thoại:</th>
                      <td><a href="tel:<?php echo $don_hang['dien_thoai']; ?>"><?php echo $don_hang['dien_thoai']; ?></a></td>
                  </tr>
                  <tr>
                      <th><i class="fas fa-map-marker-alt"></i> Địa chỉ:</th>
                      <td><?php echo $don_hang['dia_chi']; ?></td>
                  </tr>
                  <tr>
                      <th><i class="fas fa-sticky-note"></i> Ghi chú:</th>
                      <td><?php echo !empty($don_hang['ghi_chu']) ? $don_hang['ghi_chu'] : '<em>Không có</em>'; ?></td>
                  </tr>
              </table>
          </div>
      </div>
      
      <div class="san-pham-don-hang">
          <h3><i class="fas fa-shopping-cart"></i> Sản Phẩm Đã Đặt</h3>
          <div class="table-responsive">
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
                                      <div class="san-pham-image">
                                          <img src="../uploads/<?php echo $item['hinh_anh']; ?>" alt="<?php echo $item['ten_san_pham']; ?>" class="hinh-anh-nho">
                                      </div>
                                      <div class="san-pham-details">
                                          <span class="san-pham-name"><?php echo $item['ten_san_pham']; ?></span>
                                          <?php if (!empty($item['ma_san_pham'])): ?>
                                              <span class="san-pham-sku">SKU: <?php echo $item['ma_san_pham']; ?></span>
                                          <?php endif; ?>
                                      </div>
                                  </td>
                                  <td class="gia"><?php echo dinh_dang_tien($item['gia']); ?></td>
                                  <td class="so-luong"><?php echo $item['so_luong']; ?></td>
                                  <td class="thanh-tien"><?php echo dinh_dang_tien($item['thanh_tien']); ?></td>
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
                          <td class="tong-tien"><strong><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></strong></td>
                      </tr>
                  </tfoot>
              </table>
          </div>
      </div>
      
      <div class="hanh-dong-don-hang">
          <a href="index.php?trang=don-hang&hanh-dong=xoa&id=<?php echo $don_hang['id']; ?>" class="btn-xoa" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
              <i class="fas fa-trash"></i> Xóa đơn hàng
          </a>
          
          <button type="button" class="btn-in" onclick="inDonHang()">
              <i class="fas fa-print"></i> In đơn hàng
          </button>
      </div>
  </div>
</div>

<!-- Phần in đơn hàng (ẩn mặc định) -->
<div id="print-area" class="print-area">
    <div class="print-header">
        <div class="company-info">
            <h1>iToys: Thế giới đồ chơi</h1>
            <p>Địa chỉ: QL21, TT. Xuân Mai, Chương Mỹ, Hà Nội</p>
            <p>Điện thoại: 0566191650 | Email: lienhe@iToys.com</p>
            <p>Website: www.itoys.com</p>
        </div>
        <div class="order-title">
            <h2>ĐƠN HÀNG #<?php echo $don_hang['id']; ?></h2>
            <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($don_hang['ngay_tao'])); ?></p>
            <p>Trạng thái: <?php echo $trang_thai_text; ?></p>
        </div>
    </div>
    
    <div class="print-customer-info">
        <div class="info-section">
            <h3>THÔNG TIN KHÁCH HÀNG</h3>
            <p><strong>Họ tên:</strong> <?php echo $don_hang['ho_ten']; ?></p>
            <p><strong>Email:</strong> <?php echo $don_hang['email']; ?></p>
            <p><strong>Điện thoại:</strong> <?php echo $don_hang['dien_thoai']; ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo $don_hang['dia_chi']; ?></p>
            <?php if (!empty($don_hang['ghi_chu'])): ?>
                <p><strong>Ghi chú:</strong> <?php echo $don_hang['ghi_chu']; ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="print-products">
        <h3>DANH SÁCH SẢN PHẨM</h3>
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($chi_tiet_san_pham) > 0): ?>
                    <?php $i = 1;
                    foreach ($chi_tiet_san_pham as $item): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $item['ten_san_pham']; ?></td>
                            <td><?php echo dinh_dang_tien($item['gia']); ?></td>
                            <td><?php echo $item['so_luong']; ?></td>
                            <td><?php echo dinh_dang_tien($item['thanh_tien']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Không có sản phẩm nào trong đơn hàng này.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                    <td><strong><?php echo dinh_dang_tien($don_hang['tong_tien']); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="print-footer">
        <div class="signature-section">
            <div class="signature">
                <p>Người lập phiếu</p>
                <p>(Ký, ghi rõ họ tên)</p>
                <div class="signature-line"></div>
            </div>
            <div class="signature">
                <p>Người nhận hàng</p>
                <p>(Ký, ghi rõ họ tên)</p>
                <div class="signature-line"></div>
            </div>
        </div>
        <div class="thank-you">
            <p>Cảm ơn quý khách đã mua hàng tại cửa hàng của chúng tôi!</p>
        </div>
    </div>
</div>

<script>
function inDonHang() {
    // Hiển thị phần in
    var printArea = document.getElementById('print-area');
    printArea.style.display = 'block';
    
    // In trang
    window.print();
    
    // Sau khi in xong, ẩn phần in đi
    setTimeout(function() {
        printArea.style.display = 'none';
    }, 100);
}
</script>