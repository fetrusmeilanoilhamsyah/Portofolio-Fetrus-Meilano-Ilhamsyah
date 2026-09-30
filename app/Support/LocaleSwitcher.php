<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class LocaleSwitcher
{
    /**
     * Dapatkan URL untuk bahasa alternatif dari halaman saat ini.
     * Meneruskan parameter rute dan query string.
     */
    public static function switchUrl(Request $request): string
    {
        $currentRoute = Route::current();

        if (! $currentRoute) {
            // Jika tidak ada rute aktif, fallback ke / atau /en
            $locale = app()->getLocale();
            $path = ltrim($request->getPathInfo(), '/');

            if ($locale === 'id') {
                return url('en/'.$path.($request->getQueryString() ? '?'.$request->getQueryString() : ''));
            } else {
                $path = preg_replace('#^en/?#', '', $path);

                return url($path.($request->getQueryString() ? '?'.$request->getQueryString() : ''));
            }
        }

        $routeName = $currentRoute->getName();
        if (! $routeName) {
            return url('/');
        }

        $parameters = $currentRoute->parameters();
        $query = $request->query();

        // Gabungkan parameter rute dan query string
        $allParams = array_merge($parameters, $query);

        if (str_starts_with($routeName, 'en.')) {
            // Sedang di EN, ingin ke ID
            $newRouteName = substr($routeName, 3);
        } else {
            // Sedang di ID, ingin ke EN
            $newRouteName = 'en.'.$routeName;
        }

        // Cek apakah rute baru ada
        if (Route::has($newRouteName)) {
            return route($newRouteName, $allParams);
        }

        // Fallback jika tidak ketemu
        return url(app()->getLocale() === 'en' ? '/' : '/en');
    }
}
