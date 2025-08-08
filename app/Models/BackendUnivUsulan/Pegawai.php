<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Penggunaan trait ini sudah benar

    protected $guarded = ['id'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tmt_pangkat'       => 'date',
        'tanggal_lahir'     => 'date',
        'tmt_jabatan'       => 'date',
        'tmt_pns'           => 'date',
        'tmt_cpns'          => 'date',
    ];

    // [PERBAIKAN UTAMA]
    // Method 'roles()' di bawah ini telah dihapus.
    // Kita tidak memerlukannya lagi karena sudah ditangani oleh trait 'HasRoles'.

    public function usulans(): HasMany
    {
        return $this->hasMany(Usulan::class, 'pegawai_id', 'id');
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_terakhir_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_terakhir_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(SubSubUnitKerja::class, 'unit_kerja_terakhir_id');
    }
}
