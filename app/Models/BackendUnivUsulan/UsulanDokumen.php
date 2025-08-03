<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsulanDokumen extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usulan_dokumens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'usulan_id',
        'nama_dokumen',
        'path_file',
        'diupload_oleh_id',
    ];

    /**
     * Relasi ke usulan induk.
     */
    public function usulan(): BelongsTo
    {
        return $this->belongsTo(Usulan::class);
    }

    /**
     * Relasi ke pegawai yang mengupload file.
     */
    public function diuploadOleh(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'diupload_oleh_id');
    }
}
