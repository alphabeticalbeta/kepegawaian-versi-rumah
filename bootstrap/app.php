<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // [PERBAIKAN] Daftarkan rute backend Anda di sini,
        // menggunakan 'then' untuk memastikannya masuk dalam grup 'web'
        then: function () {
            Route::middleware('web') // -> Ini kuncinya
                ->group(base_path('routes/backend.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ======================================================================
        //      DAFTARKAN ALIAS UNTUK MIDDLEWARE 'ROLE' DI SINI
        // ======================================================================
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            // Tambahkan alias lain di sini jika perlu, contoh:
            // 'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
