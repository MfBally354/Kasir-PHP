# 🚀 Quick Start - Sistem Kasir di Debian

Panduan cepat untuk menjalankan Sistem Kasir dengan Docker di Debian Linux.

---

## 📍 Lokasi Project Anda
```bash
/home/iqbal/database/kasir-app
```

---

## 🔧 Persiapan Awal

### 1. **Install Docker & Docker Compose**

Saya sudah buatkan script otomatis:

```bash
cd /home/iqbal/database/kasir-app

# Download script (atau copy manual dari artifact)
chmod +x setup-docker.sh

# Jalankan dengan sudo
sudo bash setup-docker.sh
```

Script ini akan:
- Install Docker Engine
- Install Docker Compose
- Menambahkan user ke docker group
- Start Docker service

**PENTING:** Setelah install, logout dan login kembali, atau jalankan:
```bash
newgrp docker
```

### 2. **Verifikasi Instalasi**

```bash
docker --version
# Output: Docker version 24.x.x

docker compose version
# Output: Docker Compose version v2.x.x
```

---

## 📂 Struktur File yang Diperlukan

Pastikan file-file ini ada di `/home/iqbal/database/kasir-app/`:

```
kasir-app/
├── docker-compose.yml          ← Wajib ada
├── Dockerfile                  ← Wajib ada
├── .dockerignore              ← Opsional
├── .htaccess                  ← Opsional tapi recommended
├── config/
│   └── database.php           ← Update dengan config Docker
├── database/
│   └── kasir_db.sql          ← File SQL Anda yang sudah ada
├── uploads/
│   └── products/             ← Buat folder ini
└── ... (file PHP lainnya)
```

### Buat File yang Belum Ada:

```bash
cd /home/iqbal/database/kasir-app

# Buat file docker-compose.yml
nano docker-compose.yml
# Copy isi dari artifact "Docker Setup untuk Sistem Kasir PHP"

# Buat Dockerfile
nano Dockerfile
# Copy isi dari artifact "Dockerfile - PHP Apache Setup"

# Update config/database.php
nano config/database.php
# Copy isi dari artifact "config/database.php - Konfigurasi Docker"

# Buat folder uploads jika belum ada
mkdir -p uploads/products
chmod -R 777 uploads
```

---

## 🚀 Menjalankan Aplikasi

### Step 1: Pastikan di direktori project
```bash
cd /home/iqbal/database/kasir-app
```

### Step 2: Build dan jalankan container
```bash
docker compose up -d --build
```

Output yang diharapkan:
```
[+] Building 45.2s (15/15) FINISHED
[+] Running 4/4
 ✔ Network kasir_network        Created
 ✔ Container kasir_db           Started
 ✔ Container kasir_phpmyadmin   Started
 ✔ Container kasir_web          Started
```

### Step 3: Tunggu MySQL siap (~30-60 detik)
```bash
# Cek logs MySQL
docker compose logs -f db

# Tunggu sampai muncul:
# [Server] /usr/sbin/mysqld: ready for connections
```

Tekan `Ctrl+C` untuk keluar dari logs.

### Step 4: Cek status container
```bash
docker compose ps
```

Semua container harus status "Up".

### Step 5: Akses aplikasi
Buka browser:
- **Aplikasi Web**: http://localhost:8090
- **phpMyAdmin**: http://localhost:8091

---

## 🔐 Login Credentials

Sesuai dengan database Anda yang sudah ada:

### Admin
```
URL: http://localhost:8090/admin/dashboard.php
Username: admin
Password: admin123
```

### Kasir
```
URL: http://localhost:8090/auth/login.php
Username: kasir1
Password: kasir123
```

### phpMyAdmin
```
URL: http://localhost:8091
Server: db
Username: root
Password: root
```

---

## 🛠️ Perintah Docker yang Sering Digunakan

```bash
# Lihat status container
docker compose ps

# Lihat logs semua container
docker compose logs -f

# Lihat logs container tertentu
docker compose logs -f web
docker compose logs -f db

# Restart container
docker compose restart

# Stop container
docker compose down

# Start container yang sudah di-build
docker compose up -d

# Rebuild dan restart
docker compose up -d --build

# Masuk ke container web (untuk debugging)
docker exec -it kasir_web bash

# Masuk ke MySQL
docker exec -it kasir_db mysql -uroot -proot kasir_db
```

---

## 🐛 Troubleshooting

### Error: "permission denied"
```bash
# Pastikan user di docker group
sudo usermod -aG docker $USER
newgrp docker

# Atau logout dan login kembali
```

### Error: "port already in use"
```bash
# Cek port yang digunakan
sudo netstat -tlnp | grep 8090
sudo netstat -tlnp | grep 3307

# Stop service yang menggunakan port tersebut
# Atau ubah port di docker-compose.yml
```

### Container tidak start
```bash
# Lihat error logs
docker compose logs

# Coba rebuild
docker compose down
docker compose up -d --build
```

### Database connection error
```bash
# Pastikan MySQL sudah ready
docker compose logs db | grep "ready for connections"

# Restart database
docker compose restart db

# Tunggu 30-60 detik
```

### Permission error pada uploads
```bash
chmod -R 777 uploads/

# Atau dari dalam container
docker exec -it kasir_web chmod -R 777 /var/www/html/uploads
```

### Reset semua (Fresh start)
```bash
# HATI-HATI: Ini akan menghapus semua data!
docker compose down -v
docker compose up -d --build
```

---

## 📊 Monitoring

### Cek resource usage
```bash
docker stats
```

### Cek disk usage
```bash
docker system df
```

### Clean up
```bash
# Hapus image yang tidak dipakai
docker image prune -a

# Hapus semua yang tidak terpakai
docker system prune -a
```

---

## 🔄 Update Aplikasi

Jika ada perubahan kode PHP:

```bash
# Tidak perlu rebuild, cukup restart
docker compose restart web
```

Jika ada perubahan Dockerfile:

```bash
# Rebuild image
docker compose up -d --build
```

---

## 🌐 Akses dari Komputer Lain (Jaringan Lokal)

Cari IP address Debian Anda:
```bash
ip addr show | grep "inet "
# Misalnya: 192.168.1.100
```

Dari komputer lain di jaringan yang sama:
```
http://192.168.1.100:8090
```

**Catatan:** Pastikan firewall mengizinkan port 8090:
```bash
sudo ufw allow 8090
sudo ufw allow 8091
```

---

## 📝 Checklist Setup

- [ ] Docker & Docker Compose terinstall
- [ ] User ditambahkan ke docker group
- [ ] File docker-compose.yml dibuat
- [ ] File Dockerfile dibuat
- [ ] config/database.php sudah update
- [ ] Folder uploads/products ada dan writable
- [ ] `docker compose up -d --build` berhasil
- [ ] Container semua status "Up"
- [ ] Akses http://localhost:8090 berhasil
- [ ] Login admin berhasil
- [ ] Upload test image berhasil

---

## 🆘 Bantuan Lebih Lanjut

Jika masih ada masalah:

1. **Cek logs detail:**
   ```bash
   docker compose logs -f
   ```

2. **Cek config database:**
   ```bash
   cat config/database.php
   ```

3. **Test koneksi MySQL:**
   ```bash
   docker exec -it kasir_db mysql -uroot -proot -e "SHOW DATABASES;"
   ```

4. **Restart semua:**
   ```bash
   docker compose restart
   ```

---

**Selamat mencoba! 🎉**
