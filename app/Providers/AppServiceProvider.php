<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <--- Import ini
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Otomatis paksa semua URL/Asset jadi HTTPS saat di mode production (Railway)
        URL::forceHttps($this->app->isProduction());
    }
}