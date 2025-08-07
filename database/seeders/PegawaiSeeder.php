<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\Pegawai;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawais = [
            ['nip' => '199001012015011001', 'nama_lengkap' => 'Budi Santoso', 'email' => 'budi.santoso@example.com', 'is_admin' => false],
            ['nip' => '199202022016022002', 'nama_lengkap' => 'Citra Lestari', 'email' => 'citra.lestari@example.com', 'is_admin' => false],
            ['nip' => '199405242024061001', 'nama_lengkap' => 'Muhammad Rivani Ibrahim', 'email' => 'admin.fakultas@kepegawaian.com', 'is_admin' => true],
        ];

        foreach ($pegawais as $pegawaiData) {
            $pegawai = Pegawai::updateOrCreate(
                ['nip' => $pegawaiData['nip']],
                [
                    'pangkat_terakhir_id' => 1,
                    'jabatan_terakhir_id' => 1,
                    'unit_kerja_terakhir_id' => 1,
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
                    'status_kepegawaian' => 'PNS',
                    'password' => Hash::make('password')
                ]
            );

            // FIX: Ganti 'Pegawai' dengan 'Pegawai Unmul' sesuai RoleSeeder
            $pegawai->assignRole('Pegawai Unmul');

            if ($pegawaiData['is_admin']) {
                $pegawai->assignRole('Admin Fakultas');
                $pegawai->assignRole('Admin Universitas Usulan');
            }
        }

        $this->command->info('✅ Pegawai seeder berhasil!');
        $this->command->info('Login: NIP 199405242024061001, Password: password');
    }
}
