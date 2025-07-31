<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Role;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk 3 pegawai
        $pegawais = [
            [
                'nip' => '199001012015011001',
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
            ],
            [
                'nip' => '199202022016022002',
                'nama_lengkap' => 'Citra Lestari',
                'email' => 'citra.lestari@example.com',
            ],
            [
                'nip' => '199405242024061001', // NIP yang sudah ada akan di-update
                'nama_lengkap' => 'Muhammad Rivani Ibrahim',
                'email' => 'alpha.cloud24@gmail.com',
            ],
        ];

        // Ambil role default "Pegawai"
        $defaultRole = Role::where('name', 'Pegawai')->first();

        foreach ($pegawais as $pegawaiData) {
            // updateOrCreate akan membuat data baru atau memperbarui jika NIP sudah ada
            $pegawai = Pegawai::updateOrCreate(
                ['nip' => $pegawaiData['nip']],
                [
                    'pangkat_terakhir_id' => 1, // Asumsi ID 1 ada
                    'jabatan_terakhir_id' => 1, // Asumsi ID 1 ada
                    'unit_kerja_terakhir_id' => 1, // Asumsi ID 1 ada
                    'jenis_pegawai' => 'Dosen',
                    'nama_lengkap' => $pegawaiData['nama_lengkap'],
                    'email' => $pegawaiData['email'],
                    'tempat_lahir' => 'Samarinda',
                    'tanggal_lahir' => '1990-01-01',
                    'jenis_kelamin' => 'Laki-Laki',
                    'nomor_handphone' => '081234567890',
                    'tmt_pangkat' => now(),
                    'sk_pangkat_terakhir' => 'path/to/dummy.pdf',
                    'tmt_jabatan' => now(),
                    'sk_jabatan_terakhir' => 'path/to/dummy.pdf',
                    'pendidikan_terakhir' => 'Sarjana (S1)',
                    'ijazah_terakhir' => 'path/to/dummy.pdf',
                    'transkrip_nilai_terakhir' => 'path/to/dummy.pdf',
                    'predikat_kinerja_tahun_pertama' => 'Baik',
                    'skp_tahun_pertama' => 'path/to/dummy.pdf',
                    'predikat_kinerja_tahun_kedua' => 'Baik',
                    'skp_tahun_kedua' => 'path/to/dummy.pdf',
                ]
            );

            // Tetapkan role "Pegawai" jika role tersebut ada
            if ($defaultRole) {
                $pegawai->roles()->syncWithoutDetaching([$defaultRole->id]);
            }
        }
    }
}
