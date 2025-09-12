<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\VisiMisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisiMisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get all visi misi data for display
            $visiMisi = VisiMisi::orderBy('jenis', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();


            // Separate visi and misi
            $visi = $visiMisi->where('jenis', 'visi');
            $misi = $visiMisi->where('jenis', 'misi');

            return view('backend.layouts.views.admin-universitas.visi-misi', [
                'user' => Auth::user(),
                'visiMisi' => $visiMisi,
                'visi' => $visi,
                'misi' => $misi
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading visi misi page', [
                'error' => $e->getMessage()
            ]);

            return view('backend.layouts.views.admin-universitas.visi-misi', [
                'user' => Auth::user(),
                'visiMisi' => collect(),
                'visi' => collect(),
                'misi' => collect()
            ])->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    /**
     * Get all visi misi data for API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData()
    {
        try {
            $visiMisi = VisiMisi::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $visiMisi
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching visi misi data from database', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.layouts.views.admin-universitas.visi-misi', [
            'user' => Auth::user(),
            'visiMisi' => VisiMisi::orderBy('jenis', 'asc')->orderBy('created_at', 'desc')->get(),
            'visi' => VisiMisi::where('jenis', 'visi')->orderBy('created_at', 'desc')->get(),
            'misi' => VisiMisi::where('jenis', 'misi')->orderBy('created_at', 'desc')->get(),
            'showCreateModal' => true
        ]);
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
            $request->validate([
                'jenis' => 'required|in:visi,misi',
                'konten' => 'required|string|max:5000',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'jenis.required' => 'Jenis visi/misi harus dipilih.',
                'jenis.in' => 'Jenis harus berupa visi atau misi.',
                'konten.required' => 'Konten tidak boleh kosong.',
                'konten.max' => 'Konten maksimal 5000 karakter.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus berupa aktif atau tidak aktif.'
            ]);

            // Save to database
            $visiMisi = VisiMisi::create([
                'jenis' => $request->jenis,
                'konten' => $request->konten,
                'status' => $request->status
            ]);

            Log::info('Visi Misi data saved to database', [
                'id' => $visiMisi->id,
                'jenis' => $request->jenis,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin-universitas.visi-misi.index')
                ->with('success', 'Data ' . ucfirst($request->jenis) . ' berhasil disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error saving visi misi data to database', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $visiMisi = VisiMisi::findOrFail($id);

            return view('backend.layouts.views.admin-universitas.visi-misi', [
                'user' => Auth::user(),
                'visiMisi' => VisiMisi::orderBy('jenis', 'asc')->orderBy('created_at', 'desc')->get(),
                'visi' => VisiMisi::where('jenis', 'visi')->orderBy('created_at', 'desc')->get(),
                'misi' => VisiMisi::where('jenis', 'misi')->orderBy('created_at', 'desc')->get(),
                'editingVisiMisi' => $visiMisi,
                'showEditModal' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading visi misi for edit', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin-universitas.visi-misi.index')
                ->with('error', 'Data tidak ditemukan.');
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
            $request->validate([
                'jenis' => 'required|in:visi,misi',
                'konten' => 'required|string|max:5000',
                'status' => 'required|in:aktif,tidak_aktif'
            ], [
                'jenis.required' => 'Jenis visi/misi harus dipilih.',
                'jenis.in' => 'Jenis harus berupa visi atau misi.',
                'konten.required' => 'Konten tidak boleh kosong.',
                'konten.max' => 'Konten maksimal 5000 karakter.',
                'status.required' => 'Status harus dipilih.',
                'status.in' => 'Status harus berupa aktif atau tidak aktif.'
            ]);

            // Find and update in database
            $visiMisi = VisiMisi::findOrFail($id);
            $visiMisi->update([
                'jenis' => $request->jenis,
                'konten' => $request->konten,
                'status' => $request->status
            ]);

            Log::info('Visi Misi data updated in database', [
                'id' => $visiMisi->id,
                'jenis' => $request->jenis,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin-universitas.visi-misi.index')
                ->with('success', 'Data ' . ucfirst($request->jenis) . ' berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating visi misi data in database', [
                'id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
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
            $visiMisi = VisiMisi::findOrFail($id);
            $jenis = $visiMisi->jenis;
            $visiMisi->delete();

            Log::info('Visi Misi data deleted from database', [
                'id' => $id,
                'jenis' => $jenis,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin-universitas.visi-misi.index')
                ->with('success', 'Data ' . ucfirst($jenis) . ' berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting visi misi data from database', [
                'id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin-universitas.visi-misi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $visiMisi = VisiMisi::findOrFail($id);

            return view('backend.layouts.views.admin-universitas.visi-misi', [
                'user' => Auth::user(),
                'visiMisi' => VisiMisi::orderBy('jenis', 'asc')->orderBy('created_at', 'desc')->get(),
                'visi' => VisiMisi::where('jenis', 'visi')->orderBy('created_at', 'desc')->get(),
                'misi' => VisiMisi::where('jenis', 'misi')->orderBy('created_at', 'desc')->get(),
                'viewingVisiMisi' => $visiMisi,
                'showViewModal' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading visi misi for view', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin-universitas.visi-misi.index')
                ->with('error', 'Data tidak ditemukan.');
        }
    }

    /**
     * Show delete confirmation page.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function delete($id)
    {
        try {
            $visiMisi = VisiMisi::findOrFail($id);

            return view('backend.layouts.views.admin-universitas.visi-misi', [
                'user' => Auth::user(),
                'visiMisi' => VisiMisi::orderBy('jenis', 'asc')->orderBy('created_at', 'desc')->get(),
                'visi' => VisiMisi::where('jenis', 'visi')->orderBy('created_at', 'desc')->get(),
                'misi' => VisiMisi::where('jenis', 'misi')->orderBy('created_at', 'desc')->get(),
                'deletingVisiMisi' => $visiMisi,
                'showDeleteModal' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading visi misi for delete', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin-universitas.visi-misi.index')
                ->with('error', 'Data tidak ditemukan.');
        }
    }
}
