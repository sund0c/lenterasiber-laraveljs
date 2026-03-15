<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $nonce = base64_encode(random_bytes(16));
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        $ct = $response->headers->get('Content-Type', '');
        if (str_contains($ct, 'text/html') || $ct === '') {
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

            // Bedakan CSP untuk frontend publik vs admin
            $isAdmin = str_starts_with($request->path(), 'admin') ||
                str_starts_with($request->path(), 'portal-internal');

            if ($isAdmin) {
                // Admin: strict — hanya self + nonce
                $csp =
                    "default-src 'self'; " .
                    "script-src 'self' 'nonce-{$nonce}'; " .
                    "style-src 'self' 'unsafe-inline'; " .
                    "img-src 'self' data: blob:; " .
                    "font-src 'self'; " .
                    "connect-src 'self'; " .
                    "frame-ancestors 'none';";
            } else {
                // Frontend publik: izinkan asset lokal (font, img dari storage)
                $csp =
                    "default-src 'self'; " .
                    "script-src 'self' 'unsafe-inline'; " .
                    "style-src 'self' 'unsafe-inline'; " .
                    "img-src 'self' data: blob: storage:; " .
                    "font-src 'self' data:; " .
                    "connect-src 'self'; " .
                    "frame-ancestors 'none';";
            }

            $response->headers->set('Content-Security-Policy', $csp);
        }

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
