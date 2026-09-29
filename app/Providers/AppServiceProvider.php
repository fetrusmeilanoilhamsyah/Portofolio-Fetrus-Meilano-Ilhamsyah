<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Konfigurasi fallback translatable:
        // - fallbackLocale 'id': jika locale diminta tidak ada terjemahannya, gunakan 'id'
        // - allowEmptyStringForTranslation false (default): string kosong dianggap tidak ada,
        //   sehingga form admin yang mengirim string kosong untuk 'en' otomatis fall back ke 'id'
        Translatable::fallback(fallbackLocale: 'id');
    }
}
