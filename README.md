# 🛒 Sistem Kasir PHP - Point of Sale System

[![PHP Version](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-brightgreen.svg)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

> Sistem kasir modern berbasis web dengan 3 role pengguna: **Admin**, **Kasir**, dan **Client (Pembeli)**. Dilengkapi dengan manajemen produk, transaksi real-time, laporan penjualan, dan approval workflow yang lengkap.

---

## 📸 Preview

### Dashboard Admin
<!-- Ganti dengan screenshot dashboard admin -->

<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/f3602386-d55f-48b0-ba90-0b3fb1fc0e4e" />


### Transaksi Kasir
<!-- Ganti dengan screenshot halaman transaksi -->
```
[Screenshot Halaman Transaksi - Tampilkan POS interface dengan calculator]
```

### Client Shopping
<!-- Ganti dengan screenshot katalog produk -->
```
[Screenshot Katalog Produk - Tampilkan grid produk dengan filter]
```

---

## ✨ Fitur Utama

### 👨‍💼 Admin Dashboard
- 📊 **Dashboard Analytics** - Statistik penjualan real-time dengan grafik
- 📦 **Manajemen Produk** - CRUD produk lengkap dengan upload gambar
- 🏷️ **Manajemen Kategori** - Organisasi produk berdasarkan kategori
- 👥 **Manajemen User** - Kelola admin, kasir, dan client
- 📈 **Laporan Penjualan** - Filter berdasarkan tanggal, kasir, metode pembayaran
- 🔔 **Approval System** - Review dan approve/reject pembatalan transaksi dari kasir
- ⚠️ **Monitor Stok** - Notifikasi produk dengan stok menipis
- 🏆 **Best Selling Products** - Produk terlaris dengan analytics

### 💰 Kasir POS System
- 🖥️ **Point of Sale Interface** - UI modern dan responsif
- 🧮 **Smart Calculator** - Calculator digital terintegrasi
- 🔍 **Product Search** - Pencarian produk cepat dengan filter kategori
- 💳 **Multiple Payment Methods** - Cash, Debit, Credit, E-Wallet
- 🧾 **Print Receipt** - Cetak struk otomatis
- 📋 **Transaction History** - Riwayat transaksi harian
- ↩️ **Cancel Request System** - Request pembatalan ke admin dengan approval workflow
- ⚡ **Fast Checkout** - Proses transaksi dalam hitungan detik

### 🛍️ Client Shopping Experience
- 🏪 **Product Catalog** - Browse produk dengan gambar dan deskripsi
- 🔎 **Advanced Search** - Cari produk berdasarkan nama atau kategori
- 🛒 **Shopping Cart** - Keranjang belanja dengan update quantity
- 📱 **Online Checkout** - Sistem pemesanan online
- 📦 **Order Tracking** - Lacak status pesanan real-time
- 💬 **Order Notes** - Tambahkan catatan pada pesanan
- 📜 **Order History** - Riwayat pembelian lengkap

---

## 🚀 Teknologi Stack

| Kategori | Teknologi |
|----------|-----------|
| **Backend** | PHP 8.1+ (Native, OOP) |
| **Database** | MySQL 8.0 / MariaDB 10.4+ |
| **Frontend** | Bootstrap 5.3, jQuery 3.7 |
| **Icons** | Bootstrap Icons 1.11 |
| **Containerization** | Docker & Docker Compose |
| **Web Server** | Apache 2.4 |
| **Architecture** | MVC Pattern, RESTful API Ready |

---

## 📋 Persyaratan Sistem

### Metode 1: Docker (Recommended) ⭐
- Docker Engine 20.10+
- Docker Compose V2+
- 2GB RAM minimum
- 5GB disk space

### Metode 2: Native PHP/MySQL
- PHP 8.1 atau lebih tinggi
- MySQL 8.0 / MariaDB 10.4+
- Apache Web Server
- PHP Extensions: PDO, GD, mbstring, zip

---

## 🐳 Quick Start dengan Docker

### 1️⃣ Clone Repository
```bash
git clone https://github.com/username/kasir-app.git
cd kasir-app
```

### 2️⃣ Jalankan Docker (Satu Perintah!)
```bash
# Untuk Raspberry Pi 3 (ARM32v7)
docker compose up -d --build

# Untuk Laptop/PC (x86_64)
docker compose -f docker-compose.dev.yml up -d --build
```

### 3️⃣ Tunggu Setup Selesai
Database akan otomatis diimport. Tunggu sekitar **60 detik** pertama kali.

### 4️⃣ Akses Aplikasi
```
🌐 Web App:       http://localhost:8090
🗄️ phpMyAdmin:    http://localhost:8091
```

### 5️⃣ Login
| Role | Username | Password |
|------|----------|----------|
| **Admin** | admin | admin123 |
| **Kasir** | kasir1 | kasir123 |
| **Client** | *Register baru* | - |

**Admin URL**: `http://localhost:8090/admin/dashboard.php`

---

## 💻 Setup Native PHP/MySQL

### 1️⃣ Clone & Setup
```bash
git clone https://github.com/username/kasir-app.git
cd kasir-app
```

### 2️⃣ Buat Database
```bash
mysql -u root -p

CREATE DATABASE kasir_db;
USE kasir_db;
SOURCE database/kasir_db.sql;
EXIT;
```

### 3️⃣ Konfigurasi Database
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'kasir_db');
```

### 4️⃣ Set Permissions
```bash
chmod -R 755 kasir-app/
chmod -R 777 kasir-app/uploads/
```

### 5️⃣ Akses
```
http://localhost/kasir-app
```

---

## 🎯 Struktur Folder

```
kasir-app/
├── 📁 admin/              # Admin dashboard & management
├── 📁 kasir/              # Kasir POS system
├── 📁 client/             # Client shopping interface
├── 📁 auth/               # Authentication (login, register)
├── 📁 assets/             # CSS, JS, images
│   ├── css/style.css
│   ├── js/main.js
│   └── js/calculator.js
├── 📁 classes/            # PHP Classes (OOP)
│   ├── Auth.php
│   ├── Database.php
│   ├── Product.php
│   ├── Transaction.php
│   └── User.php
├── 📁 config/             # Configuration files
│   ├── config.php
│   └── database.php
├── 📁 database/           # SQL schema & migrations
│   └── kasir_db.sql
├── 📁 includes/           # Reusable components
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── functions.php
├── 📁 uploads/products/   # Product images storage
├── 🐳 docker-compose.yml  # Docker config (ARM)
├── 🐳 Dockerfile          # Docker image
└── 📄 README.md           # This file
```

---

## 🔐 Default Credentials

### Database (Docker)
```yaml
Host: localhost / db (dalam Docker)
Port: 3307
Username: iqbal
Password: #semarangwhj354iqbal#
Database: kasir_db
```

### Aplikasi
| Role | Username | Password | URL |
|------|----------|----------|-----|
| **Admin** | admin | admin123 | `/admin/dashboard.php` |
| **Kasir** | kasir1 | kasir123 | `/auth/login.php` (tab Kasir) |
| **Client** | *Buat akun baru* | - | `/auth/register.php` |

**⚠️ PENTING**: Ganti semua password default setelah instalasi!

---

## 🎨 Fitur Khusus

### 🧮 Smart Calculator
Kasir dilengkapi dengan calculator digital yang terintegrasi untuk menghitung jumlah bayar dan kembalian secara otomatis.

### ↩️ Approval Workflow
Sistem pembatalan transaksi dengan approval dari admin:
1. **Kasir** mengajukan request pembatalan
2. **Admin** review dan approve/reject
3. **Stok** otomatis dikembalikan jika approved

### 📊 Real-time Analytics
Dashboard admin menampilkan:
- Transaksi hari ini
- Pendapatan real-time
- Produk terlaris
- Stok menipis
- Grafik penjualan

### 🎯 Role-Based Access Control
Setiap role memiliki akses yang berbeda:
- **Admin**: Full access ke semua fitur
- **Kasir**: Transaksi, history, approval pending orders
- **Client**: Shopping, cart, order history

---

## 🐳 Docker Commands

### Basic Operations
```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# Restart containers
docker compose restart

# View logs
docker compose logs -f

# View specific service logs
docker compose logs -f web
docker compose logs -f db
```

### Database Management
```bash
# Access MySQL shell
docker exec -it kasir_db mysql -uiqbal -p

# Backup database
docker exec kasir_db mysqldump -uiqbal -p'#semarangwhj354iqbal#' kasir_db > backup.sql

# Restore database
docker exec -i kasir_db mysql -uiqbal -p'#semarangwhj354iqbal#' kasir_db < backup.sql
```

### Maintenance
```bash
# Fresh restart (clear all data)
docker compose down -v
docker compose up -d --build

# Clean unused images
docker system prune -a
```

---

## 🌐 Akses dari Jaringan Lokal

Aplikasi otomatis dapat diakses dari perangkat lain di jaringan yang sama:

```bash
# Cek IP address
hostname -I

# Akses dari perangkat lain
http://[IP_ADDRESS]:8090
```

**Contoh**: `http://192.168.1.100:8090`

---

## 🛠️ Troubleshooting

### Database Connection Failed
```bash
# Cek container status
docker compose ps

# Lihat logs database
docker compose logs db

# Tunggu database ready
# Database butuh 30-60 detik untuk initialize pertama kali
```

### Port Already in Use
```bash
# Cek port yang digunakan
sudo netstat -tlnp | grep 8090

# Stop service yang conflict atau edit port di docker-compose.yml
```

### Permission Error pada Uploads
```bash
chmod -R 777 uploads/

# Atau dari dalam container
docker exec -it kasir_web chmod -R 777 /var/www/html/uploads
```

### Cart Data JSON Error
Jika ada error saat checkout, clear browser cache dan cookies, lalu coba lagi.

---

## 🚨 Known Issues & Solutions

| Issue | Solution |
|-------|----------|
| Database slow di Raspberry Pi | Edit `docker-compose.yml`, kurangi `innodb_buffer_pool_size` ke 128M |
| Build time lama di Raspberry Pi | Normal, butuh 5-10 menit karena CPU lambat |
| phpMyAdmin tidak bisa login | Gunakan user: `iqbal`, password: `#semarangwhj354iqbal#` |

---

## 📚 API Documentation (Coming Soon)

Sistem ini sudah siap untuk dikembangkan menjadi RESTful API dengan endpoint:

```
GET    /api/products              # List products
POST   /api/transactions          # Create transaction
GET    /api/transactions/{id}     # Get transaction detail
PUT    /api/transactions/{id}     # Update transaction
DELETE /api/transactions/{id}     # Cancel transaction
```

---

## 🤝 Contributing

Kontribusi sangat diterima! Silakan fork repository ini dan submit Pull Request.

### Development Workflow
1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📝 Changelog

### Version 1.0.0 (2025-01-18)
- ✅ Initial release
- ✅ Admin dashboard dengan analytics
- ✅ Kasir POS system dengan calculator
- ✅ Client shopping interface
- ✅ Approval workflow untuk pembatalan transaksi
- ✅ Docker support untuk ARM dan x86
- ✅ Multi-payment methods
- ✅ Print receipt system

---

## 🔮 Roadmap

- [ ] RESTful API untuk mobile app
- [ ] Barcode scanner integration
- [ ] SMS/Email notification
- [ ] Multi-branch support
- [ ] Inventory forecasting
- [ ] Export laporan ke PDF/Excel
- [ ] Dark mode theme
- [ ] PWA (Progressive Web App)

---

## 📧 Contact & Support

- **Developer**: Your Name
- **Email**: your.email@example.com
- **GitHub**: [@yourusername](https://github.com/yourusername)
- **Website**: [your-website.com](https://your-website.com)

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- Bootstrap Team untuk UI framework
- Docker Team untuk containerization platform
- PHP Community
- MySQL/MariaDB Team
- Icons by Bootstrap Icons

---

## ⭐ Show Your Support

Jika project ini membantu Anda, berikan ⭐ di GitHub repository!

---

<div align="center">

**Built with ❤️ using PHP & Bootstrap**

[![GitHub Stars](https://img.shields.io/github/stars/MfBally354/Kasir-PHP?style=social)](https://github.com/MfBally354/Kasir-PHP/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/MfBally354/Kasir-PHP?style=social)](https://github.com/MfBally354/Kasir-PHP/network/members)

</div>




