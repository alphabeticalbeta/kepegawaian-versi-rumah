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
        // === FAKULTAS TEKNIK ===
        $fakultasTeknik = UnitKerja::create(['nama' => 'Fakultas Teknik']);

        // Jurusan di bawah Fakultas Teknik
        $jurusanInformatika = SubUnitKerja::create(['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Informatika']);
        $jurusanSipil = SubUnitKerja::create(['unit_kerja_id' => $fakultasTeknik->id, 'nama' => 'S1 Teknik Sipil']);

        // Prodi di bawah Jurusan Informatika
        SubSubUnitKerja::create(['sub_unit_kerja_id' => $jurusanInformatika->id, 'nama' => 'Prodi Rekayasa Perangkat Lunak']);
        SubSubUnitKerja::create(['sub_unit_kerja_id' => $jurusanInformatika->id, 'nama' => 'Prodi Jaringan Komputer']);

        // Prodi di bawah Jurusan Teknik Sipil
        SubSubUnitKerja::create(['sub_unit_kerja_id' => $jurusanSipil->id, 'nama' => 'Prodi Struktur Bangunan']);


        // === FAKULTAS EKONOMI DAN BISNIS ===
        $fakultasEkonomi = UnitKerja::create(['nama' => 'Fakultas Ekonomi dan Bisnis']);

        // Jurusan di bawah Fakultas Ekonomi
        $jurusanManajemen = SubUnitKerja::create(['unit_kerja_id' => $fakultasEkonomi->id, 'nama' => 'S1 Manajemen']);
        $jurusanAkuntansi = SubUnitKerja::create(['unit_kerja_id' => $fakultasEkonomi->id, 'nama' => 'S1 Akuntansi']);

        // Prodi di bawah Jurusan Manajemen
        SubSubUnitKerja::create(['sub_unit_kerja_id' => $jurusanManajemen->id, 'nama' => 'Prodi Manajemen Pemasaran']);
        SubSubUnitKerja::create(['sub_unit_kerja_id' => $jurusanManajemen->id, 'nama' => 'Prodi Manajemen SDM']);
    }
}
