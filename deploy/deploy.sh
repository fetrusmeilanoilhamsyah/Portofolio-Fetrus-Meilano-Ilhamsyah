#!/bin/bash
set -e

echo "Memulai proses deploy Portofolio..."

# Pastikan dijalankan oleh pengguna portfolio
if [ "$(whoami)" != "portfolio" ]; then
    echo "ERROR: Skrip ini harus dijalankan sebagai pengguna 'portfolio'"
    exit 1
fi

cd /var/www/portofolio

# Persiapkan folder composer khusus agar tidak memakai home yang tidak ada
export COMPOSER_HOME="/var/www/portofolio/storage/composer"
export COMPOSER_CACHE_DIR="/var/www/portofolio/storage/composer/cache"
mkdir -p "$COMPOSER_HOME"
mkdir -p "$COMPOSER_CACHE_DIR"
# Pastikan git mengabaikan folder storage (sudah bawaan Laravel, tapi aman)

# 1. Masukkan mode maintenance
php artisan down --refresh=15 --secret="deploy-$(date +%s)" || true

# Tambahkan trap agar maintenance mode otomatis diangkat bila skrip gagal di tengah jalan
trap 'php artisan up; echo "Deploy selesai atau dihentikan. Maintenance mode dimatikan."' EXIT

# 2. Tarik kode terbaru secara aman (hanya fast-forward)
git pull origin master --ff-only || {
    echo "ERROR: Gagal menarik pembaruan. Ada perubahan lokal atau konflik pada git."
    exit 1
}

# 3. Instal dependensi PHP
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Eksekusi migrasi database
php artisan migrate --force

# 5. Bersihkan dan buat cache konfigurasi, rute, view
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Pastikan symlink storage ada secara idempoten
[ -L public/storage ] || php artisan storage:link

# 7. Bersihkan cache response tamu murni (redis/file cache utama yang menampung cache tamu)
php artisan cache:clear

# (Langkah 8 hapus artisan up karena sudah ditangani oleh trap EXIT)
