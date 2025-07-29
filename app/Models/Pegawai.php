<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pangkat_terakhir_id',
        'jabatan_terakhir_id',
        'unit_kerja_terakhir_id',
        'role',
        'jenis_pegawai',
        'nip',
        'nuptk',
        'gelar_depan',
        'gelar_belakang',
        'nomor_kartu_pegawai',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_handphone',
        'tmt_cpns',
        'sk_cpns_terakhir',
        'tmt_pns',
        'sk_pns_terakhir',
        'tmt_pangkat',
        'sk_pangkat_terakhir',
        'tmt_jabatan',
        'sk_jabatan_terakhir',
        'pendidikan_terakhir',
        'ijazah_terakhir',
        'transkrip_nilai_terakhir',
        'sk_penyetaraan_ijazah',
        'disertasi_thesis_terakhir',
        'mata_kuliah_diampu',
        'ranting_ilmu_kepakaran',
        'url_profil_sinta',
        'predikat_kinerja_tahun_pertama',
        'skp_tahun_pertama',
        'predikat_kinerja_tahun_kedua',
        'skp_tahun_kedua',
        'nilai_konversi',
        'pak_konversi',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => 'array', // Otomatis mengubah JSON -> Array dan sebaliknya
        'tanggal_lahir' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
        'tmt_pangkat' => 'date',
        'tmt_jabatan' => 'date',
    ];

    /**
     * Get the pangkat for the pegawai.
     */
    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_terakhir_id');
    }

    /**
     * Get the jabatan for the pegawai.
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_terakhir_id');
    }

    /**
     * Get the unit kerja for the pegawai.
     */
    public function unitKerja()
    {
        return $this->belongsTo(SubSubUnitKerja::class, 'unit_kerja_terakhir_id');
    }
}
