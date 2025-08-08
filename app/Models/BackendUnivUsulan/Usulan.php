<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_badge_class',
        'can_edit',
        'is_read_only',
        'formatted_created_date',
        'days_since_created'
    ];

    // =====================================
    // RELATIONSHIPS
    // =====================================

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
        return $this->hasMany(UsulanLog::class)->orderBy('created_at', 'desc');
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

    // =====================================
    // ACCESSORS (Computed Properties)
    // =====================================

    /**
     * Get status badge CSS class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status_usulan) {
            'Draft' => 'bg-gray-100 text-gray-800',
            'Diajukan' => 'bg-blue-100 text-blue-800',
            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
            'Perlu Perbaikan' => 'bg-orange-100 text-orange-800',
            'Dikembalikan' => 'bg-red-100 text-red-800',
            'Disetujui' => 'bg-green-100 text-green-800',
            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
            'Ditolak' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Check if usulan can be edited
     */
    public function getCanEditAttribute(): bool
    {
        return in_array($this->status_usulan, [
            'Draft',
            'Perlu Perbaikan',
            'Dikembalikan'
        ]);
    }

    /**
     * Check if usulan is read-only
     */
    public function getIsReadOnlyAttribute(): bool
    {
        return in_array($this->status_usulan, [
            'Diajukan',
            'Sedang Direview',
            'Disetujui',
            'Direkomendasikan'
        ]);
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedCreatedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->isoFormat('D MMMM YYYY') : '-';
    }

    /**
     * Get days since created
     */
    public function getDaysSinceCreatedAttribute(): int
    {
        return $this->created_at ? $this->created_at->diffInDays(now()) : 0;
    }

    // =====================================
    // DATA USULAN ACCESSORS
    // =====================================

    /**
     * Get karya ilmiah data from data_usulan
     */
    public function getKaryaIlmiahAttribute(): ?array
    {
        return $this->data_usulan['karya_ilmiah'] ?? null;
    }

    /**
     * Get dokumen usulan data from data_usulan
     */
    public function getDokumenUsulanAttribute(): ?array
    {
        return $this->data_usulan['dokumen_usulan'] ?? null;
    }

    /**
     * Get syarat khusus data from data_usulan
     */
    public function getSyaratKhususAttribute(): ?array
    {
        return $this->data_usulan['syarat_khusus'] ?? null;
    }

    /**
     * Get pegawai snapshot data from data_usulan
     */
    public function getPegawaiSnapshotAttribute(): ?array
    {
        return $this->data_usulan['pegawai_snapshot'] ?? null;
    }

    /**
     * Get metadata from data_usulan
     */
    public function getMetadataAttribute(): ?array
    {
        return $this->data_usulan['metadata'] ?? null;
    }

    // =====================================
    // DOCUMENT HELPER METHODS
    // =====================================

    /**
     * Check if specific document exists (supports both old and new structure)
     */
    public function hasDocument(string $documentName): bool
    {
        // Check new structure
        if (!empty($this->data_usulan['dokumen_usulan'][$documentName]['path'])) {
            return true;
        }

        // Check old structure (fallback)
        if (!empty($this->data_usulan[$documentName])) {
            return true;
        }

        return false;
    }

    /**
     * Get document path (supports both old and new structure)
     */
    public function getDocumentPath(string $documentName): ?string
    {
        // Check new structure first
        if (!empty($this->data_usulan['dokumen_usulan'][$documentName]['path'])) {
            return $this->data_usulan['dokumen_usulan'][$documentName]['path'];
        }

        // Fallback to old structure
        if (!empty($this->data_usulan[$documentName])) {
            return $this->data_usulan[$documentName];
        }

        return null;
    }

    /**
     * Get all document names that exist
     */
    public function getExistingDocuments(): array
    {
        $documents = [];
        $documentKeys = [
            'pakta_integritas',
            'bukti_korespondensi',
            'turnitin',
            'upload_artikel',
            'bukti_syarat_guru_besar'
        ];

        foreach ($documentKeys as $key) {
            if ($this->hasDocument($key)) {
                $documents[] = $key;
            }
        }

        return $documents;
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status_usulan', $status);
    }

    /**
     * Scope untuk filter by jenis usulan
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_usulan', $jenis);
    }

    /**
     * Scope untuk filter by pegawai
     */
    public function scopeByPegawai($query, int $pegawaiId)
    {
        return $query->where('pegawai_id', $pegawaiId);
    }

    /**
     * Scope untuk filter by periode
     */
    public function scopeByPeriode($query, int $periodeId)
    {
        return $query->where('periode_usulan_id', $periodeId);
    }

    /**
     * Scope untuk usulan yang bisa diedit
     */
    public function scopeEditable($query)
    {
        return $query->whereIn('status_usulan', ['Draft', 'Perlu Perbaikan', 'Dikembalikan']);
    }

    /**
     * Scope untuk usulan yang read-only
     */
    public function scopeReadOnly($query)
    {
        return $query->whereIn('status_usulan', ['Diajukan', 'Sedang Direview', 'Disetujui', 'Direkomendasikan']);
    }

    /**
     * Scope untuk usulan yang masih aktif (belum selesai)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status_usulan', ['Direkomendasikan', 'Ditolak']);
    }

    /**
     * Scope untuk usulan yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status_usulan', ['Direkomendasikan', 'Ditolak']);
    }

    /**
     * Scope untuk recent usulan
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // =====================================
    // UTILITY METHODS
    // =====================================

    /**
     * Check if usulan is for jabatan promotion
     */
    public function isJabatanPromotion(): bool
    {
        return $this->jenis_usulan === 'jabatan';
    }

    /**
     * Check if usulan is for pangkat promotion
     */
    public function isPangkatPromotion(): bool
    {
        return $this->jenis_usulan === 'pangkat';
    }

    /**
     * Check if usulan is draft
     */
    public function isDraft(): bool
    {
        return $this->status_usulan === 'Draft';
    }

    /**
     * Check if usulan is submitted
     */
    public function isSubmitted(): bool
    {
        return !in_array($this->status_usulan, ['Draft']);
    }

    /**
     * Check if usulan is approved
     */
    public function isApproved(): bool
    {
        return in_array($this->status_usulan, ['Disetujui', 'Direkomendasikan']);
    }

    /**
     * Check if usulan is rejected
     */
    public function isRejected(): bool
    {
        return $this->status_usulan === 'Ditolak';
    }

    /**
     * Check if usulan needs revision
     */
    public function needsRevision(): bool
    {
        return in_array($this->status_usulan, ['Perlu Perbaikan', 'Dikembalikan']);
    }

    /**
     * Get latest log entry
     */
    public function getLatestLog(): ?UsulanLog
    {
        return $this->logs()->first();
    }

    /**
     * Get submission summary for display
     */
    public function getSubmissionSummary(): array
    {
        return [
            'id' => $this->id,
            'jenis_usulan' => $this->jenis_usulan,
            'status_usulan' => $this->status_usulan,
            'status_badge_class' => $this->status_badge_class,
            'can_edit' => $this->can_edit,
            'is_read_only' => $this->is_read_only,
            'formatted_created_date' => $this->formatted_created_date,
            'days_since_created' => $this->days_since_created,
            'pegawai_name' => $this->pegawai?->nama_lengkap,
            'jabatan_lama' => $this->jabatanLama?->jabatan,
            'jabatan_tujuan' => $this->jabatanTujuan?->jabatan,
            'periode_nama' => $this->periodeUsulan?->nama_periode,
            'existing_documents' => $this->getExistingDocuments(),
            'latest_log' => $this->getLatestLog()?->getFormattedEntry(),
        ];
    }

    // =====================================
    // BOOT METHOD
    // =====================================

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // HANYA auto-create initial log saat usulan dibuat
        // Status change logging dilakukan manual di controller untuk kontrol yang lebih baik
        static::created(function ($usulan) {
            UsulanLog::create([
                'usulan_id' => $usulan->id,
                'status_sebelumnya' => null,
                'status_baru' => $usulan->status_usulan,
                'catatan' => 'Usulan dibuat dengan status ' . $usulan->status_usulan,
                'dilakukan_oleh_id' => $usulan->pegawai_id,
            ]);
        });

    // HAPUS static::updating untuk menghindari masalah auth
    // Manual logging di controller lebih reliable
    }

// =====================================
// HELPER METHOD untuk Manual Logging
// =====================================

/**
 * Create log entry for this usulan
 */
    public function createLog(string $statusBaru, ?string $statusSebelumnya = null, ?string $catatan = null, ?int $userId = null): UsulanLog
    {
        $userId = $userId ?? auth()->id() ?? $this->pegawai_id;

        if (!$catatan) {
            if ($statusSebelumnya && $statusSebelumnya !== $statusBaru) {
                $catatan = "Status diubah dari {$statusSebelumnya} ke {$statusBaru}";
            } else {
                $catatan = "Status diperbarui menjadi {$statusBaru}";
            }
        }

        return UsulanLog::create([
            'usulan_id' => $this->id,
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $statusBaru,
            'catatan' => $catatan,
            'dilakukan_oleh_id' => $userId,
        ]);
    }
}
