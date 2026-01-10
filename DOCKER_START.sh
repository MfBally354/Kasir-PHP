#!/bin/bash

# ===================================
# DOCKER_START.sh
# Script untuk menjalankan Sistem Kasir
# di Raspberry Pi 3 dengan Docker
# ===================================

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  🐳 Sistem Kasir PHP - Docker Setup${NC}"
echo -e "${BLUE}  Raspberry Pi 3 (ARM32v7)${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Check if running as root
if [ "$EUID" -eq 0 ]; then 
    echo -e "${YELLOW}⚠️  Jangan jalankan script ini sebagai root!${NC}"
    echo -e "${YELLOW}   Gunakan: bash DOCKER_START.sh${NC}"
    exit 1
fi

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker belum terinstall!${NC}"
    echo -e "${YELLOW}   Install dengan: curl -fsSL https://get.docker.com | sh${NC}"
    exit 1
fi

# Check if Docker Compose is installed
if ! docker compose version &> /dev/null; then
    echo -e "${RED}❌ Docker Compose belum terinstall!${NC}"
    echo -e "${YELLOW}   Install dengan: sudo apt-get install docker-compose-plugin${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Docker terinstall${NC}"
echo -e "${GREEN}✅ Docker Compose terinstall${NC}"
echo ""

# Check if docker daemon is running
if ! docker ps &> /dev/null; then
    echo -e "${YELLOW}⚠️  Docker daemon tidak berjalan${NC}"
    echo -e "${YELLOW}   Mencoba start Docker...${NC}"
    sudo systemctl start docker
    sleep 2
fi

echo -e "${BLUE}📂 Direktori: $(pwd)${NC}"
echo ""

# Check if necessary files exist
if [ ! -f "docker-compose.yml" ]; then
    echo -e "${RED}❌ File docker-compose.yml tidak ditemukan!${NC}"
    exit 1
fi

if [ ! -f "Dockerfile" ]; then
    echo -e "${RED}❌ File Dockerfile tidak ditemukan!${NC}"
    exit 1
fi

if [ ! -f "database/kasir_db.sql" ]; then
    echo -e "${RED}❌ File database/kasir_db.sql tidak ditemukan!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Semua file ditemukan${NC}"
echo ""

# Create uploads directory if not exists
if [ ! -d "uploads/products" ]; then
    echo -e "${YELLOW}📁 Membuat direktori uploads/products...${NC}"
    mkdir -p uploads/products
    chmod -R 777 uploads
fi

# Stop existing containers
echo -e "${BLUE}🛑 Menghentikan container yang sedang berjalan...${NC}"
docker compose down 2>/dev/null || true
echo ""

# Build and start containers
echo -e "${BLUE}🔨 Building dan starting containers...${NC}"
echo -e "${YELLOW}⏳ Proses ini akan memakan waktu 5-10 menit pertama kali${NC}"
echo -e "${YELLOW}   (terutama pada Raspberry Pi 3)${NC}"
echo ""

docker compose up -d --build

echo ""
echo -e "${GREEN}✅ Containers berhasil dijalankan!${NC}"
echo ""

# Wait for database to be ready
echo -e "${BLUE}⏳ Menunggu database siap...${NC}"
echo -e "${YELLOW}   Database MariaDB membutuhkan 30-60 detik untuk initialize${NC}"

for i in {1..60}; do
    if docker compose exec -T db mysqladmin ping -h localhost -uiqbal -p'#semarangwhj354iqbal#' &> /dev/null; then
        echo -e "${GREEN}✅ Database siap! (${i} detik)${NC}"
        break
    fi
    echo -n "."
    sleep 1
    
    if [ $i -eq 60 ]; then
        echo ""
        echo -e "${YELLOW}⚠️  Database belum ready setelah 60 detik${NC}"
        echo -e "${YELLOW}   Tapi container mungkin masih initialize...${NC}"
        echo -e "${YELLOW}   Cek logs: docker compose logs db${NC}"
    fi
done

echo ""

# Show container status
echo -e "${BLUE}📊 Status Containers:${NC}"
docker compose ps
echo ""

# Show access URLs
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  🎉 Setup Selesai!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}📍 Akses Aplikasi:${NC}"
echo -e "   ${GREEN}Web:${NC}         http://$(hostname -I | awk '{print $1}'):8090"
echo -e "   ${GREEN}phpMyAdmin:${NC}  http://$(hostname -I | awk '{print $1}'):8091"
echo ""
echo -e "${BLUE}🔐 Login Credentials:${NC}"
echo -e "   ${GREEN}Admin:${NC}"
echo -e "      URL: /admin/dashboard.php"
echo -e "      User: admin"
echo -e "      Pass: admin123"
echo ""
echo -e "   ${GREEN}Kasir:${NC}"
echo -e "      URL: /auth/login.php"
echo -e "      User: kasir1"
echo -e "      Pass: kasir123"
echo ""
echo -e "   ${GREEN}phpMyAdmin:${NC}"
echo -e "      User: iqbal"
echo -e "      Pass: #semarangwhj354iqbal#"
echo ""
echo -e "${BLUE}🛠️  Perintah Berguna:${NC}"
echo -e "   ${YELLOW}Lihat logs:${NC}       docker compose logs -f"
echo -e "   ${YELLOW}Restart:${NC}          docker compose restart"
echo -e "   ${YELLOW}Stop:${NC}             docker compose down"
echo -e "   ${YELLOW}Start ulang:${NC}      bash DOCKER_START.sh"
echo -e "   ${YELLOW}Fresh install:${NC}    docker compose down -v && bash DOCKER_START.sh"
echo ""
echo -e "${GREEN}========================================${NC}"
echo ""

# Show logs
echo -e "${BLUE}📋 Menampilkan logs (tekan Ctrl+C untuk keluar):${NC}"
sleep 2
docker compose logs -f
