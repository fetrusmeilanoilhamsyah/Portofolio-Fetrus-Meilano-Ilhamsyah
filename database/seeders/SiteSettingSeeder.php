<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Seeder idempoten: tidak membuat baris baru jika sudah ada,
     * dan tidak menimpa isi yang sudah diubah lewat admin.
     */
    public function run(): void
    {
        // Gunakan firstOrCreate tanpa mengisi data apa pun agar tidak menimpa
        // isi yang sudah ada. Jika belum ada, buat dengan nilai placeholder.
        if (SiteSetting::query()->exists()) {
            // Sudah ada—tidak lakukan apa-apa
            return;
        }

        SiteSetting::create([
            'name' => '[ISI: nama lengkap]',
            'role' => [
                'id' => '[ISI: peran/jabatan dalam bahasa Indonesia]',
                'en' => '[ISI: peran/jabatan dalam bahasa Inggris]',
            ],
            'intro_home' => [
                'id' => '[ISI: kalimat pembuka halaman Home dalam bahasa Indonesia]',
                'en' => '[ISI: opening sentence for Home page in English]',
            ],
            'about_body' => [
                'id' => '[ISI: isi halaman About dalam bahasa Indonesia (markdown)]',
                'en' => '[ISI: About page content in English (markdown)]',
            ],
            'photo' => null,
            'cv_file' => null,
            'location' => '[ISI: kota/lokasi]',
            'open_to_work' => false,
            'open_to_work_note' => null,
            'skills' => null,
            'og_image' => null,
        ]);
    }
}
