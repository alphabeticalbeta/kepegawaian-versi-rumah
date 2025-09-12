<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\AplikasiKepegawaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AplikasiKepegawaianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Build query with filters - mengikuti pola data pegawai
        $query = AplikasiKepegawaian::query()
            ->when($request->search, function ($q, $search) {
                return $q->where(function($query) use ($search) {
                    $query->where('nama_aplikasi', 'like', "%{$search}%")
                          ->orWhere('sumber', 'like', "%{$search}%")
                          ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->latest();

        // Apply pagination dengan query string preservation
        $aplikasis = $query->paginate(10)->withQueryString();

        return view('backend.layouts.views.admin-universitas.aplikasi-kepegawaian', compact('aplikasis'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'nama_aplikasi' => 'required|string|max:255',
                'sumber' => 'required|string|max:255',
                'keterangan' => 'required|string|max:1000',
                'link' => 'required|url|max:500',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);

            // Sanitize input
            $data = [
                'nama_aplikasi' => strip_tags(trim($validated['nama_aplikasi'])),
                'sumber' => strip_tags(trim($validated['sumber'])),
                'keterangan' => strip_tags(trim($validated['keterangan'])),
                'link' => filter_var($validated['link'], FILTER_SANITIZE_URL),
                'status' => $validated['status']
            ];

            // Save to database
            AplikasiKepegawaian::create($data);

            return redirect()->route('admin-universitas.aplikasi-kepegawaian.index')
                           ->with('success', 'Data aplikasi kepegawaian berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('Error saving aplikasi kepegawaian data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'nama_aplikasi' => 'required|string|max:255',
                'sumber' => 'required|string|max:255',
                'keterangan' => 'required|string|max:1000',
                'link' => 'required|url|max:500',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);

            // Find and update in database
            $aplikasi = AplikasiKepegawaian::findOrFail($id);
            $aplikasi->update([
                'nama_aplikasi' => strip_tags(trim($validated['nama_aplikasi'])),
                'sumber' => strip_tags(trim($validated['sumber'])),
                'keterangan' => strip_tags(trim($validated['keterangan'])),
                'link' => filter_var($validated['link'], FILTER_SANITIZE_URL),
                'status' => $validated['status']
            ]);

            return redirect()->route('admin-universitas.aplikasi-kepegawaian.index')
                           ->with('success', 'Data aplikasi kepegawaian berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error updating aplikasi kepegawaian data', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Find and delete from database
            $aplikasi = AplikasiKepegawaian::findOrFail($id);
            $aplikasi->delete();

            return redirect()->route('admin-universitas.aplikasi-kepegawaian.index')
                           ->with('success', 'Data aplikasi kepegawaian berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting aplikasi kepegawaian data', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
