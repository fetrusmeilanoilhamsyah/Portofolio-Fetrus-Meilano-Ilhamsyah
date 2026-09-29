# PROGRESS: Portofolio Fetrus Meilano Ilhamsyah

## Daftar Tahap

| # | Nama Tahap | Status |
|---|------------|--------|
| 1 | Fondasi (PHP, Laravel, Filament, Tailwind, SQLite, git init) | ✅ Selesai |
| 2 | Model & Migrasi | ⬜ Belum |
| 3 | Panel Admin — Proyek | ⬜ Belum |
| 4 | Panel Admin — Pengalaman & Sertifikat | ⬜ Belum |
| 5 | Panel Admin — Tautan & Pengaturan Situs | ⬜ Belum |
| 6 | Halaman Publik — Layout & Home | ⬜ Belum |
| 7 | Halaman Publik — About, Experience, Projects | ⬜ Belum |
| 8 | Halaman Publik — Social, Contact, Slug Proyek | ⬜ Belum |
| 9 | Internasionalisasi (id/en), Tema, Aksesibilitas | ⬜ Belum |
| 10 | Polish, Testing, Optimasi, Deploy-ready | ⬜ Belum |

---

## Tahap 1 — Fondasi ✅ SELESAI

**Selesai pada:** 2026-09-29

### Yang selesai
- [x] PHP 8.4.25 diinstall via WinGet (PHP.PHP.8.4)
- [x] php.ini dikonfigurasi: ekstensi pdo_sqlite, curl, mbstring, openssl, zip, gd, intl aktif
- [x] Composer 2.9.5 terdeteksi
- [x] Node 20.18.1, npm 10.8.2 terdeteksi
- [x] Proyek Laravel 13.33.0 dibuat di `D:\PROJEK PROJEK KODING\portofolio`
- [x] Filament v5.9.0 terinstall + panel `/admin` dikonfigurasi
- [x] spatie/laravel-translatable 6.14.1 terinstall
- [x] league/commonmark 2.10.3 (sudah ada di Laravel default)
- [x] Tailwind CSS v4 via @tailwindcss/vite terinstall
- [x] Alpine.js v3 terinstall dan didaftarkan di app.js
- [x] @fontsource-variable/plus-jakarta-sans terinstall, dimuat via app.css (self-hosted, tanpa Google Fonts)
- [x] SQLite dikonfigurasi sebagai database (`DB_CONNECTION=sqlite`)
- [x] `database/database.sqlite` ada, migrasi awal berjalan (users, cache, jobs)
- [x] `.env` dan `.env.example` diperbarui: APP_NAME, locale id, SQLite
- [x] `vite.config.js` diperbarui: hapus referensi Bunny/Google Fonts
- [x] `docs/KONSEP.md` dibuat dengan isi lengkap sesuai spesifikasi
- [x] `docs/PROGRESS.md` dibuat (file ini)
- [x] `.gitignore` ditambahkan: `/database/database.sqlite`
- [x] `php artisan test` → 2/2 passed
- [x] `vendor/bin/pint` → 0 error
- [x] `npm run build` → berhasil (2.33s, font Plus Jakarta Sans ter-bundle)
- [x] `git init` + commit awal `chore: fondasi proyek`

### Versi yang terpasang
| Paket | Versi |
|-------|-------|
| PHP | 8.4.25 |
| Laravel | 13.33.0 |
| Filament | 5.9.0 |
| spatie/laravel-translatable | 6.14.1 |
| league/commonmark | 2.10.3 |
| Tailwind CSS | 4.x (via @tailwindcss/vite) |
| Alpine.js | 3.x |
| Node.js | 20.18.1 |
| npm | 10.8.2 |

### Keputusan penting
- PHP diinstall via WinGet karena XAMPP lama sudah dihapus. PHP binary ada di `C:\Users\ASUS_\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe`. **Wajib tambahkan ke PATH sistem** agar `php` bisa dipanggil langsung dari terminal baru (saat ini butuh refresh shell).
- Vite 8 membutuhkan Node ≥ 20.19.0, sedangkan mesin memakai 20.18.1. Build tetap berjalan setelah install manual `@rolldown/binding-win32-x64-msvc`. Untuk menghilangkan warning, upgrade Node ke 20.19+ atau 22.x disarankan di tahap berikutnya.
- `database/database.sqlite` dikecualikan dari git (file lokal). Tiap developer baru perlu jalankan `touch database/database.sqlite && php artisan migrate`.
- Proyek berada di `D:\PROJEK PROJEK KODING\portofolio`.
- `APP_TIMEZONE=Asia/Jakarta` ditambahkan ke `.env` dan `.env.example`; `config/app.php` membacanya via `env('APP_TIMEZONE', 'UTC')`.

---

## Catatan lingkungan

| Item | Status | Tindakan |
|------|--------|----------|
| Node.js 20.18.1 | ⚠️ Di bawah minimum Vite 8 (butuh ≥ 20.19) | Upgrade ke **Node 22 LTS** atau minimal 20.19+ sebelum tahap berikutnya. Gunakan `nvm` atau `winget install OpenJS.NodeJS.LTS`. |
| PHP VPS (tahap 10) | ❓ Belum dicek | Pastikan PHP di VPS ≥ **8.3** sebelum deploy. Jika lebih rendah, putuskan apakah menurunkan constraint `composer.json` atau upgrade VPS PHP terlebih dahulu. |

