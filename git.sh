#!/bin/bash

MAX_RETRY=3
COUNT=1

echo "Menambahkan perubahan..."
git add .

echo "Melakukan commit..."
git commit -m "Auto commit" || echo "Tidak ada perubahan untuk di-commit"

while [ $COUNT -le $MAX_RETRY ]
do
  echo "Percobaan push ke-$COUNT..."

  if git push; then
    echo "✅ Push berhasil!"
    break
  else
    echo "❌ Push gagal, mencoba pull --rebase..."
    git pull --rebase
  fi

  COUNT=$((COUNT+1))
done

if [ $COUNT -gt $MAX_RETRY ]; then
  echo "🚨 Push gagal setelah $MAX_RETRY kali percobaan."
fi

git status

