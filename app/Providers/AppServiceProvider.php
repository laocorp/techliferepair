<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- Importante
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

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
        // 🔒 FUERZA BRUTA HTTPS: Vital para Cloudflare Tunnel
        if(config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Compartir configuración global (Tu lógica existente)
        if (Schema::hasTable('settings')) {
            View::share('settings', Setting::first() ?? new Setting());
        }
    }
}
