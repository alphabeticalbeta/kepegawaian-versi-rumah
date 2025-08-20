<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UsulanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get usulans that are ready for senat decision
        $usulans = Usulan::where('status_usulan', 'Direkomendasikan')
            ->with(['pegawai', 'periodeUsulan'])
            ->latest()
            ->paginate(10);

        return view('backend.layouts.views.tim-senat.usulan.index', compact('usulans'));
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
            'periodeUsulan'
        ])->findOrFail($id);

        // Check if usulan is in correct status for Tim Senat
        if ($usulan->status_usulan !== 'Direkomendasikan') {
            return redirect()->route('tim-senat.usulan.index')
                ->with('error', 'Usulan tidak dapat diproses karena status tidak sesuai.');
        }

        // Get existing validation data
        $existingValidation = $usulan->getValidasiByRole('tim_senat') ?? [];

        return view('backend.layouts.views.tim-senat.usulan.detail', compact('usulan', 'existingValidation'));
    }

    /**
     * Save validation data.
     */
    public function saveValidation(Request $request, $id)
    {
        $usulan = Usulan::findOrFail($id);

        // Check if usulan is in correct status
        if ($usulan->status_usulan !== 'Direkomendasikan') {
            return response()->json([
                'success' => false,
                'message' => 'Usulan tidak dapat diproses karena status tidak sesuai.'
            ], 422);
        }

        $actionType = $request->input('action_type');

        try {
            if ($actionType === 'autosave') {
                return $this->autosaveValidation($request, $usulan);
            } elseif ($actionType === 'tolak_usulan') {
                return $this->tolakUsulan($request, $usulan);
            } elseif ($actionType === 'setujui_usulan') {
                return $this->setujuiUsulan($request, $usulan);
            } else {
                return $this->saveSimpleValidation($request, $usulan);
            }
        } catch (\Exception $e) {
            Log::error('Tim Senat validation error', [
                'usulan_id' => $usulan->id,
                'action_type' => $actionType,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan keputusan.'
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
        $usulan->setValidasiByRole('tim_senat', $validationData, auth()->id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_senat";
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
        $usulan->setValidasiByRole('tim_senat', $validationData, auth()->id());
        $usulan->save();

        // Clear related caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_senat";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Data validasi berhasil disimpan.'
        ]);
    }

    /**
     * Reject usulan.
     */
    private function tolakUsulan(Request $request, Usulan $usulan)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Ditolak';
        $usulan->data_usulan['keputusan_senat'] = [
            'status' => 'Ditolak',
            'alasan' => $request->input('alasan_penolakan'),
            'tanggal_keputusan' => now()->toDateTimeString(),
            'senat_id' => auth()->id()
        ];
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('tim_senat', $validationData, auth()->id());
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_senat";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil ditolak.',
            'redirect' => route('tim-senat.usulan.index')
        ]);
    }

    /**
     * Approve usulan.
     */
    private function setujuiUsulan(Request $request, Usulan $usulan)
    {
        $request->validate([
            'catatan_persetujuan' => 'nullable|string|max:1000'
        ]);

        // Update usulan status
        $usulan->status_usulan = 'Disetujui';
        $usulan->data_usulan['keputusan_senat'] = [
            'status' => 'Disetujui',
            'catatan' => $request->input('catatan_persetujuan'),
            'tanggal_keputusan' => now()->toDateTimeString(),
            'senat_id' => auth()->id()
        ];
        $usulan->save();

        // Save validation data
        $validationData = $request->input('validation');
        $usulan->setValidasiByRole('tim_senat', $validationData, auth()->id());
        $usulan->save();

        // Clear caches
        $cacheKey = "usulan_validation_{$usulan->id}_tim_senat";
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil disetujui.',
            'redirect' => route('tim-senat.usulan.index')
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
