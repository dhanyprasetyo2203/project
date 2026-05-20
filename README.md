# FurniRest - Toko Furniture Online

Website toko furniture sederhana dengan PHP dan MySQL.

## 🚀 Fitur

- ✅ Tampilan produk dari database
- ✅ Form pemesanan yang menyimpan ke database
- ✅ Responsive design
- ✅ Modern UI/UX

## 📋 Persyaratan

- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web server (Apache/Nginx)

## 🛠️ Setup

### 1. Database Setup

1. Buat database MySQL baru
2. Jalankan query SQL dari file `database_setup.sql`

```sql
-- Jalankan di phpMyAdmin atau MySQL command line
-- File: database_setup.sql
```

### 2. Konfigurasi Database

Edit file `config.php` sesuai dengan setting database Anda:

```php
$host = "localhost"; // Ganti jika perlu
$user = "root"; // Username database
$password = ""; // Password database
$database = "furnirest_db"; // Nama database
```

### 3. Upload Files

Upload semua file ke web server Anda:
- `index.php`
- `config.php`
- `script.js`
- `style.css`
- `database_setup.sql`
- Folder `images/`

### 4. Akses Website

Buka `index.php` di browser Anda.

## 📁 Struktur File

```
project/
├── index.php          # Halaman utama
├── config.php         # Konfigurasi database
├── script.js          # JavaScript
├── style.css          # CSS styling
├── database_setup.sql # Query SQL database
└── images/            # Folder gambar
    ├── logo/
    ├── hero/
    ├── products/
    └── ...
```

## 🗄️ Struktur Database

### Tabel `products`
- `id` (INT, Primary Key)
- `name` (VARCHAR)
- `category` (VARCHAR)
- `price` (DECIMAL)
- `old_price` (DECIMAL, nullable)
- `rating` (DECIMAL)
- `sold` (INT)
- `description` (TEXT)
- `image` (VARCHAR)
- `colors` (VARCHAR, JSON)
- `sizes` (VARCHAR, JSON)

### Tabel `orders`
- `id` (INT, Primary Key)
- `product_id` (INT, Foreign Key)
- `customer_name` (VARCHAR)
- `customer_phone` (VARCHAR)
- `customer_address` (TEXT)
- `quantity` (INT)
- `total_price` (DECIMAL)
- `order_date` (TIMESTAMP)
- `status` (VARCHAR)

## 🔧 Cara Kerja

1. **Tampilan Produk**: Data produk diambil dari tabel `products` dan ditampilkan di halaman utama
2. **Form Pemesanan**: User mengisi form order, data disimpan ke tabel `orders`
3. **Konfirmasi**: Setelah submit, muncul pesan sukses/error

## 📞 Dukungan

Jika ada pertanyaan, silakan edit file sesuai kebutuhan Anda.

---

**Dibuat dengan ❤️ untuk keperluan pembelajaran PHP & MySQL**