<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StripGuestCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Jika user tidak login dan bukan area admin, kita hapus cookie sesi
        if (! auth()->check() && ! $request->is('admin*') && ! $request->is('livewire*') && ! $request->is('filament*')) {
            $response->headers->remove('Set-Cookie');
            
            // Hapus file sesi fisik yang terlanjur dibuat oleh Laravel
            if ($request->hasSession()) {
                $session = $request->session();
                $session->getHandler()->destroy($session->getId());
            }
        }

        return $response;
    }
}
