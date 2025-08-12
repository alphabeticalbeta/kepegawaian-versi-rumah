<?php

namespace App\Models\BackendUnivUsulan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\BackendUnivUsulan\UsulanJabatanSenat;
use App\Models\BackendUnivUsulan\UsulanDokumen;
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
     * PERBAIKAN 1: Definisi Primary Key Eksplisit
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

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
        'validasi_data',
        'catatan_verifikator',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_usulan' => 'array',
        'validasi_data' => 'array',
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

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // PERBAIKAN: Tambahkan pengecekan untuk memastikan model sudah persisted
        static::created(function ($usulan) {
            // Pastikan model sudah di-save dan memiliki ID sebelum membuat log
            if ($usulan && $usulan->exists && $usulan->getKey()) {
                try {
                    UsulanLog::create([
                        'usulan_id' => $usulan->getKey(), // Gunakan getKey() lebih aman
                        'status_sebelumnya' => null,
                        'status_baru' => $usulan->status_usulan,
                        'catatan' => 'Usulan dibuat dengan status ' . $usulan->status_usulan,
                        'dilakukan_oleh_id' => $usulan->pegawai_id,
                    ]);
                } catch (\Throwable $e) {
                    // Log error tapi jangan stop proses utama
                    \Log::error('Gagal membuat usulan log: ' . $e->getMessage(), [
                        'usulan_id' => $usulan->getKey(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }

    /**
     * Create log entry for this usulan
     */
    public function createLog(string $statusBaru, ?string $statusSebelumnya = null, ?string $catatan = null, ?int $userId = null): UsulanLog
    {
        // PERBAIKAN: Pastikan model sudah memiliki ID
        if (!$this->exists || !$this->getKey()) {
            throw new \RuntimeException('Cannot create log for usulan that hasn\'t been saved yet');
        }

        $userId = $userId ?? optional(auth()->guard('pegawai')->user())->id ?? $this->pegawai_id;

        if (!$catatan) {
            if ($statusSebelumnya && $statusSebelumnya !== $statusBaru) {
                $catatan = "Status diubah dari {$statusSebelumnya} ke {$statusBaru}";
            } else {
                $catatan = "Status diperbarui menjadi {$statusBaru}";
            }
        }

        return UsulanLog::create([
            'usulan_id' => $this->getKey(), // Gunakan getKey() lebih aman daripada $this->id
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => $statusBaru,
            'catatan' => $catatan,
            'dilakukan_oleh_id' => $userId,
        ]);
    }

    // =====================================
    // DEBUG METHOD - PERBAIKAN 4
    // =====================================

    /**
     * Debug method untuk membantu troubleshooting
     */
    public function debugModelState(): array
    {
        return [
            'exists' => $this->exists,
            'primary_key_name' => $this->getKeyName(),
            'primary_key_value' => $this->getKey(),
            'table_name' => $this->getTable(),
            'attributes' => $this->getAttributes(),
            'original' => $this->getOriginal(),
            'dirty' => $this->getDirty(),
            'was_recently_created' => $this->wasRecentlyCreated,
        ];
    }
    // =====================================
    // VALIDASI DATA HELPER METHODS
    // =====================================

    /**
     * Mendapatkan data validasi untuk role tertentu
     */
    public function getValidasiByRole(string $role): array
    {
        return $this->validasi_data[$role] ?? [];
    }

    /**
     * Set data validasi untuk role tertentu
     */
    public function setValidasiByRole(string $role, array $validasiData, int $validatedBy): void
    {
        $currentValidasi = $this->validasi_data ?? [];

        $currentValidasi[$role] = array_merge($validasiData, [
            'validated_by' => $validatedBy,
            'validated_at' => now()->toISOString()
        ]);

        $this->validasi_data = $currentValidasi;
    }

    /**
     * Cek apakah usulan sudah divalidasi oleh role tertentu
     */
    public function isValidatedByRole(string $role): bool
    {
        return !empty($this->validasi_data[$role]['validated_by']);
    }

    /**
     * Mendapatkan status validasi untuk field tertentu dari role tertentu
     */
    public function getFieldValidationStatus(string $role, string $category, string $field): string
    {
        return $this->validasi_data[$role][$category][$field]['status'] ?? 'sesuai';
    }

    /**
     * Mendapatkan keterangan validasi untuk field tertentu dari role tertentu
     */
    public function getFieldValidationKeterangan(string $role, string $category, string $field): string
    {
        return $this->validasi_data[$role][$category][$field]['keterangan'] ?? '';
    }

    /**
     * Cek apakah ada field yang tidak sesuai dari role tertentu
     */
    public function hasInvalidFields(string $role): bool
    {
        $validasi = $this->getValidasiByRole($role);

        foreach ($validasi as $category => $fields) {
            if (in_array($category, ['validated_by', 'validated_at'])) {
                continue;
            }

            foreach ($fields as $field => $data) {
                if (($data['status'] ?? 'sesuai') === 'tidak_sesuai') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Mendapatkan daftar field yang tidak sesuai dari role tertentu
     */
    public function getInvalidFields(string $role): array
    {
        $invalidFields = [];
        $validasi = $this->getValidasiByRole($role);

        foreach ($validasi as $category => $fields) {
            if (in_array($category, ['validated_by', 'validated_at'])) {
                continue;
            }

            foreach ($fields as $field => $data) {
                if (($data['status'] ?? 'sesuai') === 'tidak_sesuai') {
                    $invalidFields[] = [
                        'category' => $category,
                        'field' => $field,
                        'keterangan' => $data['keterangan'] ?? ''
                    ];
                }
            }
        }

        return $invalidFields;
    }

    /**
     * Mendapatkan informasi validator dari role tertentu
     */
    public function getValidatorInfo(string $role): ?array
    {
        $validasi = $this->getValidasiByRole($role);

        if (empty($validasi['validated_by'])) {
            return null;
        }

        return [
            'validated_by' => $validasi['validated_by'],
            'validated_at' => $validasi['validated_at'] ?? null,
            'validator_name' => null // Bisa ditambah query ke tabel pegawai jika perlu
        ];
    }

    /**
     * Reset validasi untuk role tertentu
     */
    public function resetValidasiByRole(string $role): void
    {
        $currentValidasi = $this->validasi_data ?? [];
        unset($currentValidasi[$role]);
        $this->validasi_data = $currentValidasi;
    }

    /**
     * Mendapatkan semua kategori dan field yang perlu divalidasi
     */
    public static function getValidationFields(): array
    {
        return [
            'data_pribadi' => [
                'jenis_pegawai', 'status_kepegawaian', 'nip', 'nuptk', 'gelar_depan',
                'nama_lengkap', 'gelar_belakang', 'email', 'tempat_lahir', 'tanggal_lahir',
                'jenis_kelamin', 'nomor_handphone'
            ],
            'data_kepegawaian' => [
                'pangkat_saat_usul', 'tmt_pangkat', 'jabatan_saat_usul', 'tmt_jabatan',
                'tmt_cpns', 'tmt_pns', 'unit_kerja_saat_usul'
            ],
            'data_pendidikan' => [
                'pendidikan_terakhir', 'mata_kuliah_diampu', 'ranting_ilmu_kepakaran', 'url_profil_sinta'
            ],
            'data_kinerja' => [
                'predikat_kinerja_tahun_pertama', 'predikat_kinerja_tahun_kedua', 'nilai_konversi'
            ],
            'dokumen_profil' => [
                'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
                'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua', 'pak_konversi',
                'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir'
            ],
            'karya_ilmiah' => [
                'karya_ilmiah', 'nama_jurnal', 'judul_artikel', 'penerbit_artikel', 'volume_artikel',
                'nomor_artikel', 'edisi_artikel', 'halaman_artikel', 'link_artikel', 'link_sinta',
                'link_scopus', 'link_scimago', 'link_wos'
            ],
            'dokumen_usulan' => [
                'pakta_integritas', 'bukti_korespondensi', 'turnitin', 'upload_artikel', 'bukti_syarat_guru_besar'
            ],
            'dokumen_bkd' => [
                'bkd_semester_1',
                'bkd_semester_2',
                'bkd_semester_3',
                'bkd_semester_4',
            ],
            'dokumen_pendukung' => [
                'nomor_surat_usulan',
                'file_surat_usulan',
                'nomor_berita_senat',
                'file_berita_senat',
            ],
        ];
    }

    /**
     * Get validation fields with dynamic BKD
     * NEW METHOD: Untuk mendapatkan validation fields dengan BKD dinamis
     */
    public static function getValidationFieldsWithDynamicBkd($usulan = null): array
    {
        // Get base fields
        $fields = self::getValidationFields();

        // // If usulan provided, check for additional BKD fields in data_usulan
        // if ($usulan && isset($usulan->data_usulan['dokumen_usulan'])) {
        //     $additionalBkdFields = [];

        //     foreach ($usulan->data_usulan['dokumen_usulan'] as $key => $value) {
        //         // Check if it's a BKD field and not already in default list
        //         if (str_starts_with($key, 'bkd_') && !in_array($key, $fields['dokumen_bkd'])) {
        //             $additionalBkdFields[] = $key;
        //         }
        //     }

        //     // Merge additional BKD fields if found
        //     if (!empty($additionalBkdFields)) {
        //         $fields['dokumen_bkd'] = array_merge($fields['dokumen_bkd'], $additionalBkdFields);
        //     }
        // }

        return $fields;
    }

    /**
     * Generate BKD field names based on periode
     * NEW METHOD: Generate nama field BKD berdasarkan periode usulan
     */
    public function generateBkdFieldNames(): array
    {
        if (!$this->periodeUsulan) {
            // Return default if no periode
            return [
                'bkd_semester_1',
                'bkd_semester_2',
                'bkd_semester_3',
                'bkd_semester_4',
            ];
        }

        $startDate = \Carbon\Carbon::parse($this->periodeUsulan->tanggal_mulai);
        $month = $startDate->month;
        $year = $startDate->year;

        // Determine current semester
        if ($month >= 1 && $month <= 6) {
            $currentSemester = 'genap';
            $currentYear = $year - 1;
        } else {
            $currentSemester = 'ganjil';
            $currentYear = $year;
        }

        $fields = [];
        $tempSemester = $currentSemester;
        $tempYear = $currentYear;

        // Generate 4 semester fields
        for ($i = 1; $i <= 4; $i++) {
            // Move to previous semester
            if ($tempSemester === 'ganjil') {
                $tempSemester = 'genap';
                $tempYear--;
            } else {
                $tempSemester = 'ganjil';
            }

            // Option 1: Simple numbered format
            $fields[] = 'bkd_semester_' . $i;

            // Option 2: Detailed format (uncomment if preferred)
            // $fields[] = 'bkd_' . $tempSemester . '_' . $tempYear . '_' . ($tempYear + 1);
        }

        return $fields;
    }

    /**
     * Get BKD display labels based on periode
     * NEW METHOD: Generate label display untuk BKD
     */
    public function getBkdDisplayLabels(): array
    {
        if (!$this->periodeUsulan) {
            return [
                'bkd_semester_1' => 'BKD Semester 1',
                'bkd_semester_2' => 'BKD Semester 2',
                'bkd_semester_3' => 'BKD Semester 3',
                'bkd_semester_4' => 'BKD Semester 4',
            ];
        }

        $startDate = \Carbon\Carbon::parse($this->periodeUsulan->tanggal_mulai);
        $month = $startDate->month;
        $year = $startDate->year;

        // Determine current semester
        if ($month >= 1 && $month <= 6) {
            $currentSemester = 'Genap';
            $currentYear = $year - 1;
        } else {
            $currentSemester = 'Ganjil';
            $currentYear = $year;
        }

        $labels = [];
        $tempSemester = $currentSemester;
        $tempYear = $currentYear;

        for ($i = 1; $i <= 4; $i++) {
            // Move to previous semester
            if ($tempSemester === 'Ganjil') {
                $tempSemester = 'Genap';
                $tempYear--;
            } else {
                $tempSemester = 'Ganjil';
            }

            $academicYear = $tempYear . '/' . ($tempYear + 1);
            $labels['bkd_semester_' . $i] = "BKD Semester {$tempSemester} {$academicYear}";
        }

        return $labels;
    }

    public function senatDecisions()
{
    return $this->hasMany(UsulanJabatanSenat::class, 'usulan_id');
}

/** Ambang minimal setuju dari periode (fallback 1) */
public function getSenateMinSetuju(): int
{
    return (int) ($this->periodeUsulan->senat_min_setuju ?? 1);
}

/** Hitung keputusan Senat (direkomendasikan = setuju, belum_direkomendasikan = tolak) */
public function getSenateDecisionCounts(): array
{
    $total = $this->senatDecisions()->count();
    $setuju = $this->senatDecisions()->where('keputusan', 'direkomendasikan')->count();
    $tolak  = $this->senatDecisions()->where('keputusan', 'belum_direkomendasikan')->count();

    return [
        'total' => $total,
        'setuju' => $setuju,
        'tolak'  => $tolak,
        'belum'  => max(0, $total - $setuju - $tolak),
    ];
}

    /** Lulus Senat berdasarkan minimal setuju (default ambil dari periode) */
    public function isSenateApproved(?int $minSetuju = null): bool
    {
        $min = $minSetuju ?? $this->getSenateMinSetuju();
        $counts = $this->getSenateDecisionCounts();

        // Wajib ada data senat terlebih dulu (total > 0), lalu cek ambang setuju
        return $counts['total'] > 0 && $counts['setuju'] >= $min;
    }

    /** Sudah direkomendasikan oleh tim penilai? (cek di tabel usulan_penilai) */
    public function isRecommendedByReviewer(): bool
    {
        // Sesuaikan nama kolom jika berbeda (status / rekomendasi)
        return DB::table('usulan_penilai')
            ->where('usulan_id', $this->id)
            ->whereIn('status', ['Direkomendasikan', 'direkomendasikan'])
            ->exists();
    }
}
