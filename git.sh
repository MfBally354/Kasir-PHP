#!/bin/bash
set -e

echo "Menambahkan perubahan..."
git add .

echo "Melakukan commit..."
git commit -m "Debug perubahan terbaru"

echo "Push ke repository..."
git push

echo "Status repository:"
git status

