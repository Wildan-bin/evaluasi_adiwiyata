<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use App\Services\FileEvidenceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FileEvidenceService::class, function ($app) {
            return new FileEvidenceService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
