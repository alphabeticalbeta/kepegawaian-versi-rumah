<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsulanLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usulan_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usulan_id',
        'status_sebelumnya',
        'status_baru',
        'catatan',
        'dilakukan_oleh_id',
    ];

    /**
     * Relasi ke usulan induk.
     */
    public function usulan(): BelongsTo
    {
        return $this->belongsTo(Usulan::class);
    }

    /**
     * Relasi ke pegawai yang melakukan aksi.
     */
    public function dilakukanOleh(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'dilakukan_oleh_id');
    }
}
