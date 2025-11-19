<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

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
        // Compartir la variable $settings con TODAS las vistas
        // Usamos el try-catch o el if para evitar errores si la tabla aun no existe
        if (Schema::hasTable('settings')) {
            View::share('settings', Setting::first() ?? new Setting());
        }
    }
}
