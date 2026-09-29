# Portofolio Fetrus Meilano Ilhamsyah

Situs portofolio pribadi yang dinamis dengan panel admin. Lihat [`docs/KONSEP.md`](docs/KONSEP.md) untuk gambaran lengkap dan [`docs/PROGRESS.md`](docs/PROGRESS.md) untuk status pengerjaan per tahap.

## Stack

PHP 8.4 · Laravel 13 · Filament 5 · Tailwind CSS 4 · Alpine.js 3 · SQLite · Vite 8

## Menjalankan di lokal (Windows)

### Prasyarat

- PHP ≥ 8.3 — jika belum ada, install via WinGet:
  ```powershell
  winget install PHP.PHP.8.4
  ```
  Setelah install, **buka terminal baru** agar PATH terupdate. Jika `php -v` masih tidak dikenali, tambahkan path PHP ke variabel lingkungan `PATH` secara manual melalui System Properties.

- Composer ≥ 2 — https://getcomposer.org/download/
- Node.js ≥ 20.19 atau 22 LTS — https://nodejs.org (gunakan `nvm` untuk mengelola versi)
- Git

### Langkah setup

```powershell
# 1. Salin file environment
cp .env.example .env

# 2. Install dependensi PHP
composer install

# 3. Generate application key
php artisan key:generate

# 4. Buat file database SQLite dan jalankan migrasi
New-Item -ItemType File -Force database/database.sqlite
php artisan migrate

# 5. Install dependensi Node
npm install

# 6. Build aset (untuk produksi)
npm run build

# Atau, jalankan dev server dengan hot-reload
npm run dev
```

### Menjalankan server lokal

```powershell
php artisan serve
# → buka http://localhost:8000
# → panel admin di http://localhost:8000/admin
```

Jalankan `npm run dev` di terminal terpisah saat pengembangan agar perubahan CSS/JS langsung terlihat.

### Catatan PATH PHP (Windows)

PHP yang diinstall via WinGet mungkin belum otomatis masuk ke PATH di terminal yang sudah terbuka. Jika `php -v` tidak dikenali, jalankan ini di terminal PowerShell untuk session tersebut:

```powershell
$env:PATH = "C:\Users\<username>\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe;" + $env:PATH
```

Ganti `<username>` dengan nama user Windows Anda. Untuk permanen, tambahkan path itu ke System Environment Variables.

## Dokumentasi

| File | Isi |
|------|-----|
| [`docs/KONSEP.md`](docs/KONSEP.md) | Tujuan, halaman, stack, desain, aturan kerja |
| [`docs/PROGRESS.md`](docs/PROGRESS.md) | Status 10 tahap pengerjaan |
