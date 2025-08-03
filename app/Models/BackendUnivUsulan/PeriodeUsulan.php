<?php

namespace App\Models\BackendUnivUsulan; // <-- LOKASI NAMESPACE DIPERBARUI

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeUsulan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'periode_usulans';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_periode',
        'tahun_periode',
        'jenis_usulan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_mulai_perbaikan',
        'tanggal_selesai_perbaikan',
        'status',
    ];

    /**
     * Mendefinisikan relasi "one-to-many" ke model Usulan.
     * Satu periode bisa memiliki banyak usulan.
     */
    public function usulans(): HasMany
    {
        // Kita akan membuat model Usulan di namespace yang sama
        return $this->hasMany(Usulan::class);
    }
}
