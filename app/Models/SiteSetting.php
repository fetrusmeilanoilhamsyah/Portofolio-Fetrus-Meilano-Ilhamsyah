<?php

namespace App\Models;

use App\Models\Concerns\DispatchesContentChanged;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'name',
    'role',
    'intro_home',
    'about_body',
    'cv_summary',
    'cv_show_photo',
    'photo',
    'cv_file',
    'location',
    'open_to_work',
    'open_to_work_note',
    'skills',
    'og_image',
])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use DispatchesContentChanged, HasFactory, HasTranslations;

    public array $translatable = ['role', 'intro_home', 'about_body', 'open_to_work_note', 'cv_summary'];

    protected $attributes = [
        'name' => '',
        'open_to_work' => false,
        'cv_show_photo' => false,
    ];

    protected function casts(): array
    {
        return [
            'open_to_work' => 'boolean',
            'cv_show_photo' => 'boolean',
            'skills' => 'array',
        ];
    }

    /**
     * Ambil baris pengaturan situs, atau buat jika belum ada.
     * Hanya satu baris yang diizinkan.
     */
    public static function current(): static
    {
        return static::firstOrCreate([]);
    }

    /**
     * Cegah pembuatan baris kedua: selalu kembalikan baris yang sudah ada.
     */
    protected static function booted(): void
    {
        static::creating(function (SiteSetting $setting) {
            if (static::exists()) {
                return false;
            }
        });
    }

    /**
     * Cek apakah sudah ada baris di tabel.
     */
    public static function exists(): bool
    {
        return static::query()->exists();
    }
}
