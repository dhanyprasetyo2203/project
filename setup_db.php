<?php
require_once 'config.php';

try {
    $db->beginTransaction();

    $db->exec("CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        old_price DECIMAL(10,2) DEFAULT NULL,
        rating DECIMAL(3,1) DEFAULT 0,
        sold INT DEFAULT 0,
        description TEXT,
        image VARCHAR(255),
        colors VARCHAR(255),
        sizes VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        product_id INT NOT NULL,
        customer_name VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        customer_address TEXT NOT NULL,
        quantity INT DEFAULT 1,
        total_price DECIMAL(10,2) NOT NULL,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(50) DEFAULT 'pending',
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
    )");

    $count = $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO products (name, category, price, old_price, rating, sold, description, image, colors, sizes) VALUES
            ('Sofa Minimalis L-Shape', 'Sofa', 3500000.00, 4500000.00, 4.8, 234, 'Sofa modern dengan busa berkualitas tinggi, nyaman untuk bersantai.', 'assets/images/products/produk-1.jpg', 'Coklat, Abu-abu, Hitam', '2 Seater, 3 Seater, L-Shape'),
            ('Meja Makan Kayu Jati', 'Meja', 2800000.00, 3200000.00, 4.6, 156, 'Meja makan dari kayu jati solid, cocok untuk 4-6 orang.', 'assets/images/products/produk-2.jpg', 'Natural, Walnut', '140cm, 160cm, 180cm'),
            ('Kursi Kantor Ergonomis', 'Kursi', 850000.00, 950000.00, 4.7, 89, 'Kursi kantor dengan desain ergonomis dan adjustable height.', 'assets/images/products/produk-3.jpg', 'Hitam, Putih, Biru', 'Standard, Large'),
            ('Lemari Pakaian 3 Pintu', 'Lemari', 2200000.00, 2500000.00, 4.5, 67, 'Lemari pakaian dengan 3 pintu geser dan ruang penyimpanan luas.', 'assets/images/products/produk-4.jpg', 'Coklat, Putih, Abu-abu', '180cm, 200cm, 220cm'),
            ('Tempat Tidur Queen Size', 'Tempat Tidur', 3200000.00, 3800000.00, 4.9, 145, 'Tempat tidur queen size dengan headboard elegan dan storage drawer.', 'assets/images/products/produk-5.jpg', 'Coklat, Putih, Hitam', '160x200cm, 180x200cm')");
    }

    $db->commit();
    echo "✅ Setup selesai. Tabel products dan orders sudah tersedia.\n";
    if ($count == 0) {
        echo "✅ Data produk awal telah ditambahkan.\n";
    } else {
        echo "ℹ️ Tabel products sudah berisi data, tidak menambahkan ulang.\n";
    }
} catch (PDOException $e) {
    $db->rollBack();
    echo "❌ Setup gagal: " . $e->getMessage();
}
?>
