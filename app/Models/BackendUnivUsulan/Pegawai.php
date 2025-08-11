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

    protected $fillable = [
        // Data Utama & Autentikasi
        'jenis_pegawai',
        'status_kepegawaian',
        'nip',
        'nuptk',
        'gelar_depan',
        'nama_lengkap',
        'gelar_belakang',
        'email',
        'password',
        'nomor_kartu_pegawai',

        // Data Pribadi
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_handphone',

        // Relasi ke Tabel Lain
        'pangkat_terakhir_id',
        'jabatan_terakhir_id',
        'unit_kerja_terakhir_id',
        'unit_kerja_id', // Untuk admin fakultas

        // Data Kepegawaian (TMT)
        'tmt_cpns',
        'tmt_pns',
        'tmt_pangkat',
        'tmt_jabatan',

        // Data Pendidikan
        'pendidikan_terakhir',

        // Data Fungsional (Khusus Dosen)
        'mata_kuliah_diampu',
        'ranting_ilmu_kepakaran',
        'url_profil_sinta',

        // Data Kinerja & PAK
        'predikat_kinerja_tahun_pertama',
        'predikat_kinerja_tahun_kedua',
        'nilai_konversi',

        // Path Dokumen (File)
        'foto',
        'sk_cpns',
        'sk_pns',
        'sk_pangkat_terakhir',
        'sk_jabatan_terakhir',
        'ijazah_terakhir',
        'transkrip_nilai_terakhir',
        'sk_penyetaraan_ijazah',
        'disertasi_thesis_terakhir',
        'skp_tahun_pertama',
        'skp_tahun_kedua',
        'pak_konversi',
    ];

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

    public function unitKerjaPengelola()
    {
        // Relasi ini menghubungkan kolom 'unit_kerja_id' di tabel 'pegawais'
        // dengan 'id' di tabel 'unit_kerjas'.
        return $this->belongsTo(\App\Models\BackendUnivUsulan\UnitKerja::class, 'unit_kerja_id');
    }
}
