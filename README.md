# Portofolio Fetrus Meilano Ilhamsyah

Sistem manajemen konten dan antarmuka publik untuk situs portofolio pribadi yang dinamis. Proyek ini dirancang untuk memungkinkan pengelolaan penuh atas proyek, pengalaman, sertifikat, dan pengaturan situs melalui panel administrasi, tanpa perlu memodifikasi kode sumber secara manual.

## Arsitektur dan Teknologi

Proyek ini dibangun menggunakan kerangka kerja dan teknologi berikut:

- **Bahasa & Kerangka Kerja Dasar:** PHP 8.4 dan Laravel 13
- **Panel Administrasi:** Filament 5
- **Antarmuka Pengguna (Publik):** Tailwind CSS 4 dan Alpine.js 3
- **Basis Data:** SQLite
- **Manajemen Aset:** Vite 8

## Fitur Utama

- **Manajemen Konten Dinamis:** Pengelolaan entri proyek, riwayat pengalaman, sertifikat, dan tautan melalui antarmuka admin.
- **Lokalisasi (i18n):** Dukungan bahasa Indonesia sebagai bahasa baku dan bahasa Inggris sebagai alternatif.
- **Sistem Tema:** Mode terang dan gelap yang terintegrasi dengan preferensi sistem, dikelola di sisi klien (client-side).
- **Keamanan Terpadu:** Autentikasi dua faktor (2FA) untuk admin, pembatasan akses (rate limiting), dan pengamanan header HTTP.
- **Pengoptimalan Media Otomatis:** Konversi format gambar menjadi WebP dan penyesuaian resolusi saat pengunggahan untuk efisiensi ruang dan jaringan.
- **Performa Tinggi:** Implementasi mekanisme singgahan (cache) halaman penuh khusus tamu, serta pembuatan peta situs (sitemap) dan metadata secara dinamis untuk pengoptimalan mesin pencari (SEO).

## Prasyarat Sistem

Pastikan lingkungan pengembangan Anda memenuhi persyaratan minimum berikut:

- **PHP:** Versi 8.3 atau lebih baru (membutuhkan ekstensi: `pdo_sqlite`, `curl`, `mbstring`, `openssl`, `zip`, `gd`, `intl`, `exif`)
- **Composer:** Versi 2.0 atau lebih baru
- **Node.js:** Versi 20.19 atau lebih baru (disarankan versi 22 LTS)
- **Git**

## Panduan Instalasi (Lingkungan Lokal Windows)

Ikuti langkah-langkah berikut pada terminal PowerShell Anda untuk mengonfigurasi proyek secara lokal:

```powershell
# 1. Salin konfigurasi lingkungan (environment)
cp .env.example .env

# 2. Pasang dependensi PHP
composer install

# 3. Hasilkan kunci aplikasi
php artisan key:generate

# 4. Buat berkas basis data SQLite dan jalankan migrasi skema
New-Item -ItemType File -Force database/database.sqlite
php artisan migrate

# 5. Buat tautan simbolis untuk penyimpanan media (wajib untuk akses publik)
php artisan storage:link

# 6. Pasang dependensi Node.js
npm install

# 7. Kompilasi aset untuk produksi
npm run build

# Atau jalankan peladen pengembangan aset untuk pembaruan waktu nyata (hot-reload)
# npm run dev
```

### Menjalankan Peladen Lokal

Jalankan perintah berikut untuk memulai peladen lokal PHP:

```powershell
php artisan serve
```

- **Antarmuka Publik:** `http://localhost:8000`
- **Panel Administrasi:** `http://localhost:8000/admin`

*Catatan:* Jika Anda menggunakan PHP yang diinstal melalui WinGet dan perintah `php` belum dikenali, pastikan direktori instalasi PHP telah ditambahkan ke dalam variabel lingkungan `PATH` di sistem operasi Anda.

## Dokumentasi Tambahan

Untuk panduan lebih mendalam mengenai arsitektur, pedoman pengerjaan, dan status penyelesaian, silakan merujuk pada dokumen berikut:

- [`docs/KONSEP.md`](docs/KONSEP.md) — Dokumen spesifikasi utama yang memuat tujuan proyek, daftar halaman, tumpukan teknologi, pedoman desain antarmuka, dan aturan kerja.
- [`docs/PROGRESS.md`](docs/PROGRESS.md) — Rekam jejak pengembangan yang mencatat detail penyelesaian tahap demi tahap serta keputusan teknis yang telah diambil.
- [`docs/DEPLOY.md`](docs/DEPLOY.md) — Panduan penyebaran (deployment) aplikasi ke peladen produksi (VPS).
