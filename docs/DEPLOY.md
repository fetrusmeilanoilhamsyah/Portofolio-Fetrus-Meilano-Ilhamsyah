# Panduan Deployment Portofolio ke VPS

Panduan ini memuat langkah-langkah *deployment* ke VPS yang sudah memiliki layanan lain (bot Telegram, webhook, API lokal). Prioritas utama adalah **isolasi** agar situs tidak mengganggu layanan yang sudah ada.

## 1. Pemeriksaan Awal VPS

Jalankan perintah berikut sebelum menginstal apa pun:

```bash
# Cek OS dan versi
cat /etc/os-release

# Cek memori (RAM)
free -m

# Cek ruang disk
df -h

# Cek port yang terbuka (pastikan bot dan webhook aman)
ss -tlnp

# Cek versi Nginx dan PHP
nginx -v
php -v

# Lihat situs apa saja yang aktif di Nginx
ls -l /etc/nginx/sites-enabled/
```

**Kapan harus berhenti?**
- Jika `free -m` menunjukkan kolom `available` (atau `free`) kurang dari **400 MB**, *deployment* berisiko membuat VPS kehabisan memori (OOM kill). Sebaiknya gunakan VPS terpisah.
- Periksa `ss -tlnp`. Pastikan layanan API lokal dan port internal lainnya hanya mendengarkan di `127.0.0.1`. Jangan mengubah port yang sudah berjalan.

## 2. Instalasi PHP & Ekstensi (Tanpa Mengganggu Versi Lama)

Portofolio ini membutuhkan **PHP >= 8.3**. Jika VPS menggunakan PHP versi lama (misal 8.1), Anda bisa menginstal PHP 8.3 secara paralel (di Ubuntu/Debian via repositori `ondrej/php`).

```bash
# Contoh untuk Ubuntu/Debian
sudo apt update
sudo apt install php8.4-fpm php8.4-cli php8.4-sqlite3 php8.4-mbstring php8.4-gd php8.4-xml php8.4-curl php8.4-intl php8.4-zip php8.4-exif
```
> **Penting**: Ekstensi `exif` wajib agar foto dari HP tidak tersimpan miring. `gd` wajib untuk konversi gambar. Jalankan `composer check-platform-reqs` di lokal untuk memvalidasi.

## 3. Membuat Pengguna Sistem Khusus

Untuk mengisolasi aplikasi agar tidak bisa membaca `/opt` (tempat bot berjalan) atau token `.env` layanan lain:

```bash
sudo adduser --system --group --disabled-login --no-create-home portfolio

# Buat direktori web
sudo mkdir -p /var/www/portofolio
sudo chown -R portfolio:portfolio /var/www/portofolio
```
*(Pastikan folder bot di `/opt` dimiliki oleh root atau pengguna bot, dan direktori tersebut di-chmod `750` atau `700` agar tidak bisa dibaca publik/grup lain).* 

## 4. Konfigurasi Pool PHP-FPM Khusus

Jangan gunakan pool `www` bawaan. Salin file `deploy/php-fpm-pool.conf` ke `/etc/php/8.4/fpm/pool.d/portfolio.conf` (sesuaikan jalur versi PHP).

```bash
sudo cp deploy/php-fpm-pool.conf /etc/php/8.4/fpm/pool.d/portfolio.conf
```
Pool ini memiliki `pm = ondemand` (hemat RAM) dan membatasi `open_basedir` murni ke `/var/www/portofolio` dan `/tmp` saja.
*Setelah disalin, restart PHP-FPM:* `sudo systemctl restart php8.4-fpm`.

## 5. Konfigurasi Nginx

Salin file Nginx dari `deploy/nginx.conf` ke `/etc/nginx/sites-available/portofolio.conf`, lalu buat symlink ke `sites-enabled/`.

```bash
# Cadangkan konfigurasi lama nginx
sudo cp -r /etc/nginx /etc/nginx_backup

sudo cp deploy/nginx.conf /etc/nginx/sites-available/portofolio.conf
sudo ln -s /etc/nginx/sites-available/portofolio.conf /etc/nginx/sites-enabled/

# Wajib tes nginx sebelum reload!
sudo nginx -t
sudo systemctl reload nginx
```

**Perlindungan `/admin` dan `/livewire`:**
- **Opsi A (Cloudflare Access - Disarankan):** Buat kebijakan *Zero Trust* di Cloudflare Access yang hanya mengizinkan email Anda untuk jalur `/admin*` dan `/livewire*`.
- **Opsi B (Nginx IP Restrict):** Tambahkan blok Nginx di dalam server block:
  ```nginx
  location ~ ^/(admin|livewire) {
      allow 1.2.3.4; # IP Rumah/Kantor Anda
      deny all;
      try_files $uri $uri/ /index.php?$query_string;
  }
  ```

## 6. Persiapan Kode & `.env`

Di lokal, jalankan *build* aset Vite (karena Node tidak dipasang di VPS):
```bash
npm run build
```

Unggah seluruh folder (kecuali `.git` dan `/node_modules`) menggunakan `rsync` ke `/var/www/portofolio`.
```bash
rsync -avz --exclude '.git' --exclude 'node_modules' ./ root@[IP_VPS_ANDA]:/var/www/portofolio/
```

Di VPS, salin `.env.production.example` menjadi `.env`:
```bash
cp .env.production.example .env
# Isi APP_KEY dengan kunci baru dari `php artisan key:generate`
```

### Mengatur IP Asli Cloudflare (Jika Pakai CF Proxy)
Buka `.env` dan tambahkan IP proxy Cloudflare di `TRUSTED_PROXIES`. File `.env.production.example` sudah menyediakan daftar default IPv4 dan IPv6 Cloudflare. Ini penting agar *rate-limiter* tidak memblokir IP Cloudflare.

## 7. Izin File (Permissions) & Logrotate

```bash
# Ubah kepemilikan
sudo chown -R portfolio:www-data /var/www/portofolio
sudo find /var/www/portofolio -type f -exec chmod 644 {} \;
sudo find /var/www/portofolio -type d -exec chmod 755 {} \;

# Beri izin tulis untuk folder khusus
sudo chown -R portfolio:www-data /var/www/portofolio/storage
sudo chown -R portfolio:www-data /var/www/portofolio/bootstrap/cache

# Pastikan database sqlite bisa ditulisi
sudo chmod -R 775 /var/www/portofolio/storage
sudo chmod -R 775 /var/www/portofolio/bootstrap/cache
sudo chmod 775 /var/www/portofolio/database
sudo chmod 664 /var/www/portofolio/database/database.sqlite
```

## 8. Menjalankan Deployment (`deploy.sh`)

Jalankan skrip deploy sebagai pengguna `portfolio`:
```bash
sudo -u portfolio bash deploy/deploy.sh
```
*Catatan:* Skrip ini akan melakukan `composer install --no-dev`, migrasi, dan *caching*. Jika Anda memakai Git di VPS, pastikan folder dimiliki secara konsisten.

## 9. Penataan Backup Berkala

Proyek ini memiliki perintah bawaan `php artisan portfolio:backup` yang akan memadatkan `database.sqlite` dan folder unggahan publik menjadi ZIP tertanggal. 

Tambahkan cron job agar skeduler berjalan (jalankan `sudo crontab -u portfolio -e`):
```bash
* * * * * cd /var/www/portofolio && php artisan schedule:run >> /dev/null 2>&1
```
Arsip akan disimpan di `storage/app/backups/`. Unduh cadangan ini berkala menggunakan SFTP atau RSYNC ke komputer lokal Anda.

## 10. HTTPS

**Opsi 1: Cloudflare Origin Certificate (Disarankan)**
1. Di Cloudflare, ubah SSL/TLS ke **Full (Strict)**.
2. Buat *Origin Certificate*, unduh `.pem` dan `.key` ke `/etc/ssl/certs/` dan `/etc/ssl/private/`.
3. Tambahkan ke `nginx.conf`: 
   ```nginx
   listen 443 ssl http2;
   ssl_certificate /etc/ssl/certs/portofolio.pem;
   ssl_certificate_key /etc/ssl/private/portofolio.key;
   ```

**Opsi 2: Let's Encrypt / Certbot**
1. Pastikan domain terhubung (tanpa proxy Cloudflare orange-cloud).
2. Jalankan `sudo certbot --nginx -d example.com`.

## 11. Pengguna Admin Pertama

Buat akun admin di produksi:
```bash
sudo -u portfolio php artisan portfolio:make-admin
```

## 12. Uji Asap (Smoke Test)

- [ ] Akses halaman Home (apakah 200 OK?).
- [ ] Ubah bahasa ke EN dan kembali ke ID.
- [ ] Akses `/admin` dan login dengan akun yang dibuat.
- [ ] Buat Proyek baru berstatus Draft, simpan.
- [ ] Unggah gambar > 3MB, pastikan tidak error dan teroptimasi jadi WebP.
- [ ] Cek rute publik, pastikan proyek Draft **tidak** muncul.
- [ ] Ubah status menjadi Terbit, pastikan sekarang proyek muncul di halaman publik.
- [ ] Akses `/sitemap.xml` dan `/robots.txt`.
- [ ] Cek *network tab* di peramban, pastikan header Keamanan (X-Frame-Options, X-Content-Type-Options) ada.

## 13. Rencana Pembatalan (Rollback)

Jika terjadi *crash* fatal:
1. Masukkan mode pemeliharaan: `php artisan down`.
2. Pulihkan cadangan dari `storage/app/backups/` menggunakan `unzip`.
3. Timpa file SQLite yang rusak.
4. Jalankan `git checkout <commit_sebelumnya>` (jika kode bermasalah).
5. Bersihkan cache: `php artisan optimize:clear`.
6. Matikan pemeliharaan: `php artisan up`.

## 14. Github Pages Situs Lama

Biarkan Github Pages tetap aktif (jangan hapus repositori lama) **sampai** situs VPS lulus uji asap 100%. Setelah semuanya lancar semalaman, barulah ganti DNS A Record / CNAME domain Anda dari Github Pages ke IP VPS (atau aktifkan proxy Cloudflare).
