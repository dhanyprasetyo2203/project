<?php
session_start();
if (!isset($_SESSION['furni_admin']) || $_SESSION['furni_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$name        = trim($data['name']        ?? '');
$category    = trim($data['category']    ?? '');
$price       = (int)($data['price']      ?? 0);
$stock       = max(0, (int)($data['stock'] ?? 50));
$image       = trim($data['image']       ?? '');
$description = trim($data['description'] ?? '');
$id          = !empty($data['id']) ? (int)$data['id'] : null;

if ($name === '' || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Nama dan harga wajib diisi']);
    exit;
}

try {
    if ($id) {
        $stmt = $db->prepare("UPDATE products SET name=?, category=?, price=?, stock=?, image=?, description=? WHERE id=?");
        $stmt->execute([$name, $category, $price, $stock, $image, $description, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO products (name, category, price, stock, image, description) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name, $category, $price, $stock, $image, $description]);
    }
    echo json_encode(['success' => true, 'id' => $id ?? $db->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
