<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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

        // ========== TAMBAHAN: PERMISSIONS UNTUK DOKUMEN ==========

        // Daftar permissions untuk akses dokumen
        $permissions = [
            'view_all_pegawai_documents',        // Admin Univ Usulan - akses semua dokumen
            'view_fakultas_pegawai_documents',   // Admin Fakultas - akses dokumen pegawai di fakultasnya
            'view_own_documents',                // Pegawai - akses dokumen sendiri
            'view_assessment_documents',         // Penilai - akses dokumen yang sedang dinilai
        ];

        // Buat permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'pegawai'
            ]);
        }

        // Assign permissions ke roles
        $adminUnivUsulan = Role::where('name', 'Admin Universitas Usulan')->first();
        if ($adminUnivUsulan) {
            $adminUnivUsulan->givePermissionTo('view_all_pegawai_documents');
        }

        $adminFakultas = Role::where('name', 'Admin Fakultas')->first();
        if ($adminFakultas) {
            $adminFakultas->givePermissionTo('view_fakultas_pegawai_documents');
        }

        $pegawaiUnmul = Role::where('name', 'Pegawai Unmul')->first();
        if ($pegawaiUnmul) {
            $pegawaiUnmul->givePermissionTo('view_own_documents');
        }

        $penilaiUniversitas = Role::where('name', 'Penilai Universitas')->first();
        if ($penilaiUniversitas) {
            $penilaiUniversitas->givePermissionTo('view_assessment_documents');
        }
    }
}
