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
