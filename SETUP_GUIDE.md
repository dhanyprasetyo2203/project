# 📖 PANDUAN SETUP FURNIREST TOKO FURNITURE

> Panduan lengkap step-by-step cara menghubungkan website dengan database MySQL

---

## ✅ STEP 1: Pastikan PHP & MySQL Sudah Berjalan

### Di Windows (XAMPP):
1. Buka **XAMPP Control Panel**
2. Klik **Start** untuk:
   - **Apache** (Web Server)
   - **MySQL** (Database)
3. Pastikan kedua-duanya berwarna hijau ✅

### Cara Akses:
- Website: `http://localhost/phpmyadmin/` (Untuk manage database)

---

## ✅ STEP 2: Buka phpMyAdmin

1. Pastikan Apache & MySQL sudah berjalan (dari Step 1)
2. Buka browser, ketik: `http://localhost/phpmyadmin`
3. Anda akan melihat halaman phpMyAdmin

**Login:**
- Username: `root`
- Password: **(kosongkan / Enter)**

---

## ✅ STEP 3: Buat Database Baru

1. Di halaman phpMyAdmin, **scroll ke bawah** atau klik menu **"New"** di sebelah kiri
2. Anda akan melihat form: **"Create new database"**
3. Isi nama database: **`furnirest_db`** (HARUS SAMA PERSIS!)
4. Pilih Collation: **`utf8_general_ci`**
5. Klik **"Create"**

**Sekarang database sudah dibuat!** ✅

---

## ✅ STEP 4: Import Data Tabel

1. Setelah database dibuat, pilih/klik database **`furnirest_db`** di sebelah kiri
2. Klik tab **"Import"** (bagian atas halaman)
3. Klik **"Choose File"**
4. Cari dan pilih file: **`database_setup.sql`** (dari folder project Anda)
5. Di bagian bawah, klik **"Import"**

**Tunggu sampai selesai...** ⏳

Setelah selesai, Anda akan melihat pesan success. Sekarang tabel `products` dan `orders` sudah ada di database!

---

## ✅ STEP 5: Verifikasi Tabel

1. Di phpMyAdmin, pilih database **`furnirest_db`**
2. Di sebelah kiri, Anda akan melihat 2 tabel:
   - **`products`** (Daftar produk furniture)
   - **`orders`** (Pesanan pelanggan)

3. Klik tabel **`products`** → tab **"Browse"**
4. Anda akan melihat 8 produk awal sudah ada!

---

## ✅ STEP 6: Konfigurasi Website

### Edit file `config.php`:

Buka file `config.php` di editor (Notepad++, VSCode, dll), pastikan seperti ini:

```php
<?php
// config.php
$host = 'localhost';
$dbname = 'furnirest_db';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("❌ Koneksi gagal: " . $e->getMessage());
}
?>
```

**PENTING:**
- `$dbname` harus: **`furnirest_db`**
- `$username` harus: **`root`**
- `$password` harus: **kosong** (jika XAMPP belum dikonfigurasi password)

Jika Anda punya password MySQL, ubah `$password = '';` menjadi `$password = 'password_anda';`

---

## ✅ STEP 7: Jalankan Website

1. Pastikan Apache sudah **Start** ✅
2. Copy folder project ke: **`C:\xampp\htdocs\furnirest\`** (atau folder sesuai keinginan)
3. Buka browser, ketik:
   ```
   http://localhost/furnirest/index.php
   ```

**Website seharusnya sudah jalan!** 🎉

---

## ⚠️ TROUBLESHOOTING

### Error: "Koneksi database gagal"
**Solusi:**
- ✅ Pastikan MySQL sudah Start di XAMPP
- ✅ Cek database name di `config.php` (harus `furnirest_db`)
- ✅ Cek username & password di `config.php`

### Error: "Table 'furnirest_db.products' doesn't exist"
**Solusi:**
- ✅ Import database_setup.sql lagi
- ✅ Atau manual buat tabel lewat phpMyAdmin

### Produk tidak muncul di website
**Solusi:**
- ✅ Cek tabel products di phpMyAdmin (Browser)
- ✅ Pastikan ada data di dalamnya
- ✅ Jika kosong, insert data manual atau import SQL lagi

### Form order tidak bisa submit
**Solusi:**
- ✅ Cek console browser (F12 → Console)
- ✅ Pastikan PHP tidak ada error
- ✅ Cek form input semua sudah diisi

---

## 🎯 CHECKLIST SETUP

- [ ] Apache berjalan (XAMPP)
- [ ] MySQL berjalan (XAMPP)
- [ ] Database `furnirest_db` dibuat
- [ ] Tabel `products` dan `orders` sudah ada
- [ ] Data awal produk sudah di-import
- [ ] `config.php` sudah dikonfigurasi
- [ ] Website bisa diakses di `http://localhost/furnirest/`
- [ ] Produk muncul di halaman utama
- [ ] Form order bisa diisi dan dikirim

---

## 📞 KONTAK BANTUAN

Jika masih ada error, cek:
1. Error message di website (jangan diabaikan!)
2. Console browser (F12)
3. phpMyAdmin untuk verifikasi database
4. File `config.php` untuk konfigurasi

---

**Selamat! Database sudah siap terhubung!** 🚀
