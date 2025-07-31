<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            PangkatSeeder::class,
            JabatanSeeder::class,
            UnitKerjaSeeder::class, // Pastikan unit kerja juga di-seed
            RoleSeeder::class,

            // Baru setelah itu panggil PegawaiSeeder
            PegawaiSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
