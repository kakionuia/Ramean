<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <--- 1. Jangan lupa import ini di atas
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 2. Tambahkan baris ini
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}