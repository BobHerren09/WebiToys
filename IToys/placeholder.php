<?php
// Tạo hình ảnh placeholder
header('Content-Type: image/jpeg');
$width = 400;
$height = 300;
$image = imagecreatetruecolor($width, $height);

// Màu nền
$bg_color = imagecolorallocate($image, 240, 240, 240);
imagefill($image, 0, 0, $bg_color);

// Màu chữ
$text_color = imagecolorallocate($image, 100, 100, 100);

// Vẽ khung
$border_color = imagecolorallocate($image, 200, 200, 200);
imagerectangle($image, 0, 0, $width - 1, $height - 1, $border_color);

// Vẽ chữ
$text = "No Image";
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
$x = ($width - $text_width) / 2;
$y = ($height - $text_height) / 2;
imagestring($image, $font_size, $x, $y, $text, $text_color);

// Xuất hình ảnh
imagejpeg($image);
imagedestroy($image);
?>


