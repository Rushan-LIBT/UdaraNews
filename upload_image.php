<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$uploadDir = 'uploads/news/';
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileType = $file['type'];

// Validate file type
if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.']);
    exit;
}

// Validate file size
if ($fileSize > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
    exit;
}

// Generate unique filename
$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
$newFileName = 'news_' . time() . '_' . uniqid() . '.' . $fileExtension;
$uploadPath = $uploadDir . $newFileName;

// Create upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Move uploaded file
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    // Return the relative path to the uploaded file
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'image_path' => $uploadPath,
        'image_url' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $uploadPath
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>