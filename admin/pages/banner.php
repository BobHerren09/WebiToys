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

// Xu ly them banner
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $hanh_dong == 'them') {
    $tieu_de = $_POST['tieu_de'];
    $mo_ta = $_POST['mo_ta'];
    $lien_ket = $_POST['lien_ket'];
    $thu_tu = (int) $_POST['thu_tu'];
    $hien_thi = isset($_POST['hien_thi']) ? 1 : 0;

    // Kiem tra thong tin
    if (empty($tieu_de)) {
        $loi = "Vui lòng nhập tiêu đề banner!";
    } else {
        // Xu ly upload hinh anh
        $hinh_anh = '';
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
                $hinh_anh = 'banner-' . time() . '.' . $file_ext;
                $upload_dir = "../uploads/";

                // Check if uploads directory exists, if not create it
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $upload_path = $upload_dir . $hinh_anh;

                // Upload file
                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    $loi = "Có lỗi xảy ra khi tải lên hình ảnh!";
                }
            }
        } else {
            $loi = "Vui lòng chọn hình ảnh cho banner!";
        }

        if (empty($loi)) {
            // Them banner vao database
            $sql = "INSERT INTO banner (tieu_de, mo_ta, hinh_anh, lien_ket, thu_tu, hien_thi) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii", $tieu_de, $mo_ta, $hinh_anh, $lien_ket, $thu_tu, $hien_thi);

            if ($stmt->execute()) {
                $thong_bao = "Thêm banner thành công!";
                // Reset form
                $_POST = array();
            } else {
                $loi = "Có lỗi xảy ra khi thêm banner!";

                // Xoa file hinh anh da upload
                if (!empty($hinh_anh) && file_exists($upload_path)) {
                    unlink($upload_path);
                }
            }
        }
    }
}

// Xu ly sua banner
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $hanh_dong == 'sua') {
    $banner_id = (int) $_POST['banner_id'];
    $tieu_de = $_POST['tieu_de'];
    $mo_ta = $_POST['mo_ta'];
    $lien_ket = $_POST['lien_ket'];
    $thu_tu = (int) $_POST['thu_tu'];
    $hien_thi = isset($_POST['hien_thi']) ? 1 : 0;

    // Kiem tra thong tin
    if (empty($tieu_de)) {
        $loi = "Vui lòng nhập tiêu đề banner!";
    } else {
        // Lay thong tin banner cu
        $sql = "SELECT * FROM banner WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $banner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $loi = "Banner không tồn tại!";
        } else {
            $banner_cu = $result->fetch_assoc();
            $hinh_anh = $banner_cu['hinh_anh']; // Giữ nguyên hình ảnh cũ nếu không có hình mới

            // Xu ly upload hinh anh moi
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
                    $hinh_anh_moi = 'banner-' . $banner_id . '-' . time() . '.' . $file_ext;
                    $upload_dir = "../uploads/";

                    // Check if uploads directory exists, if not create it
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $upload_path = $upload_dir . $hinh_anh_moi;

                    // Upload file
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Xoa hinh anh cu
                        if (!empty($banner_cu['hinh_anh']) && file_exists($upload_dir . $banner_cu['hinh_anh'])) {
                            unlink($upload_dir . $banner_cu['hinh_anh']);
                        }

                        $hinh_anh = $hinh_anh_moi;
                    } else {
                        $loi = "Có lỗi xảy ra khi tải lên hình ảnh!";
                    }
                }
            }

            if (empty($loi)) {
                // Cap nhat banner
                $sql = "UPDATE banner SET tieu_de = ?, mo_ta = ?, hinh_anh = ?, lien_ket = ?, thu_tu = ?, hien_thi = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssii", $tieu_de, $mo_ta, $hinh_anh, $lien_ket, $thu_tu, $hien_thi, $banner_id);

                if ($stmt->execute()) {
                    $thong_bao = "Cập nhật banner thành công!";
                    $hanh_dong = ''; // Reset hành động để hiển thị lại danh sách
                } else {
                    $loi = "Có lỗi xảy ra khi cập nhật banner!";
                }
            }
        }
    }
}

// Xu ly xoa banner
if ($hanh_dong == 'xoa' && isset($_GET['id'])) {
    $banner_id = (int) $_GET['id'];

    // Lay thong tin banner
    $sql = "SELECT * FROM banner WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $banner_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $banner = $result->fetch_assoc();

        // Xoa banner
        $sql = "DELETE FROM banner WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $banner_id);

        if ($stmt->execute()) {
            // Xoa hinh anh
            if (!empty($banner['hinh_anh']) && file_exists("../uploads/" . $banner['hinh_anh'])) {
                unlink("../uploads/" . $banner['hinh_anh']);
            }

            $thong_bao = "Xóa banner thành công!";
        } else {
            $loi = "Có lỗi xảy ra khi xóa banner!";
        }
    } else {
        $loi = "Banner không tồn tại!";
    }
}

// Xu ly cap nhat trang thai banner
if ($hanh_dong == 'cap-nhat-trang-thai' && isset($_GET['id']) && isset($_GET['trang-thai'])) {
    $banner_id = (int) $_GET['id'];
    $trang_thai = (int) $_GET['trang-thai'];

    $sql = "UPDATE banner SET hien_thi = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $trang_thai, $banner_id);

    if ($stmt->execute()) {
        $thong_bao = "Cập nhật trạng thái banner thành công!";
    } else {
        $loi = "Có lỗi xảy ra khi cập nhật trạng thái banner!";
    }
}

// Lay thong tin banner can sua
$banner_sua = null;
if ($hanh_dong == 'sua' && isset($_GET['id'])) {
    $banner_id = (int) $_GET['id'];

    $sql = "SELECT * FROM banner WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $banner_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $banner_sua = $result->fetch_assoc();
    } else {
        $loi = "Banner không tồn tại!";
        $hanh_dong = '';
    }
}

// Lay danh sach banner
$sql = "SELECT * FROM banner ORDER BY thu_tu ASC";
$result = $conn->query($sql);
$banner_list = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $banner_list[] = $row;
    }
}
?>

<div class="banner-page">
    <div class="page-header">
        <h1>Quản Lý Banner</h1>
        <?php if ($hanh_dong != 'them' && $hanh_dong != 'sua'): ?>
            <button type="button" class="btn-them-moi" onclick="location.href='index.php?trang=banner&hanh-dong=them'">
                <i class="fas fa-plus"></i> Thêm banner mới
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
        <!-- Form thêm banner -->
        <div class="form-container">
            <h2>Thêm Banner Mới</h2>
            <form method="POST" action="index.php?trang=banner&hanh-dong=them" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="tieu_de">Tiêu đề <span class="required">*</span></label>
                    <input type="text" id="tieu_de" name="tieu_de" value="<?php echo isset($_POST['tieu_de']) ? $_POST['tieu_de'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mo_ta">Mô tả</label>
                    <textarea id="mo_ta" name="mo_ta" rows="3"><?php echo isset($_POST['mo_ta']) ? $_POST['mo_ta'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="hinh_anh">Hình ảnh <span class="required">*</span></label>
                    <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*" required>
                    <div id="preview-container" class="preview-container">
                        <img id="preview-image" src="#" alt="Preview" style="display: none;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="lien_ket">Liên kết</label>
                    <input type="text" id="lien_ket" name="lien_ket" value="<?php echo isset($_POST['lien_ket']) ? $_POST['lien_ket'] : ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="thu_tu">Thứ tự</label>
                        <input type="number" id="thu_tu" name="thu_tu" value="<?php echo isset($_POST['thu_tu']) ? $_POST['thu_tu'] : '0'; ?>" min="0">
                    </div>
                    
                    <div class="form-group col-md-6">
                        <div class="checkbox">
                            <input type="checkbox" id="hien_thi" name="hien_thi" value="1" <?php echo (!isset($_POST['hien_thi']) || $_POST['hien_thi'] == 1) ? 'checked' : ''; ?>>
                            <label for="hien_thi">Hiển thị banner</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-luu">
                        <i class="fas fa-save"></i> Lưu banner
                    </button>
                    <a href="index.php?trang=banner" class="btn-huy">Hủy</a>
                </div>
            </form>
        </div>
    <?php elseif ($hanh_dong == 'sua' && $banner_sua): ?>
        <!-- Form sửa banner -->
        <div class="form-container">
            <h2>Sửa Banner</h2>
            <form method="POST" action="index.php?trang=banner&hanh-dong=sua" enctype="multipart/form-data">
                <input type="hidden" name="banner_id" value="<?php echo $banner_sua['id']; ?>">
                
                <div class="form-group">
                    <label for="tieu_de">Tiêu đề <span class="required">*</span></label>
                    <input type="text" id="tieu_de" name="tieu_de" value="<?php echo $banner_sua['tieu_de']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mo_ta">Mô tả</label>
                    <textarea id="mo_ta" name="mo_ta" rows="3"><?php echo $banner_sua['mo_ta']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="hinh_anh">Hình ảnh</label>
                    <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*">
                    <small>Để trống nếu không muốn thay đổi hình ảnh</small>
                    <div id="preview-container" class="preview-container">
                        <?php if (!empty($banner_sua['hinh_anh'])): ?>
                            <img id="preview-image" src="../uploads/<?php echo $banner_sua['hinh_anh']; ?>" alt="<?php echo $banner_sua['tieu_de']; ?>">
                        <?php else: ?>
                            <img id="preview-image" src="#" alt="Preview" style="display: none;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="lien_ket">Liên kết</label>
                    <input type="text" id="lien_ket" name="lien_ket" value="<?php echo $banner_sua['lien_ket']; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="thu_tu">Thứ tự</label>
                        <input type="number" id="thu_tu" name="thu_tu" value="<?php echo $banner_sua['thu_tu']; ?>" min="0">
                    </div>
                    
                    <div class="form-group col-md-6">
                        <div class="checkbox">
                            <input type="checkbox" id="hien_thi" name="hien_thi" value="1" <?php echo $banner_sua['hien_thi'] ? 'checked' : ''; ?>>
                            <label for="hien_thi">Hiển thị banner</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-luu">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                    <a href="index.php?trang=banner" class="btn-huy">Hủy</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Danh sách banner -->
        <div class="danh-sach-banner">
            <table>
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">Hình ảnh</th>
                        <th width="20%">Tiêu đề</th>
                        <th width="25%">Mô tả</th>
                        <th width="10%">Thứ tự</th>
                        <th width="10%">Trạng thái</th>
                        <th width="15%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($banner_list)): ?>
                        <?php foreach ($banner_list as $banner): ?>
                            <tr>
                                <td><?php echo $banner['id']; ?></td>
                                <td>
                                    <?php if (!empty($banner['hinh_anh'])): ?>
                                        <img src="../uploads/<?php echo $banner['hinh_anh']; ?>" alt="<?php echo $banner['tieu_de']; ?>" class="hinh-anh-nho">
                                    <?php else: ?>
                                        <span class="khong-co-hinh">Không có hình</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $banner['tieu_de']; ?></td>
                                <td><?php echo $banner['mo_ta']; ?></td>
                                <td><?php echo $banner['thu_tu']; ?></td>
                                <td>
                                    <a href="index.php?trang=banner&hanh-dong=cap-nhat-trang-thai&id=<?php echo $banner['id']; ?>&trang-thai=<?php echo $banner['hien_thi'] ? 0 : 1; ?>" class="btn-trang-thai <?php echo $banner['hien_thi'] ? 'active' : ''; ?>">
                                        <?php echo $banner['hien_thi'] ? 'Hiển thị' : 'Ẩn'; ?>
                                    </a>
                                </td>
                                <td class="hanh-dong">
                                    <a href="index.php?trang=banner&hanh-dong=sua&id=<?php echo $banner['id']; ?>" class="btn-sua">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="index.php?trang=banner&hanh-dong=xoa&id=<?php echo $banner['id']; ?>" class="btn-xoa" onclick="return confirm('Bạn có chắc chắn muốn xóa banner này?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="khong-co-du-lieu">Không có banner nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.banner-page {
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

.btn-them-moi {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 10px 15px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-them-moi:hover {
    background-color: #218838;
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
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.form-container h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 18px;
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

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
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

.danh-sach-banner {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.hinh-anh-nho {
    width: 100px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

.khong-co-hinh {
    color: #6c757d;
    font-style: italic;
}

.btn-trang-thai {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
    text-align: center;
    background-color: #f8f9fa;
    color: #6c757d;
    transition: all 0.3s;
}

.btn-trang-thai.active {
    background-color: #28a745;
    color: white;
}

.hanh-dong {
    display: flex;
    gap: 5px;
}

.btn-sua, .btn-xoa {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 24px 24px;
    border-radius: 4px;
    font-size: 13px;
    transition: background-color 0.3s;
}

.btn-sua {
    background-color: #0dcaf0;
    color: white;
}

.btn-sua:hover {
    background-color: #0bacce;
}

.btn-xoa {
    background-color: #dc3545;
    color: white;
}

.btn-xoa:hover {
    background-color: #bb2d3b;
}

.khong-co-du-lieu {
    text-align: center;
    padding: 20px;
    color: #6c757d;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .hanh-dong {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview hinh anh
    const hinhAnhInput = document.getElementById('hinh_anh');
    const previewImage = document.getElementById('preview-image');
    
    if (hinhAnhInput && previewImage) {
        hinhAnhInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>

