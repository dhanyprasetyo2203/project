<?php
/**
 * FILE: test_connection.php
 * Gunakan ini untuk test apakah database sudah terhubung
 * Buka di browser: http://localhost/furnirest/test_connection.php
 */

// Konfigurasi Database
$host = 'localhost';
$dbname = 'furnirest_db';
$username = 'root';
$password = '';

echo "<h2>🔍 TEST KONEKSI DATABASE</h2>";
echo "<hr>";

// TEST 1: Coba koneksi
echo "<b>1. TEST KONEKSI:</b><br>";
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Koneksi BERHASIL ke database '<b>$dbname</b>'<br><br>";
} catch(PDOException $e) {
    echo "❌ Koneksi GAGAL: " . $e->getMessage() . "<br><br>";
    die("<b>Solusi:</b><br>1. Pastikan MySQL sudah berjalan di XAMPP<br>2. Cek nama database di config.php<br>3. Cek username & password");
}

// TEST 2: Cek tabel
echo "<b>2. CEK TABEL:</b><br>";
try {
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('products', $tables)) {
        echo "✅ Tabel <b>products</b> ada<br>";
    } else {
        echo "❌ Tabel <b>products</b> TIDAK ada<br>";
    }
    
    if (in_array('orders', $tables)) {
        echo "✅ Tabel <b>orders</b> ada<br>";
    } else {
        echo "❌ Tabel <b>orders</b> TIDAK ada<br>";
    }
    echo "<br>";
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
}

// TEST 3: Cek data produk
echo "<b>3. CEK DATA PRODUK:</b><br>";
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'];
    
    if ($count > 0) {
        echo "✅ Ada <b>$count</b> produk di database<br>";
        echo "<table border='1' cellpadding='10' style='margin-top:10px; border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th></tr>";
        
        $products = $db->query("SELECT id, name, category, price FROM products LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>{$product['id']}</td>";
            echo "<td>{$product['name']}</td>";
            echo "<td>{$product['category']}</td>";
            echo "<td>Rp " . number_format($product['price'], 0, ',', '.') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ Database kosong, belum ada produk<br>";
    }
    echo "<br>";
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br><br>";
}

// TEST 4: Cek data orders
echo "<b>4. CEK DATA ORDERS:</b><br>";
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'];
    
    if ($count > 0) {
        echo "✅ Ada <b>$count</b> order di database<br>";
    } else {
        echo "✅ Belum ada order (Normal, website baru)<br>";
    }
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<b style='color:green;'>✅ JIKA SEMUA CEK HIJAU = DATABASE SIAP DIGUNAKAN!</b>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
    }
    h2 {
        color: #333;
    }
    table {
        background: white;
    }
    td {
        padding: 8px;
    }
</style>