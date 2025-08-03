<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usulan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usulans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pegawai_id',
        'periode_usulan_id',
        'jenis_usulan',
        'jabatan_lama_id',
        'jabatan_tujuan_id',
        'status_usulan',
        'data_usulan',
        'catatan_verifikator',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_usulan' => 'array',
    ];

    // --- DEFINISI RELASI ---

    /**
     * Relasi ke Pegawai yang mengajukan usulan.
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Relasi ke Periode Usulan.
     */
    public function periodeUsulan(): BelongsTo
    {
        return $this->belongsTo(PeriodeUsulan::class);
    }

    /**
     * Relasi ke Jabatan (jabatan lama).
     */
    public function jabatanLama(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_lama_id');
    }

    /**
     * Relasi ke Jabatan (jabatan tujuan).
     */
    public function jabatanTujuan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_tujuan_id');
    }

    /**
     * Relasi ke dokumen-dokumen pendukung.
     */
    public function dokumens(): HasMany
    {
        return $this->hasMany(UsulanDokumen::class);
    }

    /**
     * Relasi ke log riwayat usulan.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(UsulanLog::class);
    }

    /**
     * Relasi many-to-many ke Pegawai (sebagai Penilai).
     */
    public function penilais(): BelongsToMany
    {
        return $this->belongsToMany(Pegawai::class, 'usulan_penilai_jabatan', 'usulan_id', 'penilai_id')
                    ->withPivot('status_penilaian', 'catatan_penilaian')
                    ->withTimestamps();
    }
}
