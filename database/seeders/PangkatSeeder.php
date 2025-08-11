<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\Pangkat;

class PangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pangkats = [
            // ===================================
            // PANGKAT PNS GOLONGAN I (Level 1-4)
            // ===================================
            [
                'pangkat' => 'Juru Muda (I/a)',
                'hierarchy_level' => 1
            ],
            [
                'pangkat' => 'Juru Muda Tingkat I (I/b)',
                'hierarchy_level' => 2
            ],
            [
                'pangkat' => 'Juru (I/c)',
                'hierarchy_level' => 3
            ],
            [
                'pangkat' => 'Juru Tingkat I (I/d)',
                'hierarchy_level' => 4
            ],

            // ===================================
            // PANGKAT PNS GOLONGAN II (Level 5-8)
            // ===================================
            [
                'pangkat' => 'Pengatur Muda (II/a)',
                'hierarchy_level' => 5
            ],
            [
                'pangkat' => 'Pengatur Muda Tingkat I (II/b)',
                'hierarchy_level' => 6
            ],
            [
                'pangkat' => 'Pengatur (II/c)',
                'hierarchy_level' => 7
            ],
            [
                'pangkat' => 'Pengatur Tingkat I (II/d)',
                'hierarchy_level' => 8
            ],

            // ===================================
            // PANGKAT PNS GOLONGAN III (Level 9-12)
            // ===================================
            [
                'pangkat' => 'Penata Muda (III/a)',
                'hierarchy_level' => 9
            ],
            [
                'pangkat' => 'Penata Muda Tingkat I (III/b)',
                'hierarchy_level' => 10
            ],
            [
                'pangkat' => 'Penata (III/c)',
                'hierarchy_level' => 11
            ],
            [
                'pangkat' => 'Penata Tingkat I (III/d)',
                'hierarchy_level' => 12
            ],

            // ===================================
            // PANGKAT PNS GOLONGAN IV (Level 13-17)
            // ===================================
            [
                'pangkat' => 'Pembina (IV/a)',
                'hierarchy_level' => 13
            ],
            [
                'pangkat' => 'Pembina Tingkat I (IV/b)',
                'hierarchy_level' => 14
            ],
            [
                'pangkat' => 'Pembina Utama Muda (IV/c)',
                'hierarchy_level' => 15
            ],
            [
                'pangkat' => 'Pembina Utama Madya (IV/d)',
                'hierarchy_level' => 16
            ],
            [
                'pangkat' => 'Pembina Utama (IV/e)',
                'hierarchy_level' => 17
            ],

            // ===================================
            // PANGKAT NON-PNS (Tanpa Hierarchy)
            // ===================================
            [
                'pangkat' => 'Non-PNS',
                'hierarchy_level' => null
            ],
            [
                'pangkat' => 'Kontrak',
                'hierarchy_level' => null
            ],
            [
                'pangkat' => 'Honorer',
                'hierarchy_level' => null
            ],
        ];

        foreach ($pangkats as $pangkat) {
            Pangkat::updateOrCreate(
                ['pangkat' => $pangkat['pangkat']],
                $pangkat
            );
        }
    }
}
