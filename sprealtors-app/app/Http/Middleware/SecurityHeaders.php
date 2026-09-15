<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Clickjacking / MIME sniffing / referrer leakage.
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), interest-cohort=()');

        // Content Security Policy. Google Fonts + Maps embeds are the only
        // third parties; 'unsafe-inline' is needed for the inline JSON-LD and
        // the small inline handlers in the admin delete confirmations.
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "frame-src https://www.google.com https://maps.google.com",
            "connect-src 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
        ]));

        // HSTS only makes sense once the site is actually served over HTTPS.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Don't advertise the exact stack version. X-Powered-By is emitted by
        // PHP itself, so it has to be dropped at the SAPI level rather than on
        // the response object. The Server header belongs to Apache — see the
        // mod_headers rules in public/.htaccess.
        if (! headers_sent()) {
            header_remove('X-Powered-By');
        }

        return $response;
    }
}
