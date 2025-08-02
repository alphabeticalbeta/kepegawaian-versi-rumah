<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\Pangkat;

class PangkatSeeder extends Seeder
{
    public function run(): void
    {
        $pangkats = [
            ['pangkat' => 'Juru Muda (I/a)'],
            ['pangkat' => 'Juru Muda Tingkat I (I/b)'],
            ['pangkat' => 'Juru (I/c)'],
            ['pangkat' => 'Juru Tingkat I (I/d)'],
            ['pangkat' => 'Pengatur Muda (II/a)'],
            ['pangkat' => 'Pengatur Muda Tingkat I (II/b)'],
            ['pangkat' => 'Pengatur (II/c)'],
            ['pangkat' => 'Pengatur Tingkat I (II/d)'],
            ['pangkat' => 'Penata Muda (III/a)'],
            ['pangkat' => 'Penata Muda Tingkat I (III/b)'],
            ['pangkat' => 'Penata (III/c)'],
            ['pangkat' => 'Penata Tingkat I (III/d)'],
            ['pangkat' => 'Pembina (IV/a)'],
            ['pangkat' => 'Pembina Tingkat I (IV/b)'],
            ['pangkat' => 'Pembina Utama Muda (IV/c)'],
            ['pangkat' => 'Pembina Utama Madya (IV/d)'],
            ['pangkat' => 'Pembina Utama (IV/e)'],
        ];

        // Looping untuk membuat data menggunakan Eloquent
        foreach ($pangkats as $pangkat) {
            Pangkat::create($pangkat);
        }
    }
}
