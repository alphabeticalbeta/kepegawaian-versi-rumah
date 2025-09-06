<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Usulan;
use App\Models\KepegawaianUniversitas\PeriodeUsulan;
use App\Models\KepegawaianUniversitas\UsulanLog;
use App\Models\KepegawaianUniversitas\UsulanDokumen;
use App\Models\KepegawaianUniversitas\Pangkat;
use App\Services\PangkatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UsulanKepangkatanController extends Controller
{
    protected $pangkatService;

    public function __construct(PangkatService $pangkatService)
    {
        $this->pangkatService = $pangkatService;
    }

    /**
     * Display a listing of usulan for current user
     */
    public function index()
    {
        $pegawai = Auth::user();

        // Determine jenis usulan berdasarkan status kepegawaian
        $jenisUsulanPeriode = $this->determineJenisUsulanPeriode($pegawai);


        // Get periode usulan yang sesuai dengan status kepegawaian
        $periodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
            ->where('status', 'Buka')
            ->whereJsonContains('status_kepegawaian', $pegawai->status_kepegawaian)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Debug logging untuk troubleshooting
        $allPeriods = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)->get();
        Log::info('Kepangkatan Access Check', [
            'pegawai_id' => $pegawai->id,
            'status_kepegawaian' => $pegawai->status_kepegawaian,
            'jenis_usulan_periode' => $jenisUsulanPeriode,
            'periode_count' => $periodeUsulans->count(),
            'all_periods_count' => $allPeriods->count(),
            'all_periods' => $allPeriods->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama_periode' => $p->nama_periode,
                    'status_kepegawaian' => $p->status_kepegawaian,
                    'status' => $p->status
                ];
            }),
            'periode_details' => $periodeUsulans->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama_periode' => $p->nama_periode,
                    'status_kepegawaian' => $p->status_kepegawaian,
                    'status' => $p->status
                ];
            })
        ]);

        // Alternative query if no results
        if ($periodeUsulans->count() == 0) {
            // Try without JSON contains
            $altPeriodeUsulans = PeriodeUsulan::where('jenis_usulan', $jenisUsulanPeriode)
                ->where('status', 'Buka')
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            Log::info('Kepangkatan Alternative Query', [
                'pegawai_id' => $pegawai->id,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'alt_periode_count' => $altPeriodeUsulans->count(),
                'alt_periods' => $altPeriodeUsulans->map(function($p) {
                    return [
                        'id' => $p->id,
                        'nama_periode' => $p->nama_periode,
                        'status_kepegawaian' => $p->status_kepegawaian,
                        'status' => $p->status
                    ];
                })
            ]);

            // Use alternative results if found
            if ($altPeriodeUsulans->count() > 0) {
                $periodeUsulans = $altPeriodeUsulans;
                Log::info('Kepangkatan Using Alternative Results', [
                    'pegawai_id' => $pegawai->id,
                    'final_periode_count' => $periodeUsulans->count()
                ]);
            } else {
                Log::warning('Kepangkatan Access Denied - No Available Periods', [
                    'pegawai_id' => $pegawai->id,
                    'status_kepegawaian' => $pegawai->status_kepegawaian,
                    'jenis_usulan_periode' => $jenisUsulanPeriode,
                    'all_periods_count' => $allPeriods->count(),
                    'all_periods' => $allPeriods->map(function($p) {
                        return [
                            'id' => $p->id,
                            'nama_periode' => $p->nama_periode,
                            'status_kepegawaian' => $p->status_kepegawaian,
                            'status' => $p->status
                        ];
                    })
                ]);
            }
        }

        // Get usulan yang sudah dibuat oleh pegawai
        $usulans = $pegawai->usulans()
                          ->where('jenis_usulan', $jenisUsulanPeriode)
                          ->with(['periodeUsulan'])
                          ->get();

        // Logika filter yang benar:
        // 1. Periode BUKA: Tampilkan semua (sesuai status kepegawaian)
        // 2. Periode TUTUP: Hanya tampilkan jika pegawai pernah submit usulan

        $periodeBuka = $periodeUsulans->where('status', 'Buka');
        $periodeTutup = $periodeUsulans->where('status', 'Tutup');

        // Get periode IDs yang pernah submit usulan (bukan draft)
        $periodeIdsWithSubmittedUsulan = $usulans->where('status_usulan', '!=', 'draft usulan')
                                                ->pluck('periode_usulan_id')
                                                ->toArray();

        // Filter periode tutup: hanya yang pernah submit usulan
        $periodeTutupWithUsulan = $periodeTutup->whereIn('id', $periodeIdsWithSubmittedUsulan);

        // Gabungkan hasil: periode buka + periode tutup yang pernah submit
        $periodeUsulans = $periodeBuka->merge($periodeTutupWithUsulan);

        // Get status kepegawaian dari pegawai
        $statusKepegawaian = $pegawai->status_kepegawaian ?? null;

        return view('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.index', compact('periodeUsulans', 'usulans', 'pegawai', 'statusKepegawaian'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pegawai = Auth::user();

        // Validate request
        $request->validate([
            'periode_id' => 'required|exists:periode_usulans,id',
            'jenis_usulan' => 'required|in:Dosen PNS,Jabatan Administrasi,Jabatan Fungsional Tertentu,Jabatan Struktural',
        ]);

        // Get periode usulan
        $periodeUsulan = PeriodeUsulan::findOrFail($request->periode_id);

        // Validate if periode is open and accessible for this pegawai
        if ($periodeUsulan->status !== 'Buka') {
            Log::warning('Kepangkatan Store - Periode Closed', [
                'pegawai_id' => $pegawai->id,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'periode_id' => $periodeUsulan->id,
                'periode_status' => $periodeUsulan->status
            ]);
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Periode usulan sudah ditutup.');
        }

        if (!in_array($pegawai->status_kepegawaian, $periodeUsulan->status_kepegawaian ?? [])) {
            Log::warning('Kepangkatan Store - Access Denied', [
                'pegawai_id' => $pegawai->id,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'periode_id' => $periodeUsulan->id,
                'periode_status_kepegawaian' => $periodeUsulan->status_kepegawaian,
                'periode_status' => $periodeUsulan->status
            ]);
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Anda tidak memiliki akses untuk periode ini.');
        }

        // Check if pegawai already has active usulan for this periode
        $existingUsulan = $pegawai->usulans()
                                 ->where('periode_usulan_id', $request->periode_id)
                                 ->where('jenis_usulan', 'usulan-kepangkatan')
                                 ->first();

        if ($existingUsulan) {
            Log::info('Kepangkatan Store - Existing Usulan Found', [
                'pegawai_id' => $pegawai->id,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'periode_id' => $periodeUsulan->id,
                'existing_usulan_id' => $existingUsulan->id
            ]);
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.create-kepangkatan', $existingUsulan->id)
                             ->with('info', 'Anda sudah memiliki usulan untuk periode ini.');
        }

        try {
            // Create usulan dengan jenis yang dipilih
            $usulan = Usulan::create([
                'pegawai_id' => $pegawai->id,
                'periode_usulan_id' => $request->periode_id,
                'jenis_usulan' => 'usulan-kepangkatan',
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'status_usulan' => Usulan::STATUS_DRAFT_USULAN,
                'data_usulan' => [
                    'jenis_usulan_pangkat' => $request->jenis_usulan,
                    'dokumen_usulan' => []
                ]
            ]);

            Log::info('Kepangkatan Store - Usulan Created', [
                'pegawai_id' => $pegawai->id,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'periode_id' => $periodeUsulan->id,
                'usulan_id' => $usulan->id,
                'jenis_usulan_pangkat' => $request->jenis_usulan
            ]);

            // Log aktivitas
            UsulanLog::create([
                'usulan_id' => $usulan->id,
                'dilakukan_oleh_id' => $pegawai->id,
                'action' => 'created',
                'status_sebelumnya' => null,
                'status_baru' => Usulan::STATUS_DRAFT_USULAN,
                'keterangan' => 'Usulan kepangkatan dibuat dengan jenis: ' . $request->jenis_usulan,
                'catatan' => 'Usulan berhasil dibuat'
            ]);

            Log::info('Kepangkatan Store - Success', [
                'pegawai_id' => $pegawai->id,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'periode_id' => $periodeUsulan->id,
                'usulan_id' => $usulan->id,
                'jenis_usulan_pangkat' => $request->jenis_usulan
            ]);

            return redirect()->route('pegawai-unmul.usulan-kepangkatan.create-kepangkatan', $usulan->id)
                             ->with('success', 'Usulan Kepangkatan berhasil dibuat. Silakan lengkapi data dan dokumen pendukung.');

        } catch (\Exception $e) {
            Log::error('Kepangkatan Store - Failed to Create Usulan', [
                'pegawai_id' => $pegawai->id,
                'status_kepegawaian' => $pegawai->status_kepegawaian,
                'periode_id' => $request->periode_id,
                'jenis_usulan_pangkat' => $request->jenis_usulan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Terjadi kesalahan saat membuat usulan. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Load relasi yang diperlukan
        $usulan->load([
            'pegawai.unitKerja',
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'periodeUsulan',
            'pangkatTujuan'
        ]);

        // Get pegawai data for profile completeness check
        $pegawai = $usulan->pegawai;

        return view('backend.layouts.views.pegawai-unmul.usulan-kepangkatan.create-kepangkatan', compact('usulan', 'pegawai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Redirect ke halaman show karena pengeditan bisa dilakukan langsung di sana
        return redirect()->route('pegawai-unmul.usulan-kepangkatan.create-kepangkatan', $usulan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Pastikan usulan sudah tersimpan di database
        if (!$usulan->exists) {
            return back()->with('error', 'Usulan belum tersimpan di database. Silakan simpan usulan terlebih dahulu.');
        }

        $action = $request->input('action', 'simpan');

        switch ($action) {
            case 'simpan':
                return $this->simpanUsulan($request, $usulan);
            case 'kirim_ke_kepegawaian':
                return $this->kirimKeKepegawaian($request, $usulan);
            case 'kirim_perbaikan_ke_kepegawaian':
                return $this->kirimPerbaikanKeKepegawaian($request, $usulan);
            case 'kirim_perbaikan_bkn_ke_kepegawaian':
                return $this->kirimPerbaikanBknKeKepegawaian($request, $usulan);
            default:
                return $this->simpanUsulan($request, $usulan);
        }
    }

    /**
     * Simpan usulan tanpa mengubah status
     */
    private function simpanUsulan(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);

        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Handle document uploads
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);

        // Update usulan - hanya ubah status jika belum ada status atau masih Draft
        $updateData = [
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
        ];

        // Hanya ubah status ke Draft jika status saat ini null atau sudah Draft
        if (is_null($usulan->status_usulan) || $usulan->status_usulan === Usulan::STATUS_DRAFT_USULAN) {
            $updateData['status_usulan'] = Usulan::STATUS_DRAFT_USULAN;
        }
        // Jika status sudah ada (misalnya Permintaan Perbaikan), status TIDAK diubah

        $usulan->update($updateData);

        // Update dokumen_usulan in data_usulan
        $dataUsulan = $usulan->data_usulan;
        $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
        $usulan->update(['data_usulan' => $dataUsulan]);

        // Save documents to usulan_dokumens table
        $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.create-kepangkatan', $usulan)
            ->with('success', 'Usulan berhasil disimpan.');
    }

    /**
     * Kirim usulan ke Kepegawaian Universitas
     */
    private function kirimKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);

        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Check status - hanya bisa dikirim dari Draft Usulan
        if ($usulan->status_usulan !== Usulan::STATUS_DRAFT_USULAN && !is_null($usulan->status_usulan)) {
            return back()->with('error', 'Status usulan tidak valid untuk aksi ini. Hanya usulan dengan status Draft yang dapat dikirim.');
        }

        // Update usulan
        $usulan->update([
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
            'status_usulan' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Handle document uploads if any
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);
        if (!empty($dokumenPaths)) {
            // Update dokumen_usulan in data_usulan
            $dataUsulan = $usulan->data_usulan;
            $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
            $usulan->update(['data_usulan' => $dataUsulan]);

            // Save documents to usulan_dokumens table
            $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);
        }

        // Log aktivitas
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'dilakukan_oleh_id' => Auth::id(),
            'action' => 'submitted',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'status_baru' => Usulan::STATUS_USULAN_DIKIRIM_KE_KEPEGAWAIAN_UNIVERSITAS,
            'keterangan' => 'Usulan dikirim ke Kepegawaian Universitas',
            'catatan' => 'Usulan berhasil dikirim'
        ]);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.create-kepangkatan', $usulan)
            ->with('success', 'Usulan berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Kirim perbaikan usulan ke Kepegawaian Universitas
     */
    private function kirimPerbaikanKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);

        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Check status
        if ($usulan->status_usulan !== Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_KEPEGAWAIAN_UNIVERSITAS) {
            return back()->with('error', 'Status usulan tidak valid untuk aksi ini.');
        }

        // Update usulan
        $usulan->update([
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
            'status_usulan' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS
        ]);

        // Handle document uploads if any
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);
        if (!empty($dokumenPaths)) {
            // Update dokumen_usulan in data_usulan
            $dataUsulan = $usulan->data_usulan;
            $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
            $usulan->update(['data_usulan' => $dataUsulan]);

            // Save documents to usulan_dokumens table
            $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);
        }

        // Log aktivitas
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'dilakukan_oleh_id' => Auth::id(),
            'action' => 'submitted_revision',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'status_baru' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_KEPEGAWAIAN_UNIVERSITAS,
            'keterangan' => 'Perbaikan usulan dikirim ke Kepegawaian Universitas',
            'catatan' => 'Perbaikan usulan berhasil dikirim'
        ]);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.create-kepangkatan', $usulan)
            ->with('success', 'Perbaikan usulan berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Kirim perbaikan usulan dari BKN ke Kepegawaian Universitas
     */
    private function kirimPerbaikanBknKeKepegawaian(Request $request, Usulan $usulan)
    {
        // Validate request
        $request->validate([
            'pangkat_tujuan_id' => 'required|exists:pangkats,id',
        ]);

        // Check if pangkat tujuan is valid (higher hierarchy than current)
        $currentPangkat = $usulan->pegawai->pangkat;
        $pangkatTujuan = \App\Models\KepegawaianUniversitas\Pangkat::find($request->pangkat_tujuan_id);

        if (!$pangkatTujuan) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan tidak ditemukan.']);
        }

        if ($pangkatTujuan->hierarchy_level <= ($currentPangkat->hierarchy_level ?? 0)) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki level hierarki lebih tinggi dari pangkat saat ini.']);
        }

        if ($pangkatTujuan->status_pangkat !== ($currentPangkat->status_pangkat ?? 'PNS')) {
            return back()->withErrors(['pangkat_tujuan_id' => 'Pangkat tujuan harus memiliki status yang sama dengan pangkat saat ini.']);
        }

        // Check status
        if ($usulan->status_usulan !== Usulan::STATUS_PERMINTAAN_PERBAIKAN_KE_PEGAWAI_DARI_BKN) {
            return back()->with('error', 'Status usulan tidak valid untuk aksi ini.');
        }

        // Update usulan
        $usulan->update([
            'pangkat_tujuan_id' => $request->pangkat_tujuan_id,
            'status_usulan' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN
        ]);

        // Handle document uploads if any
        $dokumenPaths = $this->handleDocumentUploads($request, $usulan);
        if (!empty($dokumenPaths)) {
            // Update dokumen_usulan in data_usulan
            $dataUsulan = $usulan->data_usulan;
            $dataUsulan['dokumen_usulan'] = array_merge($dataUsulan['dokumen_usulan'] ?? [], $dokumenPaths);
            $usulan->update(['data_usulan' => $dataUsulan]);

            // Save documents to usulan_dokumens table
            $this->saveUsulanDocuments($usulan, $dokumenPaths, $usulan->pegawai);
        }

        // Log aktivitas
        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'dilakukan_oleh_id' => Auth::id(),
            'action' => 'submitted_bkn_revision',
            'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
            'status_baru' => Usulan::STATUS_USULAN_PERBAIKAN_DARI_PEGAWAI_KE_BKN,
            'keterangan' => 'Perbaikan usulan dari BKN dikirim ke Kepegawaian Universitas',
            'catatan' => 'Perbaikan usulan dari BKN berhasil dikirim'
        ]);

        return redirect()->route('pegawai-unmul.usulan-kepangkatan.create-kepangkatan', $usulan)
            ->with('success', 'Perbaikan usulan dari BKN berhasil dikirim ke Kepegawaian Universitas.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Pastikan usulan masih dalam status yang bisa dihapus
        $deletableStatuses = [
            'Draft Usulan',
        ];

        if (!in_array($usulan->status_usulan, $deletableStatuses) && !is_null($usulan->status_usulan)) {
            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Usulan tidak dapat dihapus karena sudah diproses.');
        }

        try {
            // Hapus dokumen usulan jika ada
            if (isset($usulan->data_usulan['dokumen_usulan'])) {
                foreach ($usulan->data_usulan['dokumen_usulan'] as $docType => $docData) {
                    if (isset($docData['path'])) {
                        Storage::disk('local')->delete($docData['path']);
                    }
                }
            }

            // Hapus dokumen dari tabel usulan_dokumens
            UsulanDokumen::where('usulan_id', $usulan->id)->delete();

            // Hapus usulan
            $usulan->delete();

            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('success', 'Usulan Kepangkatan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Failed to delete usulan kepangkatan', [
                'usulan_id' => $usulan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('pegawai-unmul.usulan-kepangkatan.index')
                             ->with('error', 'Gagal menghapus usulan. Silakan coba lagi.');
        }
    }

    /**
     * Handle document uploads for usulan kepangkatan
     */
    private function handleDocumentUploads($request, $usulan): array
    {
        $filePaths = [];
        $uploadPath = 'usulan-dokumen/' . $usulan->pegawai->id . '/' . date('Y/m');

        // Document keys for usulan kepangkatan
        $documentKeys = $this->getDocumentKeys($usulan);

        foreach ($documentKeys as $key) {
            if ($request->hasFile($key)) {
                try {
                    $file = $request->file($key);

                    // Validate file
                    $this->validateUploadedFile($file, $key);

                    // Upload file
                    $path = $file->store($uploadPath, 'local');

                    $filePaths[$key] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                        'uploaded_by' => $usulan->pegawai->id,
                    ];


                } catch (\Throwable $e) {
                    Log::error("Failed to upload document", [
                        'document_key' => $key,
                        'error' => $e->getMessage(),
                        'pegawai_id' => $usulan->pegawai->id
                    ]);
                    throw new \RuntimeException("Gagal mengupload dokumen $key: " . $e->getMessage());
                }
            }
        }

        return $filePaths;
    }

    /**
     * Get document keys for usulan kepangkatan
     */
    private function getDocumentKeys($usulan): array
    {
        $jenisUsulanPangkat = $usulan->data_usulan['jenis_usulan_pangkat'] ?? '';

        $baseKeys = [];

        if ($jenisUsulanPangkat === 'Dosen PNS') {
            $baseKeys = ['dokumen_ukom_sk_jabatan'];
        } elseif ($jenisUsulanPangkat === 'Jabatan Administrasi') {
            $baseKeys = ['surat_pencantuman_gelar', 'surat_lulus_ujian_dinas'];
        } elseif ($jenisUsulanPangkat === 'Jabatan Fungsional Tertentu') {
            $baseKeys = ['dokumen_uji_kompetensi'];
        } elseif ($jenisUsulanPangkat === 'Jabatan Struktural') {
            $baseKeys = ['surat_pelantikan_berita_acara', 'surat_pencantuman_gelar', 'sertifikat_diklat_pim_pkm'];
        }

        return $baseKeys;
    }

    /**
     * Validate uploaded file
     */
    private function validateUploadedFile($file, string $key): void
    {
        // Check file size (1MB max)
        $maxSize = 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            throw new \RuntimeException("File $key terlalu besar. Maksimal 1MB.");
        }

        // Check file type
        $allowedMimes = ['application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \RuntimeException("File $key harus berformat PDF.");
        }

        // Check file signature for PDF
        $handle = fopen($file->getRealPath(), 'r');
        $header = fread($handle, 4);
        fclose($handle);

        if ($header !== '%PDF') {
            throw new \RuntimeException("File $key bukan file PDF yang valid.");
        }

        // Check if file is readable
        if (!is_readable($file->getRealPath())) {
            throw new \RuntimeException("File $key tidak dapat dibaca.");
        }
    }

    /**
     * Simpan dokumen ke tabel usulan_dokumens
     */
    private function saveUsulanDocuments($usulan, array $dokumenPaths, $pegawai): void
    {
        foreach ($dokumenPaths as $nama => $fileData) {
            UsulanDokumen::create([
                'usulan_id' => $usulan->id,
                'diupload_oleh_id' => $pegawai->id,
                'nama_dokumen' => $nama,
                'path' => $fileData['path'],
            ]);
        }
    }

    /**
     * Show document for usulan kepangkatan
     */
    public function showDocument(Usulan $usulanKepangkatan, $field)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulanKepangkatan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Get document path from data_usulan
        $dokumenUsulan = $usulanKepangkatan->data_usulan['dokumen_usulan'] ?? [];
        $documentData = $dokumenUsulan[$field] ?? null;

        if (!$documentData || !isset($documentData['path'])) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        $path = $documentData['path'];

        // Check if file exists
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Check if this is a download request
        if (request()->has('download') && request()->get('download') == '1') {
            $filename = basename($path);
            return Storage::disk('local')->download($path, $filename);
        }

        // Return file for viewing
        return Storage::disk('local')->response($path);
    }
    /**
     * Determine jenis usulan untuk periode
     */
    protected function determineJenisUsulanPeriode($pegawai): string
    {
        $jenisUsulan = 'usulan-kepangkatan';
        Log::info('determineJenisUsulanPeriode - Kepangkatan', [
            'pegawai_id' => $pegawai->id,
            'status_kepegawaian' => $pegawai->status_kepegawaian,
            'jenis_usulan' => $jenisUsulan
        ]);
        return $jenisUsulan;
    }

    // Method getLogs dihapus - sudah digabung ke UsulanPegawaiController
}
