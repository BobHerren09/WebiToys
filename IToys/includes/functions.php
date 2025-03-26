<?php
// Ham lay danh sach san pham
function lay_danh_sach_san_pham($conn, $gioi_han = 10, $trang = 1, $danh_muc_id = null)
{
    $trang = max(1, $trang); // Đảm bảo trang luôn >= 1
    $bat_dau = ($trang - 1) * $gioi_han;

    $dieu_kien = " WHERE trang_thai = 1"; // Chỉ lấy sản phẩm có trạng thái hiển thị
    if ($danh_muc_id) {
        $dieu_kien .= " AND danh_muc_id = $danh_muc_id";
    }

    $sql = "SELECT * FROM san_pham$dieu_kien ORDER BY id DESC LIMIT $bat_dau, $gioi_han";
    $ket_qua = $conn->query($sql);

    $san_pham = array();
    if ($ket_qua->num_rows > 0) {
        while ($row = $ket_qua->fetch_assoc()) {
            $san_pham[] = $row;
        }
    }

    return $san_pham;
}

// Ham lay chi tiet san pham theo ID
function lay_san_pham_theo_id($conn, $id)
{
    $sql = "SELECT * FROM san_pham WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $ket_qua = $stmt->get_result();

    if ($ket_qua->num_rows > 0) {
        return $ket_qua->fetch_assoc();
    }

    return null;
}

// Ham lay danh sach danh muc
function lay_danh_sach_danh_muc($conn)
{
    $sql = "SELECT * FROM danh_muc ORDER BY thu_tu ASC";
    $ket_qua = $conn->query($sql);

    $danh_muc = array();
    if ($ket_qua->num_rows > 0) {
        while ($row = $ket_qua->fetch_assoc()) {
            $danh_muc[] = $row;
        }
    }

    return $danh_muc;
}

// Ham lay danh sach don hang
function lay_danh_sach_don_hang($conn, $gioi_han = 10, $trang = 1, $trang_thai = null)
{
    $trang = max(1, $trang); // Đảm bảo trang luôn >= 1
    $bat_dau = ($trang - 1) * $gioi_han;

    $dieu_kien = "";
    if ($trang_thai !== null) {
        $dieu_kien = " WHERE trang_thai = $trang_thai";
    }

    $sql = "SELECT * FROM don_hang$dieu_kien ORDER BY id DESC LIMIT $bat_dau, $gioi_han";
    $ket_qua = $conn->query($sql);

    $don_hang = array();
    if ($ket_qua->num_rows > 0) {
        while ($row = $ket_qua->fetch_assoc()) {
            $don_hang[] = $row;
        }
    }

    return $don_hang;
}

// Ham lay chi tiet don hang
function lay_chi_tiet_don_hang($conn, $don_hang_id)
{
    $sql = "SELECT ct.*, sp.ten_san_pham, sp.hinh_anh 
          FROM chi_tiet_don_hang ct 
          JOIN san_pham sp ON ct.san_pham_id = sp.id 
          WHERE ct.don_hang_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $don_hang_id);
    $stmt->execute();
    $ket_qua = $stmt->get_result();

    $chi_tiet = array();
    if ($ket_qua->num_rows > 0) {
        while ($row = $ket_qua->fetch_assoc()) {
            $chi_tiet[] = $row;
        }
    }

    return $chi_tiet;
}

// Hàm lấy số lượng đã bán của sản phẩm
function lay_so_luong_da_ban($conn, $san_pham_id)
{
    $sql = "SELECT SUM(ct.so_luong) as tong_ban 
            FROM chi_tiet_don_hang ct 
            JOIN don_hang dh ON ct.don_hang_id = dh.id 
            WHERE ct.san_pham_id = ? AND dh.trang_thai = 3";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $san_pham_id);
    $stmt->execute();
    $ket_qua = $stmt->get_result();

    if ($ket_qua->num_rows > 0) {
        $row = $ket_qua->fetch_assoc();
        return $row['tong_ban'] ? $row['tong_ban'] : 0;
    }

    return 0;
}

// Ham them san pham vao gio hang
function them_vao_gio_hang($san_pham_id, $so_luong = 1)
{
    if (!isset($_SESSION['gio_hang'])) {
        $_SESSION['gio_hang'] = array();
    }

    // Kiem tra san pham da co trong gio hang chua
    if (isset($_SESSION['gio_hang'][$san_pham_id])) {
        $_SESSION['gio_hang'][$san_pham_id] += $so_luong;
    } else {
        $_SESSION['gio_hang'][$san_pham_id] = $so_luong;
    }

    return true;
}

// Ham cap nhat so luong san pham trong gio hang
function cap_nhat_gio_hang($san_pham_id, $so_luong)
{
    if (isset($_SESSION['gio_hang'][$san_pham_id])) {
        if ($so_luong <= 0) {
            xoa_san_pham_gio_hang($san_pham_id);
        } else {
            $_SESSION['gio_hang'][$san_pham_id] = $so_luong;
        }
    }

    return true;
}

// Ham xoa san pham khoi gio hang
function xoa_san_pham_gio_hang($san_pham_id)
{
    if (isset($_SESSION['gio_hang'][$san_pham_id])) {
        unset($_SESSION['gio_hang'][$san_pham_id]);
    }

    return true;
}

// Ham xoa toan bo gio hang
function xoa_gio_hang()
{
    $_SESSION['gio_hang'] = array();
    return true;
}

// Ham lay tong so san pham trong gio hang
function tong_so_san_pham_gio_hang()
{
    $tong = 0;

    if (isset($_SESSION['gio_hang'])) {
        foreach ($_SESSION['gio_hang'] as $so_luong) {
            $tong += $so_luong;
        }
    }

    return $tong;
}

// Ham tinh tong gia tri gio hang
function tong_gia_tri_gio_hang($conn)
{
    $tong = 0;

    if (isset($_SESSION['gio_hang']) && count($_SESSION['gio_hang']) > 0) {
        foreach ($_SESSION['gio_hang'] as $san_pham_id => $so_luong) {
            $san_pham = lay_san_pham_theo_id($conn, $san_pham_id);
            if ($san_pham) {
                // Sử dụng giá khuyến mãi nếu có
                $gia = ($san_pham['gia_khuyen_mai'] && $san_pham['gia_khuyen_mai'] < $san_pham['gia'])
                    ? $san_pham['gia_khuyen_mai']
                    : $san_pham['gia'];
                $tong += $gia * $so_luong;
            }
        }
    }

    return $tong;
}

// Ham tao don hang moi
function tao_don_hang($conn, $khach_hang_id, $ho_ten, $email, $dien_thoai, $dia_chi, $ghi_chu = '')
{
    $tong_tien = tong_gia_tri_gio_hang($conn);
    $trang_thai = 0; // Đơn hàng mới
    $ngay_tao = date('Y-m-d H:i:s');

    $sql = "INSERT INTO don_hang (khach_hang_id, ho_ten, email, dien_thoai, dia_chi, ghi_chu, tong_tien, trang_thai, ngay_tao) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssis", $khach_hang_id, $ho_ten, $email, $dien_thoai, $dia_chi, $ghi_chu, $tong_tien, $trang_thai, $ngay_tao);

    if ($stmt->execute()) {
        $don_hang_id = $conn->insert_id;

        // Them chi tiet don hang
        foreach ($_SESSION['gio_hang'] as $san_pham_id => $so_luong) {
            $san_pham = lay_san_pham_theo_id($conn, $san_pham_id);
            if ($san_pham) {
                // Sử dụng giá khuyến mãi nếu có
                $gia = ($san_pham['gia_khuyen_mai'] && $san_pham['gia_khuyen_mai'] < $san_pham['gia'])
                    ? $san_pham['gia_khuyen_mai']
                    : $san_pham['gia'];
                $thanh_tien = $gia * $so_luong;

                $sql = "INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia, thanh_tien) 
                      VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiidd", $don_hang_id, $san_pham_id, $so_luong, $gia, $thanh_tien);
                $stmt->execute();
            }
        }

        // Xoa gio hang sau khi dat hang thanh cong
        $_SESSION['gio_hang'] = array();

        return $don_hang_id;
    }

    return false;
}

// Ham cap nhat trang thai don hang
function cap_nhat_trang_thai_don_hang($conn, $don_hang_id, $trang_thai)
{
    $sql = "UPDATE don_hang SET trang_thai = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $trang_thai, $don_hang_id);

    return $stmt->execute();
}

// Ham lay danh sach nguoi dung
function lay_danh_sach_nguoi_dung($conn, $gioi_han = 10, $trang = 1)
{
    $trang = max(1, $trang); // Đảm bảo trang luôn >= 1
    $bat_dau = ($trang - 1) * $gioi_han;

    $sql = "SELECT * FROM nguoi_dung ORDER BY id DESC LIMIT $bat_dau, $gioi_han";
    $ket_qua = $conn->query($sql);

    $nguoi_dung = array();
    if ($ket_qua->num_rows > 0) {
        while ($row = $ket_qua->fetch_assoc()) {
            $nguoi_dung[] = $row;
        }
    }

    return $nguoi_dung;
}

// Ham dang ky tai khoan moi
function dang_ky_tai_khoan($conn, $ho_ten, $email, $mat_khau, $dien_thoai = '', $dia_chi = '')
{
    // Kiem tra email da ton tai chua
    $sql = "SELECT * FROM nguoi_dung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $ket_qua = $stmt->get_result();

    if ($ket_qua->num_rows > 0) {
        return false; // Email da ton tai
    }

    // Ma hoa mat khau
    $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);
    $ngay_tao = date('Y-m-d H:i:s');

    $sql = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, dien_thoai, dia_chi, ngay_tao) 
          VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $ho_ten, $email, $mat_khau_hash, $dien_thoai, $dia_chi, $ngay_tao);

    if ($stmt->execute()) {
        return $conn->insert_id;
    }

    return false;
}

// Ham dang nhap
function dang_nhap($conn, $email, $mat_khau)
{
    $sql = "SELECT * FROM nguoi_dung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $ket_qua = $stmt->get_result();

    if ($ket_qua->num_rows > 0) {
        $nguoi_dung = $ket_qua->fetch_assoc();

        // Kiem tra mat khau
        if (password_verify($mat_khau, $nguoi_dung['mat_khau'])) {
            // Dang nhap thanh cong
            $_SESSION['nguoi_dung_id'] = $nguoi_dung['id'];
            $_SESSION['nguoi_dung_ten'] = $nguoi_dung['ho_ten'];
            $_SESSION['nguoi_dung_email'] = $nguoi_dung['email'];

            // Lưu avatar nếu có
            if (!empty($nguoi_dung['avatar'])) {
                $_SESSION['nguoi_dung_avatar'] = $nguoi_dung['avatar'];
            }

            return true;
        }
    }

    return false;
}

// Ham dang xuat
function dang_xuat()
{
    // Xoa thong tin nguoi dung trong session
    unset($_SESSION['nguoi_dung_id']);
    unset($_SESSION['nguoi_dung_ten']);
    unset($_SESSION['nguoi_dung_email']);
    unset($_SESSION['nguoi_dung_avatar']);

    // Giu lai gio hang
    $gio_hang = isset($_SESSION['gio_hang']) ? $_SESSION['gio_hang'] : array();

    // Xoa toan bo session
    session_unset();

    // Khoi phuc gio hang
    $_SESSION['gio_hang'] = $gio_hang;
}

// Ham kiem tra nguoi dung da dang nhap chua
function da_dang_nhap()
{
    return isset($_SESSION['nguoi_dung_id']);
}

// Ham dinh dang tien te
function dinh_dang_tien($so)
{
    return number_format($so, 0, ',', '.') . ' đ';
}

// Ham tao URL than thien
function tao_url_than_thien($chuoi)
{
    $chuoi = trim($chuoi);
    $chuoi = strtolower($chuoi);
    $chuoi = preg_replace('/[^a-z0-9-]/', '-', $chuoi);
    $chuoi = preg_replace('/-+/', '-', $chuoi);
    $chuoi = trim($chuoi, '-');

    return $chuoi;
}

// Ham phan trang
function tao_phan_trang($trang_hien_tai, $tong_so_trang, $url_co_so)
{
    $html = '<div class="phan-trang">';

    // Nut trang truoc
    if ($trang_hien_tai > 1) {
        $html .= '<a href="' . $url_co_so . '&trang=' . ($trang_hien_tai - 1) . '" class="trang-truoc">Trước</a>';
    }

    // Cac trang
    $bat_dau = max(1, $trang_hien_tai - 2);
    $ket_thuc = min($tong_so_trang, $trang_hien_tai + 2);

    for ($i = $bat_dau; $i <= $ket_thuc; $i++) {
        if ($i == $trang_hien_tai) {
            $html .= '<span class="trang-hien-tai">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $url_co_so . '&trang=' . $i . '">' . $i . '</a>';
        }
    }

    // Nut trang sau
    if ($trang_hien_tai < $tong_so_trang) {
        $html .= '<a href="' . $url_co_so . '&trang=' . ($trang_hien_tai + 1) . '" class="trang-sau">Sau</a>';
    }

    $html .= '</div>';

    return $html;
}
?>

