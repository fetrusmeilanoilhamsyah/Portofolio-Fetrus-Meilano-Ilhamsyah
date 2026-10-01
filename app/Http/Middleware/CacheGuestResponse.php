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
        $tab = $request->query('tab');
        $queryString = $tab ? "?tab={$tab}" : '';
        $cacheKey = 'guest_response_'.$version.'_'.sha1($request->path().$queryString);

        if ($cached = Cache::get($cacheKey)) {
            return response($cached['content'], $cached['status'])
                ->header('Content-Type', $cached['content_type'])
                ->header('X-Cache', 'HIT');
        }

        $response = $next($request);

        if ($response->isSuccessful() && ! $response->headers->has('Set-Cookie')) {
            Cache::put($cacheKey, [
                'status' => $response->getStatusCode(),
                'content' => $response->getContent(),
                'content_type' => $response->headers->get('Content-Type') ?? 'text/html',
            ], now()->addDays(7));
            $response->headers->set('X-Cache', 'MISS');
        }

        return $response;
    }
}
