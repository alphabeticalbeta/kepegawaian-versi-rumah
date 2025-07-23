<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
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
        // Mengatur route model binding secara eksplisit jika diperlukan
        Route::pattern('id', '[0-9]+');

        // Mengatur namespace default untuk route
        Route::middleware('web')
            ->group(function () {
                require base_path('routes/frontend.php');
                require base_path('routes/backend.php');
            });
    }
}
