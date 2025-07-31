<?php
namespace App\Policies;
use App\Models\Pegawai;
class PegawaiPolicy
{
    /**
     * Tentukan apakah pegawai bisa melihat daftar resource.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return $pegawai->roles->contains('name', 'Admin Universitas Usulan');
    }
    /**
     * Tentukan apakah pegawai bisa memperbarui resource.
     */
    public function update(Pegawai $pegawai, Pegawai $model): bool
    {
        return $pegawai->roles->contains('name', 'Admin Universitas Usulan');
    }
}
