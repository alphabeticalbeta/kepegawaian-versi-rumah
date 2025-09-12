<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StrukturOrganisasiController extends Controller
{
    /**
     * Display the struktur organisasi management page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get current struktur organisasi data
            $strukturData = $this->getCurrentStrukturData();

            return view('backend.layouts.views.admin-universitas.struktur-organisasi', [
                'strukturData' => $strukturData
            ]);

        } catch (\Exception $e) {
            Log::error('Error in StrukturOrganisasiController index:', ['error' => $e->getMessage()]);

            return view('backend.layouts.views.admin-universitas.struktur-organisasi', [
                'strukturData' => null,
                'error' => 'Terjadi kesalahan saat memuat data struktur organisasi'
            ]);
        }
    }

    /**
     * Store/Update struktur organisasi image
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'struktur_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'description' => 'nullable|string|max:500'
            ]);

            // Handle file upload
            if ($request->hasFile('struktur_image')) {
                $file = $request->file('struktur_image');
                $filename = 'struktur-organisasi-' . time() . '.' . $file->getClientOriginalExtension();

                // Ensure directory exists
                $directory = 'struktur-organisasi';
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Store file in public storage
                $path = $file->storeAs($directory, $filename, 'public');

                Log::info('Struktur organisasi image uploaded successfully:', [
                    'filename' => $filename,
                    'path' => $path,
                    'description' => $request->input('description')
                ]);

                return redirect()->route('admin-universitas.struktur-organisasi.index')
                    ->with('success', 'Gambar struktur organisasi berhasil diupload');
            }

            return redirect()->route('admin-universitas.struktur-organisasi.index')
                ->with('error', 'File tidak ditemukan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin-universitas.struktur-organisasi.index')
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error in StrukturOrganisasiController store:', ['error' => $e->getMessage()]);

            return redirect()->route('admin-universitas.struktur-organisasi.index')
                ->with('error', 'Terjadi kesalahan saat mengupload gambar: ' . $e->getMessage());
        }
    }

    /**
     * Delete struktur organisasi image
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        try {
            // Get current file
            $directory = 'struktur-organisasi';
            $files = Storage::disk('public')->files($directory);

            if (!empty($files)) {
                // Get the most recent file
                $latestFile = collect($files)->sortByDesc(function ($file) {
                    return Storage::disk('public')->lastModified($file);
                })->first();

                // Delete the file
                Storage::disk('public')->delete($latestFile);

                Log::info('Struktur organisasi image deleted:', ['file' => $latestFile]);

                return redirect()->route('admin-universitas.struktur-organisasi.index')
                    ->with('success', 'Gambar struktur organisasi berhasil dihapus');
            }

            return redirect()->route('admin-universitas.struktur-organisasi.index')
                ->with('error', 'Tidak ada gambar yang dapat dihapus');

        } catch (\Exception $e) {
            Log::error('Error in StrukturOrganisasiController destroy:', ['error' => $e->getMessage()]);

            return redirect()->route('admin-universitas.struktur-organisasi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Get current struktur organisasi data
     *
     * @return array|null
     */
    private function getCurrentStrukturData()
    {
        try {
            $directory = 'struktur-organisasi';
            $files = Storage::disk('public')->files($directory);

            if (empty($files)) {
                return null;
            }

            // Get the most recent file
            $latestFile = collect($files)->sortByDesc(function ($file) {
                return Storage::disk('public')->lastModified($file);
            })->first();

            $filename = basename($latestFile);
            $imageUrl = '/storage/' . $latestFile;

            return [
                'id' => 1,
                'image_url' => $imageUrl,
                'description' => 'Struktur organisasi Universitas Mulawarman',
                'filename' => $filename,
                'path' => $latestFile,
                'created_at' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($latestFile)),
                'updated_at' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($latestFile))
            ];

        } catch (\Exception $e) {
            Log::error('Error getting current struktur data:', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
