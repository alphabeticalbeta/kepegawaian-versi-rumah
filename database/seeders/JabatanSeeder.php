<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\Jabatan;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            // ===================================
            // DOSEN FUNGSIONAL (Hierarki Level 1-5)
            // ===================================
            [
                'jabatan' => 'Tenaga Pengajar',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 1
            ],
            [
                'jabatan' => 'Asisten Ahli',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 2
            ],
            [
                'jabatan' => 'Lektor',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 3
            ],
            [
                'jabatan' => 'Lektor Kepala',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 4
            ],
            [
                'jabatan' => 'Guru Besar',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'hierarchy_level' => 5
            ],

            // ===================================
            // DOSEN FUNGSI TAMBAHAN (Non-Hierarki)
            // ===================================
            [
                'jabatan' => 'Ketua Jurusan',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsi Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Wakil Dekan',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsi Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Dekan',
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsi Tambahan',
                'hierarchy_level' => null
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN FUNGSIONAL TERTENTU (Sample Hierarki Level 1-2)
            // ===================================
            [
                'jabatan' => 'Arsiparis Ahli Pertama',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 1
            ],
            [
                'jabatan' => 'Arsiparis Ahli Muda',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'hierarchy_level' => 2
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN FUNGSIONAL UMUM (Non-Hierarki)
            // ===================================
            [
                'jabatan' => 'Staf Administrasi',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Koordinator Administrasi',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'hierarchy_level' => null
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN STRUKTURAL (Non-Hierarki, TIDAK ADA USULAN)
            // ===================================
            [
                'jabatan' => 'Kepala Sub Bagian',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Kepala Bagian',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Kepala Biro',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'hierarchy_level' => null
            ],

            // ===================================
            // TENAGA KEPENDIDIKAN TUGAS TAMBAHAN (Non-Hierarki)
            // ===================================
            [
                'jabatan' => 'Koordinator Program',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan',
                'hierarchy_level' => null
            ],
            [
                'jabatan' => 'Sekretaris Fakultas',
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Tugas Tambahan',
                'hierarchy_level' => null
            ],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrCreate(
                [
                    'jabatan' => $jabatan['jabatan'],
                    'jenis_pegawai' => $jabatan['jenis_pegawai'],
                    'jenis_jabatan' => $jabatan['jenis_jabatan']
                ],
                $jabatan
            );
        }
    }
}
