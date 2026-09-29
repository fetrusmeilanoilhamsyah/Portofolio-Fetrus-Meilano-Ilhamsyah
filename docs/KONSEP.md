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
Laravel 13.33.0, Filament v5.9.0 (panel admin), Blade + Tailwind CSS v4 + Alpine.js v3, SQLite, spatie/laravel-translatable, league/commonmark 2.10.3, Vite. Node 20.18.1 hanya dipakai saat build di laptop. Tidak ada Redis, antrean, atau layanan pihak ketiga selain yang tertulis di sini. Tidak ada React, Next.js, Supabase, atau Firebase.

## Model konten (ringkas)
- Proyek: satu tabel untuk semua jenis (bot, web, sistem, magang, kegiatan). Tautan opsional: Telegram, situs, demo, kode. Tombol di halaman publik hanya muncul kalau tautannya diisi. Punya status draft atau terbit, unggulan, urutan, dan media (gambar, video pendek, atau URL).
- Pengalaman: kerja, magang, organisasi, pendidikan.
- Sertifikat: nama, penerbit, tanggal, kategori, gambar atau PDF, tautan verifikasi.
- Tautan: dikelompokkan menjadi akun, saluran, kontak. Tautan bertipe saluran punya daftar sorotan yang diisi manual.
- Pengaturan situs: profil, teks Home dan About, foto, file CV, keahlian, status "terbuka untuk kerja".
- Semua yang tampil di publik punya saklar terbit/sembunyi.

## Desain
Tata letak dan alur UX mengikuti pola situs pribadi dengan sidebar kiri (desktop) dan menu geser (mobile), tapi identitas visual harus berbeda dan tidak boleh terlihat seperti salinan situs lain.
- Sidebar: nama, satu baris peran, titik status hanya kalau "terbuka untuk kerja" aktif di admin, toggle bahasa dan tema, menu, tombol palet perintah. Tanpa foto bulat besar, tanpa lencana centang biru.
- Warna (terang): latar #F6F3EE, permukaan #FFFFFF, teks #1C1917, teks redup #6B645C, garis #E3DDD3, aksen #C8501E (hover #A63F14).
- Warna (gelap): latar #14110F, permukaan #1D1A17, teks #EDE8E1, teks redup #A39B90, garis #2C2723, aksen #E8743F.
- Semua kombinasi teks dan latar harus lolos kontras WCAG AA. Cek, jangan asumsikan.
- Bukan kuning, bukan biru-navy, bukan ungu.
- Satu font: Plus Jakarta Sans (self-host). Font mono bawaan sistem untuk kode.
- Sudut membulat kecil (6 sampai 8 px), garis tipis, hampir tanpa bayangan. Pembatas berupa garis solid tipis, bukan putus-putus.
- Ikon: Lucide sebagai SVG inline.
- Animasi: hanya fade dan geser halus 150 sampai 200 ms, wajib menghormati `prefers-reduced-motion`.
- Dilarang: partikel, efek glitch, teks gradien, glassmorphism, spinner loading layar penuh, animasi berlebihan, gambar stok.
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

## Di luar cakupan (jangan dibuat kecuali diminta)
Formulir kontak, komentar, buku tamu, login publik, chat, dasbor pantauan bot, integrasi TikTok atau Instagram, blog.
