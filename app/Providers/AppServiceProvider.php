<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <--- Jangan lupa import ini
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tambahkan baris ini agar semua asset/URL otomatis pakai HTTPS
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}