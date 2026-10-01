<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheGuestResponse
{
    public function handle(Request $request, Closure $next)
    {
        if (
            ! $request->isMethod('GET') && ! $request->isMethod('HEAD') ||
            auth()->check() ||
            $request->is('admin*') ||
            $request->is('livewire*') ||
            $request->is('filament*') ||
            config('app.debug') // Jangan cache saat debug true
        ) {
            return $next($request);
        }

        $version = Cache::get('guest_cache_version', 1);
        $cacheKey = 'guest_response_'.$version.'_'.sha1($request->fullUrl());

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            if (method_exists($response, 'withCookie')) {
                foreach ($response->headers->getCookies() as $cookie) {
                    $response->headers->removeCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
                }
            }
            Cache::put($cacheKey, $response, now()->addDays(7));
        }

        return $response;
    }
}
