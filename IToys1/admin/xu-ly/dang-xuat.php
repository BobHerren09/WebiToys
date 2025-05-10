<?php
session_start();

// Xoa session admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_ten']);

// Xoa toan bo session
session_destroy();

// Chuyen huong ve trang dang nhap
header("Location: ../dang-nhap.php");
exit();
?>


