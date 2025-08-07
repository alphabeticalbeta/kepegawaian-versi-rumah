<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackendUnivUsulan\UnitKerja;
use App\Models\BackendUnivUsulan\SubUnitKerja;
use App\Models\BackendUnivUsulan\SubSubUnitKerja;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =========================================================
        //         [!!!] KODE YANG SUDAH DIPERBAIKI [!!!]
        // =========================================================

        // === FAKULTAS TEKNIK ===
        // Buat Fakultas Teknik HANYA SATU KALI
        $fakultasTeknik = UnitKerja::firstOrCreate(['nama' => 'Fakultas Teknik']);

        // --- Jurusan di bawah Fakultas Teknik ---
        $jurusanInformatika = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Informatika']
        );
        $jurusanSipil = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Teknik Sipil']
        );
        // Tambahkan jurusan lain di Fakultas Teknik di sini...

        // --- Prodi di bawah Jurusan Informatika ---
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanInformatika->id, 'nama' => 'Prodi Rekayasa Perangkat Lunak']
        );
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanInformatika->id, 'nama' => 'Prodi Jaringan Komputer']
        );

        // --- Prodi di bawah Jurusan Teknik Sipil ---
        SubSubUnitKerja::firstOrCreate(
            ['sub_unit_kerja_id' => $jurusanSipil->id, 'nama' => 'Prodi Struktur Bangunan']
        );


        // === FAKULTAS EKONOMI DAN BISNIS ===
        // Buat Fakultas Ekonomi HANYA SATU KALI
        $fakultasEkonomi = UnitKerja::firstOrCreate(['nama' => 'Fakultas Ekonomi dan Bisnis']);

        // --- Jurusan di bawah Fakultas Ekonomi ---
        $jurusanManajemen = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasEkonomi->id, 'nama' => 'S1 Manajemen']
        );
        $jurusanAkuntansi = SubUnitKerja::firstOrCreate(
            ['unit_kerja_id' => $fakultasEkonomi->id, 'nama' => 'S1 Akuntansi']
        );
        // Tambahkan jurusan lain di Fakultas Ekonomi di sini...
    }
}
