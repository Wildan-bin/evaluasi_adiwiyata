<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCSRFTokenInResponse
{
    /**
     * Middleware untuk ensure CSRF token selalu fresh di setiap response
     * Ini penting untuk SPA agar auto-recovery dari 419 error
     *
     * Cara kerja:
     * - Interceptor JS akan retry request saat 419 error
     * - Token diambil dari meta tag yang selalu updated di sini
     * - Jadi SPA tidak perlu manual refresh halaman
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Untuk HTML responses, inject csrf token di meta tag
        if ($response instanceof Response && str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            $token = csrf_token();

            // Modify response content untuk update/ensure meta tag csrf-token
            $content = $response->getContent();
            if (strpos($content, '<meta name="csrf-token"') === false) {
                // Jika belum ada, tambahkan
                $metaTag = '<meta name="csrf-token" content="' . $token . '">';
                $content = str_replace('</head>', $metaTag . '</head>', $content);
            } else {
                // Jika sudah ada, update nilai
                $content = preg_replace(
                    '/<meta name="csrf-token" content="[^"]*"/',
                    '<meta name="csrf-token" content="' . $token . '"',
                    $content
                );
            }

            $response->setContent($content);
        }

        return $response;
    }
}
