<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class UsulanValidationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get usulans that are ready for university validation
        // Include usulans that were returned for revision but can be processed again
        $usulans = Usulan::whereIn('status_usulan', [
                'Diusulkan ke Universitas',
                'Perbaikan Usulan', // Include returned usulans
                'Sedang Direview'   // Include those being reviewed (can be returned)
            ])
            ->with(['pegawai.unitKerja.subUnitKerja.unitKerja', 'periodeUsulan'])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.admin-univ-usulan.usulan.validation-index', compact('usulans'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usulan = Usulan::with([
            'pegawai.unitKerja.subUnitKerja.unitKerja',
            'pegawai.pangkat',
            'pegawai.jabatan',
            'jabatanLama',
            'jabatanTujuan',
            'periodeUsulan'
        ])->findOrFail($id);

        // Check if usulan is in correct status for Admin Universitas
        $allowedStatuses = ['Diusulkan ke Universitas', 'Perbaikan Usulan', 'Sedang Direview'];
        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return redirect()->route('admin-univ-usulan.usulan.index')
                ->with('error', 'Usulan tidak dapat divalidasi karena status tidak sesuai.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('admin_universitas') ?? [];

        // Determine if Admin Universitas can edit (based on status)
        $canEdit = in_array($usulan->status_usulan, ['Diusulkan ke Universitas', 'Perbaikan Usulan', 'Sedang Direview']);

        // Get active penilais for selection
        $penilais = \App\Models\BackendUnivUsulan\Penilai::getActivePenilais();

        return view('backend.layouts.views.admin-univ-usulan.usulan.detail', compact('usulan', 'existingValidation', 'canEdit', 'penilais'));
    }

    /**
     * Save validation data.
     */
    public function saveValidation(Request $request, $id)
    {
        $usulan = Usulan::findOrFail($id);

        $actionType = $request->input('action_type');

        // Check if usulan is in correct status for the action
        $allowedStatuses = ['Diusulkan ke Universitas'];

        // For return actions, also allow already processed usulans to be returned again
        if (in_array($actionType, ['return_to_pegawai', 'return_to_fakultas', 'forward_to_penilai', 'return_from_penilai'])) {
            $allowedStatuses[] = 'Perbaikan Usulan';
            $allowedStatuses[] = 'Sedang Direview';
        }

        if (!in_array($usulan->status_usulan, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat divalidasi karena status tidak sesuai.'
            ], 422);
        }

        try {
            if ($actionType === 'autosave') {
                return $this->autosaveValidation($request, $usulan);
            } elseif ($actionType === 'return_to_pegawai') {
                return $this->returnToPegawai($request, $usulan);
            } elseif ($actionType === 'return_to_fakultas') {
                return $this->returnToFakultas($request, $usulan);
            } elseif ($actionType === 'forward_to_penilai') {
                return $this->forwardToPenilai($request, $usulan);
            } elseif ($actionType === 'forward_to_senat') {
                return $this->forwardToSenat($request, $usulan);
            } elseif ($actionType === 'return_from_penilai') {
                return $this->returnFromPenilai($request, $usulan);
            } else {
                return $this->saveSimpleValidation($request, $usulan);
            }
        } catch (\Exception $e) {
            Log::error('Admin Universitas validation error', [
                'usulan_id' => $usulan->id,
                'action_type' => $actionType,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan validasi.'
            ], 500);
        }
    }

    /**
     * Auto-save validation data.
     */
    private function autosaveValidation(Request $request, Usulan $usulan)
    {
        $validationData = json_decode($request->input('validation_data'), true);

        // Save validation data using the model method
        $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Data validasi tersimpan otomatis.'
        ]);
    }

    /**
     * Save simple validation.
     */
    private function saveSimpleValidation(Request $request, Usulan $usulan)
    {
        $validationData = $request->input('validation');

        // Save validation data using the model method
        $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Data validasi berhasil disimpan.'
        ]);
    }

    /**
     * Return usulan to pegawai for revision.
     */
    private function returnToPegawai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Perbaikan Usulan';
        $usulan->catatan_verifikator = $request->input('catatan_umum');
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
            $usulan->save();
        }

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan ke Pegawai untuk perbaikan.',
            'redirect' => route('admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Forward usulan to Tim Penilai.
     */
    private function forwardToPenilai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000',
            'selected_penilais' => 'required|array|min:1',
            'selected_penilais.*' => 'exists:penilais,id'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Sedang Direview';

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Assign selected penilais to usulan
        $selectedPenilais = $request->input('selected_penilais');
        $usulan->penilais()->sync($selectedPenilais);

        // Add forward information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['forward_to_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_forward' => now()->toDateTimeString(),
            'admin_id' => Auth::id(),
            'selected_penilais' => $selectedPenilais
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => '🎉 Usulan berhasil diteruskan ke Tim Penilai! Status usulan telah berubah menjadi "Sedang Direview". Tim Penilai akan segera memproses usulan ini.',
            'redirect' => route('admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Return usulan to fakultas for revision.
     */
    private function returnToFakultas(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Perbaikan Usulan';
        $usulan->catatan_verifikator = $request->input('catatan_umum');
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            // If validation data is JSON string, decode it
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
            $usulan->save();
        }

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan ke Admin Fakultas untuk perbaikan.',
            'redirect' => route('admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Forward usulan to Tim Senat.
     */
    private function forwardToSenat(Request $request, Usulan $usulan)
    {
        // Check if Tim Penilai has given recommendation
        $hasRecommendation = $usulan->validasi_data['tim_penilai']['recommendation'] ?? false;
        if ($hasRecommendation !== 'direkomendasikan') {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat diteruskan ke senat karena belum ada rekomendasi dari tim penilai.'
            ], 422);
        }

        $request->validate([
            'catatan_umum' => 'nullable|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Direkomendasikan';

        // Save validation data with forward note
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());

        // Add forward information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['forward_to_senat'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_forward' => now()->toDateTimeString(),
            'admin_id' => Auth::id()
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diteruskan ke Tim Senat.',
            'redirect' => route('admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Return usulan from Tim Penilai back to Admin Universitas.
     */
    private function returnFromPenilai(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_umum' => 'required|string|max:1000'
        ]);

        // Update usulan status back to 'Diusulkan ke Universitas'
        $usulan->status_usulan = 'Diusulkan ke Universitas';

        // Save validation data
        $validationData = $request->input('validation');
        if ($validationData) {
            if (is_string($validationData)) {
                $validationData = json_decode($validationData, true);
            }
            $usulan->setValidasiByRole('admin_universitas', $validationData, Auth::id());
        }

        // Add return information to validation data
        $currentValidasi = $usulan->validasi_data;
        $currentValidasi['admin_universitas']['return_from_penilai'] = [
            'catatan' => $request->input('catatan_umum'),
            'tanggal_return' => now()->toDateTimeString(),
            'admin_id' => Auth::id()
        ];
        $usulan->validasi_data = $currentValidasi;
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_admin_universitas";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil dikembalikan dari Tim Penilai ke Admin Universitas.',
            'redirect' => route('admin-univ-usulan.usulan.index')
        ]);
    }

    /**
     * Show document.
     */
    public function showDocument($usulanId, $field)
    {
        $usulan = Usulan::findOrFail($usulanId);

        // Get document path based on field
        $docPath = $usulan->getDocumentPath($field);

        if (!$docPath || !file_exists(storage_path('app/' . $docPath))) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return response()->file(storage_path('app/' . $docPath));
    }

    /**
     * Show pegawai document.
     */
    public function showPegawaiDocument($usulanId, $field)
    {
        $usulan = Usulan::with('pegawai')->findOrFail($usulanId);

        // Get pegawai document path
        $docPath = $usulan->pegawai->$field ?? null;

        if (!$docPath || !file_exists(storage_path('app/' . $docPath))) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return response()->file(storage_path('app/' . $docPath));
    }
}
