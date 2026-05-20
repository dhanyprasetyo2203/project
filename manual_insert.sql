-- Manual SQL untuk menambah data langsung di phpMyAdmin
-- Pilih database: furninest_db
-- Salin seluruh query ini ke tab SQL, lalu klik GO.

INSERT INTO products (name, category, price, old_price, rating, sold, description, image, colors, sizes) VALUES
('Meja Belajar Minimalis', 'Meja', 980000.00, NULL, 4.5, 12, 'Meja belajar minimalis cocok untuk ruang kecil.', 'assets/images/products/produk-9.jpg', 'Hitam,Putih', '120x60cm');

INSERT INTO products (name, category, price, old_price, rating, sold, description, image, colors, sizes) VALUES
('Sofa Santai Modern', 'Sofa', 2590000.00, NULL, 4.7, 32, 'Sofa santai modern dengan bantalan empuk untuk ruang tamu.', 'assets/images/products/produk-10.jpg', 'Abu-abu,Hitam', '2 Seater,3 Seater');

INSERT INTO orders (product_id, customer_name, customer_phone, customer_address, quantity, total_price, status) VALUES
(1, 'Rina', '081234567890', 'Jl. Merdeka No 10', 1, 3500000.00, 'pending');
