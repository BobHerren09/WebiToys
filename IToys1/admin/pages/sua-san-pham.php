<?php
// Kiem tra quyen truy cap
if (!isset($_SESSION['admin_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

// Lay ID san pham
$san_pham_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($san_pham_id <= 0) {
    header("Location: index.php?trang=san-pham");
    exit();
}

// Lay thong tin san pham
$sql = "SELECT * FROM san_pham WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $san_pham_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?trang=san-pham");
    exit();
}

$san_pham = $result->fetch_assoc();

// Lay danh sach danh muc
$sql = "SELECT * FROM danh_muc ORDER BY thu_tu ASC";
$result = $conn->query($sql);
$danh_muc_list = array();
while ($row = $result->fetch_assoc()) {
    $danh_muc_list[] = $row;
}

// Lay hinh anh phu
$sql = "SELECT * FROM hinh_anh_san_pham WHERE san_pham_id = ? ORDER BY thu_tu ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $san_pham_id);
$stmt->execute();
$result = $stmt->get_result();
$hinh_anh_phu = array();
while ($row = $result->fetch_assoc()) {
    $hinh_anh_phu[] = $row;
}

// Xu ly cap nhat san pham
$thong_bao = '';
$loi = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lay thong tin san pham
    $ten_san_pham = $_POST['ten_san_pham'];
    $danh_muc_id = (int) $_POST['danh_muc_id'];
    $mo_ta_ngan = $_POST['mo_ta_ngan'];
    $mo_ta = $_POST['mo_ta'];
    $gia = str_replace(',', '', $_POST['gia']);
    $gia_khuyen_mai = !empty($_POST['gia_khuyen_mai']) ? str_replace(',', '', $_POST['gia_khuyen_mai']) : null;
    $so_luong = (int) $_POST['so_luong'];
    $noi_bat = isset($_POST['noi_bat']) ? 1 : 0;
    $trang_thai = isset($_POST['trang_thai']) ? 1 : 0;

    // Kiem tra thong tin
    if (empty($ten_san_pham)) {
        $loi = "Vui lòng nhập tên sản phẩm!";
    } elseif ($danh_muc_id <= 0) {
        $loi = "Vui lòng chọn danh mục!";
    } elseif (!is_numeric($gia) || $gia <= 0) {
        $loi = "Giá sản phẩm không hợp lệ!";
    } elseif (!empty($gia_khuyen_mai) && (!is_numeric($gia_khuyen_mai) || $gia_khuyen_mai <= 0 || $gia_khuyen_mai >= $gia)) {
        $loi = "Giá khuyến mãi không hợp lệ!";
    } else {
        // Xu ly upload hinh anh
        $hinh_anh = $san_pham['hinh_anh']; // Giữ nguyên hình ảnh cũ nếu không có hình mới

        if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
            $file_name = $_FILES['hinh_anh']['name'];
            $file_tmp = $_FILES['hinh_anh']['tmp_name'];
            $file_size = $_FILES['hinh_anh']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Kiem tra dinh dang file
            $allowed_exts = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_ext, $allowed_exts)) {
                $loi = "Chỉ cho phép tải lên các file hình ảnh (jpg, jpeg, png, gif)!";
            } elseif ($file_size > 2097152) { // 2MB
                $loi = "Kích thước file không được vượt quá 2MB!";
            } else {
                // Tao ten file moi
                $hinh_anh_moi = 'san-pham-' . $san_pham_id . '-' . time() . '.' . $file_ext;
                $upload_dir = "../uploads/";

                // Check if uploads directory exists, if not create it
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $upload_path = $upload_dir . $hinh_anh_moi;

                // Upload file
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Xoa hinh anh cu
                    if (!empty($hinh_anh) && file_exists($upload_dir . $hinh_anh)) {
                        unlink($upload_dir . $hinh_anh);
                    }

                    $hinh_anh = $hinh_anh_moi;
                } else {
                    $loi = "Có lỗi xảy ra khi tải lên hình ảnh! Vui lòng kiểm tra quyền thư mục.";
                }
            }
        }

        if (empty($loi)) {
            // Cap nhat san pham
            $sql = "UPDATE san_pham SET 
                    ten_san_pham = ?, 
                    danh_muc_id = ?, 
                    mo_ta_ngan = ?, 
                    mo_ta = ?, 
                    gia = ?, 
                    gia_khuyen_mai = ?, 
                    hinh_anh = ?, 
                    so_luong = ?, 
                    noi_bat = ?, 
                    trang_thai = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissddsiiii", $ten_san_pham, $danh_muc_id, $mo_ta_ngan, $mo_ta, $gia, $gia_khuyen_mai, $hinh_anh, $so_luong, $noi_bat, $trang_thai, $san_pham_id);

            if ($stmt->execute()) {
                // Xu ly xoa hinh anh phu
                if (isset($_POST['xoa_hinh_anh']) && is_array($_POST['xoa_hinh_anh'])) {
                    foreach ($_POST['xoa_hinh_anh'] as $hinh_anh_id) {
                        $sql = "SELECT hinh_anh FROM hinh_anh_san_pham WHERE id = ? AND san_pham_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $hinh_anh_id, $san_pham_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $hinh_anh_xoa = $row['hinh_anh'];

                            // Xoa hinh anh tu database
                            $sql = "DELETE FROM hinh_anh_san_pham WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $hinh_anh_id);
                            $stmt->execute();

                            // Xoa file hinh anh
                            if (!empty($hinh_anh_xoa) && file_exists("../uploads/$hinh_anh_xoa")) {
                                unlink("../uploads/$hinh_anh_xoa");
                            }
                        }
                    }
                }

                // Xu ly upload hinh anh phu moi
                if (isset($_FILES['hinh_anh_phu']) && count($_FILES['hinh_anh_phu']['name']) > 0) {
                    for ($i = 0; $i < count($_FILES['hinh_anh_phu']['name']); $i++) {
                        if ($_FILES['hinh_anh_phu']['error'][$i] == 0) {
                            $file_name_phu = $_FILES['hinh_anh_phu']['name'][$i];
                            $file_tmp_phu = $_FILES['hinh_anh_phu']['tmp_name'][$i];
                            $file_size_phu = $_FILES['hinh_anh_phu']['size'][$i];
                            $file_ext_phu = strtolower(pathinfo($file_name_phu, PATHINFO_EXTENSION));

                            // Kiem tra dinh dang file
                            if (in_array($file_ext_phu, $allowed_exts) && $file_size_phu <= 2097152) {
                                // Tao ten file moi
                                $hinh_anh_phu_moi = 'san-pham-' . $san_pham_id . '-phu-' . time() . '-' . $i . '.' . $file_ext_phu;
                                $upload_path_phu = "../uploads/" . $hinh_anh_phu_moi;

                                // Upload file
                                if (move_uploaded_file($file_tmp_phu, $upload_path_phu)) {
                                    // Them hinh anh phu vao database
                                    $sql = "INSERT INTO hinh_anh_san_pham (san_pham_id, hinh_anh, thu_tu) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    $thu_tu = count($hinh_anh_phu) + $i + 1;
                                    $stmt->bind_param("isi", $san_pham_id, $hinh_anh_phu_moi, $thu_tu);
                                    $stmt->execute();
                                }
                            }
                        }
                    }
                }

                $thong_bao = "Cập nhật sản phẩm thành công!";

                // Cap nhat lai thong tin san pham
                $sql = "SELECT * FROM san_pham WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $san_pham_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $san_pham = $result->fetch_assoc();

                // Cap nhat lai danh sach hinh anh phu
                $sql = "SELECT * FROM hinh_anh_san_pham WHERE san_pham_id = ? ORDER BY thu_tu ASC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $san_pham_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $hinh_anh_phu = array();
                while ($row = $result->fetch_assoc()) {
                    $hinh_anh_phu[] = $row;
                }
            } else {
                $loi = "Có lỗi xảy ra khi cập nhật sản phẩm!";
            }
        }
    }
}
?>

<div class="sua-san-pham-page">
    <div class="page-header">
        <h1>Sửa Sản Phẩm</h1>
        <a href="index.php?trang=san-pham" class="btn-quay-lai">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
    
    <?php if (!empty($thong_bao)): ?>
        <div class="thong-bao-thanh-cong"><?php echo $thong_bao; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($loi)): ?>
        <div class="thong-bao-loi"><?php echo $loi; ?></div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="ten_san_pham">Tên sản phẩm <span class="required">*</span></label>
                    <input type="text" id="ten_san_pham" name="ten_san_pham" value="<?php echo $san_pham['ten_san_pham']; ?>" required>
                </div>
                
                <div class="form-group col-md-4">
                    <label for="danh_muc_id">Danh mục <span class="required">*</span></label>
                    <select id="danh_muc_id" name="danh_muc_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($danh_muc_list as $dm): ?>
                            <option value="<?php echo $dm['id']; ?>" <?php echo ($san_pham['danh_muc_id'] == $dm['id']) ? 'selected' : ''; ?>>
                                <?php echo $dm['ten_danh_muc']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
           <div class="form-row">
              <div class="form-group col-md-4">
    <label for="gia">Giá <span class="required">*</span></label>
    <input type="text" id="gia" name="gia" value="<?php echo number_format($san_pham['gia']); ?>" required>
</div>

<div class="form-group col-md-4">
    <label for="gia_khuyen_mai">Giá khuyến mãi</label>
    <input type="text" id="gia_khuyen_mai" name="gia_khuyen_mai" value="<?php echo $san_pham['gia_khuyen_mai'] ? number_format($san_pham['gia_khuyen_mai']) : ''; ?>">
</div>

                
                <div class="form-group col-md-4">
                    <label for="so_luong">Số lượng</label>
                    <input type="number" id="so_luong" name="so_luong" value="<?php echo $san_pham['so_luong']; ?>" min="0">
                </div>
            </div>
            
            <div class="form-group">
                <label for="mo_ta_ngan">Mô tả ngắn</label>
                <textarea id="mo_ta_ngan" name="mo_ta_ngan" rows="3"><?php echo $san_pham['mo_ta_ngan']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="mo_ta">Mô tả chi tiết</label>
                <textarea id="mo_ta" name="mo_ta" rows="6"><?php echo $san_pham['mo_ta']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="hinh_anh">Hình ảnh chính</label>
                <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*">
                <small>Để trống nếu không muốn thay đổi hình ảnh</small>
                <div id="preview-container" class="preview-container">                 
                    <img id="preview-image" src="../uploads/<?php echo $san_pham['hinh_anh']; ?>" alt="<?php echo $san_pham['ten_san_pham']; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Hình ảnh phụ hiện tại</label>
                <div class="hinh-anh-phu-container">
                    <?php if (count($hinh_anh_phu) > 0): ?>
                        <?php foreach ($hinh_anh_phu as $hinh): ?>
                            <div class="hinh-anh-phu-item">
                                <img src="../uploads/<?php echo $hinh['hinh_anh']; ?>" alt="Hình ảnh phụ">
                                <div class="checkbox">
                                    <input type="checkbox" id="xoa_hinh_<?php echo $hinh['id']; ?>" name="xoa_hinh_anh[]" value="<?php echo $hinh['id']; ?>">
                                    <label for="xoa_hinh_<?php echo $hinh['id']; ?>">Xóa</label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Không có hình ảnh phụ</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="hinh_anh_phu">Thêm hình ảnh phụ mới</label>
                <input type="file" id="hinh_anh_phu" name="hinh_anh_phu[]" accept="image/*" multiple>
                <div id="preview-container-phu" class="preview-container-phu">
                    <!-- Hinh anh phu se duoc hien thi o day -->
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="checkbox">
                        <input type="checkbox" id="noi_bat" name="noi_bat" value="1" <?php echo $san_pham['noi_bat'] ? 'checked' : ''; ?>>
                        <label for="noi_bat">Sản phẩm nổi bật</label>
                    </div>
                </div>
                
                <div class="form-group col-md-6">
                    <div class="checkbox">
                        <input type="checkbox" id="trang_thai" name="trang_thai" value="1" <?php echo $san_pham['trang_thai'] ? 'checked' : ''; ?>>
                        <label for="trang_thai">Hiển thị sản phẩm</label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-luu">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
                <a href="index.php?trang=san-pham" class="btn-huy">Hủy</a>
            </div>
        </form>
    </div>
</div>

<style>
.sua-san-pham-page {
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 24px;
    margin: 0;
}

.btn-quay-lai {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    background-color: #6c757d;
    color: white;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.btn-quay-lai:hover {
    background-color: #5c636a;
}

.thong-bao-thanh-cong {
    background-color: #d1e7dd;
    color: #0f5132;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.thong-bao-loi {
    background-color: #f8d7da;
    color: #842029;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.form-container {
    margin-top: 20px;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
}

.form-group {
    margin-bottom: 20px;
    padding: 0 10px;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
}

.col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.required {
    color: #dc3545;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
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

.checkbox {
    display: flex;
    align-items: center;
    gap: 5px;
}

.checkbox input {
    margin: 0;
}

.preview-container {
    margin-top: 10px;
    max-width: 300px;
}

.preview-container img {
    max-width: 100%;
    border-radius: 4px;
}

.hinh-anh-phu-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
}

.hinh-anh-phu-item {
    width: 120px;
    text-align: center;
}

.hinh-anh-phu-item img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 5px;
}

.preview-container-phu {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.preview-item {
    position: relative;
    width: 100px;
    height: 100px;
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn-luu, .btn-huy {
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-luu {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background-color: #28a745;
    color: white;
    border: none;
}

.btn-luu:hover {
    background-color: #218838;
}

.btn-huy {
    background-color: #f8f9fa;
    color: #212529;
    border: 1px solid #ddd;
    text-decoration: none;
    text-align: center;
}

.btn-huy:hover {
    background-color: #e9ecef;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .col-md-4, .col-md-6, .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

<script>
    
document.addEventListener('DOMContentLoaded', function() {
    // Preview hình ảnh chính
    const hinhAnhInput = document.getElementById('hinh_anh');
    const previewImage = document.getElementById('preview-image');
    
    hinhAnhInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Preview hình ảnh phụ
    const hinhAnhPhuInput = document.getElementById('hinh_anh_phu');
    const previewContainerPhu = document.getElementById('preview-container-phu');
    
    hinhAnhPhuInput.addEventListener('change', function() {
        previewContainerPhu.innerHTML = '';
        
        if (this.files) {
            const fileCount = Math.min(this.files.length, 5);
            
            for (let i = 0; i < fileCount; i++) {
                const file = this.files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview ' + (i + 1);
                    
                    previewItem.appendChild(img);
                    previewContainerPhu.appendChild(previewItem);
                }
                
                reader.readAsDataURL(file);
            }
        }
    });
    
    // Định dạng giá tiền
    const giaInput = document.getElementById('gia');
    const giaKhuyenMaiInput = document.getElementById('gia_khuyen_mai');
    
    new AutoNumeric(giaInput, {
        digitGroupSeparator: ',',
        decimalPlaces: 0,
        currencySymbol: ' đ',
        currencySymbolPlacement: 's',
        unformatOnSubmit: true
    });
    
    new AutoNumeric(giaKhuyenMaiInput, {
        digitGroupSeparator: ',',
        decimalPlaces: 0,
        currencySymbol: ' đ',
        currencySymbolPlacement: 's',
        unformatOnSubmit: true
    });
});


</script>
