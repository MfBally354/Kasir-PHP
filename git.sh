#!/bin/bash

# ====== WARNA ======
BLUE='\033[1;34m' # BIRU
GREEN='\033[1;32m' # HIJAU
PINK='\033[1;35m' # MERAH MUDA
DARK_RED='\033[1;31m' # MERAH GELAP
NC='\033[0m' # TANPA WARNA


MAX_RETRY=3
COUNT=1

echo -e "${BLUE}Menambahkan perubahan...${NC}" # Menambahkan perubahan akan berwarna BIRU
git add .

echo -e "${BLUE}Melakukan commit...${NC}" # Melakukan git commit akan berwarna BIRU
git commit -m "Auto commit" || echo -e "${PINK}Tidak ada perubahan untuk di-commit${NC}" # Jika tidak ada yang bisa di commit akan berwarna MERAH MUDA 

while [ $COUNT -le $MAX_RETRY ] # Melakukan perulanggan pada push, jika push gagal selama beberapa kali akan dibatalkan
do
  echo -e "${BLUE}Percobaan push ke-$COUNT...${NC}"

  if git push; then
    echo -e "${GREEN}✅ Push berhasil!${NC}"
    break
  else
    echo -e "${PINK}❌ Push gagal, mencoba pull --rebase...${NC}"
    git pull --rebase
  fi

  COUNT=$((COUNT+1))
done

if [ $COUNT -gt $MAX_RETRY ]; then
  echo -e "${DARK_RED}🚨 Push gagal setelah $MAX_RETRY kali percobaan.${NC}"
fi

git status

