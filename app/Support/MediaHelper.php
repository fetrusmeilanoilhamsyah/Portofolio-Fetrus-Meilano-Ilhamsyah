<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Konversi path di disk 'public' menjadi URL yang bisa diakses browser.
 *
 * Gunakan helper ini untuk SEMUA gambar, video, dan PDF yang disimpan
 * dari panel admin — jangan menulis URL file dengan cara lain.
 *
 * Contoh:
 *   media_url('covers/img_abc123.webp')
 *   // => '/storage/covers/img_abc123.webp'
 *
 *   media_url(null)  // => null
 *   media_url('')    // => null
 */
class MediaHelper
{
    /**
     * Konversi path relatif di disk 'public' menjadi URL penuh.
     *
     * @param  string|null  $path  Path relatif terhadap root disk 'public', mis. 'covers/img.webp'
     * @return string|null URL lengkap, atau null jika path kosong/null
     */
    public static function url(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
