# 🐳 Docker Setup - Sistem Kasir PHP

Panduan lengkap untuk menjalankan Sistem Kasir menggunakan Docker.

---

## 📋 Prerequisites

Pastikan sudah terinstall:
- **Docker** (versi 20.10+)
- **Docker Compose** (versi 1.29+)

Cek versi:
```bash
docker --version
docker-compose --version
```

---

## 🚀 Quick Start

### 1. **Clone atau Download Project**
```bash
cd kasir-app
```

### 2. **Build dan Jalankan Container**
```bash
docker-compose up -d --build
```

Perintah ini akan:
- Build image PHP Apache
- Download image MySQL 8.0
- Download image phpMyAdmin
- Membuat dan menjalankan semua container
- Import database otomatis

### 3. **Akses Aplikasi**

Tunggu sekitar 30-60 detik untuk MySQL selesai initialize, kemudian akses:

| Service | URL | Keterangan |
|---------|-----|------------|
| **Web Aplikasi** | http://localhost:8090 | Port utama aplikasi |
| **phpMyAdmin** | http://localhost:8091 | Manajemen database |

---

## 🔐 Credentials

### Database MySQL
```
Host: db (dari container) / localhost:3307 (dari host)
Database: kasir_db
Username: kasir_user
Password: kasir_pass
Root Password: root123
```

### phpMyAdmin
```
URL: http://localhost:8091
Username: root
Password: root123
```

### Aplikasi Login

**Admin:**
```
URL: http://localhost:8090/admin/dashboard.php
Username: admin
Password: admin123
```

**Kasir:**
```
URL: http://localhost:8090/auth/login.php
Username: kasir1
Password: kasir123
```

**Client:**
```
URL: http://localhost:8090/auth/login.php → Tab Pembeli
Username: client1
Password: client123
```

---

## 📂 Struktur Docker

```
kasir-app/
├── docker-compose.yml      # Konfigurasi semua services
├── Dockerfile              # Build PHP Apache
├── config/
│   └── database.php        # Konfigurasi koneksi DB (sudah disesuaikan)
├── database/
│   └── kasir_db.sql       # Auto-import saat pertama kali
└── uploads/                # Persistent storage
```

---

## 🛠️ Docker Commands

### Start Containers
```bash
docker-compose up -d
```

### Stop Containers
```bash
docker-compose down
```

### Restart Containers
```bash
docker-compose restart
```

### View Logs
```bash
# Semua logs
docker-compose logs -f

# Logs specific service
docker-compose logs -f web
docker-compose logs -f db
```

### Check Running Containers
```bash
docker-compose ps
```

### Access Container Shell
```bash
# Web container
docker exec -it kasir_web bash

# Database container
docker exec -it kasir_db bash
```

### Rebuild Containers (jika ada perubahan Dockerfile)
```bash
docker-compose up -d --build
```

### Remove All (termasuk volumes)
```bash
docker-compose down -v
```

---

## 🔄 Import Ulang Database

Jika perlu import ulang database:

```bash
# Method 1: Via Docker Exec
docker exec -i kasir_db mysql -uroot -proot123 kasir_db < database/kasir_db.sql

# Method 2: Via phpMyAdmin
# Akses http://localhost:8091
# Login → Import → Pilih kasir_db.sql
```

---

## 📁 Persistent Data

### Volumes yang Dibuat:
- **mysql_data**: Data MySQL (persistent)
- **./uploads**: File upload produk (persistent)

Data ini akan tetap ada meskipun container dihapus (kecuali pakai `docker-compose down -v`).

---

## 🐛 Troubleshooting

### Port Sudah Digunakan
Jika port 8090 atau 3307 sudah digunakan, edit `docker-compose.yml`:

```yaml
services:
  web:
    ports:
      - "8080:80"  # Ganti 8090 ke 8080
  
  db:
    ports:
      - "3308:3306"  # Ganti 3307 ke 3308
```

### Database Connection Error
```bash
# Cek apakah MySQL sudah ready
docker-compose logs db

# Tunggu sampai muncul: "ready for connections"
# Biasanya butuh 30-60 detik pertama kali
```

### Permission Error pada Uploads
```bash
# Dari host
chmod -R 777 uploads/

# Atau dari container
docker exec -it kasir_web chmod -R 777 /var/www/html/uploads
```

### Container Tidak Start
```bash
# Cek logs error
docker-compose logs

# Rebuild dari awal
docker-compose down
docker-compose up -d --build
```

### Reset Semua (Fresh Install)
```bash
# HATI-HATI: Ini akan menghapus semua data!
docker-compose down -v
docker-compose up -d --build
```

---

## 🔧 Konfigurasi Lanjutan

### Ubah PHP Settings
Edit `Dockerfile`, tambahkan di section PHP config:
```dockerfile
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini
```

Rebuild:
```bash
docker-compose up -d --build
```

### Backup Database
```bash
# Backup
docker exec kasir_db mysqldump -uroot -proot123 kasir_db > backup_$(date +%Y%m%d).sql

# Restore
docker exec -i kasir_db mysql -uroot -proot123 kasir_db < backup_20250109.sql
```

### Update BASE_URL di config.php
Edit `config/config.php`:
```php
define('BASE_URL', 'http://localhost:8090');
```

---

## 📊 Monitoring

### Resource Usage
```bash
docker stats
```

### Check Health
```bash
# Test web server
curl http://localhost:8090

# Test MySQL
docker exec kasir_db mysql -uroot -proot123 -e "SELECT 'OK' as status;"
```

---

## 🚀 Production Deployment

Untuk production, edit `docker-compose.yml`:

```yaml
services:
  web:
    environment:
      - PHP_DISPLAY_ERRORS=Off
      - PHP_ERROR_REPORTING=0
  
  db:
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}  # Gunakan .env
      MYSQL_PASSWORD: ${DB_PASSWORD}
```

Buat file `.env`:
```env
DB_ROOT_PASSWORD=strong_password_here
DB_PASSWORD=another_strong_password
```

---

## 📝 Notes

1. **First Time Setup**: Database auto-import dari `database/kasir_db.sql`
2. **File Uploads**: Tersimpan di `./uploads/products/` (persistent)
3. **Development Mode**: Error reporting enabled untuk debugging
4. **Auto-restart**: Container akan auto-restart jika crash

---

## 🆘 Support

Jika ada masalah:
1. Cek logs: `docker-compose logs -f`
2. Restart: `docker-compose restart`
3. Rebuild: `docker-compose up -d --build`
4. Fresh install: `docker-compose down -v && docker-compose up -d --build`

---

## ✅ Checklist Instalasi

- [ ] Docker dan Docker Compose terinstall
- [ ] Clone/download project
- [ ] Run `docker-compose up -d --build`
- [ ] Tunggu MySQL ready (~60 detik)
- [ ] Akses http://localhost:8090
- [ ] Login dengan credential default
- [ ] Upload gambar produk test (cek permission)
- [ ] Test transaksi
- [ ] Cek phpMyAdmin http://localhost:8091

---

**Selamat menggunakan Sistem Kasir dengan Docker! 🎉**
