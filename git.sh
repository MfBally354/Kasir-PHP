#!/bin/bash

echo "Menambahkan git add .";
git add .

echo "Mengcommit!!!";
git commit -m "Debug"

echo "Mengpush hasil";
git push

echo "Status git";
git status
