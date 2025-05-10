<?php<?php
session_start();
include_once("../includes/functions.php");

// Dang xuat
dang_xuat();

// Chuyen huong ve trang chu
header("Location: ../index.php");
exit();
?>

