<?php
session_start();

// Periksa GD library
if (!function_exists('imagecreate')) {
    die('GD library belum aktif di PHP.');
}

// Buat kode captcha 6 karakter
$captcha_code = '';
$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
for ($i = 0; $i < 6; $i++) {
    $captcha_code .= $chars[rand(0, strlen($chars) - 1)];
}

// Simpan di session
$_SESSION['captcha'] = $captcha_code;

// Buat gambar
$width = 150;
$height = 50;
$image = imagecreate($width, $height);

// Warna
$bg_color = imagecolorallocate($image, 255, 255, 255); // putih
$text_color = imagecolorallocate($image, 13, 71, 161); // biru gelap
$line_color = imagecolorallocate($image, 200, 200, 200); // garis noise

// Tambahkan noise garis
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand()%$height, $width, rand()%$height, $line_color);
}

// Tambahkan teks captcha
$font_size = 5; // built-in font
imagestring($image, $font_size, 25, 15, $captcha_code, $text_color);

// Header gambar
header('Content-Type: image/png');
header('Cache-Control: no-cache, must-revalidate');

// Output gambar
imagepng($image);
imagedestroy($image);
?>
