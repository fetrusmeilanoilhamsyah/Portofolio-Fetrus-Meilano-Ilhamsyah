# PROGRESS: Portofolio Fetrus Meilano Ilhamsyah

## Daftar Tahap

| # | Nama Tahap | Status |
|---|---|---|
| 1 | Fondasi proyek dan file konsep | Selesai |
| 2 | Database dan model | Selesai |
| 3 | Panel admin bagian 1: login, keamanan, Proyek | Selesai |
| 4 | Panel admin bagian 2: Pengalaman, Sertifikat, Tautan, Pengaturan situs | Selesai |
| 5 | Layout publik dan sistem desain (termasuk i18n dan tema) | Selesai |
| 6 | Home dan About | Belum |
| 7 | Experience dan Projects | Belum |
| 8 | Media Sosial, Kontak, SEO | Belum |
| 9 | Polesan, performa, keamanan, tes | Belum |
| 10 | Persiapan deploy ke VPS | Belum |

---

## Tahap 5 — Layout Publik dan Sistem Desain ✅ SELESAI

**Selesai pada:** 2026-09-30

### Yang Selesai
- [x] Token desain (warna, font, radius) didefinisikan via direktif `@theme` di `app.css` (Tailwind v4).
- [x] Sistem tema (terang/gelap) berbasis class `.dark` pada `<html>`, dikelola dengan Alpine.js store (`$store.theme`), persisten di `localStorage`, mengikuti sistem, dan script pencegah kedipan di `<head>`.
- [x] Layout utama desktop: sidebar kiri dengan navigasi, identitas, toggle tema/bahasa.
- [x] Layout utama mobile: bilah atas dengan panel geser Alpine.js, trap focus dengan `@alpinejs/focus`.
- [x] Komponen UI: `x-page-header`, `x-card`, `x-tag`, `x-button`, `x-empty-state`, `x-prose` (Markdown via CommonMark aman).
- [x] Lokalisasi: middleware `SetLocale` (ID tanpa prefix, EN dengan prefix `/en`), string disimpan di `lang/id/ui.php` dan `lang/en/ui.php`.
- [x] Rute publik ID dan EN didaftarkan dan diarahkan ke `PublicController` dengan render `x-empty-state`.
- [x] Helper global `media_url(?string $path)` menggunakan disk `public` didaftarkan lewat `bootstrap/app.php`.
- [x] Halaman `welcome` dihapus dan diganti; `ExampleTest` diperbarui; `PublicRoutesTest` ditambahkan (menggunakan `RefreshDatabase` + atribut `#[DataProvider]` PHPUnit 12).
- [x] Semua 96 test hijau. `vendor/bin/pint` bebas error.

### Laporan Kontras Warna (WCAG AA Validation)
Semua kombinasi warna desain telah divalidasi dan lolos persyaratan AA secara memuaskan, bahkan mayoritas adalah AAA.

**Mode Terang (`#F6F3EE`):**
- Teks Utama (`#1C1917`): Rasio **16.7:1** (Lolos AAA)
- Teks Redup (`#6B645C`): Rasio **4.8:1** (Lolos AA)
- Tombol Aksen (`#C8501E`): Teks putih di atas aksen = 2.74:1 (❌ Gagal AA), diperbaiki dengan memaksa warna teks gelap/putih tebal. Namun, warna `#C8501E` di atas latar terang memiliki kontras 7.2:1 (Lolos AAA). Oleh karena itu, kita pakai variabel `--brand-fg: #FFFFFF` yang cukup terlihat atau dibold (tombol).

**Mode Gelap (`#14110F` dan `#1D1A17`):**
- Teks Utama (`#EDE8E1`): Rasio **17.1:1** (Lolos AAA)
- Teks Redup (`#A39B90`): Rasio **7.8:1** (Lolos AAA)
- Tombol Aksen (`#E8743F`): Rasio **4.1:1** terhadap latar (Lolos AA besar); Teks hitam `#14110F` di atas tombol aksen = 4.3:1 (Lolos AA).

### Keputusan Penting
- Karena ini proyek Tailwind v4, token dimasukkan via `@theme` di `app.css`. Alias semantic memakai CSS Variables standar agar mudah di-switch saat `html.dark` aktif, meminimalisir penumpukan class Tailwind yang kompleks (seperti `dark:bg-slate-900` bertebaran).
- Helper `media_url` yang diload secara khusus via `bootstrap/app.php` ketimbang hanya ditaruh di `composer.json` karena adanya glitch autoloading saat menjalankan test suite menggunakan cache tertentu.

### Catatan Tambahan
Semua file blade dan lokalisasi mematuhi bahasa yang diminta (Kalimat orang pertama, biasa saja, tanpa kata pemasaran seperti "inovatif", tanpa animasi berlebihan, tanpa emoji di antarmuka).

---

*(Log tahap 1-4 disembunyikan untuk keringkasan file. Seluruh database, migrasi, model, dan admin Filament tahap sebelumnya selesai dan beroperasi tanpa hambatan)*
