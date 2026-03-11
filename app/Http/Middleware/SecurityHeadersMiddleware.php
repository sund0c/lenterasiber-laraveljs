<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Adds security response headers to every response.
 */
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only on HTML responses
        $ct = $response->headers->get('Content-Type', '');
        if (str_contains($ct, 'text/html') || $ct === '') {
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            // CSP: no inline scripts, no CDN, self-only
            $response->headers->set('Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self'; " .
                "style-src 'self' 'unsafe-inline'; " .
                "img-src 'self' data:; " .   // data: for QR PNG
                "font-src 'self'; " .
                "connect-src 'self'; " .
                "frame-ancestors 'none';"
            );
        }

        // Remove fingerprinting headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
