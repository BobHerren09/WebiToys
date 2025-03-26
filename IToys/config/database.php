<?php
// Thong tin ket noi co so du lieu
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'cua_hang_do_choi';

// Tao ket noi
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Kiem tra ket noi
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Dat charset
$conn->set_charset("utf8");
?>


