# 🐳 Docker Setup - Sistem Kasir PHP untuk Raspberry Pi 3

Panduan lengkap menjalankan Sistem Kasir di **Raspberry Pi 3 (ARM32v7)** menggunakan Docker.

---

## 🎯 Spesifikasi

- **Platform:** Raspberry Pi 3 (ARM 32-bit)
- **OS:** Raspbian/Raspberry Pi OS
- **PHP:** 8.1 with Apache
- **Database:** MariaDB (ARM32v7)
- **Port Web:** 8090
- **Port MySQL:** 3307
- **Port phpMyAdmin:** 8091

---

## 📋 Prerequisites

### 1. Install Docker

```bash
# Install Docker
curl -fsSL https://get.docker.com | sh

# Tambahkan user ke docker group
sudo usermod -aG docker $USER

# Logout dan login kembali, atau:
newgrp docker

# Verifikasi
docker --version
```

### 2. Install Docker Compose (jika belum ada)

```bash
# Install Docker Compose Plugin
sudo apt-get update
sudo apt-get install docker-compose-plugin

# Verifikasi
docker compose version
```

---

## 🚀 Quick Start (Cara Tercepat)

### Metode 1: Menggunakan Script Otomatis

```bash
cd /home/iqbal/database/kasir-app

# Buat script executable
chmod +x DOCKER_START.sh

# Jalankan
bash DOCKER_START.sh
```

Script akan otomatis:
- ✅ Cek semua dependencies
- ✅ Build Docker images
- ✅ Start containers
- ✅ Wait database ready
- ✅ Show status dan URLs

---

### Metode 2: Manual Commands

```bash
cd /home/iqbal/database/kasir-app

# Build dan start containers
docker compose up -d --build

# Tunggu database ready (~60 detik)
docker compose logs -f db

# Tekan Ctrl+C saat muncul "ready for connections"

# Cek status
docker compose ps
```

Akses aplikasi:
- **Web:** http://192.168.1.16:8090
- **phpMyAdmin:** http://192.168.1.16:8091

---

## 🔐 Login Credentials

### Aplikasi Web

**Admin:**
```
URL: http://192.168.1.16:8090/admin/dashboard.php
Username: admin
Password: admin123
```

**Kasir:**
```
URL: http://192.168.1.16:8090/auth/login.php
Tab: Kasir
Username: kasir1
Password: kasir123
```

**Client/Pembeli:**
```
URL: http://192.168.1.16:8090/auth/register.php
(Registrasi akun baru)
```

### phpMyAdmin

```
URL: http://192.168.1.16:8090/phpmyadmin
Username: iqbal
Password: #semarangwhj354iqbal#
```

### MySQL (dari Host)

```bash
mysql -h 127.0.0.1 -P 3307 -u iqbal -p
# Password: #semarangwhj354iqbal#
```

---

## 📂 Struktur File

```
kasir-app/
├── docker-compose.yml          # Konfigurasi Docker services
├── Dockerfile                  # Build PHP Apache ARM32v7
├── DOCKER_START.sh            # Script auto-start
├── config/
│   └── database.php           # Config database (gunakan 'db' sebagai host)
├── database/
│   └── kasir_db.sql          # Auto-import saat pertama kali
├── uploads/                   # Persistent storage untuk gambar
└── ... (file PHP lainnya)
```

---

## 🛠️ Docker Commands

### Basic Commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# Restart containers
docker compose restart

# Rebuild images
docker compose up -d --build
```

### Monitoring

```bash
# Lihat status containers
docker compose ps

# Lihat logs semua services
docker compose logs -f

# Lihat logs service tertentu
docker compose logs -f web
docker compose logs -f db
docker compose logs -f phpmyadmin

# Lihat resource usage
docker stats
```

### Debugging

```bash
# Masuk ke container web (shell)
docker exec -it kasir_web bash

# Masuk ke MySQL
docker exec -it kasir_db mysql -uiqbal -p

# Lihat file config
docker exec kasir_web cat /var/www/html/config/database.php

# Test koneksi PHP ke MySQL
docker exec kasir_web php -r "echo 'Testing MySQL connection...'; \$pdo = new PDO('mysql:host=db;dbname=kasir_db', 'iqbal', '#semarangwhj354iqbal#'); echo 'Success!';"
```

### Maintenance

```bash
# Stop dan hapus semua (HATI-HATI!)
docker compose down -v

# Fresh install
docker compose down -v
docker compose up -d --build

# Backup database
docker exec kasir_db mysqldump -uiqbal -p'#semarangwhj354iqbal#' kasir_db > backup_$(date +%Y%m%d).sql

# Restore database
docker exec -i kasir_db mysql -uiqbal -p'#semarangwhj354iqbal#' kasir_db < backup_20250110.sql

# Clean unused images (hemat space)
docker system prune -a
```

---

## 🐛 Troubleshooting

### ❌ Error: "database connection failed"

**Penyebab:** Database belum siap

**Solusi:**
```bash
# Cek logs database
docker compose logs db

# Tunggu hingga muncul "ready for connections"
# Biasanya butuh 30-60 detik pertama kali

# Restart database
docker compose restart db

# Tunggu 1 menit, lalu cek lagi
```

---

### ❌ Error: "port already in use"

**Penyebab:** Port 8090 atau 3307 sudah digunakan

**Solusi:**
```bash
# Cek port yang digunakan
sudo netstat -tlnp | grep 8090
sudo netstat -tlnp | grep 3307

# Stop service yang conflict, atau
# Edit port di docker-compose.yml:
nano docker-compose.yml

# Ganti:
#   ports:
#     - "8091:80"   # Ganti 8090 ke 8091
```

---

### ❌ Error: "no space left on device"

**Penyebab:** Raspberry Pi storage penuh

**Solusi:**
```bash
# Cek disk usage
df -h

# Hapus Docker images yang tidak terpakai
docker system prune -a

# Hapus logs lama
sudo journalctl --vacuum-time=7d
```

---

### ❌ Container "kasir_web" exit dengan error

**Solusi:**
```bash
# Lihat logs detail
docker compose logs web

# Cek syntax error PHP
docker exec kasir_web php -l /var/www/html/index.php

# Rebuild
docker compose up -d --build web
```

---

### ⚠️ Database sangat lambat di Raspberry Pi 3

**Normal!** Raspberry Pi 3 memiliki CPU dan RAM terbatas.

**Optimasi:**
```bash
# Edit docker-compose.yml, kurangi buffer pool:
services:
  db:
    command: >
      --innodb_buffer_pool_size=128M  # Dari 256M ke 128M
```

---

## 📊 Performance Tips untuk Raspberry Pi 3

### 1. **Kurangi Memory Usage**

Edit `docker-compose.yml`:
```yaml
services:
  db:
    command: >
      --innodb_buffer_pool_size=128M
      --max_connections=50
```

### 2. **Disable phpMyAdmin (jika tidak diperlukan)**

Comment out section phpMyAdmin di `docker-compose.yml`:
```yaml
# phpmyadmin:
#   image: arm32v7/phpmyadmin:latest
#   ...
```

### 3. **Gunakan Docker BuildKit**

```bash
export DOCKER_BUILDKIT=1
docker compose up -d --build
```

### 4. **Monitoring Resource**

```bash
# Cek memory usage
free -h

# Cek CPU usage
top

# Cek Docker stats
docker stats --no-stream
```

---

## 🔄 Update Aplikasi

### Update Code PHP (tidak perlu rebuild)

```bash
# Edit file PHP
nano client/checkout.php

# Restart web container
docker compose restart web

# Selesai!
```

### Update Docker Configuration

```bash
# Edit docker-compose.yml atau Dockerfile
nano docker-compose.yml

# Rebuild
docker compose up -d --build
```

---

## 🌐 Akses dari Jaringan Lokal

Aplikasi otomatis bisa diakses dari komputer lain di jaringan yang sama:

```
http://192.168.1.16:8090
```

Ganti `192.168.1.16` dengan IP Raspberry Pi Anda:
```bash
hostname -I
```

**Buka Firewall (jika perlu):**
```bash
sudo ufw allow 8090
sudo ufw allow 8091
```

---

## ✅ Checklist Setup

- [ ] Docker terinstall (`docker --version`)
- [ ] Docker Compose terinstall (`docker compose version`)
- [ ] User ada di docker group (`groups`)
- [ ] File `docker-compose.yml` ada
- [ ] File `Dockerfile` ada
- [ ] File `database/kasir_db.sql` ada
- [ ] Run `docker compose up -d --build`
- [ ] Tunggu database ready (~60 detik)
- [ ] Akses http://192.168.1.16:8090
- [ ] Login admin berhasil
- [ ] Upload test image berhasil

---

## 📝 Notes Penting

### Khusus Raspberry Pi 3:

1. **Build Time:** Proses `docker compose build` akan memakan waktu **5-10 menit** pertama kali karena Raspberry Pi 3 memiliki CPU yang lambat.

2. **First Start:** Database MariaDB butuh **60-90 detik** untuk initialize pertama kali.

3. **Memory:** Raspberry Pi 3 hanya punya 1GB RAM. Jangan jalankan terlalu banyak container sekaligus.

4. **Storage:** Pastikan ada minimal **2GB** free space untuk Docker images dan data.

5. **Networking:** Pastikan Pi terhubung ke jaringan dengan baik.

### Image ARM32v7 yang Digunakan:

- **PHP:** `arm32v7/php:8.1-apache`
- **MariaDB:** `linuxserver/mariadb:arm32v7-latest`
- **phpMyAdmin:** `arm32v7/phpmyadmin:latest`

Semua image ini **TESTED dan COMPATIBLE** dengan Raspberry Pi 3 (ARM 32-bit).

---

## 🆘 Bantuan

Jika masih ada masalah:

1. **Cek logs lengkap:**
   ```bash
   docker compose logs
   ```

2. **Cek status:**
   ```bash
   docker compose ps
   ```

3. **Fresh restart:**
   ```bash
   docker compose down
   docker compose up -d --build
   ```

4. **Nuclear option (reset semua):**
   ```bash
   docker compose down -v
   docker system prune -a
   docker compose up -d --build
   ```

---

**Good luck! 🎉 Selamat menggunakan Sistem Kasir di Raspberry Pi 3!**
