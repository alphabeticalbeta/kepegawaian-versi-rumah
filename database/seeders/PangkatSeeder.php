<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- Pastikan ini ada
use Carbon\Carbon; // <-- Pastikan ini ada

class PangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pangkats = [
            'Juru Muda (I/a)',
            'Juru Muda Tingkat I (I/b)',
            'Juru Tingkat I (I/d)',
            'Penata Muda (III/a)',
            'Penata Muda Tingkat I (III/b)',
            'Penata (III/c)',
            'Penata Tingkat I (III/d)',
            'Pembina (IV/a)',
            'Pembina Tingkat I (IV/b)',
            'Pembina Utama Muda (IV/c)',
            'Pembina Utama Madya (IV/d)',
            'Pembina Utama (IV/e)',
        ];

        // Siapkan data untuk dimasukkan secara massal
        $dataToInsert = [];
        $timestamp = Carbon::now(); // Gunakan satu timestamp yang sama untuk semua

        foreach ($pangkats as $pangkat) {
            $dataToInsert[] = [
                'pangkat' => $pangkat,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Masukkan semua data sekaligus dalam satu perintah
        DB::table('pangkats')->insert($dataToInsert);
    }
}
