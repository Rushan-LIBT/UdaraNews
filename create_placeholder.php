<?php
// Create placeholder images locally to avoid CORB issues
header('Content-Type: image/jpeg');
header('Cache-Control: max-age=3600');

// Get parameters
$width = isset($_GET['w']) ? (int)$_GET['w'] : 800;
$height = isset($_GET['h']) ? (int)$_GET['h'] : 400;
$text = isset($_GET['text']) ? $_GET['text'] : 'News Image';
$bg_color = isset($_GET['bg']) ? $_GET['bg'] : '3498db';

// Limit dimensions for security
$width = min(max($width, 100), 1200);
$height = min(max($height, 100), 800);

// Create image
$image = imagecreate($width, $height);

// Convert hex color to RGB
$bg_rgb = sscanf($bg_color, "%02x%02x%02x");
$bg = imagecolorallocate($image, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
$text_color = imagecolorallocate($image, 255, 255, 255);

// Fill background
imagefill($image, 0, 0, $bg);

// Add text
$font_size = min($width, $height) / 20;
$font_size = max($font_size, 12);

// Calculate text position
$text_box = imagettfbbox($font_size, 0, __DIR__ . '/arial.ttf', $text);
if (!$text_box) {
    // Fallback to imagestring if TTF font not available
    $text_width = imagefontwidth(5) * strlen($text);
    $text_height = imagefontheight(5);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    imagestring($image, 5, $x, $y, $text, $text_color);
} else {
    $text_width = $text_box[4] - $text_box[0];
    $text_height = $text_box[1] - $text_box[7];
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2 + $text_height;
    imagettftext($image, $font_size, 0, $x, $y, $text_color, __DIR__ . '/arial.ttf', $text);
}

// Output image
imagejpeg($image, null, 85);
imagedestroy($image);
?>