<?php
session_start();
if (!isset($_SESSION['furni_admin']) || $_SESSION['furni_admin'] !== true) {
    http_response_code(403); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit;
}
require_once '../config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);

try {
    $db->prepare("DELETE FROM orders WHERE id=?")->execute([$id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
