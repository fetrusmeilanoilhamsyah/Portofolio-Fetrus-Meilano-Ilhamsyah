<?php

use App\Support\MediaHelper;

if (! function_exists('media_url')) {
    /**
     * Konversi path di disk 'public' menjadi URL yang bisa diakses browser.
     *
     * Kembalikan null untuk path kosong atau null.
     * Gunakan helper ini untuk semua gambar, video, dan PDF dari panel admin.
     */
    function media_url(?string $path): ?string
    {
        return MediaHelper::url($path);
    }
}
