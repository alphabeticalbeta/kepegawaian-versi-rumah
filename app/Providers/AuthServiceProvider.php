<?php

namespace App\Providers;

use App\Models\Pegawai;
use App\Policies\PegawaiPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
    {
    protected $policies = [
            Pegawai::class => PegawaiPolicy::class, // <-- Daftarkan Policy di sini
        ];
        public function boot(): void
        {
            // Kosongkan method boot dari Gate yang lama
        }
    }
