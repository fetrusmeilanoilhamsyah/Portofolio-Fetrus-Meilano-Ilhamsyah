<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk mengatur locale berdasarkan prefix URL.
 *
 * - Indonesia (id): default, tanpa prefix — /about, /projects, dll.
 * - Inggris (en): dengan prefix /en — /en/about, /en/projects, dll.
 *
 * Pilihan bahasa juga disimpan di session ('locale') agar toggle bahasa
 * dapat mempertahankan halaman yang sedang dibuka.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1) === 'en' ? 'en' : 'id';

        App::setLocale($locale);

        return $next($request);
    }
}
