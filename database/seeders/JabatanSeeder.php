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
            // Jabatan Dosen
            ['jabatan' => 'Asisten Ahli', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional'],
            ['jabatan' => 'Lektor', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional'],
            ['jabatan' => 'Lektor Kepala', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional'],
            ['jabatan' => 'Profesor', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsional'],
            ['jabatan' => 'Dekan', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsi Tambahan'],
            ['jabatan' => 'Wakil Dekan', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsi Tambahan'],
            ['jabatan' => 'Ketua Jurusan', 'jenis_pegawai' => 'Dosen', 'jenis_jabatan' => 'Dosen Fungsi Tambahan'],

            // Jabatan Tenaga Kependidikan
            ['jabatan' => 'Kepala Biro', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Struktural'],
            ['jabatan' => 'Kepala Bagian', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Struktural'],
            ['jabatan' => 'Analis Kepegawaian Ahli Muda', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu'],
            ['jabatan' => 'Arsiparis Ahli Pertama', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu'],
            ['jabatan' => 'Pengelola Pengadaan Barang/Jasa', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu'],
            ['jabatan' => 'Staf Administrasi', 'jenis_pegawai' => 'Tenaga Kependidikan', 'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate($jabatan);
        }
    }
}
