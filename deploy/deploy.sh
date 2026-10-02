#!/bin/bash
set -e

echo "Memulai proses deploy Portofolio..."

# Pastikan dijalankan oleh pengguna portfolio
if [ "$(whoami)" != "portfolio" ]; then
    echo "ERROR: Skrip ini harus dijalankan sebagai pengguna 'portfolio'"
    exit 1
fi

cd /var/www/portofolio

# 1. Masukkan mode maintenance
php artisan down --refresh=15 --secret="deploy-$(date +%s)" || true

# 2. Tarik kode terbaru
git pull origin main

# 3. Instal dependensi PHP
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Eksekusi migrasi database
php artisan migrate --force

# 5. Bersihkan dan buat cache konfigurasi, rute, view
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Pastikan symlink storage ada
php artisan storage:link

# 7. Bersihkan cache response tamu murni (redis/file cache utama yang menampung cache tamu)
php artisan cache:clear

# 8. Matikan mode maintenance
php artisan up

echo "Deploy berhasil diselesaikan!"
