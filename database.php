CREATE DATABASE IF NOT EXISTS furninest_db;
USE furninest_db;

-- Tabel produk (isi sendiri nanti via phpMyAdmin)
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    price INT NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100)
);

-- Contoh data produk
INSERT INTO products (name, price, image, category) VALUES
('Nordic Velvet Sofa', 4500000, 'assets/images/hero/hero-1.jpg', 'Sofa'),
('Eames Chair', 3200000, 'assets/images/hero/hero-2.jpg', 'Kursi'),
('Modern Cabinet', 2800000, 'assets/images/hero/hero-3.jpg', 'Lemari'),
('Luxury Bed', 7800000, 'assets/images/products/produk-5.jpg', 'Tempat Tidur');

-- Tabel pesanan (menyimpan order dari customer)
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT DEFAULT 1,
    total_price INT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);