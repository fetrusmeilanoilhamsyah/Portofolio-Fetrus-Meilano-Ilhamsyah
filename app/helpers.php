<?php

use App\Support\MediaHelper;
use App\Support\RouteHelper;
use App\Support\TextHelper;

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

if (! function_exists('localized_route')) {
    function localized_route(string $name, array $params = []): string
    {
        return RouteHelper::localized($name, $params);
    }
}

if (! function_exists('public_text')) {
    function public_text(?string $value): ?string
    {
        return TextHelper::publicText($value);
    }
}
