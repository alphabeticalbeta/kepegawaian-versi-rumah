<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfilController extends Controller
{
    /**
     * Display visi and misi page
     *
     * @return View
     */
    public function visiMisi(): View
    {
        try {
            // Get visi misi data from database
            $visiMisi = \App\Models\VisiMisi::where('status', 'aktif')
                ->orderBy('jenis', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Separate visi and misi
            $visi = $visiMisi->where('jenis', 'visi')->first();
            $misi = $visiMisi->where('jenis', 'misi')->first();

            return view('frontend.layouts.profil.visi-misi', [
                'visi' => $visi,
                'misi' => $misi,
                'visiMisi' => $visiMisi
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ProfilController visiMisi:', ['error' => $e->getMessage()]);

            return view('frontend.layouts.profil.visi-misi', [
                'visi' => null,
                'misi' => null,
                'visiMisi' => collect(),
                'error' => 'Terjadi kesalahan saat memuat data visi dan misi'
            ]);
        }
    }

    /**
     * Display struktur organisasi page
     *
     * @return View
     */
    public function strukturOrganisasi(): View
    {
        try {
            // Get current struktur organisasi data
            $strukturData = $this->getCurrentStrukturData();

            return view('frontend.layouts.profil.struktur-organisasi', [
                'strukturData' => $strukturData
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ProfilController strukturOrganisasi:', ['error' => $e->getMessage()]);

            return view('frontend.layouts.profil.struktur-organisasi', [
                'strukturData' => null,
                'error' => 'Terjadi kesalahan saat memuat data struktur organisasi'
            ]);
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
