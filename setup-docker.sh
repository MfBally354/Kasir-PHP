#!/bin/bash

# ===================================
# Setup Docker di Debian/Ubuntu
# Script untuk install Docker & Docker Compose
# ===================================

echo "=========================================="
echo "  Setup Docker untuk Sistem Kasir PHP"
echo "=========================================="
echo ""

# Cek apakah user adalah root
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  Script ini harus dijalankan sebagai root atau dengan sudo"
    echo "Gunakan: sudo bash setup-docker.sh"
    exit 1
fi

echo "📦 Step 1: Update system packages..."
apt-get update

echo ""
echo "📦 Step 2: Install prerequisites..."
apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

echo ""
echo "🔑 Step 3: Add Docker's official GPG key..."
mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg

echo ""
echo "📝 Step 4: Set up Docker repository..."
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

echo ""
echo "🔄 Step 5: Update package index..."
apt-get update

echo ""
echo "🐳 Step 6: Install Docker Engine..."
apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

echo ""
echo "👥 Step 7: Add current user to docker group..."
# Dapatkan user yang menjalankan sudo
REAL_USER=$(logname 2>/dev/null || echo $SUDO_USER)
if [ ! -z "$REAL_USER" ]; then
    usermod -aG docker $REAL_USER
    echo "✅ User '$REAL_USER' ditambahkan ke docker group"
else
    echo "⚠️  Tidak dapat mendeteksi user. Jalankan manual: sudo usermod -aG docker \$USER"
fi

echo ""
echo "🚀 Step 8: Start Docker service..."
systemctl start docker
systemctl enable docker

echo ""
echo "✅ Step 9: Verify Docker installation..."
docker --version
docker compose version

echo ""
echo "=========================================="
echo "  ✅ Docker berhasil diinstall!"
echo "=========================================="
echo ""
echo "⚠️  PENTING: Logout dan login kembali agar perubahan group berlaku"
echo "           Atau jalankan: newgrp docker"
echo ""
echo "📝 Next steps:"
echo "   1. cd /home/iqbal/database/kasir-app"
echo "   2. docker compose up -d --build"
echo "   3. Akses http://localhost:8090"
echo ""
echo "🔍 Cek status: docker compose ps"
echo "📋 Lihat logs: docker compose logs -f"
echo ""
