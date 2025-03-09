<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // تجاوز توجيه Vite
        Blade::directive('vite', function ($expression) {
            return "<?php /* Vite bypassed */ ?>";
        });
    }
}