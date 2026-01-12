# 🛒 Sistem Kasir PHP - Dokumentasi Lengkap

Sistem kasir berbasis web dengan 3 role: **Admin**, **Kasir**, dan **Client (Pembeli)**.

---

## 📋 Daftar Isi

1. [Fitur Utama](#fitur-utama)
2. [Teknologi](#teknologi)
3. [Instalasi](#instalasi)
4. [Struktur Database](#struktur-database)
5. [Akun Default](#akun-default)
6. [Panduan Penggunaan](#panduan-penggunaan)
7. [File Struktur](#file-struktur)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Fitur Utama

### 👨‍💼 **Admin**
- ✅ Dashboard dengan statistik lengkap
- ✅ Kelola produk (CRUD) dengan upload gambar
- ✅ Kelola kategori produk
- ✅ Kelola users (Admin, Kasir, Client)
- ✅ Laporan penjualan
- ✅ Monitor stok produk
- ✅ Produk terlaris

### 👨‍💻 **Kasir**
- ✅ Dashboard kasir
- ✅ Transaksi POS dengan calculator
- ✅ Pilih produk dari katalog
- ✅ Hitung kembalian otomatis
- ✅ Cetak struk pembayaran
- ✅ Riwayat transaksi
- ✅ Multiple payment methods

### 👤 **Client (Pembeli)**
- ✅ Dashboard pembeli
- ✅ Katalog produk dengan search & filter
- ✅ Keranjang belanja
- ✅ Checkout online
- ✅ Riwayat pesanan
- ✅ Detail pesanan

---

## 💻 Teknologi

- **Backend**: PHP 7.4+ (Native PHP, PDO)
- **Database**: MySQL 5.7+
- **Frontend**: 
  - Bootstrap 5.3
  - Bootstrap Icons
  - jQuery 3.7
- **Architecture**: OOP (Object Oriented Programming)
- **Security**: 
  - Password hashing (bcrypt)
  - Prepared statements (SQL Injection prevention)
  - Session management
  - Role-based access control

---

## 📦 Instalasi

### 1. **Requirements**
```bash
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache Web Server
- Git (optional)
```

### 2. **Clone atau Download Project**
```bash
# Jika sudah membuat struktur folder
cd kasir-app

# Atau download dan extract
```

### 3. **Import Database**
```bash
# Login ke MySQL
mysql -u root -p

# Buat database dan import
mysql -u root -p kasir_db < database/kasir_db.sql
```

Atau via phpMyAdmin:
1. Buka phpMyAdmin
2. Create database `kasir_db`
3. Import file `database/kasir_db.sql`

### 4. **Konfigurasi Database**
Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Sesuaikan
define('DB_PASS', '');            // Sesuaikan
define('DB_NAME', 'kasir_db');
```

### 5. **Konfigurasi Base URL**
Edit file `config/config.php`:
```php
define('BASE_URL', 'http://localhost/kasir-app'); // Sesuaikan dengan URL kamu
```

### 6. **Set Permissions (Linux/Mac)**
```bash
chmod -R 755 kasir-app/
chmod -R 777 kasir-app/uploads/
```

### 7. **Akses Aplikasi**
```
http://localhost/kasir-app
```

---

## 🗄️ Struktur Database

### Tabel Utama:

1. **users** - Data user (admin, kasir, client)
2. **categories** - Kategori produk
3. **products** - Data produk
4. **transactions** - Transaksi header
5. **transaction_details** - Detail item transaksi
6. **cart** - Keranjang belanja client

### Entity Relationship:
```
users (1) -----> (N) transactions
products (1) --> (N) transaction_details
categories (1) -> (N) products
users (1) -----> (N) cart (N) <----- (1) products
```

---

## 🔑 Akun Default

### Admin
```
Username: admin
Password: admin123
URL: http://localhost/kasir-app/admin/dashboard.php
```
⚠️ **PENTING**: Admin login langsung ke `/admin/dashboard.php` (tidak dari halaman login)

### Kasir
```
Username: kasir1
Password: kasir123
```

### Client
Registrasi melalui: `http://localhost/kasir-app/auth/register.php`

---

## 📖 Panduan Penggunaan

### Untuk Admin:

1. **Login**: Akses `/admin/dashboard.php`
2. **Tambah Produk**:
   - Menu: Produk → Tambah Produk
   - Isi form dan upload gambar
   - Simpan
3. **Tambah User**:
   - Menu: Users → Tambah User
   - Pilih role (admin/kasir/client)
4. **Lihat Laporan**:
   - Menu: Laporan
   - Filter berdasarkan tanggal

### Untuk Kasir:

1. **Login**: `/auth/login.php` → Tab Kasir
2. **Transaksi Baru**:
   - Klik "Transaksi Baru"
   - Pilih produk dari katalog
   - Masukkan jumlah bayar di calculator
   - Klik "Hitung Kembalian"
   - Proses pembayaran
3. **Cetak Struk**: Otomatis redirect ke halaman cetak

### Untuk Client:

1. **Registrasi**: `/auth/register.php`
2. **Login**: `/auth/login.php` → Tab Pembeli
3. **Belanja**:
   - Browse produk
   - Tambah ke keranjang
   - Checkout
4. **Lihat Pesanan**: Menu "Pesanan Saya"

---

## 📁 File Struktur

```
kasir-app/
├── config/
│   ├── database.php          # Koneksi database
│   └── config.php            # Konfigurasi aplikasi
├── includes/
│   ├── header.php            # Header HTML
│   ├── footer.php            # Footer HTML
│   ├── navbar.php            # Navigation bar
│   └── functions.php         # Helper functions
├── classes/
│   ├── Auth.php              # Authentication
│   ├── Database.php          # Database operations
│   ├── Product.php           # Product management
│   ├── Transaction.php       # Transaction handling
│   └── User.php              # User management
├── assets/
│   ├── css/style.css         # Custom styling
│   ├── js/main.js            # Main JavaScript
│   └── js/calculator.js      # Calculator for kasir
├── admin/                    # Admin pages
├── kasir/                    # Kasir pages
├── client/                   # Client pages
├── auth/                     # Auth pages
├── database/                 # SQL file
├── uploads/products/         # Product images
└── index.php                 # Landing page
```

---

## 🎨 Theme Warna

```css
- Background: #F5F7FA (abu terang)
- Navbar: #1E3A8A (biru tua/navy)
- Success/Bayar: #22C55E (hijau)
- Danger/Batal: #EF4444 (merah)
```

---

## 🔧 Troubleshooting

### Error: Database connection failed
```bash
- Cek username/password di config/database.php
- Pastikan MySQL service running
- Cek nama database sudah benar
```

### Error: Page not found
```bash
- Cek BASE_URL di config/config.php
- Pastikan .htaccess ada (jika pakai Apache)
```

### Gambar produk tidak muncul
```bash
- Cek folder uploads/products/ ada dan writable
- chmod 777 uploads/products/
```

### Calculator tidak berfungsi
```bash
- Cek browser console untuk error JavaScript
- Pastikan jQuery dan calculator.js ter-load
```

### Session error
```bash
- Cek permission folder session PHP
- session.save_path di php.ini
```

---

## 📝 Catatan Penting

1. **Security**: 
   - Ganti password default setelah instalasi
   - Jangan expose database credentials
   - Enable HTTPS untuk production

2. **Backup**:
   - Backup database secara rutin
   - Backup folder uploads/

3. **Development**:
   - Gunakan `error_reporting(0)` untuk production
   - Aktifkan logging untuk debugging

4. **Performance**:
   - Optimize images sebelum upload
   - Add index pada kolom yang sering di-query
   - Gunakan caching jika traffic tinggi

---

## 👨‍💻 Developer

Dibuat dengan ❤️ menggunakan PHP Native + Bootstrap

---

## 📄 License

Free to use for educational purposes. Silahkan!

---

## 🆘 Support

Jika ada pertanyaan atau issue:
1. Cek dokumentasi di atas
2. Cek troubleshooting section
3. Review kode di file terkait

**Selamat menggunakan Sistem Kasir!!! 🎉**

