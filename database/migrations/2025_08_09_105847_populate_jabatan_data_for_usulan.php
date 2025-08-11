<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate jabatan data untuk Dosen Fungsional
        $jabatanDosen = [
            [
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'jabatan' => 'Tenaga Pengajar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'jabatan' => 'Asisten Ahli',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'jabatan' => 'Lektor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'jabatan' => 'Lektor Kepala',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Dosen',
                'jenis_jabatan' => 'Dosen Fungsional',
                'jabatan' => 'Guru Besar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Populate jabatan data untuk Tenaga Kependidikan
        $jabatanTendik = [
            [
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Umum',
                'jabatan' => 'Pelaksana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'jabatan' => 'Pustakawan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Fungsional Tertentu',
                'jabatan' => 'Pranata Laboratorium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'jabatan' => 'Kepala Bagian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pegawai' => 'Tenaga Kependidikan',
                'jenis_jabatan' => 'Tenaga Kependidikan Struktural',
                'jabatan' => 'Kepala Sub Bagian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert semua data
        DB::table('jabatans')->insert(array_merge($jabatanDosen, $jabatanTendik));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Perintah asli Anda untuk menghapus data
        DB::table('jabatans')->whereIn('jenis_jabatan', [
            'Dosen Fungsional',
            'Tenaga Kependidikan Fungsional Umum',
            'Tenaga Kependidikan Fungsional Tertentu',
            'Tenaga Kependidikan Struktural'
        ])->delete();

        // Mengaktifkan kembali pengecekan relasi
        Schema::enableForeignKeyConstraints();
    }
};
