<?php
session_start();
if (!isset($_SESSION['furni_user']) && !isset($_SESSION['furni_admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../config.php';
header('Content-Type: application/json');

try {
    $rows = $db->query("SELECT * FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Format persis seperti array JS yang lama, supaya semua fungsi tetap jalan
    $products = array_map(function($p) {
        return [
            'id'       => (int)$p['id'],
            'name'     => $p['name'],
            'category' => $p['category'] ?? '',
            'price'    => (int)$p['price'],
            'oldPrice' => (int)round($p['price'] * 1.25),
            'rating'   => 4.7,
            'sold'     => 120,
            'stock'    => (int)($p['stock'] ?? 50),
            'desc'     => $p['description'] ?? '',
            'image'    => $p['image'] ?? '',
            'colors'   => ['Default'],
            'sizes'    => ['Standard'],
        ];
    }, $rows);

    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
