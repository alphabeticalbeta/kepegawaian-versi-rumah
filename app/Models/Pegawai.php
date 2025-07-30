<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;

class Pegawai extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'pangkat_terakhir_id',
        'jabatan_terakhir_id',
        'unit_kerja_terakhir_id',
        'jenis_pegawai',
        'nip',
        'nuptk',
        'gelar_depan',
        'nama_lengkap',
        'gelar_belakang',
        'nomor_kartu_pegawai',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_handphone',
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
        'sk_cpns',
        'sk_pns',
        'role',
        'foto',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_pangkat' => 'date',
        'tmt_jabatan' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
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

    //  * Relasi many-to-many ke model Role.
    //  * Seorang pegawai bisa memiliki banyak role.
    //  */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'pegawai_role');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pegawai) {
            // Jika password tidak diisi secara manual dari form (karena formnya disembunyikan),
            // atur nilainya menjadi NIP pegawai.
            if (empty($pegawai->password)) {
                $pegawai->password = $pegawai->nip;
            }
        });

        // Event yang berjalan otomatis SETELAH pegawai baru dibuat
        static::created(function ($pegawai) {
            // Cari role dengan nama 'Pegawai'
            $defaultRole = Role::where('name', 'Pegawai')->first();
            if ($defaultRole) {
                // Lampirkan role tersebut ke pegawai yang baru dibuat
                $pegawai->roles()->attach($defaultRole->id);
            }
        });
    }
}
