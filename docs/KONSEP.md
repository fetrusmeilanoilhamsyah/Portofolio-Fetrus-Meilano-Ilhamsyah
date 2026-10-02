# KONSEP: Portofolio Fetrus Meilano Ilhamsyah

## Tujuan
Situs portofolio pribadi yang dinamis. Semua isi (proyek, pengalaman, sertifikat, tautan, teks profil) diedit dari panel admin tanpa menyentuh kode. Ini bukan situs statis. Pemilik ingin bisa menambah proyek baru kapan saja dan menerbitkannya saat siap.

## Halaman publik
- `/` Home
- `/about`
- `/experience` (dua tab: Pengalaman dan Sertifikat)
- `/projects` dan `/projects/{slug}`
- `/social` (Media Sosial)
- `/contact`
- `/admin` panel admin (khusus pemilik)

Bahasa: Indonesia sebagai default (tanpa prefix), Inggris di prefix `/en`. Field bahasa Inggris boleh kosong; kalau kosong tampilkan versi Indonesia. Nama rute tetap berbahasa Inggris, label di layar diterjemahkan.

## Stack
| Komponen | Versi |
|---|---|
| PHP | 8.4.25 (minimum efektif: **8.3**, dituntut `composer.json`; Filament menuntut `^8.2`) |
| Laravel | 13.33.0 |
| Filament | 5.9.0 |
| spatie/laravel-translatable | 6.14.1 |
| league/commonmark | 2.10.3 |
| Tailwind CSS | 4.x (via `@tailwindcss/vite`) |
| Alpine.js | 3.x |
| Node.js | 20.18.1 (akan diupgrade ke 22 LTS — Vite 8 butuh ≥ 20.19) |
| Vite | 8.x |

Database: SQLite. Tidak ada Redis, antrean, atau layanan pihak ketiga selain yang tertulis di sini. Tidak ada React, Next.js, Supabase, atau Firebase.

**Catatan Tailwind v4:** Token desain (warna, font, radius) didefinisikan lewat direktif `@theme` di file CSS (`resources/css/app.css`), **bukan** lewat `tailwind.config.js`. Tidak ada file `tailwind.config.js` di proyek ini.

**Catatan VPS (tahap 10):** Versi PHP minimum yang dituntut saat ini adalah **8.3**. Sebelum deploy, pastikan PHP di VPS ≥ 8.3. Jika VPS memakai PHP versi lebih lama, buat keputusan eksplisit sebelum mengubah constraint di `composer.json`.

## Model konten (ringkas)
- Proyek: satu tabel untuk semua jenis (bot, web, sistem, magang, kegiatan). Tautan opsional: Telegram, situs, demo, kode. Tombol di halaman publik hanya muncul kalau tautannya diisi. Punya status draft atau terbit, unggulan, urutan, dan media (gambar, video pendek, atau URL).
- Pengalaman: kerja, magang, organisasi, pendidikan.
- Sertifikat: nama, penerbit, tanggal, kategori, gambar atau PDF, tautan verifikasi.
- Tautan: dikelompokkan menjadi akun, saluran, kontak. Tautan bertipe saluran punya daftar sorotan yang diisi manual.
- Pengaturan situs: profil, teks Home dan About, foto, file CV, keahlian, status "terbuka untuk kerja".
- Semua yang tampil di publik punya saklar terbit/sembunyi.

## Desain
Tata letak dan alur UX mengikuti pola situs pribadi dengan sidebar kiri (desktop) dan menu geser (mobile), tapi identitas visual harus berbeda dan tidak boleh terlihat seperti salinan situs lain.
- Sidebar: nama, satu baris peran, titik status hanya kalau "terbuka untuk kerja" aktif di admin, toggle bahasa dan tema, menu, tombol palet perintah. (Keputusan pemilik: Foto profil ditampilkan besar dan di tengah pada sidebar, dan label teks "terbuka untuk kerja" dari antarmuka publik dihapus meski kolom database tetap ada).
- Toggle tema dan bahasa berbentuk kapsul (`rounded-full`) dengan indikator bergeser.
- Warna (terang): latar #FFFFFF, permukaan #FFFFFF, teks #111827, teks redup #4B5563, garis #E5E7EB, aksen (oranye solid) #C8501E (hover #A63F14), ok #15803d.
- Warna (gelap): latar #09090B, permukaan #09090B, teks #FAFAFA, teks redup #A1A1AA, garis #27272A, aksen (oranye solid) #E8743F (hover #C85F2A), ok #4ade80.
- Efek tekan: `active:scale-[0.98]` dan `active:opacity-90` pada tombol, kartu, dan tautan diperbolehkan (gerak halus 150 sampai 200 ms, tanpa animasi dekoratif).
- Semua kombinasi teks dan latar harus lolos kontras WCAG AA. Cek, jangan asumsikan.
- Bukan kuning, bukan biru-navy, bukan ungu.
- Satu font: Plus Jakarta Sans (self-host). Font mono bawaan sistem untuk kode.
- Sudut membulat kecil (6 sampai 8 px), garis tipis, dilarang ada bayangan (shadow). Pembatas berupa garis solid tipis, bukan putus-putus.
- Ikon: Lucide sebagai SVG inline.
- Animasi: hanya fade dan geser halus 150 sampai 200 ms, wajib menghormati `prefers-reduced-motion`.
- Dilarang: partikel, efek glitch, teks gradien, glassmorphism, bayangan, sudut membulat besar, spinner loading layar penuh, animasi berlebihan, gambar stok.
- Ikuti tema sistem (terang/gelap) dan jangan ada kedipan tema saat halaman dimuat.

## Tulisan
- Kalimat orang pertama, biasa saja, tulis hal yang benar-benar terjadi.
- Jangan mengarang angka, klien, penghargaan, atau riwayat. Kalau data belum ada, tulis penanda `[ISI: ...]` yang gampang dicari, jangan isi teks tiruan.
- Hindari kata pemasaran seperti "inovatif", "cutting-edge", "seamless", "solusi terpadu", "transformasi digital".
- Tanpa emoji di antarmuka publik maupun admin.
- Teks antarmuka Indonesia yang natural, bukan terjemahan kaku.
- Setiap gambar wajib punya teks alternatif; admin harus memaksa pengisiannya.

## Aturan kerja (untuk agent)
1. Sebelum mulai, baca `docs/KONSEP.md` dan `docs/PROGRESS.md`.
2. Kerjakan hanya tahap yang diminta. Jangan mengerjakan tahap berikutnya.
3. Jangan mengganti stack, menambah layanan, atau menambah paket besar di luar yang tertulis tanpa bertanya dulu.
4. Kalau ada keputusan yang belum dicakup, tanya, jangan menebak.
5. Jangan mengarang isi konten (lihat bagian Tulisan).
6. Semua perubahan skema lewat migrasi. Jangan mengedit migrasi yang sudah pernah dijalankan; buat migrasi baru.
7. Tiap tahap ditutup dengan: jalankan `php artisan test` dan `vendor/bin/pint`, perbarui `docs/PROGRESS.md` (apa yang selesai, apa yang belum, keputusan penting), commit dengan pesan yang jelas, lalu berhenti dan laporkan cara saya mencobanya.
8. Jangan menjalankan perintah apa pun di server produksi atau VPS.
9. Sebelum mengimpor atau memakai kelas Filament, pastikan kelas itu ada di versi terpasang (cek `vendor/filament` atau `class_exists`). Jangan menulis API Filament dari ingatan versi lama. Setiap resource wajib punya tes yang merender halaman daftar, buat, dan ubah.
10. Dilarang menyembunyikan tes yang gagal: jangan menangkap kegagalan asersi (try/catch ExpectationFailedException), jangan memakai assertTrue(true) sebagai pengganti asersi, jangan memakai markTestSkipped untuk kegagalan yang bisa diperbaiki. Kalau sebuah tes gagal, perbaiki penyebabnya atau laporkan dengan jujur bahwa belum bisa diselesaikan.
11. Catatan di docs/PROGRESS.md hanya boleh ditambah. Dilarang menghapus, meringkas, atau menyembunyikan catatan tahap sebelumnya. Kalau file terlalu panjang, pindahkan bagian lama ke docs/arsip/ dengan tautan, jangan dibuang.

## Di luar cakupan (jangan dibuat kecuali diminta)
Formulir kontak, komentar, buku tamu, login publik, chat, dasbor pantauan bot, integrasi TikTok atau Instagram, blog.
