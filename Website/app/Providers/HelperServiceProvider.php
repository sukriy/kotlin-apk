<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        require_once app_path().'/MyHelper.php';
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
    }
}
