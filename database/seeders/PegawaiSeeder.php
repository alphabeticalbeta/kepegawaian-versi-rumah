<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\BackendUnivUsulan\Pegawai;
// --- TAMBAHKAN MODEL UNTUK RELASI ---
// Sesuaikan path model ini jika berbeda di proyek Anda
use App\Models\BackendUnivUsulan\Pangkat;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\UnitKerja;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Ambil data master yang relevan terlebih dahulu.
        //    Ini akan mengambil baris pertama dari setiap tabel.
        $pangkat = Pangkat::first();
        $jabatan = Jabatan::first();
        $unitKerja = UnitKerja::first();

        // 2. Lakukan pengecekan untuk memastikan data master ada.
        if (!$pangkat || !$jabatan || !$unitKerja) {
            $this->command->error('❌ Seeder Pangkat/Jabatan/UnitKerja belum dijalankan atau tabel kosong.');
            $this->command->error('❌ PegawaiSeeder dibatalkan.');
            return; // Hentikan seeder jika data master tidak ditemukan.
        }

        $pegawais = [
            ['nip' => '199001012015011001', 'nama_lengkap' => 'Budi Santoso', 'email' => 'budi.santoso@example.com', 'is_admin' => false],
            ['nip' => '199202022016022002', 'nama_lengkap' => 'Citra Lestari', 'email' => 'citra.lestari@example.com', 'is_admin' => false],
            ['nip' => '199405242024061001', 'nama_lengkap' => 'Muhammad Rivani Ibrahim', 'email' => 'admin.fakultas@kepegawaian.com', 'is_admin' => true],
        ];

        foreach ($pegawais as $pegawaiData) {
            $pegawai = Pegawai::updateOrCreate(
                ['nip' => $pegawaiData['nip']],
                [
                    // 3. Gunakan ID dari data yang sudah diambil secara dinamis
                    'pangkat_terakhir_id' => $pangkat->id,
                    'jabatan_terakhir_id' => $jabatan->id,
                    'unit_kerja_terakhir_id' => $unitKerja->id,
                    'jenis_pegawai' => 'Dosen',
                    'nama_lengkap' => $pegawaiData['nama_lengkap'],
                    'email' => $pegawaiData['email'],
                    'tempat_lahir' => 'Samarinda',
                    'tanggal_lahir' => '1990-01-01',
                    'jenis_kelamin' => 'Laki-Laki',
                    'nomor_handphone' => '081234567890',
                    'tmt_pangkat' => '2025-08-12 11:15:45',
                    'sk_pangkat_terakhir' => 'path/to/dummy.pdf',
                    'tmt_jabatan' => '2025-08-12 11:15:45',
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

            // Menetapkan role, bagian ini sudah benar
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
