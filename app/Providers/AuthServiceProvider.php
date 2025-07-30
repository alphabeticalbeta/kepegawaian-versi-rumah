<?php

namespace App\Providers;

use App\Models\User; // <-- PASTIKAN USE STATEMENT INI ADA
use Illuminate\Support\Facades\Gate; // <-- PASTIKAN USE STATEMENT INI ADA
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies(); // Baris ini sudah ada secara default di sini

        // Definisikan Gate: hanya user dengan role 'Admin' yang bisa lolos
        Gate::define('manage-roles', function (User $user) {
            // Asumsi: Anda memiliki relasi 'roles' di model User
            return $user->roles()->where('name', 'Admin')->exists();
        });
    }
}
