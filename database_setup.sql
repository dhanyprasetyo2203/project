-- ============================================
-- Query SQL untuk FurniRest Database
-- ============================================
-- Jalankan query ini di phpMyAdmin
-- Pilih database furnirest_db, 
-- Paste semua code ini ke tab SQL, lalu klik GO

-- ============================================
-- 1. TABEL PRODUCTS (Produk Furniture)
-- ============================================
DROP TABLE IF EXISTS products;

CREATE TABLE products (
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
);

-- ============================================
-- 2. TABEL ORDERS (Pesanan Customer)
-- ============================================
DROP TABLE IF EXISTS orders;

CREATE TABLE orders (
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
);

-- ============================================
-- 3. DATA AWAL PRODUK
-- ============================================
INSERT INTO products (name, category, price, old_price, rating, sold, description, image, colors, sizes) VALUES
('Sofa Minimalis L-Shape', 'Sofa', 3500000.00, 4500000.00, 4.8, 234, 'Sofa modern dengan busa berkualitas tinggi, nyaman untuk bersantai.', 'assets/images/products/produk-1.jpg', 'Coklat, Abu-abu, Hitam', '2 Seater, 3 Seater, L-Shape'),
('Meja Makan Kayu Jati', 'Meja', 2800000.00, 3200000.00, 4.6, 156, 'Meja makan dari kayu jati solid, cocok untuk 4-6 orang.', 'assets/images/products/produk-2.jpg', 'Natural, Walnut', '140cm, 160cm, 180cm'),
('Kursi Kantor Ergonomis', 'Kursi', 850000.00, 950000.00, 4.7, 89, 'Kursi kantor dengan desain ergonomis dan adjustable height.', 'assets/images/products/produk-3.jpg', 'Hitam, Putih, Biru', 'Standard, Large'),
('Lemari Pakaian 3 Pintu', 'Lemari', 2200000.00, 2500000.00, 4.5, 67, 'Lemari pakaian dengan 3 pintu geser dan ruang penyimpanan luas.', 'assets/images/products/produk-4.jpg', 'Coklat, Putih, Abu-abu', '180cm, 200cm, 220cm'),
('Tempat Tidur Queen Size', 'Tempat Tidur', 3200000.00, 3800000.00, 4.9, 145, 'Tempat tidur queen size dengan headboard elegan dan storage drawer.', 'assets/images/products/produk-5.jpg', 'Coklat, Putih, Hitam', '160x200cm, 180x200cm'),
('Meja Kerja Minimalis', 'Meja', 1200000.00, 1400000.00, 4.4, 78, 'Meja kerja dengan desain minimalis dan laci penyimpanan.', 'assets/images/products/produk-6.jpg', 'Putih, Coklat, Hitam', '120x60cm, 140x70cm'),
('Kursi Makan Set 4', 'Kursi', 1600000.00, 1800000.00, 4.6, 92, 'Set 4 kursi makan dengan desain modern dan upholsteri kulit sintetis.', 'assets/images/products/produk-7.jpg', 'Hitam, Coklat, Putih', 'Standard'),
('Rak Buku 5 Tingkat', 'Lemari', 950000.00, 1100000.00, 4.3, 56, 'Rak buku dengan 5 tingkat dan material kayu solid.', 'assets/images/products/produk-8.jpg', 'Natural, Walnut, Hitam', '180cm, 200cm');

-- 4. Insert data produk awal
INSERT INTO products (name, category, price, old_price, rating, sold, description, image, colors, sizes) VALUES
('Sofa Minimalis L-Shape', 'Sofa', 3500000.00, 4500000.00, 4.8, 234, 'Sofa modern dengan busa berkualitas tinggi, nyaman untuk bersantai. Desain ergonomis dan material premium.', 'assets/images/products/produk-1.jpg', '["Coklat", "Abu-abu", "Hitam"]', '["2 Seater", "3 Seater", "L-Shape"]'),
('Meja Makan Kayu Jati', 'Meja', 2800000.00, 3200000.00, 4.6, 156, 'Meja makan dari kayu jati solid, finishing natural anti gores. Cocok untuk 4-6 orang.', 'assets/images/products/produk-2.jpg', '["Natural", "Walnut"]', '["140cm", "160cm", "180cm"]'),
('Kursi Kantor Ergonomis', 'Kursi', 850000.00, 950000.00, 4.7, 89, 'Kursi kantor dengan desain ergonomis, adjustable height dan reclining backrest.', 'assets/images/products/produk-3.jpg', '["Hitam", "Putih", "Biru"]', '["Standard", "Large"]'),
('Lemari Pakaian 3 Pintu', 'Lemari', 2200000.00, 2500000.00, 4.5, 67, 'Lemari pakaian dengan 3 pintu geser, ruang penyimpanan yang luas dan elegan.', 'assets/images/products/produk-4.jpg', '["Coklat", "Putih", "Abu-abu"]', '["180cm", "200cm", "220cm"]'),
('Tempat Tidur Queen Size', 'Tempat Tidur', 3200000.00, 3800000.00, 4.9, 145, 'Tempat tidur queen size dengan headboard yang elegan dan storage drawer.', 'assets/images/products/produk-5.jpg', '["Coklat", "Putih", "Hitam"]', '["160x200cm", "180x200cm"]'),
('Meja Kerja Minimalis', 'Meja', 1200000.00, 1400000.00, 4.4, 78, 'Meja kerja dengan desain minimalis, space untuk komputer dan laci penyimpanan.', 'assets/images/products/produk-6.jpg', '["Putih", "Coklat", "Hitam"]', '["120x60cm", "140x70cm"]'),
('Kursi Makan Set 4', 'Kursi', 1600000.00, 1800000.00, 4.6, 92, 'Set 4 kursi makan dengan desain modern dan upholsteri kulit sintetis.', 'assets/images/products/produk-7.jpg', '["Hitam", "Coklat", "Putih"]', '["Standard"]'),
('Rak Buku 5 Tingkat', 'Lemari', 950000.00, 1100000.00, 4.3, 56, 'Rak buku dengan 5 tingkat, desain industrial dan material kayu solid.', 'assets/images/products/produk-8.jpg', '["Natural", "Walnut", "Hitam"]', '["180cm", "200cm"]'),
('Sofa Bed Convertible', 'Sofa', 2800000.00, 3200000.00, 4.7, 134, 'Sofa yang bisa diubah menjadi tempat tidur, ideal untuk ruang tamu kecil.', 'assets/images/products/produk-9.jpg', '["Abu-abu", "Biru", "Hijau"]', '["2 Seater", "3 Seater"]'),
('Meja Tamu Rotan', 'Meja', 750000.00, 850000.00, 4.5, 43, 'Meja tamu dari rotan sintetis, desain bohemian dan tahan lama.', 'assets/images/products/produk-10.jpg', '["Natural", "Coklat"]', '["80cm", "100cm"]'),
('Kursi Santai Recliner', 'Kursi', 1950000.00, 2200000.00, 4.8, 76, 'Kursi santai dengan fitur recliner elektrik, sangat nyaman untuk bersantai.', 'assets/images/products/produk-11.jpg', '["Hitam", "Coklat", "Putih"]', '["Standard", "Large"]'),
('Dressoir 2 Pintu', 'Lemari', 1800000.00, 2000000.00, 4.4, 38, 'Dressoir dengan 2 pintu dan laci, kombinasi kayu dan kaca tempered.', 'assets/images/products/produk-12.jpg', '["Putih", "Coklat", "Hitam"]', '["120cm", "140cm"]'),
('Tempat Tidur Minimalis', 'Tempat Tidur', 2400000.00, 2700000.00, 4.6, 89, 'Tempat tidur dengan desain minimalis, headboard geometris dan storage.', 'assets/images/products/produk-13.jpg', '["Putih", "Abu-abu", "Hitam"]', '["140x200cm", "160x200cm"]'),
('Meja Konsol', 'Meja', 1100000.00, 1250000.00, 4.2, 34, 'Meja konsol dengan 3 laci, desain vintage dan finishing oak.', 'assets/images/products/produk-14.jpg', '["Oak", "Walnut", "White"]', '["120cm", "140cm"]'),
('Kursi Bar Set 2', 'Kursi', 1400000.00, 1600000.00, 4.5, 67, 'Set 2 kursi bar tinggi dengan desain modern dan adjustable height.', 'assets/images/products/produk-15.jpg', '["Hitam", "Putih", "Silver"]', '["75cm", "80cm"]');