<?php
// Xu ly cap nhat gio hang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Xử lý xóa toàn bộ giỏ hàng
    if (isset($_POST['xoa_gio_hang'])) {
        $_SESSION['gio_hang'] = array();
        $thong_bao = "Giỏ hàng đã được xóa thành công!";
    }
    // Xử lý thêm sản phẩm vào giỏ hàng
    elseif (isset($_POST['them_san_pham'])) {
        $san_pham_id = (int) $_POST['them_san_pham'];
        if ($san_pham_id > 0) {
            them_vao_gio_hang($san_pham_id, 1);
            $thong_bao = "Sản phẩm đã được thêm vào giỏ hàng!";
        }
    }
    // Xử lý cập nhật số lượng
    elseif (isset($_POST['cap_nhat_gio_hang']) && isset($_POST['so_luong'])) {
        foreach ($_POST['so_luong'] as $san_pham_id => $so_luong) {
            $so_luong = (int) $so_luong;
            if ($so_luong > 0) {
                cap_nhat_gio_hang($san_pham_id, $so_luong);
            } else {
                xoa_san_pham_gio_hang($san_pham_id);
            }
        }
        $thong_bao = "Giỏ hàng đã được cập nhật!";
    }
}

// Xử lý xóa một sản phẩm từ giỏ hàng
if (isset($_GET['hanh-dong']) && $_GET['hanh-dong'] == 'xoa' && isset($_GET['id'])) {
    $san_pham_id = (int) $_GET['id'];
    xoa_san_pham_gio_hang($san_pham_id);
    $thong_bao = "Sản phẩm đã được xóa khỏi giỏ hàng!";
}

// Kiểm tra thông báo từ session
if (empty($thong_bao) && isset($_SESSION['thong_bao_gio_hang'])) {
    $thong_bao = $_SESSION['thong_bao_gio_hang'];
    unset($_SESSION['thong_bao_gio_hang']); // Xóa thông báo sau khi hiển thị
}

// Lay danh sach san pham trong gio hang
$san_pham_gio_hang = array();
$tong_tien = 0;

if (isset($_SESSION['gio_hang']) && count($_SESSION['gio_hang']) > 0) {
    foreach ($_SESSION['gio_hang'] as $san_pham_id => $so_luong) {
        $san_pham = lay_san_pham_theo_id($conn, $san_pham_id);

        if ($san_pham) {
            $san_pham['so_luong'] = $so_luong;
            // Sử dụng giá khuyến mãi nếu có
            $gia_hien_thi = ($san_pham['gia_khuyen_mai'] && $san_pham['gia_khuyen_mai'] < $san_pham['gia'])
                ? $san_pham['gia_khuyen_mai']
                : $san_pham['gia'];
            $san_pham['thanh_tien'] = $gia_hien_thi * $so_luong;
            $san_pham_gio_hang[] = $san_pham;
            $tong_tien += $san_pham['thanh_tien'];
        }
    }
}
?>

<div class="trang-gio-hang">
  <div class="container">
      <h1>Giỏ Hàng</h1>
      
      <?php if (!empty($thong_bao)): ?>
          <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
      <?php endif; ?>
      
      <?php if (count($san_pham_gio_hang) > 0): ?>
          <form method="POST" action="">
              <div class="bang-gio-hang">
                  <table>
                      <thead>
                          <tr>
                              <th>Sản phẩm</th>
                              <th>Giá</th>
                              <th>Số lượng</th>
                              <th>Thành tiền</th>
                              <th>Xóa</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php foreach ($san_pham_gio_hang as $san_pham): ?>
                              <tr>
                                  <td class="san-pham-info">
                                      <img src="uploads/<?php echo $san_pham['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
                                      <div>
                                          <h3><?php echo $san_pham['ten_san_pham']; ?></h3>
                                      </div>
                                  </td>
                                  <td class="gia">
                                      <?php
                                      // Sử dụng giá khuyến mãi nếu có
                                      $gia_hien_thi = ($san_pham['gia_khuyen_mai'] && $san_pham['gia_khuyen_mai'] < $san_pham['gia'])
                                          ? $san_pham['gia_khuyen_mai']
                                          : $san_pham['gia'];
                                      echo dinh_dang_tien($gia_hien_thi);
                                      ?>
                                  </td>
                                  <td class="so-luong">
                                      <div class="so-luong-control">
                                          <button type="button" class="giam-so-luong">-</button>
                                          <input type="number" name="so_luong[<?php echo $san_pham['id']; ?>]" value="<?php echo $san_pham['so_luong']; ?>" min="0" max="99">
                                          <button type="button" class="tang-so-luong">+</button>
                                      </div>
                                  </td>
                                  <td class="thanh-tien">
                                      <?php
                                      // Sử dụng giá khuyến mãi nếu có
                                      $gia_hien_thi = ($san_pham['gia_khuyen_mai'] && $san_pham['gia_khuyen_mai'] < $san_pham['gia'])
                                          ? $san_pham['gia_khuyen_mai']
                                          : $san_pham['gia'];
                                      $thanh_tien = $gia_hien_thi * $san_pham['so_luong'];
                                      echo dinh_dang_tien($thanh_tien);
                                      ?>
                                  </td>
                                  <td class="xoa">
                                      <a href="index.php?trang=gio-hang&hanh-dong=xoa&id=<?php echo $san_pham['id']; ?>" class="btn-xoa">
                                          <i class="fas fa-trash"></i>
                                      </a>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                      </tbody>
                      <tfoot>
                          <tr>
                              <td colspan="3" class="tong-cong">Tổng cộng:</td>
                              <td colspan="2" class="tong-tien"><?php echo dinh_dang_tien($tong_tien); ?></td>
                          </tr>
                      </tfoot>
                  </table>
              </div>
              
              <div class="hanh-dong-gio-hang">
                  <div class="nut-trai">
                      <a href="index.php?trang=san-pham" class="btn-tiep-tuc-mua">
                          <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
                      </a>
                      
                      <button type="submit" name="xoa_gio_hang" class="btn-xoa-gio-hang" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')">
                          <i class="fas fa-trash"></i> Xóa giỏ hàng
                      </button>
                  </div>
                  
                  <div class="nut-phai">
                      <button type="submit" name="cap_nhat_gio_hang" class="btn-cap-nhat">
                          <i class="fas fa-sync-alt"></i> Cập nhật giỏ hàng
                      </button>
                      
                      <a href="index.php?trang=thanh-toan" class="btn-thanh-toan">
                          Thanh toán <i class="fas fa-arrow-right"></i>
                      </a>
                  </div>
              </div>
          </form>
      <?php else: ?>
          <div class="gio-hang-trong">
              <i class="fas fa-shopping-cart fa-4x"></i>
              <p>Giỏ hàng của bạn đang trống</p>
              <a href="index.php?trang=san-pham" class="btn-mua-ngay">Mua sắm ngay</a>
          </div>
      <?php endif; ?>
  </div>
</div>

<style>
.trang-gio-hang {
  padding: 30px 0;
}

.trang-gio-hang h1 {
  margin-bottom: 30px;
  text-align: center;
}

    .thong-bao-thanh-cong {
        background-color: #d1e7dd;
        color: #0f5132;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
      
     }


    .bang-gio-hang {
        margin-bottom: 30px;
        overflow-x: auto;
    }

.bang-gio-hang table {
  width: 100%;
  border-collapse: collapse;
}

.bang-gio-hang th, .bang-gio-hang td {
  padding: 15px;
  text-align: center;
  border-bottom: 1px solid #e9ecef;
}

.bang-gio-hang th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.san-pham-info {
  display: flex;
  align-items: center;
  text-align: left;
}

.san-pham-info img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  margin-right: 15px;
  border-radius: 4px;
}

.san-pham-info h3 {
  font-size: 16px;
  margin-bottom: 5px;
}

.so-luong-control {
  display: flex;
  align-items: center;
  justify-content: center;
}

.so-luong-control input {
  width: 50px;
  text-align: center;
  padding: 5px;
  border: 1px solid #ddd;
  border-radius: 0;
}

.giam-so-luong, .tang-so-luong {
  width: 30px;
  height: 30px;
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  cursor: pointer;
}

.giam-so-luong {
  border-radius: 4px 0 0 4px;
}

.tang-so-luong {
  border-radius: 0 4px 4px 0;
}

.btn-xoa {
  color: #dc3545;
  font-size: 18px;
}

.tong-cong {
  text-align: right;
  font-weight: bold;
}

.tong-tien {
  font-weight: bold;
  font-size: 18px;
  color: #ff6b6b;
}

.hanh-dong-gio-hang {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
}

.nut-trai, .nut-phai {
  display: flex;
  gap: 10px;
}

.btn-tiep-tuc-mua, .btn-xoa-gio-hang, .btn-cap-nhat, .btn-thanh-toan {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 10px 20px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.btn-tiep-tuc-mua {
  background-color: #f8f9fa;
  color: #495057;
}

.btn-tiep-tuc-mua:hover {
  background-color: #e9ecef;
}

    .btn-xoa-gio-hang {
        background-color: #ff6b6b;
        color: white;
    }

.btn-xoa-gio-hang:hover {
  background-color: #c82333;
}

.btn-cap-nhat {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  color: #495057;
  cursor: pointer;
}

.btn-cap-nhat:hover {
  background-color: #e9ecef;
}

.btn-thanh-toan {
  background-color: #ff6b6b;
  color: white;
}

.btn-thanh-toan:hover {
  background-color: #ff5252;
}

.gio-hang-trong {
  text-align: center;
  padding: 50px 0;
}

.gio-hang-trong i {
  color: #adb5bd;
  margin-bottom: 20px;
}

.gio-hang-trong p {
  font-size: 18px;
  margin-bottom: 20px;
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
  .hanh-dong-gio-hang {
      flex-direction: column;
  }

  .nut-trai, .nut-phai {
      flex-direction: column;
      width: 100%;
  }

  .btn-tiep-tuc-mua, .btn-xoa-gio-hang, .btn-cap-nhat, .btn-thanh-toan {
      width: 100%;
      justify-content: center;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Xu ly nut tang giam so luong
  const giamButtons = document.querySelectorAll('.giam-so-luong');
  const tangButtons = document.querySelectorAll('.tang-so-luong');

  giamButtons.forEach(button => {
      button.addEventListener('click', function() {
          const input = this.nextElementSibling;
          let value = parseInt(input.value);
          if (value > 1) {
              input.value = value - 1;
          }
      });
  });

  tangButtons.forEach(button => {
      button.addEventListener('click', function() {
          const input = this.previousElementSibling;
          let value = parseInt(input.value);
          if (value < 99) {
              input.value = value + 1;
          }
      });
  });
});
</script>

