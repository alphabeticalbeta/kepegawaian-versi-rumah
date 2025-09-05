<?php

namespace App\Http\Controllers\Backend\AdminFakultas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Services\FileStorageService;
use App\Services\ValidationService;
use App\Services\DocumentAccessService;

/**
 * Admin Fakultas Controller
 *
 * Controller untuk mengelola fitur-fitur admin fakultas termasuk:
 * - Dashboard dengan statistik usulan
 * - Hybrid approach untuk usulan jabatan & pangkat
 * - Validasi dan approval usulan
 *
 * HYBRID APPROACH ARCHITECTURE:
 * - Separate routes: /usulan/jabatan, /usulan/pangkat
 * - Shared view template: index-dynamic.blade.php
 * - Dynamic configuration based on type
 * - Performance optimized with caching
 *
 * @package App\Http\Controllers\Backend\AdminFakultas
 * @author Development Team
 * @version 2.0 - Hybrid Approach Implementation
 */

class AdminFakultasController extends Controller
{
    private $fileStorage;
    private $validationService;
    private $documentAccessService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService, DocumentAccessService $documentAccessService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
        $this->documentAccessService = $documentAccessService;
    }
    /**
    * Menampilkan dashboard dengan daftar usulan untuk fakultas terkait.
    */
    public function dashboard()
    {
        /** @var \App\Models\KepegawaianUniversitas\Pegawai $admin */
        $admin = Auth::user();

        // Gunakan helper method untuk mendapatkan unit kerja
        $unitKerja = $this->getAdminUnitKerja($admin);

        // Gunakan helper method untuk mendapatkan periode usulan
        $periodeUsulans = $this->getPeriodeUsulanWithCount($unitKerja);

        return view('backend.layouts.views.admin-fakultas.dashboard', compact('periodeUsulans', 'unitKerja'));
    }

    /**
     * Dashboard khusus untuk usulan jabatan.
     */
    public function dashboardJabatan()
    {
        /** @var \App\Models\KepegawaianUniversitas\Pegawai $admin */
        $admin = Auth::user();

        // Gunakan helper method untuk mendapatkan unit kerja
        $unitKerja = $this->getAdminUnitKerja($admin);

        if (!$unitKerja) {
            return view('backend.layouts.views.admin-fakultas.usulan.dashboard-jabatan', [
                'periodeUsulans' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'unitKerja' => null,
                'statistics' => [
                    'total_periode' => 0,
                    'total_usulan' => 0,
                    'menunggu_validasi' => 0,
                    'dikirim_universitas' => 0,
                    'perbaikan' => 0,
                    'disetujui' => 0,
                    'ditolak' => 0
                ]
            ]);
        }

        $unitKerjaId = $unitKerja->id;

        // Get usulan yang sudah dibuat oleh pegawai di fakultas ini terlebih dahulu
        $usulans = Usulan::query()
            ->whereIn('jenis_usulan', ['jabatan', 'Usulan Jabatan', 'usulan-jabatan-dosen', 'usulan-jabatan-tendik', 'jabatan-dosen-regular', 'jabatan-dosen-pengangkatan', 'jabatan-tenaga-kependidikan'])
            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($query) use ($unitKerjaId) {
                $query->where('id', $unitKerjaId);
            })
            ->with(['periodeUsulan', 'pegawai'])
            ->get();

        // Debug usulan yang ditemukan
        Log::info('Usulan yang ditemukan untuk admin fakultas', [
            'admin_id' => $admin->id,
            'unit_kerja_id' => $unitKerjaId,
            'unit_kerja_nama' => $unitKerja->nama,
            'total_usulan_found' => $usulans->count(),
            'usulan_ids' => $usulans->pluck('id')->toArray(),
            'periode_ids_with_usulan' => $usulans->pluck('periode_usulan_id')->toArray()
        ]);

        // Get periode IDs yang sudah pernah dibuat usulan oleh pegawai di fakultas ini
        $periodeIdsWithUsulan = $usulans->pluck('periode_usulan_id')->toArray();

        // Get periode usulan khusus jabatan - Include periode yang aktif dan periode yang sudah tutup tapi pernah dibuat usulan
        $periodeUsulans = PeriodeUsulan::query()
            ->whereIn('jenis_usulan', ['jabatan', 'Usulan Jabatan', 'usulan-jabatan-dosen', 'usulan-jabatan-tendik', 'jabatan-dosen-regular', 'jabatan-dosen-pengangkatan', 'jabatan-tenaga-kependidikan'])
            ->where(function($query) use ($periodeIdsWithUsulan) {
                $query->where('status', 'Buka')  // Periode yang sedang aktif
                      ->orWhereIn('id', $periodeIdsWithUsulan);  // Periode yang sudah tutup tapi pernah dibuat usulan (untuk history)
            })
            ->withCount([
                'usulans as total_usulan' => function ($query) use ($unitKerjaId) {
                    $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                        $subQuery->where('id', $unitKerjaId);
                    });
                },
                'usulans as menunggu_validasi' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN,
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as dikirim_universitas' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as perbaikan' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as disetujui' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as ditolak' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                }
            ])
            ->latest()
            ->paginate(10);

        // Debug query results
        Log::info('Periode Usulan Pangkat Query Results untuk Admin Fakultas', [
            'admin_id' => $admin->id,
            'unit_kerja_id' => $unitKerjaId,
            'total_periode_found' => $periodeUsulans->total(),
            'periode_ids' => $periodeUsulans->pluck('id')->toArray(),
            'periode_names' => $periodeUsulans->pluck('nama_periode')->toArray(),
            'periode_statuses' => $periodeUsulans->pluck('status', 'id')->toArray(),
            'periode_ids_with_usulan' => $periodeIdsWithUsulan
        ]);

        // Debug query results
        Log::info('Periode Usulan Query Results untuk Admin Fakultas', [
            'admin_id' => $admin->id,
            'unit_kerja_id' => $unitKerjaId,
            'total_periode_found' => $periodeUsulans->total(),
            'periode_ids' => $periodeUsulans->pluck('id')->toArray(),
            'periode_names' => $periodeUsulans->pluck('nama_periode')->toArray(),
            'periode_statuses' => $periodeUsulans->pluck('status', 'id')->toArray(),
            'periode_ids_with_usulan' => $periodeIdsWithUsulan
        ]);

        // Calculate statistics
        $statistics = [
            'total_periode' => $periodeUsulans->total(),
            'total_usulan' => $periodeUsulans->sum('total_usulan'),
            'menunggu_validasi' => $periodeUsulans->sum('menunggu_validasi'),
            'dikirim_universitas' => $periodeUsulans->sum('dikirim_universitas'),
            'perbaikan' => $periodeUsulans->sum('perbaikan'),
            'disetujui' => $periodeUsulans->sum('disetujui'),
            'ditolak' => $periodeUsulans->sum('ditolak')
        ];

        return view('backend.layouts.views.admin-fakultas.usulan.dashboard-jabatan', compact('periodeUsulans', 'unitKerja', 'statistics'));
    }

    /**
     * Dashboard khusus untuk usulan pangkat.
     */
    public function dashboardPangkat()
    {
        /** @var \App\Models\KepegawaianUniversitas\Pegawai $admin */
        $admin = Auth::user();

        // Gunakan helper method untuk mendapatkan unit kerja
        $unitKerja = $this->getAdminUnitKerja($admin);

        if (!$unitKerja) {
            return view('backend.layouts.views.admin-fakultas.usulan.dashboard-pangkat', [
                'periodeUsulans' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'unitKerja' => null,
                'statistics' => [
                    'total_periode' => 0,
                    'total_usulan' => 0,
                    'menunggu_validasi' => 0,
                    'dikirim_universitas' => 0,
                    'perbaikan' => 0,
                    'disetujui' => 0,
                    'ditolak' => 0
                ]
            ]);
        }

        $unitKerjaId = $unitKerja->id;

        // Get usulan yang sudah dibuat oleh pegawai di fakultas ini terlebih dahulu
        $usulans = Usulan::query()
            ->whereIn('jenis_usulan', ['pangkat', 'Usulan Kepangkatan', 'kepangkatan'])
            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($query) use ($unitKerjaId) {
                $query->where('id', $unitKerjaId);
            })
            ->with(['periodeUsulan', 'pegawai'])
            ->get();

        // Debug usulan yang ditemukan
        Log::info('Usulan pangkat yang ditemukan untuk admin fakultas', [
            'admin_id' => $admin->id,
            'unit_kerja_id' => $unitKerjaId,
            'unit_kerja_nama' => $unitKerja->nama,
            'total_usulan_found' => $usulans->count(),
            'usulan_ids' => $usulans->pluck('id')->toArray(),
            'periode_ids_with_usulan' => $usulans->pluck('periode_usulan_id')->toArray()
        ]);

        // Get periode IDs yang sudah pernah dibuat usulan oleh pegawai di fakultas ini
        $periodeIdsWithUsulan = $usulans->pluck('periode_usulan_id')->toArray();

        // Get periode usulan khusus pangkat - Include periode yang aktif dan periode yang sudah tutup tapi pernah dibuat usulan
        $periodeUsulans = PeriodeUsulan::query()
            ->whereIn('jenis_usulan', ['pangkat', 'Usulan Kepangkatan', 'kepangkatan'])
            ->where(function($query) use ($periodeIdsWithUsulan) {
                $query->where('status', 'Buka')  // Periode yang sedang aktif
                      ->orWhereIn('id', $periodeIdsWithUsulan);  // Periode yang sudah tutup tapi pernah dibuat usulan (untuk history)
            })
            ->withCount([
                'usulans as total_usulan' => function ($query) use ($unitKerjaId) {
                    $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                        $subQuery->where('id', $unitKerjaId);
                    });
                },
                'usulans as menunggu_validasi' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_DRAFT_USULAN,
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as dikirim_universitas' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as perbaikan' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as disetujui' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIREKOMENDASI_PENILAI_UNIVERSITAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                },
                'usulans as ditolak' => function ($query) use ($unitKerjaId) {
                    $query->whereIn('status_usulan', [
                        \App\Models\KepegawaianUniversitas\Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS
                    ])
                        ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerjaId) {
                            $subQuery->where('id', $unitKerjaId);
                        });
                }
            ])
            ->latest()
            ->paginate(10);

        // Calculate statistics
        $statistics = [
            'total_periode' => $periodeUsulans->total(),
            'total_usulan' => $periodeUsulans->sum('total_usulan'),
            'menunggu_validasi' => $periodeUsulans->sum('menunggu_validasi'),
            'dikirim_universitas' => $periodeUsulans->sum('dikirim_universitas'),
            'perbaikan' => $periodeUsulans->sum('perbaikan'),
            'disetujui' => $periodeUsulans->sum('disetujui'),
            'ditolak' => $periodeUsulans->sum('ditolak')
        ];

        return view('backend.layouts.views.admin-fakultas.usulan.dashboard-pangkat', compact('periodeUsulans', 'unitKerja', 'statistics'));
    }



    /**
     * Menampilkan detail satu usulan spesifik untuk VALIDASI.
     * OPTIMIZED: Menggunakan query scopes dan caching untuk performa optimal
     */
    public function show(Usulan $usulan)
    {
        try {
            $currentRole = 'Admin Fakultas';
            $pegawai = $usulan->pegawai;

            // Get admin data with null safety check
            if (!$pegawai) {
                Log::error('Show usulan failed: No authenticated user', [
                    'usulan_id' => $usulan->id
                ]);
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $adminFakultasId = $pegawai->unit_kerja_id;

            // OPTIMASI: Gunakan eager loading yang optimal
            $usulan->load([
                'pegawai:id,nama_lengkap,email,nip,gelar_depan,gelar_belakang,pangkat_terakhir_id,jabatan_terakhir_id,unit_kerja_id,jenis_pegawai,status_kepegawaian,nuptk,tempat_lahir,tanggal_lahir,jenis_kelamin,nomor_handphone,nomor_kartu_pegawai,tmt_pangkat,tmt_jabatan,tmt_cpns,tmt_pns,pendidikan_terakhir,nama_universitas_sekolah,nama_prodi_jurusan,mata_kuliah_diampu,ranting_ilmu_kepakaran,url_profil_sinta,predikat_kinerja_tahun_pertama,predikat_kinerja_tahun_kedua,nilai_konversi,ijazah_terakhir,transkrip_nilai_terakhir,sk_pangkat_terakhir,sk_jabatan_terakhir,skp_tahun_pertama,skp_tahun_kedua,pak_konversi,sk_cpns,sk_pns,sk_penyetaraan_ijazah,disertasi_thesis_terakhir',
                'pegawai.pangkat:id,pangkat',
                'pegawai.jabatan:id,jabatan',
                'pegawai.unitKerja:id,nama,sub_unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja:id,nama,unit_kerja_id',
                'pegawai.unitKerja.subUnitKerja.unitKerja:id,nama',
                'jabatanLama:id,jabatan',
                'jabatanTujuan:id,jabatan',
                'periodeUsulan:id,nama_periode,tanggal_mulai,tanggal_selesai,status',
                'dokumens:id,usulan_id,nama_dokumen,path',
                'logs:id,usulan_id,status_baru,catatan,created_at,dilakukan_oleh_id',
                'logs.dilakukanOleh:id,nama_lengkap'
            ]);

            // Get fakultas ID directly (no cache for now to ensure fresh data)
            $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

            if (!$adminFakultasId || !$usulanPegawaiFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
                Log::warning('Admin Fakultas mencoba akses usulan dari fakultas lain.', [
                    'admin_id' => $pegawai->id,
                    'admin_name' => $pegawai->nama_lengkap ?? 'Unknown',
                    'admin_fakultas_id' => $adminFakultasId,
                    'usulan_id' => $usulan->id,
                    'usulan_fakultas_id' => $usulanPegawaiFakultasId
                ]);
                return redirect()->route('admin-fakultas.dashboard')
                    ->with('error', 'Akses ditolak. Anda tidak berhak melihat usulan dari fakultas lain.');
            }

            // Get validation fields directly (no cache for now to ensure fresh data)
            $validationFields = \App\Models\KepegawaianUniversitas\Usulan::getValidationFieldsWithDynamicBkd($usulan, 'admin_fakultas');

            // Get BKD labels directly (no cache for now to ensure fresh data)
            $bkdLabels = $usulan->getBkdDisplayLabels();

            // Get validation data directly from database (no cache for now)
            // Force refresh usulan model to get latest data
            $usulan->refresh();
            $existingValidation = $usulan->getValidasiByRole('admin_fakultas');
            \Log::info('Loading validation data directly', [
                'usulan_id' => $usulan->id,
                'validation_keys' => array_keys($existingValidation),
                'has_validation_key' => isset($existingValidation['validation']),
                'validation_structure' => $existingValidation,
                'raw_validasi_data' => $usulan->validasi_data
            ]);

            // Get dokumen data directly (no cache for now to ensure fresh data)
            $dokumenData = $this->processDokumenDataForView($usulan);

            // Tambahkan log ini untuk memeriksa data validasi
            Log::info('Mengecek data validasi untuk usulan ID: ' . $usulan->id, [
                'validasi_data' => $usulan->validasi_data,
                'admin_fakultas_validation' => $usulan->validasi_data['admin_fakultas'] ?? 'Tidak ada data',
                'dokumen_pendukung' => $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? 'Tidak ada data'
            ]);

            // Get penilais data for popup
            $penilais = \App\Models\KepegawaianUniversitas\Pegawai::whereHas('roles', function($query) {
                $query->where('name', 'Penilai Universitas');
            })->orderBy('nama_lengkap')->get();

            // Determine action permissions based on status
            $canReturn = $usulan->status_usulan === Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS;
            $canForward = $usulan->status_usulan === Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS;

            return view('backend.layouts.views.admin-fakultas.usulan.detail', [
                'usulan' => $usulan,
                'validationFields' => $validationFields,
                'existingValidation' => $existingValidation,
                'bkdLabels' => $bkdLabels,
                'dokumenData' => $dokumenData,
                'penilais' => $penilais,
                // FIXED: Use consistent pattern with other controllers
                'role' => 'Admin Fakultas',
                'formAction' => route('admin-fakultas.usulan.save-validation', $usulan->id),
                'backUrl' => route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id),
                'backText' => 'Kembali ke Daftar Pengusul',
                'canEdit' => in_array($usulan->status_usulan, [Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS, Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS, Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS, Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS]),
                'config' => [
                    'canReturn' => $canReturn,
                    'canForward' => $canForward,
                    'routePrefix' => 'admin-fakultas',
                    'canEdit' => in_array($usulan->status_usulan, [Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS, Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS, Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS, Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS]),
                    'canView' => true, // Always allow viewing data
                    'submitFunctions' => ['save', 'return_to_pegawai', 'reject_to_pegawai', 'forward_to_university', 'submit_university']
                ],
                'roleConfig' => [
                    'canEdit' => in_array($usulan->status_usulan, [Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS, Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS, Usulan::STATUS_USULAN_PERBAIKAN_DARI_PENILAI_UNIVERSITAS, Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS]),
                    'canView' => true, // Always allow viewing data
                    'submitFunctions' => ['save', 'return_to_pegawai', 'reject_to_pegawai', 'forward_to_university', 'submit_university']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menampilkan detail usulan: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'admin_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data detail usulan. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan hasil validasi admin fakultas.
     */
    public function saveValidation(Request $request, Usulan $usulan)
    {
        // Block actions if usulan is already approved by Admin Fakultas
        if ($usulan->status_usulan === Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS) {
            return redirect()->back()->with('error', 'Usulan sudah disetujui dan tidak dapat diubah lagi.');
        }

        // Dispatcher: prioritise explicit action_type (complex flows)
        if ($request->filled('action_type')) {
            return $this->saveComplexValidation($request, $usulan);
        }

        // Fallback to simple validation (legacy submit with 'action')
        if ($request->has('action')) {
            return $this->saveSimpleValidation($request, $usulan);
        }

        return redirect()->back()->with('error', 'Permintaan tidak dikenali.');
    }

    /**
     * Save simple validation form
     */
    private function saveSimpleValidation(Request $request, Usulan $usulan)
    {
        // Validasi fleksibel - izinkan simpan tanpa data validation jika hanya dokumen pendukung
        $validationRules = [];

        if ($request->has('validation')) {
            $validationRules['validation'] = 'required|array';
        }

        $validationRules['action'] = 'required|in:save_draft,submit';

        $validatedData = $request->validate($validationRules);

        $adminId = Auth::id();
        $action = $validatedData['action'];
        $validationData = $validatedData['validation'] ?? [];

        DB::beginTransaction();
        try {
            // Save validation data - fleksibel tanpa pengecekan field invalid
            if (!empty($validationData)) {
                $usulan->setValidasiByRole('admin_fakultas', $validationData, $adminId);
            }

            // Update status based on action
            if ($action === 'submit') {
                $usulan->status_usulan = Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS;
                $logMessage = 'Usulan diteruskan ke universitas';
            } else {
                $logMessage = 'Draft validasi disimpan';
            }

            $usulan->save();

            // Create log
            $this->createUsulanLog($usulan, $usulan->status_usulan, $logMessage, $adminId);

            DB::commit();

            return redirect()->back()->with('success', $logMessage . ' berhasil.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menyimpan validasi: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'admin_id' => $adminId,
                'action' => $action
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan validasi.');
        }
    }

    /**
     * Save complex validation form (existing method)
     */
    private function saveComplexValidation(Request $request, Usulan $usulan)
    {
        // Ambil data awal yang dibutuhkan untuk semua aksi
        $actionType = $request->input('action_type', 'save_only');
        $adminId = Auth::id();
        $statusLama = $usulan->status_usulan;

        DB::beginTransaction();
        try {
            switch ($actionType) {
                case 'return_to_pegawai':
                    // Validasi khusus untuk aksi 'kembalikan ke pegawai'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'catatan_umum' => 'required|string|min:10|max:2000'
                    ], [
                        'catatan_umum.required' => 'Catatan untuk pegawai wajib diisi.',
                        'catatan_umum.min' => 'Catatan untuk pegawai minimal 10 karakter.',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);
                    $invalidFields = $usulan->getInvalidFields('admin_fakultas');

                    // Buat catatan lengkap untuk pegawai
                    $catatanDetail = ["Usulan dikembalikan oleh Admin Fakultas untuk perbaikan."];
                    if (count($invalidFields) > 0) {
                        $catatanDetail[] = "\nItem yang perlu diperbaiki:";
                        foreach ($invalidFields as $field) {
                            $catatanDetail[] = "• " . ucwords(str_replace('_', ' ', $field['field'])) . ": {$field['keterangan']}";
                        }
                    }
                    $catatanDetail[] = "\nCatatan Tambahan:";
                    $catatanDetail[] = $validatedData['catatan_umum'];
                    $catatanLengkap = implode("\n", $catatanDetail);

                    // Update usulan
                    $usulan->status_usulan = Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS;
                    $usulan->catatan_verifikator = $catatanLengkap;
                    $logMessage = 'Usulan dikembalikan ke Pegawai untuk perbaikan.';
                    break;

                case 'reject_to_pegawai':
                    // Validasi khusus untuk aksi 'belum direkomendasikan'
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'catatan_reject' => 'required|string|min:10|max:2000'
                    ], [
                        'catatan_reject.required' => 'Alasan belum direkomendasikan wajib diisi.',
                        'catatan_reject.min' => 'Alasan belum direkomendasikan minimal 10 karakter.',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // Buat catatan lengkap untuk pegawai
                    $catatanDetail = ["Usulan belum dapat direkomendasikan oleh Admin Fakultas."];
                    $catatanDetail[] = "\nAlasan:";
                    $catatanDetail[] = $validatedData['catatan_reject'];
                    $catatanLengkap = implode("\n", $catatanDetail);

                    // Update usulan - mencegah submit di periode ini
                    $usulan->status_usulan = Usulan::STATUS_TIDAK_DIREKOMENDASIKAN_KEPEGAWAIAN_UNIVERSITAS;
                    $usulan->catatan_verifikator = $catatanLengkap;
                    $logMessage = 'Usulan ditandai belum direkomendasikan oleh Admin Fakultas.';
                    break;

                case 'forward_to_university':
                case 'submit_university':
                    // Validasi fleksibel untuk aksi 'teruskan ke universitas'
                    // Check if this is initial submission or resend
                    $currentValidasi = $usulan->validasi_data;
                    $dokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];
                    $isInitialSubmission = empty($dokumenPendukung) || !isset($dokumenPendukung['file_surat_usulan_path']);

                    $rules = [
                        'validation' => 'required|array',
                        'dokumen_pendukung.nomor_surat_usulan' => 'nullable|string|max:255',
                        'dokumen_pendukung.nomor_berita_senat' => 'nullable|string|max:255',
                    ];

                    $messages = [
                        'dokumen_pendukung.nomor_surat_usulan.max' => 'Nomor surat usulan maksimal 255 karakter.',
                        'dokumen_pendukung.nomor_berita_senat.max' => 'Nomor berita senat maksimal 255 karakter.',
                    ];

                    // Validasi file fleksibel - tidak wajib untuk initial submission
                    if ($isInitialSubmission) {
                        // Support both bracket and dot notation
                        $rules['dokumen_pendukung.file_surat_usulan'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung.file_berita_senat'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung[file_surat_usulan]'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung[file_berita_senat]'] = 'nullable|file|mimes:pdf|max:2048';
                        $messages['dokumen_pendukung.file_surat_usulan.mimes'] = 'File surat usulan harus berformat PDF.';
                        $messages['dokumen_pendukung.file_berita_senat.mimes'] = 'File berita senat harus berformat PDF.';
                        $messages['dokumen_pendukung[file_surat_usulan].mimes'] = 'File surat usulan harus berformat PDF.';
                        $messages['dokumen_pendukung[file_berita_senat].mimes'] = 'File berita senat harus berformat PDF.';
                    }

                    $validatedData = $request->validate($rules, $messages);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // Log warning jika ada field yang tidak sesuai, tapi tetap lanjutkan proses
                    if ($usulan->hasInvalidFields('admin_fakultas')) {
                        $invalidFields = $usulan->getInvalidFields('admin_fakultas');
                        Log::warning('Admin Fakultas mengirim usulan dengan field yang tidak sesuai (dilanjutkan)', [
                            'usulan_id' => $usulan->id,
                            'admin_id' => $adminId,
                            'invalid_fields_count' => count($invalidFields),
                            'invalid_fields' => $invalidFields
                        ]);
                        // Lanjutkan proses meskipun ada field yang tidak sesuai
                    }

                                        // Simpan dokumen pendukung fakultas
                    $currentValidasi = $usulan->validasi_data;
                    $currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

                    // Update text fields (handle null values)
                    if (isset($validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {
                        $currentDokumenPendukung['nomor_surat_usulan'] = $validatedData['dokumen_pendukung']['nomor_surat_usulan'];
                    }
                    if (isset($validatedData['dokumen_pendukung']['nomor_berita_senat'])) {
                        $currentDokumenPendukung['nomor_berita_senat'] = $validatedData['dokumen_pendukung']['nomor_berita_senat'];
                    }

                    // Handle file uploads menggunakan FileStorageService
                    $currentDokumenPendukung['file_surat_usulan_path'] = $this->fileStorage->handleDokumenPendukung(
                        $request,
                        $usulan,
                        'file_surat_usulan',
                        'dokumen-fakultas/surat-usulan'
                    );

                    $currentDokumenPendukung['file_berita_senat_path'] = $this->fileStorage->handleDokumenPendukung(
                        $request,
                        $usulan,
                        'file_berita_senat',
                        'dokumen-fakultas/berita-senat'
                    );

                    $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
                    $usulan->validasi_data = $currentValidasi;

                    // Log warning jika file tidak lengkap, tapi tetap lanjutkan proses
                    if (!empty($currentDokumenPendukung['file_surat_usulan_path']) || !empty($currentDokumenPendukung['file_berita_senat_path'])) {
                        // If one file is uploaded, both should be uploaded
                        if (empty($currentDokumenPendukung['file_surat_usulan_path']) || empty($currentDokumenPendukung['file_berita_senat_path'])) {
                            Log::warning('Admin Fakultas mengirim usulan dengan file upload tidak lengkap (dilanjutkan)', [
                                'usulan_id' => $usulan->id,
                                'admin_id' => $adminId,
                                'has_surat_path' => !empty($currentDokumenPendukung['file_surat_usulan_path']),
                                'has_berita_path' => !empty($currentDokumenPendukung['file_berita_senat_path'])
                            ]);
                            // Lanjutkan proses meskipun file tidak lengkap
                        }
                    }

                    // Update status usulan
                    $usulan->status_usulan = Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS;
                    $logMessage = 'Usulan divalidasi dan diteruskan ke Universitas.';
                    break;

                                case 'resend_to_university':
                    // Validasi fleksibel - Admin Fakultas bisa kirim meskipun ada field yang tidak sesuai
                    Log::info('resend_to_university action triggered', [
                        'usulan_id' => $usulan->id,
                        'admin_id' => $adminId,
                        'request_data' => $request->all()
                    ]);

                    // Validasi fleksibel - izinkan kirim tanpa validation array
                    $validationRules = [];

                    if ($request->has('validation')) {
                        $validationRules['validation'] = 'required|array';
                    }

                    $validationRules['dokumen_pendukung'] = 'nullable|array';
                    $validationRules['dokumen_pendukung.nomor_surat_usulan'] = 'nullable|string|max:255';
                    $validationRules['dokumen_pendukung.nomor_berita_senat'] = 'nullable|string|max:255';
                    $validationRules['dokumen_pendukung.file_surat_usulan'] = 'nullable|file|mimes:pdf|max:2048';
                    $validationRules['dokumen_pendukung.file_berita_senat'] = 'nullable|file|mimes:pdf|max:2048';
                    // Support bracket notation for compatibility
                    $validationRules['dokumen_pendukung[file_surat_usulan]'] = 'nullable|file|mimes:pdf|max:2048';
                    $validationRules['dokumen_pendukung[file_berita_senat]'] = 'nullable|file|mimes:pdf|max:2048';

                    $validatedData = $request->validate($validationRules);

                    Log::info('resend_to_university validation passed', [
                        'usulan_id' => $usulan->id,
                        'validation_data' => $validatedData['validation'] ?? null,
                        'dokumen_pendukung_data' => $validatedData['dokumen_pendukung'] ?? null
                    ]);

                    // Simpan validation data jika ada
                    if (isset($validatedData['validation'])) {
                        $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);
                    }

                    // Log warning jika ada field yang tidak sesuai, tapi tetap lanjutkan proses
                    if (isset($validatedData['validation']) && $usulan->hasInvalidFields('admin_fakultas')) {
                        $invalidFields = $usulan->getInvalidFields('admin_fakultas');
                        Log::warning('Admin Fakultas mengirim kembali usulan dengan field yang tidak sesuai (dilanjutkan)', [
                            'usulan_id' => $usulan->id,
                            'admin_id' => $adminId,
                            'invalid_fields_count' => count($invalidFields),
                            'invalid_fields' => $invalidFields
                        ]);
                        // Lanjutkan proses meskipun ada field yang tidak sesuai
                    }

                    // Simpan dokumen pendukung jika ada
                    if (isset($validatedData['dokumen_pendukung'])) {
                        $currentValidasi = $usulan->validasi_data;
                        $currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

                        // Update text fields jika ada
                        if (isset($validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {
                            $currentDokumenPendukung['nomor_surat_usulan'] = $validatedData['dokumen_pendukung']['nomor_surat_usulan'];
                        }
                        if (isset($validatedData['dokumen_pendukung']['nomor_berita_senat'])) {
                            $currentDokumenPendukung['nomor_berita_senat'] = $validatedData['dokumen_pendukung']['nomor_berita_senat'];
                        }

                        // Handle file uploads menggunakan FileStorageService
                        $currentDokumenPendukung['file_surat_usulan_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_surat_usulan',
                            'dokumen-fakultas/surat-usulan'
                        );

                        $currentDokumenPendukung['file_berita_senat_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_berita_senat',
                            'dokumen-fakultas/berita-senat'
                        );

                        $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
                        $usulan->validasi_data = $currentValidasi;

                        Log::info('Dokumen pendukung saved for resend_to_university', [
                            'usulan_id' => $usulan->id,
                            'dokumen_pendukung' => $currentDokumenPendukung
                        ]);
                    }

                    // Update status usulan untuk perbaikan dari Kepegawaian Universitas
                    $usulan->status_usulan = Usulan::STATUS_USULAN_PERBAIKAN_DARI_ADMIN_FAKULTAS_KE_KEPEGAWAIAN_UNIVERSITAS;

                    $logMessage = 'Usulan perbaikan berhasil dikirim ke Kepegawaian Universitas.';
                    break;

                case 'kirim_perbaikan':
                    // Alias untuk return_to_pegawai - backward compatibility
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'catatan_umum' => 'required|string|min:10|max:2000'
                    ], [
                        'catatan_umum.required' => 'Catatan untuk pegawai wajib diisi.',
                        'catatan_umum.min' => 'Catatan untuk pegawai minimal 10 karakter.',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);
                    $invalidFields = $usulan->getInvalidFields('admin_fakultas');

                    // Buat catatan lengkap untuk pegawai
                    $catatanDetail = ["Usulan dikembalikan oleh Admin Fakultas untuk perbaikan."];
                    if (count($invalidFields) > 0) {
                        $catatanDetail[] = "\nItem yang perlu diperbaiki:";
                        foreach ($invalidFields as $field) {
                            $catatanDetail[] = "• " . ucwords(str_replace('_', ' ', $field['field'])) . ": {$field['keterangan']}";
                        }
                    }
                    $catatanDetail[] = "\nCatatan Tambahan:";
                    $catatanDetail[] = $validatedData['catatan_umum'];
                    $catatanLengkap = implode("\n", $catatanDetail);

                    // Update usulan
                    $usulan->status_usulan = Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS;
                    $usulan->catatan_verifikator = $catatanLengkap;
                    $logMessage = 'Usulan dikembalikan ke Pegawai untuk perbaikan.';
                    break;

                case 'perbaikan_usulan':
                    // Alias untuk return_to_pegawai - backward compatibility
                    $validatedData = $request->validate([
                        'validation' => 'required|array',
                        'catatan_umum' => 'required|string|min:10|max:2000'
                    ], [
                        'catatan_umum.required' => 'Catatan untuk pegawai wajib diisi.',
                        'catatan_umum.min' => 'Catatan untuk pegawai minimal 10 karakter.',
                    ]);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);
                    $invalidFields = $usulan->getInvalidFields('admin_fakultas');

                    // Buat catatan lengkap untuk pegawai
                    $catatanDetail = ["Usulan dikembalikan oleh Admin Fakultas untuk perbaikan."];
                    if (count($invalidFields) > 0) {
                        $catatanDetail[] = "\nItem yang perlu diperbaiki:";
                        foreach ($invalidFields as $field) {
                            $catatanDetail[] = "• " . ucwords(str_replace('_', ' ', $field['field'])) . ": {$field['keterangan']}";
                        }
                    }
                    $catatanDetail[] = "\nCatatan Tambahan:";
                    $catatanDetail[] = $validatedData['catatan_umum'];
                    $catatanLengkap = implode("\n", $catatanDetail);

                    // Update usulan
                    $usulan->status_usulan = Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS;
                    $usulan->catatan_verifikator = $catatanLengkap;
                    $logMessage = 'Usulan dikembalikan ke Pegawai untuk perbaikan.';
                    break;

                case 'rekomendasikan':
                    // Alias untuk forward_to_university - backward compatibility
                    // Check if this is initial submission or resend
                    $currentValidasi = $usulan->validasi_data;
                    $dokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];
                    $isInitialSubmission = empty($dokumenPendukung) || !isset($dokumenPendukung['file_surat_usulan_path']);

                    $rules = [
                        'validation' => 'required|array',
                        'dokumen_pendukung.nomor_surat_usulan' => 'nullable|string|max:255',
                        'dokumen_pendukung.nomor_berita_senat' => 'nullable|string|max:255',
                    ];

                    $messages = [
                        'dokumen_pendukung.nomor_surat_usulan.max' => 'Nomor surat usulan maksimal 255 karakter.',
                        'dokumen_pendukung.nomor_berita_senat.max' => 'Nomor berita senat maksimal 255 karakter.',
                    ];

                    // Only require files for initial submission if files are provided
                    if ($isInitialSubmission) {
                        // Support both bracket and dot notation
                        $rules['dokumen_pendukung.file_surat_usulan'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung.file_berita_senat'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung[file_surat_usulan]'] = 'nullable|file|mimes:pdf|max:2048';
                        $rules['dokumen_pendukung[file_berita_senat]'] = 'nullable|file|mimes:pdf|max:2048';
                        $messages['dokumen_pendukung.file_surat_usulan.mimes'] = 'File surat usulan harus berformat PDF.';
                        $messages['dokumen_pendukung.file_berita_senat.mimes'] = 'File berita senat harus berformat PDF.';
                        $messages['dokumen_pendukung[file_surat_usulan].mimes'] = 'File surat usulan harus berformat PDF.';
                        $messages['dokumen_pendukung[file_berita_senat].mimes'] = 'File berita senat harus berformat PDF.';
                    }

                    $validatedData = $request->validate($rules, $messages);

                    $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);

                    // Cek lagi setelah data validasi disimpan (warning only, not blocking)
                    if ($usulan->hasInvalidFields('admin_fakultas')) {
                        $invalidFields = $usulan->getInvalidFields('admin_fakultas');
                        Log::warning('Admin Fakultas mengirim usulan dengan field yang tidak sesuai', [
                            'usulan_id' => $usulan->id,
                            'admin_id' => $adminId,
                            'invalid_fields_count' => count($invalidFields),
                            'invalid_fields' => $invalidFields
                        ]);
                    }

                    // Simpan dokumen pendukung fakultas
                    $currentValidasi = $usulan->validasi_data;
                    $currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

                    // Update text fields (handle null values)
                    if (isset($validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {
                        $currentDokumenPendukung['nomor_surat_usulan'] = $validatedData['dokumen_pendukung']['nomor_surat_usulan'];
                    }
                    if (isset($validatedData['dokumen_pendukung']['nomor_berita_senat'])) {
                        $currentDokumenPendukung['nomor_berita_senat'] = $validatedData['dokumen_pendukung']['nomor_berita_senat'];
                    }

                    // Handle file uploads menggunakan FileStorageService
                    $currentDokumenPendukung['file_surat_usulan_path'] = $this->fileStorage->handleDokumenPendukung(
                        $request,
                        $usulan,
                        'file_surat_usulan',
                        'dokumen-fakultas/surat-usulan'
                    );

                    $currentDokumenPendukung['file_berita_senat_path'] = $this->fileStorage->handleDokumenPendukung(
                        $request,
                        $usulan,
                        'file_berita_senat',
                        'dokumen-fakultas/berita-senat'
                    );

                    $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
                    $usulan->validasi_data = $currentValidasi;

                    // Update status usulan
                    $usulan->status_usulan = Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS;
                    $logMessage = 'Usulan divalidasi dan diteruskan ke Universitas.';
                    break;

                default: // save_only
                    // Validasi fleksibel untuk aksi 'simpan saja'
                    Log::info('save_only action triggered', [
                        'usulan_id' => $usulan->id,
                        'admin_id' => $adminId,
                        'request_data' => $request->all()
                    ]);

                    // Validasi fleksibel - izinkan simpan tanpa validation array
                    $validationRules = [];

                    if ($request->has('validation')) {
                        $validationRules['validation'] = 'required|array';
                    }

                    if ($request->has('dokumen_pendukung')) {
                        $validationRules['dokumen_pendukung'] = 'nullable|array';
                        $validationRules['dokumen_pendukung.nomor_surat_usulan'] = 'nullable|string|max:255';
                        $validationRules['dokumen_pendukung.nomor_berita_senat'] = 'nullable|string|max:255';
                        $validationRules['dokumen_pendukung.file_surat_usulan'] = 'nullable|file|mimes:pdf|max:2048';
                        $validationRules['dokumen_pendukung.file_berita_senat'] = 'nullable|file|mimes:pdf|max:2048';
                        // Support bracket notation for compatibility
                        $validationRules['dokumen_pendukung[file_surat_usulan]'] = 'nullable|file|mimes:pdf|max:2048';
                        $validationRules['dokumen_pendukung[file_berita_senat]'] = 'nullable|file|mimes:pdf|max:2048';
                    }

                    $validatedData = $request->validate($validationRules);

                    Log::info('Validation passed', [
                        'usulan_id' => $usulan->id,
                        'validation_data' => $validatedData['validation'] ?? null,
                        'dokumen_pendukung_data' => $validatedData['dokumen_pendukung'] ?? null
                    ]);

                    // Simpan validation data jika ada
                    if (isset($validatedData['validation'])) {
                        $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $adminId);
                        Log::info('setValidasiByRole completed', [
                            'usulan_id' => $usulan->id,
                            'final_validasi_data' => $usulan->validasi_data
                        ]);
                    }

                    // Simpan dokumen pendukung jika ada
                    if (isset($validatedData['dokumen_pendukung'])) {
                        $currentValidasi = $usulan->validasi_data;
                        $currentDokumenPendukung = $currentValidasi['admin_fakultas']['dokumen_pendukung'] ?? [];

                        // Update text fields jika ada
                        if (isset($validatedData['dokumen_pendukung']['nomor_surat_usulan'])) {
                            $currentDokumenPendukung['nomor_surat_usulan'] = $validatedData['dokumen_pendukung']['nomor_surat_usulan'];
                        }
                        if (isset($validatedData['dokumen_pendukung']['nomor_berita_senat'])) {
                            $currentDokumenPendukung['nomor_berita_senat'] = $validatedData['dokumen_pendukung']['nomor_berita_senat'];
                        }

                        // Handle file uploads menggunakan FileStorageService
                        $currentDokumenPendukung['file_surat_usulan_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_surat_usulan',
                            'dokumen-fakultas/surat-usulan'
                        );

                        $currentDokumenPendukung['file_berita_senat_path'] = $this->fileStorage->handleDokumenPendukung(
                            $request,
                            $usulan,
                            'file_berita_senat',
                            'dokumen-fakultas/berita-senat'
                        );

                        $currentValidasi['admin_fakultas']['dokumen_pendukung'] = $currentDokumenPendukung;
                        $usulan->validasi_data = $currentValidasi;

                        Log::info('Dokumen pendukung saved', [
                            'usulan_id' => $usulan->id,
                            'dokumen_pendukung' => $currentDokumenPendukung
                        ]);
                    }

                    // DON'T change status automatically - keep it as current status
                    $logMessage = 'Data berhasil disimpan oleh Admin Fakultas.';
                    break;
            }

            Log::info('Before saving usulan', [
                'usulan_id' => $usulan->id,
                'validasi_data' => $usulan->validasi_data
            ]);

            $usulan->save();

            Log::info('After saving usulan', [
                'usulan_id' => $usulan->id,
                'saved' => true
            ]);

            $usulan->createLog($usulan->status_usulan, $statusLama, $logMessage, $adminId);

            // OPTIMASI: Clear cache setelah data berubah
            $this->clearUsulanCache($usulan);
            $this->clearAdminCache($adminId);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $logMessage,
                    'status' => $usulan->status_usulan,
                ]);
            }

            return redirect()->route('admin-fakultas.dashboard')->with('success', 'Aksi pada usulan berhasil diproses.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dimasukkan tidak valid. Silakan periksa kembali.',
                    'errors' => $e->errors(),
                ], 422);
            }
            // Penting: Mengembalikan ke halaman sebelumnya dengan error dan input lama
            return redirect()->back()->withErrors($e->errors())->withInput()->with('error', 'Data yang dimasukkan tidak valid. Silakan periksa kembali.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal menyimpan validasi: ' . $e->getMessage(), ['usulan_id' => $usulan->id]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses validasi.',
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses validasi.');
        }
    }

    /**
     * Menampilkan daftar pengusul per periode.
     */
    public function showPendaftar(PeriodeUsulan $periodeUsulan)
    {
        /** @var \App\Models\KepegawaianUniversitas\Pegawai $admin */
        $admin = Auth::user();
        $unitKerjaId = $admin->unit_kerja_id;

        // PERBAIKAN: Tampilkan semua usulan (bukan hanya yang berstatus "Diajukan")
        // agar admin fakultas bisa melihat riwayat semua usulan yang pernah mereka proses
        $usulans = Usulan::query()
            ->where('periode_usulan_id', $periodeUsulan->id)
            // HAPUS filter status_usulan agar semua usulan ditampilkan
            // ->where('status_usulan', 'Diajukan')
            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($query) use ($unitKerjaId) {
                $query->where('id', $unitKerjaId);
            })
            ->with(['pegawai', 'jabatanLama', 'jabatanTujuan'])
            ->latest()
            ->paginate(15);

        return view('backend.layouts.views.admin-fakultas.usulan.pengusul', [
            'periode' => $periodeUsulan,
            'usulans' => $usulans,
        ]);
    }

    public function showUsulanDocument(Usulan $usulan, $field)
    {
        $user = Auth::user();

        // Authorization check untuk admin fakultas
        $adminFakultasId = $user->unit_kerja_id;
        $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

        if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
            abort(403, 'Akses ditolak. Anda tidak berhak melihat dokumen dari fakultas lain.');
        }

        // Authorization check using DocumentAccessService (sama seperti pegawai)
        if (!$this->documentAccessService->canAccessDocument($user, $usulan, $field)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat dokumen ini.');
        }

        // Validate field access
        if (!$this->documentAccessService->validateFieldAccess($user, $field)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // Get file path
        $filePath = $usulan->getDocumentPath($field);

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        // Determine correct disk based on field type (sama seperti pegawai)
        $disk = $this->documentAccessService->getDiskForField($field);

        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // Log document access
        $this->documentAccessService->logDocumentAccess($user, $usulan, $field, true);

        // Serve file
        $fullPath = Storage::disk($disk)->path($filePath);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Show dokumen profil pegawai untuk Admin Fakultas
     */
    public function showPegawaiDocument(Usulan $usulan, $field)
    {
        $user = Auth::user();

        // Authorization check
        $adminFakultasId = $user->unit_kerja_id;
        $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

        if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
            abort(403, 'Akses ditolak. Anda tidak berhak melihat dokumen dari fakultas lain.');
        }

        // Authorization check using DocumentAccessService
        if (!$this->documentAccessService->canAccessDocument($user, $usulan, $field)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat dokumen ini.');
        }

        // Validate field access
        if (!$this->documentAccessService->validateFieldAccess($user, $field)) {
            abort(404, 'Jenis dokumen profil tidak valid.');
        }

        // Ambil path dokumen dari pegawai
        $filePath = $usulan->pegawai->{$field} ?? null;

        if (!$filePath) {
            abort(404, 'File tidak ditemukan');
        }

        // Determine correct disk based on field type
        $disk = $this->documentAccessService->getDiskForField($field);
        if (!Storage::disk($disk)->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        // Log document access
        $this->documentAccessService->logDocumentAccess($user, $usulan, $field, true);

        // Serve file
        $fullPath = Storage::disk($disk)->path($filePath);

        $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Show dokumen pendukung fakultas
     */
    public function showDokumenPendukung(Usulan $usulan, $field)
    {
        // Authorization check
        $admin = Auth::user();
        $adminFakultasId = $admin->unit_kerja_id;
        $usulanPegawaiFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

        if (!$adminFakultasId || $adminFakultasId !== $usulanPegawaiFakultasId) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi field
        $allowedFields = ['file_surat_usulan', 'file_berita_senat'];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen pendukung tidak valid.');
        }

        // Ambil path dari validasi data
        $dokumenPendukung = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
        $pathKey = $field . '_path';
        $filePath = $dokumenPendukung[$pathKey] ?? null;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File dokumen pendukung tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($filePath));
    }

    /**
     * Helper method untuk mendapatkan unit kerja admin fakultas
     */
    private function getAdminUnitKerja($admin)
    {
        try {
            // Coba ambil unit kerja langsung dari admin
            if ($admin->unit_kerja_id) {
                return \App\Models\KepegawaianUniversitas\UnitKerja::find($admin->unit_kerja_id);
            }

            // Fallback: ambil dari hierarki jika admin tidak punya unit_kerja_id
            if ($admin->unit_kerja_id) {
                $subSubUnit = \App\Models\KepegawaianUniversitas\SubSubUnitKerja::with('subUnitKerja.unitKerja')
                    ->find($admin->unit_kerja_id);

                if ($subSubUnit && $subSubUnit->subUnitKerja && $subSubUnit->subUnitKerja->unitKerja) {
                    return $subSubUnit->subUnitKerja->unitKerja;
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting admin unit kerja: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper method untuk mendapatkan periode usulan dengan count
     */
    private function getPeriodeUsulanWithCount($unitKerja)
    {
        if (!$unitKerja) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        try {
            return \App\Models\KepegawaianUniversitas\PeriodeUsulan::where('status', 'Buka') // Hanya periode yang aktif
                ->withCount([
                    'usulans as jumlah_pengusul' => function ($query) use ($unitKerja) {
                        $query->whereIn('status_usulan', [
                            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DIKIRIM_KE_ADMIN_FAKULTAS,
                            \App\Models\KepegawaianUniversitas\Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS
                        ])
                            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                                $subQuery->where('id', $unitKerja->id);
                            });
                    },
                    'usulans as perbaikan' => function ($query) use ($unitKerja) {
                        $query->whereIn('status_usulan', [
                            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_DARI_ADMIN_FAKULTAS,
                            \App\Models\KepegawaianUniversitas\Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_ADMIN_FAKULTAS_DARI_KEPEGAWAIAN_UNIVERSITAS
                        ])
                            ->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                                $subQuery->where('id', $unitKerja->id);
                            });
                    },
                    'usulans as total_usulan' => function ($query) use ($unitKerja) {
                        $query->whereHas('pegawai.unitKerja.subUnitKerja.unitKerja', function ($subQuery) use ($unitKerja) {
                            $subQuery->where('id', $unitKerja->id);
                        });
                    }
                ])->latest()->paginate(10);
        } catch (\Exception $e) {
            \Log::error('Error getting periode usulan: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }
    }






    /**
     * Get formatted status badge untuk UI
     */
    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'Buka':
                return [
                    'class' => 'bg-green-100 text-green-800',
                    'text' => 'Buka'
                ];
            case 'Tutup':
                return [
                    'class' => 'bg-red-100 text-red-800',
                    'text' => 'Tutup'
                ];
            default:
                return [
                    'class' => 'bg-gray-100 text-gray-800',
                    'text' => $status
                ];
        }
    }

    /**
 * FIXED: Enhanced autosave method yang memastikan semua data tersimpan
 * Tambahkan method ini ke AdminFakultasController
 */
    public function autosaveValidation(Request $request, Usulan $usulan)
    {
        // Block actions if usulan is already approved by Admin Fakultas
        if ($usulan->status_usulan === Usulan::STATUS_USULAN_DISETUJUI_ADMIN_FAKULTAS) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan sudah disetujui dan tidak dapat diubah lagi.'
            ], 403);
        }

        try {
            \Log::info('Autosave request received', [
                'usulan_id' => $usulan->id,
                'request_data_keys' => array_keys($request->all()),
                'validation_keys' => array_keys($request->input('validation', []))
            ]);

            // Enhanced validation
            $validatedData = $request->validate([
                'validation' => 'required|array',
                'validation.*' => 'array',
                'validation.*.*' => 'array',
                'validation.*.*.status' => 'required|in:sesuai,tidak_sesuai',
                'validation.*.*.keterangan' => 'nullable|string',
                'action_type' => 'required|in:autosave,save_only'
            ]);

            // Quick authorization check with null safety
            $admin = Auth::user();
            if (!$admin) {
                \Log::error('Autosave failed: No authenticated user', [
                    'usulan_id' => $usulan->id,
                    'request_data' => $request->all()
                ]);
                return response()->json(['error' => 'Authentication required'], 401);
            }

            $adminFakultasId = $admin->unit_kerja_id;
            $usulanFakultasId = $usulan->pegawai?->unitKerja?->subUnitKerja?->unit_kerja_id;

            if (!$adminFakultasId || !$usulanFakultasId || $adminFakultasId !== $usulanFakultasId) {
                \Log::warning('Autosave unauthorized access attempt', [
                    'usulan_id' => $usulan->id,
                    'admin_id' => $admin->id,
                    'admin_fakultas_id' => $adminFakultasId,
                    'usulan_fakultas_id' => $usulanFakultasId,
                    'admin_name' => $admin->nama_lengkap ?? 'Unknown'
                ]);
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Use the improved setValidasiByRole method for consistency
            $usulan->setValidasiByRole('admin_fakultas', $validatedData['validation'], $admin->id);

            // DON'T change status automatically - Admin Fakultas needs to fill dokumen pendukung first
            // Status should remain 'Diajukan' until Admin Fakultas explicitly submits with dokumen pendukung

            $usulan->save();

            // Refresh the model to ensure we have the latest data
            $usulan->refresh();

            // Clear specific caches untuk memastikan data terbaru dimuat saat reload
            Cache::forget("existing_validation_{$usulan->id}_admin_fakultas");
            Cache::forget("validation_fields_{$usulan->id}_admin_fakultas");
            Cache::forget("bkd_labels_{$usulan->id}");
            Cache::forget("dokumen_data_{$usulan->id}");
            Cache::forget("usulan_fakultas_{$usulan->id}");
            Cache::forget("admin_fakultas_id_" . Auth::id());

            // Clear usulan model cache
            Cache::forget("usulan_{$usulan->id}");
            Cache::forget("usulan_validation_{$usulan->id}_admin_fakultas");

            // Log cache clearing
            \Log::info('Cache cleared for usulan', [
                'usulan_id' => $usulan->id,
                'admin_id' => Auth::id()
            ]);

            // Get the saved validation data for logging
            $savedValidation = $usulan->getValidasiByRole('admin_fakultas');
            $validationData = $savedValidation['validation'] ?? [];

            // Log success dengan detail
            \Log::info('Autosave successful', [
                'usulan_id' => $usulan->id,
                'saved_categories' => array_keys($validationData),
                'total_fields_saved' => array_sum(array_map('count', $validationData)),
                'status' => $usulan->status_usulan,
                'validation_data_structure' => $savedValidation,
                'raw_validation_data' => $usulan->validasi_data
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'saved_fields' => array_sum(array_map('count', $validationData)),
                'categories' => array_keys($validationData)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Autosave validation failed', [
                'usulan_id' => $usulan->id,
                'errors' => $e->errors(),
                'input' => $request->input('validation', [])
            ]);

            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Autosave error: ' . $e->getMessage(), [
                'usulan_id' => $usulan->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Save failed'], 500);
        }
    }

    /**
     * NEW: Process validation fields dengan data dari helper
     */
    private function processValidationFieldsForView(Usulan $usulan): array
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);
        $validationFields = Usulan::getValidationFieldsWithDynamicBkd($usulan);
        $processedFields = [];

        foreach ($validationFields as $category => $fields) {
            $processedFields[$category] = [];

            foreach ($fields as $field) {
                // Get processed data from helper
                $fieldValue = $fieldHelper->getFieldValue($category, $field);

                $processedFields[$category][$field] = [
                    'raw_value' => $fieldValue,
                    'display_value' => $fieldValue,
                    'has_link' => $this->checkIfFieldHasLink($fieldValue),
                    'field_type' => $this->determineFieldType($category, $field)
                ];
            }
        }

        return $processedFields;
    }

    /**
     * NEW: Process dokumen data untuk ditampilkan di view
     */
    private function processDokumenDataForView(Usulan $usulan): array
    {
        $fieldHelper = new \App\Helpers\UsulanFieldHelper($usulan);

        return [
            'dokumen_profil' => $this->processDokumenProfil($usulan, $fieldHelper),
            'dokumen_usulan' => $this->processDokumenUsulan($usulan, $fieldHelper),
            'dokumen_bkd' => $this->processDokumenBkd($usulan, $fieldHelper),
            'dokumen_pendukung' => $this->processDokumenPendukung($usulan, $fieldHelper)
        ];
    }

    /**
     * SAFE: Process dokumen profil dengan override routing untuk Admin Fakultas
     */
    private function processDokumenProfil(Usulan $usulan, $fieldHelper): array
    {
        $profilFields = [
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
            'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'pak_konversi', 'sk_cpns', 'sk_pns', 'sk_penyetaraan_ijazah',
            'disertasi_thesis_terakhir'
        ];

        $processed = [];
        foreach ($profilFields as $field) {
            // Ambil raw data dari pegawai (TANPA helper routing)
            $dokumenPath = $usulan->pegawai->{$field} ?? null;

            if (!empty($dokumenPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-pegawai-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-500">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => ucwords(str_replace('_', ' ', $field)),
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }


    /**
     * NEW: Process dokumen usulan
     */
    private function processDokumenUsulan(Usulan $usulan, $fieldHelper): array
    {
        $usulanFields = [
            'pakta_integritas', 'bukti_korespondensi', 'turnitin',
            'upload_artikel', 'bukti_syarat_guru_besar'
        ];

        $processed = [];
        foreach ($usulanFields as $field) {
            // Ambil raw path dari model (TANPA helper routing)
            $docPath = $usulan->getDocumentPath($field);

            if (!empty($docPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-600">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => ucwords(str_replace('_', ' ', $field)),
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

      /**
     * NEW: Process dokumen BKD
     */
    private function processDokumenBkd(Usulan $usulan, $fieldHelper): array
    {
        $bkdLabels = $usulan->getBkdDisplayLabels();
        $processed = [];

        foreach ($bkdLabels as $field => $label) {
            // Ambil raw path dari model (TANPA helper routing)
            $docPath = $usulan->getDocumentPath($field);

            // Handle legacy BKD mapping jika diperlukan
            if (empty($docPath) && str_starts_with($field, 'bkd_semester_')) {
                $docPath = $this->findLegacyBkdPath($usulan, $field, $label);
            }

            if (!empty($docPath)) {
                // Generate route khusus untuk Admin Fakultas
                $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $field]);
                $linkHtml = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center gap-1">✓ Lihat Dokumen</a>';
                $hasDocument = true;
                $status = 'available';
            } else {
                $linkHtml = '<span class="text-red-600">✗ Belum diunggah</span>';
                $hasDocument = false;
                $status = 'missing';
            }

            $processed[$field] = [
                'label' => $label,
                'link_html' => $linkHtml,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

    /**
     * NEW: Process dokumen pendukung (dari admin fakultas)
     */
    private function processDokumenPendukung(Usulan $usulan, $fieldHelper): array
    {
        $pendukungFields = [
            'nomor_surat_usulan', 'file_surat_usulan',
            'nomor_berita_senat', 'file_berita_senat'
        ];

        $processed = [];
        foreach ($pendukungFields as $field) {
            if (str_starts_with($field, 'file_')) {
                // Handle file fields dengan override routing
                $validasiData = $usulan->validasi_data['admin_fakultas']['dokumen_pendukung'] ?? [];
                $pathKey = $field . '_path';
                $path = $validasiData[$pathKey] ?? $validasiData[$field] ?? null;

                // Handle array path
                if (is_array($path)) {
                    $path = $path['path'] ?? $path['value'] ?? $path[0] ?? null;
                }

                if (!empty($path) && \Storage::disk('public')->exists($path)) {
                    $route = route('admin-fakultas.usulan.show-dokumen-pendukung', [$usulan->id, $field]);
                    $value = '<a href="' . $route . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">✓ Lihat Dokumen</a>';
                    $hasDocument = true;
                    $status = 'available';
                } else {
                    $value = '<span class="text-red-600">✗ Belum diunggah</span>';
                    $hasDocument = false;
                    $status = 'missing';
                }
            } else {
                // Handle text fields - bisa pakai helper karena tidak ada routing
                $value = $fieldHelper->getFieldValue('dokumen_pendukung', $field);
                $hasDocument = false;
                $status = (trim($value) !== '' && $value !== '-') ? 'filled' : 'empty';
            }

            $processed[$field] = [
                'label' => $this->getDokumenPendukungLabel($field),
                'value' => $value,
                'has_link' => strpos($value, '<a href=') !== false,
                'has_document' => $hasDocument,
                'status' => $status
            ];
        }

        return $processed;
    }

    /**
     * SAFE: Helper untuk find legacy BKD path (extracted from helper logic)
     */
    private function findLegacyBkdPath(Usulan $usulan, string $field, string $label): ?string
    {
        if (!str_starts_with($field, 'bkd_semester_')) {
            return null;
        }

        $num = (int) str_replace('bkd_semester_', '', $field);
        if ($num < 1 || $num > 4) {
            return null;
        }

        // Parse label untuk mendapatkan semester dan tahun
        if (preg_match('/BKD\s+Semester\s+(Ganjil|Genap)\s+(\d{4})\/(\d{4})/i', $label, $m)) {
            $sem = strtolower($m[1]); // ganjil|genap
            $y1  = $m[2];
            $y2  = $m[3];

            // Coba exact legacy key
            $legacyKey = 'bkd_' . $sem . '_' . $y1 . '_' . $y2;
            $docPath = $usulan->getDocumentPath($legacyKey);

            if (!empty($docPath)) {
                return $docPath;
            }

            // Scan semua key BKD di data usulan
            $dokumenUsulan = $usulan->data_usulan['dokumen_usulan'] ?? [];
            foreach ($dokumenUsulan as $k => $info) {
                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                        return is_array($info) ? ($info['path'] ?? null) : $info;
                    }
                }
            }

            // Fallback ke struktur lama
            $dataUsulan = $usulan->data_usulan ?? [];
            foreach ($dataUsulan as $k => $info) {
                if (preg_match('/^bkd_(ganjil|genap)_(\d{4})_(\d{4})$/i', (string) $k, $mm)) {
                    if (strtolower($mm[1]) === $sem && $mm[2] === $y1 && $mm[3] === $y2) {
                        return is_array($info) ? ($info['path'] ?? null) : $info;
                    }
                }
            }
        }

        return null;
    }

    /**
     * NEW: Helper untuk label dokumen pendukung
     */
    private function getDokumenPendukungLabel(string $field): string
    {
        $labels = [
            'nomor_surat_usulan' => 'Nomor Surat Usulan Fakultas',
            'file_surat_usulan' => 'Dokumen Surat Usulan Fakultas',
            'nomor_berita_senat' => 'Nomor Berita Senat',
            'file_berita_senat' => 'Dokumen Berita Senat'
        ];

        return $labels[$field] ?? ucwords(str_replace('_', ' ', $field));
    }

    /**
     * NEW: Helper untuk status dokumen pendukung
     */
    private function getDokumenPendukungStatus(string $value): string
    {
        if (strpos($value, '✓ Lihat Dokumen') !== false) {
            return 'available';
        } elseif (strpos($value, '✗ Belum diunggah') !== false || strpos($value, '✗ File tidak ditemukan') !== false) {
            return 'missing';
        } elseif (trim($value) !== '' && $value !== '-') {
            return 'filled';
        }

        return 'empty';
    }

    /**
     * NEW: Check if field value contains link
     */
    private function checkIfFieldHasLink(string $fieldValue): bool
    {
        return strpos($fieldValue, '<a href=') !== false;
    }

    /**
     * NEW: Determine field type for display
     */
    private function determineFieldType(string $category, string $field): string
    {
        if (in_array($category, ['dokumen_profil', 'dokumen_usulan', 'dokumen_bkd'])) {
            return 'document_link';
        } elseif ($category === 'dokumen_pendukung') {
            return str_contains($field, 'file_') ? 'document_file' : 'text_field';
        } elseif (str_contains($field, 'link_')) {
            return 'external_link';
        }

        return 'text_field';
    }

    /**
     * Determine file disk based on field type
     */
    private function getFileDisk($field): string
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'pak_integrasi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        return in_array($field, $sensitiveFiles) ? 'local' : 'public';
    }

    /**
     * Clear cache for usulan
     */
    private function clearUsulanCache(Usulan $usulan): void
    {
        $cacheKeys = [
            "validation_fields_{$usulan->id}",
            "validation_fields_{$usulan->id}_admin_fakultas",
            "bkd_labels_{$usulan->id}",
            "existing_validation_{$usulan->id}_admin_fakultas",
            "dokumen_data_{$usulan->id}",
            "usulan_fakultas_{$usulan->id}",
            "usulan_{$usulan->id}",
            "usulan_validation_{$usulan->id}_admin_fakultas"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Log cache clearing
        \Log::info('Usulan cache cleared', [
            'usulan_id' => $usulan->id,
            'cache_keys' => $cacheKeys
        ]);
    }

    /**
     * Clear admin cache
     */
    private function clearAdminCache(int $adminId): void
    {
        Cache::forget("admin_fakultas_id_{$adminId}");
    }


}
