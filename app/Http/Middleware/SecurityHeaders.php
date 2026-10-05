<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Jangan set header untuk file statis jika melewati PHP, tapi biasanya diset di level web server
        if (method_exists($response, 'header')) {
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->header('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), browsing-topics=()');

            if (app()->environment('production')) {
                $response->header('Strict-Transport-Security', 'max-age=31536000');
            }

            // CSP Ketat (Enforced)
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob: https:; font-src 'self' data:; connect-src 'self'; media-src 'self' blob: https:; object-src 'none'; frame-src 'self' https:; report-uri /api/csp-report";
            $response->header('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
