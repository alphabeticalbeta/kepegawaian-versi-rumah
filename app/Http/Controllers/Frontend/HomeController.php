<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil 4 berita terbaru
        $berita = Informasi::berita()
            ->published()
            ->notExpired()
            ->byPriority()
            ->latest('tanggal_publish')
            ->limit(4)
            ->get();

        // Ambil 4 pengumuman terbaru
        $pengumuman = Informasi::pengumuman()
            ->published()
            ->notExpired()
            ->byPriority()
            ->latest('tanggal_publish')
            ->limit(4)
            ->get();

        // Ambil pengumuman featured untuk carousel
        $pengumumanFeatured = Informasi::pengumuman()
            ->published()
            ->notExpired()
            ->featured()
            ->latest('tanggal_publish')
            ->limit(5)
            ->get();

        // Ambil data struktur organisasi dari storage
        $strukturOrganisasi = $this->getCurrentStrukturData();

        return view('frontend.index', compact('berita', 'pengumuman', 'pengumumanFeatured', 'strukturOrganisasi'));
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
                'title' => 'Struktur Organisasi Universitas Mulawarman',
                'image_url' => $imageUrl,
                'description' => 'Struktur organisasi Universitas Mulawarman yang menunjukkan hierarki dan hubungan antar unit kerja.',
                'filename' => $filename,
                'path' => $latestFile,
                'created_at' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($latestFile)),
                'updated_at' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($latestFile))
            ];

        } catch (\Exception $e) {
            Log::error('Error getting current struktur data in HomeController:', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
