<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function boot(): void
    {
        // Jika berjalan di Vercel
        if (env('VERCEL_URL')) {
            $this->app->bind('path.public', function () {
                return base_path('public');
            });
        }
    }
}
