<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilai extends Model
{
    use HasFactory;

    protected $table = 'penilais';

    protected $fillable = [
        'nama_lengkap',
        'nip',
        'email',
        'bidang_keahlian',
        'status',
        'created_at',
        'updated_at'
    ];

    /**
     * Get usulans that this penilai is assigned to
     */
    public function usulans()
    {
        return $this->belongsToMany(Usulan::class, 'usulan_penilai', 'penilai_id', 'usulan_id')
                    ->withTimestamps();
    }

    /**
     * Get active penilais
     */
    public static function getActivePenilais()
    {
        return self::where('status', 'aktif')
                   ->orderBy('nama_lengkap')
                   ->get();
    }

    /**
     * Get penilais by bidang keahlian
     */
    public static function getPenilaisByBidang($bidang)
    {
        return self::where('status', 'aktif')
                   ->where('bidang_keahlian', 'like', "%{$bidang}%")
                   ->orderBy('nama_lengkap')
                   ->get();
    }
}
