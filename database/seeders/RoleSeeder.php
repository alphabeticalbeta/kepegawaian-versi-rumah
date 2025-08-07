<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar peran yang akan dibuat di dalam sistem
        $roles = [
            'Admin Universitas Usulan',
            'Admin Universitas',
            'Admin Fakultas',
            'Penilai Universitas',
            'Pegawai Unmul' // Menggunakan nama peran yang lebih spesifik
        ];

        // Lakukan perulangan untuk setiap nama peran
        foreach ($roles as $roleName) {
            // Buat peran HANYA JIKA belum ada.
            // [INI YANG DIPERBAIKI] guard_name harus 'pegawai' agar sesuai dengan config/auth.php
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'pegawai']
            );
        }
    }
}
