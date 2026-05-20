<?php
session_start();
if (!isset($_SESSION['furni_admin']) || $_SESSION['furni_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diunggah.']);
    exit;
}

$file     = $_FILES['image'];
$allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSize  = 5 * 1024 * 1024; // 5 MB

if (!in_array($file['type'], $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Format file harus JPG, PNG, WebP, atau GIF.']);
    exit;
}
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5 MB.']);
    exit;
}

$uploadDir = __DIR__ . '/../assets/images/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('product_', true) . '.' . strtolower($ext);
$dest     = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file.']);
    exit;
}

echo json_encode([
    'success' => true,
    'path'    => 'assets/images/uploads/' . $filename
]);
