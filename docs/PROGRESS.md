# PROGRESS: Portofolio Fetrus Meilano Ilhamsyah

## Daftar Tahap

| # | Nama Tahap | Status |
|---|---|---|
| 1 | Fondasi proyek dan file konsep | Selesai |
| 2 | Database dan model | Selesai |
| 3 | Panel admin bagian 1: login, keamanan, Proyek | Selesai |
| 4 | Panel admin bagian 2: Pengalaman, Sertifikat, Tautan, Pengaturan situs | Selesai |
| 5 | Layout publik dan sistem desain (termasuk i18n dan tema) | Selesai |
| 6 | Home dan About | Selesai |
| 7 | Experience dan Projects | Selesai |
| 8 | Media Sosial, Kontak, SEO | Selesai |
| 9 | Polesan, performa, keamanan, tes | Selesai |
| 10 | Persiapan deploy ke VPS | Selesai |

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

### Penambahan Tahap 3b (tambahan terakhir)
- **Halaman Profil Admin:** `->profile()` ditambahkan ke `AdminPanelProvider`. Panel kini memiliki halaman `/admin/profile` yang berisi form ganti nama, email, kata sandi, dan **seksi manajemen 2FA** (karena `multiFactorAuthentication` aktif, Filament menampilkan UI setup/nonaktifkan 2FA di sana).
- **Perintah Reset 2FA:** `portfolio:reset-2fa` dibuat di `app/Console/Commands/Reset2faCommand.php` untuk kondisi terkunci.
- **Tes Tambahan:** `ImageOptimizerTest` (3a & 3b, EXIF di-skip karena ekstensi tidak ada), tes profil di `AdminPanelTest`, tes reset 2FA di `Reset2faCommandTest`, dan tes tambahan di `ProjectResourceTest` (3c & 3d).

---

## Cara Menggunakan 2FA

### Mengaktifkan 2FA
1. Login ke `/admin` dengan email dan kata sandi admin.
2. Klik nama pengguna di pojok kanan bawah sidebar, lalu pilih **Edit Profil** — atau akses langsung `/admin/profile`.
3. Di bagian **Autentikasi Dua Faktor**, klik tombol **Set up** pada "App Authentication".
4. Pindai kode QR dengan aplikasi autentikator (Google Authenticator, Authy, dll).
5. Masukkan kode OTP 6 digit untuk mengonfirmasi.
6. **Simpan kode pemulihan** yang ditampilkan — simpan di tempat yang aman dan terpisah dari perangkat.

### Masuk dengan 2FA Aktif
1. Masukkan email dan kata sandi seperti biasa.
2. Masukkan kode OTP dari aplikasi autentikator saat diminta.

### Pemulihan jika Perangkat Hilang
1. **Gunakan kode pemulihan** (recovery code) yang sudah disimpan — masukkan di kolom OTP saat login.
2. Setelah berhasil masuk, segera nonaktifkan 2FA di halaman profil, lalu aktifkan ulang dengan perangkat baru dan simpan kode pemulihan baru.

### Jalan Terakhir: Reset via Server (jika perangkat DAN kode pemulihan hilang)
Jalankan perintah ini di server melalui SSH atau akses terminal langsung:
```
php artisan portfolio:reset-2fa
```
Perintah akan meminta konfirmasi interaktif. Setelah reset, 2FA dinonaktifkan — admin dapat masuk hanya dengan email + kata sandi, lalu mengaktifkan ulang 2FA dari halaman profil.

---

## Catatan Teknis

### Trait `FillsTranslatableAttributes`
Trait ini (di `app/Filament/Concerns/FillsTranslatableAttributes.php`) ternyata **belum tentu dibutuhkan** pada spatie/laravel-translatable 6.14.1, karena versi tersebut mengembalikan array terjemahan saat serialisasi — Filament mungkin sudah memetakannya dengan benar tanpa trait ini. Trait dipertahankan karena:
- Semua tes lolos dengan kehadirannya.
- Tidak berbahaya (tidak mengubah data, hanya memastikan data diisi).
- Menghapusnya memerlukan investigasi lebih lanjut yang di luar cakupan tahap ini.
  
### Kebutuhan Server Produksi  
Ekstensi PHP berikut wajib diaktifkan (via php.ini atau paket sistem) agar fitur berfungsi penuh:  
- gd (dengan dukungan WebP): Diperlukan untuk memanipulasi, memotong, dan mengoptimasi gambar yang diunggah.  
- exif: Diperlukan untuk membaca metadata orientasi kamera. Tanpa ini, foto portrait yang diambil dari ponsel akan tersimpan miring tanpa peringatan.  
- mbstring & intl: Digunakan secara luas oleh Laravel dan Filament untuk penanganan string multi-bahasa.  
- pdo_sqlite (atau pdo_mysql): Driver basis data.  
Hasil composer check-platform-reqs:
```
Checking platform requirements for packages in the vendor dir
composer-runtime-api 2.2.2      success
ext-ctype            *          success provided by symfony/polyfill-ctype
ext-dom              20031129   success
ext-exif             8.4.25     success
ext-fileinfo         8.4.25     success
ext-filter           8.4.25     success
ext-gd               8.4.25     success
ext-hash             8.4.25     success
ext-iconv            8.4.25     success
ext-intl             8.4.25     success
ext-json             8.4.25     success
ext-libxml           8.4.25     success
ext-mbstring         *          success provided by symfony/polyfill-mbstring
ext-openssl          8.4.25     success
ext-pcre             8.4.25     success
ext-pdo_sqlite       8.4.25     success
ext-phar             8.4.25     success
ext-session          8.4.25     success
ext-tokenizer        8.4.25     success
ext-xml              8.4.25     success
ext-xmlreader        8.4.25     success
ext-xmlwriter        8.4.25     success
ext-zip              1.22.8     success
php                  8.4.25     success
```


---

## Tahap 4 — Panel admin bagian 2 Selesai

**Selesai pada:** 2026-09-30

### Yang selesai
- [x] Teks notifikasi aksi massal `ProjectsTable` diperbaiki menjadi "tidak punya cover atau teks alternatif cover".
- [x] Tes scope model (`published`, `ordered`) ditambah untuk semua model konten publik.
- [x] Resource `Experience` dibuat dengan tab translatable manual, konversi gambar via `ImageOptimizer`, dan validasi Filament 5.
- [x] Resource `Certificate` dibuat, dengan field datalist kustom untuk kategori bebas.
- [x] Resource `Link` dibuat dengan relasi `link_highlights`. Halaman Edit menggunakan `mutateRecordDataUsing` di RelationManager untuk translatable array.
- [x] Halaman tunggal `SiteSettings` dibangun dengan schema form. Penyimpanan memakai `SiteSetting::current()` untuk menghindari duplikasi baris.
- [x] Navigasi sidebar Filament dikelompokkan menjadi Konten (Proyek, Pengalaman, Sertifikat), Tautan, dan Pengaturan.
- [x] Pintasan pembuatan (Proyek, Sertifikat, Pengaturan) ditambah ke Dasbor dengan `DashboardShortcutsWidget`, widget statistik bawaan dihapus.
- [x] Trait `FillsTranslatableAttributes` diterapkan ke seluruh Edit page resource baru.
- [x] Trait `DispatchesContentChanged` diterapkan ke seluruh model konten publik baru.
- [x] Tes Livewire menyeluruh untuk list, create, dan edit resource baru.

### Keputusan penting
- **RelationManager Translasi:** Menggunakan metode `mutateRecordDataUsing` pada `EditAction` untuk mengatur ulang properti array terjemahan pada form modal tabel Sorotan.
- **Datalist Kategori:** Alih-alih membuat relasi Tag untuk sertifikat, instruksi "bebas tapi disarankan" diselesaikan memakai form `TextInput` dengan datalist yang dipopulasi otomatis (pluck) dari entri unik yang sudah ada di database.
- **Widget Dasbor:** Widget stat bawaan dihilangkan sesuai permintaan, diganti dengan Widget kustom yang menyajikan pintasan sederhana sesuai prioritas alur kerja admin.




## Tahap 5 — Layout Publik dan Sistem Desain ✅ SELESAI

**Selesai pada:** 2026-09-30

### Yang Selesai
- [x] Token desain (warna, font, radius) didefinisikan via direktif @theme di app.css (Tailwind v4).
- [x] Sistem tema (terang/gelap) berbasis class .dark pada <html>, dikelola dengan Alpine.js store (`.theme`), persisten di localStorage, mengikuti sistem, dan script pencegah kedipan di <head>.
- [x] Layout utama desktop: sidebar kiri dengan navigasi, identitas dari SiteSetting, toggle tema/bahasa.
- [x] Layout utama mobile: bilah atas dengan panel geser Alpine.js, trap focus dengan @alpinejs/focus.
- [x] Komponen UI: x-page-header, x-card, x-tag, x-button, x-empty-state, x-prose (Markdown via CommonMark aman).
- [x] Lokalisasi: middleware SetLocale tanpa efek samping session. Toggle bahasa dipusatkan di LocaleSwitcher.
- [x] Rute publik ID dan EN didaftarkan dan diarahkan ke PublicController dengan render x-empty-state.
- [x] View Composer mendaftarkan data SiteSetting ke layout secara efisien (satu kueri).
- [x] Halaman welcome dihapus.
- [x] Skrip sebaris dihapus dan diganti utility class CSS.
- [x] Helper global media_url(?string $path) menggunakan disk public didaftarkan lewat composer.json secara standar.
- [x] Pengujian komprehensif (kontras warna, rute, locale switcher, helper).


### Koreksi 4b dan 5b
- Memperbaiki salah ketik di laporan Tahap 5 akibat karakter kontrol (app.css, \.theme, \).

### Laporan Kontras Warna (WCAG AA Validation)
Semua kombinasi warna desain telah divalidasi dan diuji via ColorContrastTest.

**Mode Terang (#F6F3EE):**
- Teks Utama / Latar: **15.8:1** (Lolos AAA)
- Teks Redup / Latar: **5.27:1** (Lolos AA)
- Teks Brand (#A63F14) / Latar: **5.68:1** (Lolos AA)
- Teks Putih / Tombol Aksen (#C8501E): **4.54:1** (Lolos AA)

**Mode Gelap (#14110F dan #1D1A17):**
- Teks Utama / Latar: **15.4:1** (Lolos AAA)
- Teks Redup / Latar: **6.85:1** (Lolos AAA)
- Teks Brand (#E8743F) / Latar: **6.27:1** (Lolos AA)
- Teks Gelap / Tombol Aksen (#E8743F): **6.27:1** (Lolos AA)

### Keputusan Penting
- Pemanggilan SiteSetting::current() dihindari pada View untuk mencegah penulisan database (side effect). View Composer digunakan dengan SiteSetting::query()->first().
- Logika toggle bahasa diekstraksi ke LocaleSwitcher untuk menangani route parameter dan query string dengan aman.

## Tahap 6 — Home dan About ✅ SELESAI

**Selesai pada:** 2026-09-30

### Yang Selesai
- [x] Merapikan tahap 5: mengoreksi karakter kontrol di PROGRESS.md, memperbaiki komentar SetLocale, menyembunyikan tombol Palet Perintah (TODO tahap 9), dan merapikan layout grid di `<main>` (menggunakan properti `maxWidth` pada `x-layouts.public`).
- [x] Perbaikan tampilan tombol toggle tema (`theme-toggle`) dan bahasa (`lang-toggle`) di sidebar menjadi kotak batas halus, agar tidak tampak sekadar teks.
- [x] Halaman Home: menampilkan intro dari pengaturan situs, daftar proyek unggulan (maksimal 3, filter `published`, `featured`, `ordered`), dan proyek terbaru (maksimal 3, yang tidak unggulan).
- [x] Halaman About: menampilkan teks markdown tentang, tombol unduh CV (jika ada file), ringkasan keterampilan dari pengaturan situs, dan ringkasan pendidikan terbaru (filter `kind=pendidikan`).
- [x] Tampilan _empty state_ untuk skenario belum ada data di beranda maupun about.
- [x] Semua elemen teks antarmuka ditarik dari `ui.php` dengan fallback bahasa otomatis.
- [x] Komponen UI tambahan: `x-project-card` untuk tampilan grid dan `x-project-list-item` untuk tampilan daftar (list).
- [x] Pengujian fitur: HomeAboutTest memastikan rute publik (home dan about) mengabaikan konten draft, menampilkan data sesuai, dan menangani ketiadaan CV dengan elegan. Tes php artisan test berjalan lancar dan semua lolos (hijau).

### Keputusan Penting
- **View Composer**: Mengingat variabel `$siteSetting` diperlukan baik di `layouts.public` maupun di view `pages.*`, saya mengubah View Composer (di `ViewServiceProvider`) agar melakukan bind ke `['components.layouts.public', 'pages.*']`. Untuk menghindari eksekusi kueri berulang, kueri di-cache dengan static variable di dalam penutup (closure) composer.
- **Keahlian (Skills)**: Tidak menggunakan diagram batang atau angka tingkat penguasaan sesuai arahan. Hanya badge/tag rapi berdasarkan kelompok keahlian.
- **Batasan Kolom Layout**: Layout kontainer utama diatur dengan parameter `$maxWidth` (default `max-w-3xl`) agar bagian utama situs terpusat alami, tetapi dapat diperbesar untuk halaman lain jika dibutuhkan nantinya.

## Tahap 7 — Experience dan Projects ✅ SELESAI

**Selesai pada:** 2026-09-30

### Yang Selesai
- [x] Status tabel tahap 7 telah diselesaikan.
- [x] Halaman `/experience` dengan dua tab: Pengalaman dan Sertifikat. Diimplementasikan menggunakan state URL via Alpine.js (`?tab=`) dan pushState, serta bisa diakses via keyboard.
- [x] Tab Pengalaman: Menampilkan riwayat kerja, magang, organisasi, dan pendidikan dalam timeline vertikal, diurutkan menurut `started_at` DESC.
- [x] Tab Sertifikat: Menampilkan grid sertifikat dengan filter kategori dan pencarian client-side via Alpine.js. Klik sertifikat akan memunculkan modal (bisa ditutup dengan escape dan fokus kembali).
- [x] Halaman `/projects`: Menampilkan daftar proyek yang terbit dengan filter tipe (hanya menampilkan tipe yang memiliki proyek) dan pencarian client-side via Alpine.js. Diurutkan menurut `sort_order` lalu `published_at` DESC.
- [x] Halaman `/projects/{slug}`: Menampilkan detail proyek.
- [x] Tombol tautan proyek ditampilkan secara kondisional (Coba Bot, Kunjungi Situs, Demo, Lihat Kode) bergantung isi kolom.
- [x] Galeri media proyek: Gambar memakai aspek rasio, Video dimuat dengan `preload="none"`, di-pause saat keluar layar via native `IntersectionObserver`, dan dimainkan saat tampil di viewport. Embed iframe hanya dimuat saat elemen diklik.
- [x] Navigasi proyek sebelumnya dan berikutnya diimplementasikan berbasis urutan `sort_order` lalu `published_at` DESC.
- [x] Proyek draft dilarang untuk publik (mengembalikan halaman empty state / 404).
- [x] Semua kartu list dan item proyek memiliki empty state wajar.
- [x] Tes khusus dibuat di `ExperienceProjectTest` untuk asersi bahwa: proyek dan sertifikat draft tidak muncul, filter kategori bekerja baik, dan tampilan link kondisional sukses di-render.
- [x] Rute Public `test_project_show_returns_200` diperbaiki untuk menampung validasi `firstOrFail()`.
- [x] `php artisan test` 116/116 passing (hijau).
- [x] `vendor/bin/pint` dijalankan.

### Keputusan Penting
- **IntersectionObserver:** Digunakan implementasi native JavaScript di `x-data="videoObserver()"` pada Alpine.js, ketimbang menambahkan package pihak ketiga `@alpinejs/intersect`, demi mematuhi aturan tidak menambah paket (tanpa izin).
- **Tab URL State:** URL tab di-sync memanfaatkan history `pushState` agar perubahan tab langsung terefleksi ke address bar (contoh: `?tab=sertifikat`), yang berguna jika halaman dibagikan tanpa harus memuat ulang dari server.
- **Batas Unggahan Video:** Mengubah batas bawaan Livewire `temporary_file_upload.rules` menjadi maksimal 25 MB (via `config/livewire.php`) dan form Filament menjadi 20 MB, karena batasan aslinya memblokir unggahan video wajar (16 MB). Batas `upload_max_filesize` dan `post_max_size` PHP di server lokal juga telah dinaikkan.
- **Kutu (Bug) Enum Filament:** Memperbaiki *bug* pada `ProjectForm` yang sebelumnya menggunakan nilai asli *Enum Object* untuk validasi tampilan (membuat kolom unggah video sempat menghilang). Ini diperbaiki dengan pengecekan `instanceof \BackedEnum`.

## Tahap 8 — Media Sosial, Kontak, SEO ✅ SELESAI

**Selesai pada:** 2026-10-01

### Yang Selesai
- [x] Halaman `/social`: Menampilkan tautan akun dan saluran dengan sorotan, sesuai dengan konfigurasi yang diterbitkan di panel admin.
- [x] Halaman `/contact`: Menampilkan kontak dengan tombol salin (copy to clipboard) yang interaktif tanpa formulir pengiriman email.
- [x] SEO Metadata: Menerapkan meta deskripsi, Open Graph, Twitter card, canonical URL, dan hreflang di semua halaman publik melalui `<x-layouts.public>`.
- [x] Peta Situs & Robots: Menambahkan `robots.txt` dan `sitemap.xml` dinamis.
- [x] Favicons: Menerapkan favicon (SVG, PNG, Apple Touch Icon).
- [x] Warna tema dan layout konsisten menggunakan CSS variable dari `@theme`.

### Keputusan Penting
- **Pengambilan SEO Media:** URL untuk `og:image` dipastikan selalu dalam bentuk URL absolut (lengkap dengan `http`/`https`).
- **Komponen UI:** Halaman dirapikan agar konsisten menggunakan komponen internal `<x-card>` dan `<x-button>`.

## Koreksi 6b dan 8b ✅ SELESAI

**Selesai pada:** 2026-10-01

### Yang Selesai
- [x] Menghapus token warna `primary-*` dan `accent-*` yang tidak terdefinisi dan menggantinya dengan `ink`, `surf`, `line`, dan `brand`. Menambahkan pengujian `DesignGuardTest` agar kelas-kelas terlarang (`rounded-xl/2xl/3xl`, `shadow-*`, `backdrop-blur`) tidak kembali.
- [x] Video dibatasi maksimal 8 MB dengan kompresi lokal (menggunakan `ffmpeg -i input.mp4 -vcodec libx264 -crf 28 -preset fast -an output.mp4`). Di production, butuh `upload_max_filesize` dan `post_max_size` PHP minimal 10M atau 12M.
- [x] Membatasi unggahan gambar hanya untuk format JPEG, PNG, WEBP, GIF, dan AVIF (menghapus celah bypass SVG dan PDF di optimasi gambar).
- [x] Memperbaiki sematan (embed) dengan attribute `sandbox`, `referrerpolicy`, dan memastikan parameter tautan hanya untuk host yang diizinkan (Youtube, Vimeo, Streamable).
- [x] Menerapkan `x-cloak` alih-alih `style="display: none;"` pada modal pengalaman kerja.
- [x] Memperbaiki `robots.txt` dengan menghapus `Disallow: /en/admin` (rute tersebut tidak valid, cukup `/admin`).

## Tahap 9 — Polesan, Performa, Keamanan, Tes ✅ SELESAI

**Selesai pada:** 2026-10-01

### Yang Selesai
- [x] **Palet Perintah (Command Palette):** 
  - Dibangun menggunakan Alpine.js (<x-command-palette>).
  - Shortcut keyboard (Ctrl+K / Cmd+K), navigasi panah atas/bawah, Enter, dan Escape.
  - Data JSON di-fetch satu kali dari endpoint /api/command-palette dan difilter di sisi klien (client-side).
  - Termasuk halaman statis dan semua proyek yang sudah diterbitkan.
- [x] **Performa (Cache Halaman Publik):** 
  - Membuat CacheGuestResponse middleware khusus untuk pengunjung tamu.
  - Sesi dan cookie otomatis dihapus dari respons yang di-cache agar tidak bocor.
  - Memanfaatkan event ContentChanged dengan listener ClearGuestCache untuk memvalidasi (increment version) cache secara otomatis saat konten diedit via admin.
- [x] **Gambar & Layout Shifts:** 
  - Atribut width, height, dan loading="lazy" ditambahkan ke seluruh <img /> di halaman publik (About, Experience, Projects, Project Show).
  - Layout shifts berhasil dicegah melalui kombinasi Tailwind aspect-video, aspect-[4/3], dan aspect-square.
- [x] **Keamanan (Security):** 
  - SecurityHeaders middleware global ditambahkan untuk X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy.
  - HSTS diset pada mode production.
  - CSP diterapkan dalam mode Report-Only dan endpoint /api/csp-report dibuka (di-exclude dari CSRF).
  - Komponen x-prose untuk Markdown dipastikan merender aman (html_input: strip, allow_unsafe_links: false).
  - Middleware throttle:60,1 ditambahkan ke rute web untuk rate limiting.
  - APP_DEBUG=false diset secara default di .env.example.
- [x] **Aksesibilitas (A11y):** 
  - Urutan fokus keyboard logis.
  - Link "Lewati ke konten" (skip to content) diimplementasikan dan berfungsi baik.
  - Semua tombol interaktif (toggle tema, toggle bahasa, tombol menu mobile, dll) memiliki aria-label yang sesuai.
- [x] **Pengujian Otomatis:**
  - Tes spesifik StageNineTest ditambahkan. Meliputi asersi JSON endpoint, cache logic, middleware cache untuk guest/auth, keamanan header, dan sanitasi markdown.
  - Semua 125 pengujian hijau (php artisan test).
  - Linter bersih (vendor/bin/pint).
- [x] **Lighthouse:** 
  - Lighthouse dijalankan pada halaman Home (/).
  - **Performance:** 96
  - **Accessibility:** 100
  - **Best Practices:** 100
  - **SEO:** 92
  - (Target: Performance > 90, Accessibility > 95 telah terpenuhi secara sempurna)

### Keputusan Penting
- **Custom Cache Middleware:** Mengimplementasikan custom cache (CacheGuestResponse) dan increment versi kunci cache (guest_cache_version) daripada memasang ekstensi berat seperti spatie/laravel-responsecache, memastikan aplikasi tetap ringan tanpa package eksternal.
- **CSP Report Only:** Saat ini CSP diterapkan pada mode Report-Only. Mengingat Filament dan Alpine.js menggunakan inline-scripts/eval untuk operasi tertentu, pembatasan ketat harus diuji matang pada server staging sebelum diberlakukan penuh (enforced).

### Tahap 8
- Dibuat halaman social dan contact (profil mobile, tanpa form email).
- SEO meta tags ditambahkan dengan ShareImage (OG), `og:image` menggunakan URL absolut di layout, dan `media_url()` dibuat relatif secara standar.
- Semua data diambil dari Filament melalui tabel `links` dan pengaturannya.
- Sitemap dan robots.txt dihasilkan dinamis dari database tanpa package eksternal.
- Batasan yang diketahui: (a) `StripGuestCookies` menghapus cookie dari respons tamu, tetapi `StartSession` Laravel 11 tetap berjalan dan menulis berkas sesi kosong. Bisa dibersihkan GC otomatis, atau grup middleware web bisa dipecah untuk rute statis di masa depan. (b) CSP berjalan pada `Report-Only` dengan `unsafe-inline` dan `unsafe-eval` karena Alpine.js standar; versi CSP-build Alpine.js diperlukan jika ingin enforcing penuh.

### Koreksi 9b
- Middleware CacheGuestResponse diperbaiki agar menserialisasi array (status, content, header) alih-alih objek Response, dipindahkan ke belakang (append) grup web, dan otomatis menghapus Set-Cookie agar cache efisien dan tidak bocor ke user lain.
- .env.example dikembalikan ke lokal (APP_ENV=local, APP_DEBUG=true), lalu dibuat .env.production.example terpisah.
- Throttle (pembatas laju) dikhususkan untuk rute web publik (tidak berdampak pada Filament/Livewire) dan CSP Report di-throttle terpisah (10,1) dengan pembatasan 2KB body (menyimpan ke log channel csp khusus).
- TrustProxies dikonfigurasi melalui custom middleware App\Http\Middleware\TrustProxies yang membaca config('portfolio.trusted_proxies') (dan diatur lewat .env TRUSTED_PROXIES), menggantikan middleware default Laravel.
- Preload dan includeSubDomains dihapus dari HSTS karena domain belum final.
- ui.pages terjemahan bahasa ('Halaman', 'Pages') ditambahkan. Dibuat TranslationTest untuk memastikan kunci bahasa aman.
- Aksesibilitas: role="dialog", aria-modal, combobox, listbox, dan focus trap pada Command Palette serta Sertifikat modal. Focus kembali (restore) ditangani via Alpine x-trap.
- Evaluasi Lighthouse (Mobile Profil) pada halaman tambahan: /projects, /social, /contact, dan detail proyek meraih rata-rata skor: Perf 95, A11y 100, Best Practices 100, SEO 100.




### Koreksi 9c (Polesan Terakhir Sebelum Deploy)
- Bug Filament Dashboard yang berantakan karena array warna `primary-*` telah diatasi dengan mengubah nilai konfigurasi kembali ke string (hex `#C8501E`).
- Token warna Tailwind sisa di template (`text-brand-ink-hover`, dll) telah dibersihkan dan diproteksi via `UndefinedColorTokenTest`.
- Batasan format unggahan gambar resmi dipersempit (`jpeg`, `png`, `webp`, `gif`, `avif`) via `acceptedFileTypes` untuk menghindari manipulasi PDF/SVG, plus penambahan fail-safe pada optimasi gambar.
- Fitur embed eksternal kini memiliki validasi URL sisi host (menggunakan rule kustom yang membatasi Youtube, Vimeo, dan Streamable).
- Berkas *cache* dan tes sisa di root repo dihapus (`fix.php`, `fix_design.php`, `lh-projects.json`) dan telah dimasukkan ke `.gitignore`.
- Dokumen `PROGRESS.md` dibersihkan dari *typo* karakter kontrol dan sisa penghapusan, divalidasi penuh oleh linter pengujian karakter Markdown.
- Peringatan navigasi Command Palette kini dipusatkan ke sistem lokalisasi Laravel (`ui.palette_results`).



### Tahap 9d (Poles Tampilan Publik)
**Inisiatif Agen:**
- Memperbaiki .gitignore menjadi UTF-8 tanpa BOM.
- Menambahkan subjudul pada halaman melalui berkas bahasa (id dan en).
- Mengurangi bayangan pada project-card dan memperbaiki kontras garis `--line` mode gelap menjadi `#27272A`. (Catatan kontras: rasio `--line` `#27272A` terhadap `--canvas`/`--surf` `#09090B` di mode gelap adalah sekitar 1.3:1 yang cukup untuk garis batas dekoratif).
- Menertibkan nilai jarak elemen di CSS dan HTML.
- Memperkenalkan efek animasi slide up memudar CSS dasar yang menghormati prefers-reduced-motion.
- Menambahkan StageNineDTest yang menegaskan integritas perubahan pada tahap ini.
- Memperbarui desain kartu Proyek dan Sertifikat menjadi lebih menonjol dengan badge daftar teknologi, meniru struktur referensi namun tetap patuh pada identitas flat di KONSEP.md (tanpa bayangan, sudut 8px).
- Memperbarui bagian Keahlian (Skills) di halaman Tentang Saya menjadi komponen interaktif dengan Alpine.js.
- Mengubah arsitektur *grid* daftar Proyek dan Sertifikat dari *client-side* Alpine.js menjadi *server-side* murni (Blade @foreach) untuk SEO dan mencegah *delay* render.
- Memperbaiki sistem *margin/padding* pada *timeline* riwayat pengalaman agar tidak terpotong di layar *mobile*.

**Permintaan Pemilik:**
- Menyempurnakan layout sidebar desktop dan bilah atas mobile dengan identitas foto yang besar dan di tengah, ikon navigasi Lucide, toggle bahasa & tema gabungan.
- Menghapus label teks "Terbuka untuk bekerja" dari antarmuka publik (tetap ada di admin/database).
- Mewarnai semua tombol tindakan (Navigasi, Pagination Proyek, CV) dengan warna primer oranye solid.
- Mengubah tombol Ganti Tema dan Ganti Bahasa menjadi kapsul (*pill*) terkotak dengan indikator bergeser halus.
- Menyempurnakan rasa taktil: Menggunakan animasi tekan ringan (`active:scale-[0.98]` dan `active:opacity-90`) pada tombol, kartu, dan tautan dengan durasi 150-200ms ease-out.
- Memindahkan letak foto profil: Dihapus dari konten beranda, dan dipindah agar tampil besar dan dominan di dalam sidebar (desktop) serta di bagian atas menu geser (mobile), meniru referensi.

### Koreksi 9e
- **Sinkronisasi Dokumen:** Mengatur ulang `docs/KONSEP.md` untuk mencerminkan nilai HEX token yang benar-benar aktif di CSS.
- **Toggle Bahasa:** Mengubah logika *toggle* bahasa dari Alpine.js dengan penundaan `setTimeout` kembali menjadi tag `<a>` tautan murni. Ini mengizinkan fungsi natif peramban seperti Ctrl+Klik.
- **Kueri View Composer:** Memindahkan kueri Eloquent untuk tautan sosial media sidebar ke dalam `ViewServiceProvider` menggunakan `View::composer`.
- **Tautan Beranda:** Menambahkan tautan teks "Lihat pengalaman" di bawah barisan tombol utama di halaman Beranda.
- **Pembersihan Command Palette:** Memperbaiki kelas Tailwind untuk latar belakang palet perintah yang tembus pandang (`bg-surface` menjadi `bg-surf`), menyesuaikan warna teks, dan menghapus titik oranye berdenyut dari tombol navigasi *mobile*.
- **Pengecekan View Transition:** Menghapus implementasi View Transitions API karena SPA/Swup ditolak pemilik.

## Tahap 10 — Persiapan Deploy ke VPS ✅ SELESAI

**Selesai pada:** 2026-10-02

### Yang Selesai
- [x] Membuat folder `deploy/` berisi \
ginx.conf\, `php-fpm-pool.conf`, dan `deploy.sh`.
- [x] Memperbarui `.env.production.example` dengan konfigurasi absolut SQLite dan *trusted proxies* Cloudflare.
- [x] Membuat panduan langkah demi langkah di `docs/DEPLOY.md` yang aman, terisolasi, dan tidak mengganggu layanan lain di VPS.
- [x] Membuat perintah backup otomatis `php artisan portfolio:backup` yang mencadangkan SQLite dan unggahan (maksimal 7 cadangan terakhir) dan mendaftarkannya di Console Kernel / Routes.
- [x] Menjalankan uji coba akhir: tes lulus semua (hijau) dan kode telah dirapikan (pint).

### Keputusan Penting
- **Isolasi Pengguna:** Menggunakan pengguna sistem khusus `portfolio` dengan PHP-FPM pool tersendiri agar proses tidak bisa mengakses folder root atau bot lain di VPS.
- **Tidak Dieksekusi Otomatis:** Sesuai permintaan, tidak ada perintah yang benar-benar dijalankan di VPS oleh agen; semuanya dituangkan dalam panduan untuk dijalankan manual secara sadar oleh pemilik.
- **Pembatasan Nginx:** File *upload* dari `/storage` dicegah dari eksekusi PHP dengan arahan konfigurasi murni demi keamanan tambahan.

### Perbaikan Tambahan
- Menambahkan input `TagsInput` untuk kolom `stack` di `ProjectForm` (tab Umum) yang sebelumnya tidak tersedia di panel admin. Menggunakan `dehydrateStateUsing` untuk membersihkan tag duplikat dan string kosong secara otomatis sesuai permintaan.

### Perbaikan Infrastruktur Deploy
- **deploy.sh**: Menambahkan `trap` untuk keamanan mode maintenance, memperbaiki cara `git pull` agar `fast-forward` saja pada branch `master`, menangani `storage:link` agar idempoten, dan mengatur folder `.cache` Composer khusus.
- **DEPLOY.md**: Memperbaiki urutan langkah HTTPS agar menghindari error 525 (memasang origin certificate sebelum mengubah ke Cloudflare Full Strict).
- **nginx.conf**: Menambahkan contoh konfigurasi port 443 SSL (terkomentar) dan membatasi pemrosesan PHP hanya pada `index.php` demi keamanan.

### Penyelesaian Bug Login Produksi (Cloudflare + Filament)
- **Status:** Selesai pada 2026-10-03
- **Force HTTPS:** Tidak ditambahkan. Dengan Cloudflare Full (Strict) dan TLS di Nginx, Laravel membaca HTTPS langsung; perubahan manual itu hanya perlu saat mode Flexible.
- **Publikasi Aset Livewire:** Nginx yang memblokir dinamisasi berkas JS dicegah dengan menjalankan php artisan livewire:publish --assets dan filament:assets. File statis kini diletakkan secara permanen di /public, diizinkan oleh Nginx, dan memulihkan SPA admin secara menyeluruh.

## Tahap 11   CV cetak otomatis ' SELESAI
**Selesai pada:** 2026-10-04
### Yang Selesai
- [x] Migrasi kolom \show_on_cv\, \cv_summary\, dan \cv_show_photo\ tanpa mengubah migrasi lama.
- [x] Model dan factory diperbarui dengan kolom-kolom baru tersebut.
- [x] Admin Filament diperbarui dengan saklar 'Tampil di CV' untuk Pengalaman, Sertifikat, dan Tautan.
- [x] Halaman Pengaturan Situs di admin ditambahi bidang 'Ringkasan CV' dan saklar foto.
- [x] Rute \/cv\ dan \/en/cv\ serta metode pengontrol \cv()\ yang menghimpun dan memfilter data terbit.
- [x] Tampilan \cv.blade.php\ dengan tata letak satu kolom untuk CV, bebas dari [ISI:...].
- [x] Kelas CSS \.cv-paper\ dan aturan cetak \@page\ di \pp.css\ tanpa palet warna baru (lokal override token).
- [x] Tautan 'Lihat CV versi web' di halaman About dan meta noindex pada CV.
- [x] 151 tes dilewati (0 gagal, 0 skipped), termasuk 7 tes spesifik untuk halaman CV cetak dan Livewire admin.
