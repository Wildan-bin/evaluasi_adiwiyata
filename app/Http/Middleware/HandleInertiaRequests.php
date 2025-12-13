<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            // CSRF token di-share ke Inertia props supaya bisa diakses di JS
            // Interceptor di bootstrap.js akan ambil token dari sini untuk setiap request
            'csrf_token' => csrf_token(),
        ];
    }
}
