#!/bin/bash

# ====== WARNA ======
BLUE='\033[1;34m'
GREEN='\033[1;32m'
PINK='\033[1;35m'
DARK_RED='\033[1;31m'
NC='\033[0m' # No Color

MAX_RETRY=3
COUNT=1

echo -e "${BLUE}Menambahkan perubahan...${NC}"
git add .

echo -e "${BLUE}Melakukan commit...${NC}"
git commit -m "Auto commit" || echo -e "${PINK}Tidak ada perubahan untuk di-commit${NC}"

while [ $COUNT -le $MAX_RETRY ]
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

