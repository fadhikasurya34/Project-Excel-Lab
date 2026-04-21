<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function boot(): void
    {
        // Paksa HTTPS jika di lingkungan Vercel/Produksi
        if (env('VERCEL_URL') || config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Kode public path yang tadi (biar CSS tetap aman)
        if (isset($_SERVER['VERCEL_URL'])) {
            $this->app->bind('path.public', function () {
                return base_path('public');
            });
        }
    }
}
