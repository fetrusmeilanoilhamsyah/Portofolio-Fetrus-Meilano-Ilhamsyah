# PROGRESS: Portofolio Fetrus Meilano Ilhamsyah

## Daftar Tahap

| # | Nama Tahap | Status |
|---|---|---|
| 1 | Fondasi proyek dan file konsep | Selesai |
| 2 | Database dan model | Selesai |
| 3 | Panel admin bagian 1: login, keamanan, Proyek | Selesai |
| 4 | Panel admin bagian 2: Pengalaman, Sertifikat, Tautan, Pengaturan situs | Belum |
| 5 | Layout publik dan sistem desain (termasuk i18n dan tema) | Belum |
| 6 | Home dan About | Belum |
| 7 | Experience dan Projects | Belum |
| 8 | Media Sosial, Kontak, SEO | Belum |
| 9 | Polesan, performa, keamanan, tes | Belum |
| 10 | Persiapan deploy ke VPS | Belum |

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
- PHP diinstall via WinGet karena XAMPP lama sudah dihapus. PHP binary ada di `C:\Users\<username>\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe`. **Wajib tambahkan ke PATH sistem** agar `php` bisa dipanggil langsung dari terminal baru (saat ini butuh refresh shell).
- Vite 8 membutuhkan Node ≥ 20.19.0, sedangkan mesin memakai 20.18.1. Build tetap berjalan setelah install manual `@rolldown/binding-win32-x64-msvc`. Untuk menghilangkan warning, upgrade Node ke 20.19+ atau 22.x disarankan di tahap berikutnya.
- `database/database.sqlite` dikecualikan dari git (file lokal). Tiap developer baru perlu jalankan `touch database/database.sqlite && php artisan migrate`.
- Proyek berada di `D:\PROJEK PROJEK KODING\portofolio`.
- `APP_TIMEZONE=Asia/Jakarta` ditambahkan ke `.env` dan `.env.example`; `config/app.php` membacanya via `env('APP_TIMEZONE', 'UTC')`.

---

## Tahap 2 — Database dan Model ✅ SELESAI

**Selesai pada:** 2026-09-29

### Yang selesai
- [x] 7 migrasi baru: `site_settings`, `projects`, `project_media`, `experiences`, `certificates`, `links`, `link_highlights`
- [x] Semua enum diimplementasikan sebagai PHP backed enum + string kolom (tidak pakai `$table->enum()`): `ProjectType`, `ProjectStatus`, `MediaKind`, `ExperienceKind`, `LinkGroup`
- [x] 7 model baru dengan `HasTranslations`: `SiteSetting`, `Project`, `ProjectMedia`, `Experience`, `Certificate`, `Link`, `LinkHighlight`
- [x] Scope `published()` dan `ordered()` di semua model yang relevan
- [x] Slug otomatis dari judul Indonesia, unik (akhiran angka), dan immutable setelah terbit
- [x] `published_at` terisi sekali saat pertama kali diterbitkan
- [x] `SiteSetting::current()` — singleton-style, tidak bisa buat baris kedua
- [x] Fallback bahasa `id` dikonfigurasi di `AppServiceProvider` via `Translatable::fallback()`. String kosong dari form admin otomatis fall back ke `id` karena `allowEmptyStringForTranslation = false` (default)
- [x] Kunci asing cascade delete: `project_media.project_id` dan `link_highlights.link_id`
- [x] Indeks: semua yang diminta sudah ditambahkan
- [x] 7 factory untuk pengujian (`ProjectFactory`, `ProjectMediaFactory`, `ExperienceFactory`, `CertificateFactory`, `LinkFactory`, `LinkHighlightFactory`, `SiteSettingFactory`)
- [x] `SiteSettingSeeder` — idempoten, placeholder `[ISI: ...]`, tidak menimpa data yang sudah ada
- [x] `config/portfolio.php` dengan `admin_email` membaca dari `ADMIN_EMAIL` env
- [x] `ADMIN_EMAIL=` ditambahkan ke `.env.example` dan `.env`
- [x] Perintah `php artisan portfolio:make-admin` — baca email dari config, interaktif, idempoten
- [x] 29 tes — semua hijau (scope published, published_at sekali, default draft, slug unik+immutable, fallback bahasa termasuk string kosong, cascade delete, SiteSetting current, make-admin)
- [x] `migrate:fresh --seed` sukses; `db:seed` kedua kalinya tidak duplikasi
- [x] `config:cache` + `portfolio:make-admin` bekerja; `config:clear` sudah dijalankan
- [x] `php artisan test` → 29/29 hijau
- [x] `vendor/bin/pint` → 0 error

### Keputusan penting
- Default nilai model (`status`, `is_featured`, `sort_order`, `is_published`) didefinisikan di property `$attributes` di model, bukan hanya di migration. Ini penting agar Eloquent mengenal default sebelum save (tanpa perlu `refresh()` setelah create di test).
- Fallback bahasa bekerja via `Translatable::fallback(fallbackLocale: 'id')` di `AppServiceProvider`. String kosong difilter karena `allowEmptyStringForTranslation = false` (default spatie v6).
- `SiteSetting::exists()` adalah static method custom (bukan `Builder::exists()`) untuk cek apakah tabel punya baris.

### Catatan perbedaan dengan KONSEP.md
Tidak ada perbedaan yang ditemukan antara skema yang diimplementasikan dengan spesifikasi di KONSEP.md. Semua model konten yang disebutkan sudah diimplementasikan.


## Catatan lingkungan

| Item | Status | Tindakan |
|------|--------|----------|
| Node.js 20.18.1 | ⚠️ Di bawah minimum Vite 8 (butuh ≥ 20.19) | Upgrade ke **Node 22 LTS** atau minimal 20.19+ sebelum tahap berikutnya. Gunakan `nvm` atau `winget install OpenJS.NodeJS.LTS`. |
| PHP VPS (tahap 10) | ❓ Belum dicek | Pastikan PHP di VPS ≥ **8.3** sebelum deploy. Jika lebih rendah, putuskan apakah menurunkan constraint `composer.json` atau upgrade VPS PHP terlebih dahulu. |

---

## Tahap 3 — Panel admin bagian 1 Selesai

**Selesai pada:** 2026-09-29

### Yang selesai
- [x] Autentikasi panel admin dibatasi hanya untuk `ADMIN_EMAIL` via `FilamentUser::canAccessPanel`.
- [x] `noindex` ditambahkan ke head admin via `PanelsRenderHook::HEAD_START`.
- [x] Rate limiting login didukung bawaan Filament 5 (WithRateLimiting di halaman Login).
- [x] Autentikasi dua faktor (2FA) diaktifkan via `AppAuthentication` Filament 5 dengan enkripsi cast dan tabel `users` telah diperbarui.
- [x] Warna primer disesuaikan ke `#C8501E` (terang) dan `#E8743F` (gelap).
- [x] `ImageOptimizer` dibuat memakai ekstensi **GD** murni karena Imagick dan paket Intervention tidak terpasang (mengonversi ke WebP, max 1600px).
- [x] Resource `ProjectResource` dibuat (terpisah antara class Form dan Table pada Filament 5).
- [x] Tab translatable dibuat manual (title.id, title.en) tanpa memakai plugin third-party karena `spatie-laravel-translatable-plugin` resmi belum terpasang.
- [x] Validasi unggah disesuaikan: image max 5MB, video max 8MB, validasi error kustom.
- [x] Event `ContentChanged` dibuat dan dipancarkan saat `Project` dibuat/diubah/dihapus.
- [x] Slug digenerate dengan penanganan judul Indonesia kosong (fallback ke random).
- [x] Filter, aksi massal, tombol pratinjau, dan reorderable pada tabel Project selesai.
- [x] Tes khusus dibuat di `AdminPanelTest.php` (akses panel, slug immutable, fallback empty title, ImageOptimizer error).

### Keputusan penting
- **2FA:** Memakai implementasi asli dari `Filament\Auth\MultiFactor\App\AppAuthentication`.
- **ImageOptimizer:** Pakai GD bawaan PHP (imagecreatefrom*, imagecopyresampled, imagewebp) alih-alih memasang package baru, sesuai aturan proyek.
- **Translasi Admin:** Tidak memakai plugin Spatie Translatable Filament karena dilarang menambah paket tanpa izin, jadi field .id dan .en dibuat manual di form.
- **Rate Limiting:** Mengandalkan limit bawaan halaman Login Filament 5 (5 kali per menit).

### Koreksi Tahap 3b
- **Perbaikan Namespace Filament 5:** Mengganti import `Filament\Forms\Components\Tabs` menjadi `Filament\Schemas\Components\Tabs`, `Get` menjadi `Filament\Schemas\Components\Utilities\Get`, dan aksi-aksi tabel dari `Filament\Tables\Actions` menjadi `Filament\Actions`. (Catatan namespace Filament 5: Tabs, Get, Set, Section, Grid, Fieldset ada di `Schemas`; Action, BulkAction, EditAction, DeleteAction, dll ada di `Actions`).
- **Hidrasi Translatable (Edit):** Menambahkan trait `FillsTranslatableAttributes` yang memetakan manual atribut multi-bahasa di `mutateFormDataBeforeFill`, serta callback `mutateRelationshipDataBeforeFillUsing` di Repeater media.
- **Event ContentChanged:** Membuat trait `DispatchesContentChanged` dan menerapkannya pada `Project` dan `ProjectMedia` sehingga perubahan media tak terlewat.
- **2FA Recoverable:** `AppAuthentication` pada `AdminPanelProvider` kini ditautkan dengan `->recoverable()` (cara memulihkan: gunakan Recovery Code saat login).
- **Toleransi Email:** `canAccessPanel` disesuaikan menggunakan `strtolower` agar case-insensitive.
- **Enum Labels:** `ProjectType`, `ProjectStatus`, dan `MediaKind` diimpelementasikan dengan `HasLabel` sehingga tampil rapi di panel admin.
- **Optimasi Gambar Lanjutan:** Menambahkan rotasi orientasi EXIF dan pengecekan resolusi maksimal 24MP pada `ImageOptimizer`.
- **Livewire Tests:** Menambahkan tes `ProjectResourceTest` untuk memvalidasi fungsi render, penambahan, pengubahan, hidrasi, dan aksi massal.

