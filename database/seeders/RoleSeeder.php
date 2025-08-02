<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\Role;
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin Universitas',
            'Admin Universitas Usulan',
            'Admin Fakultas',
            'Penilai',
            'Pegawai',
        ];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
