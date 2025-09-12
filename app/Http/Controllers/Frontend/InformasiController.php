<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class InformasiController extends Controller
{
    /**
     * Display a listing of berita.
     */
    public function berita(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'berita' type
            $query = Informasi::berita()
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_publish', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->published();
            }

            // Apply search filter
            if ($search) {
                $query->search($search);
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_publish', $year);
            }

            // Get paginated results
            $berita = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.informasi.berita', compact('berita'));

        } catch (\Exception $e) {
            \Log::error('Error in InformasiController@berita: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $berita = collect()->paginate(8);

            return view('frontend.layouts.informasi.berita', compact('berita'));
        }
    }

    /**
     * Display a listing of pengumuman.
     */
    public function pengumuman(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'pengumuman' type
            $query = Informasi::pengumuman()
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_publish', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->published()->notExpired();
            }

            // Apply search filter
            if ($search) {
                $query->search($search);
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_publish', $year);
            }

            // Get paginated results
            $pengumuman = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.informasi.pengumuman', compact('pengumuman'));

        } catch (\Exception $e) {
            \Log::error('Error in InformasiController@pengumuman: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $pengumuman = collect()->paginate(8);

            return view('frontend.layouts.informasi.pengumuman', compact('pengumuman'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Informasi $informasi): View
    {
        try {
            // Increment view count
            $informasi->incrementViewCount();

            return view('frontend.layouts.informasi.detail', compact('informasi'));

        } catch (\Exception $e) {
            \Log::error('Error in InformasiController@show: ' . $e->getMessage());

            abort(404, 'Informasi tidak ditemukan');
        }
    }

    /**
     * Download attachment file.
     */
    public function download(Informasi $informasi, string $filename)
    {
        try {
            // Decode filename if it's URL encoded
            $filename = urldecode($filename);

            // Get lampiran array
            $lampiran = $informasi->lampiran ?? [];

            // Find the file in lampiran
            $filePath = null;
            foreach ($lampiran as $file) {
                if (basename($file) === $filename) {
                    $filePath = $file;
                    break;
                }
            }

            if (!$filePath) {
                abort(404, 'File tidak ditemukan');
            }

            // Check if file exists in storage
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath, $filename);
            }

            // Try alternative paths
            $alternativePaths = [
                'private/public/' . $filePath,
                'public/' . $filePath,
                $filePath
            ];

            foreach ($alternativePaths as $altPath) {
                if (Storage::disk('public')->exists($altPath)) {
                    return Storage::disk('public')->download($altPath, $filename);
                }
            }

            abort(404, 'File tidak ditemukan di storage');

        } catch (\Exception $e) {
            \Log::error('Error in InformasiController@download: ' . $e->getMessage());
            abort(404, 'File tidak dapat diunduh');
        }
    }

    /**
     * Get search suggestions for autocomplete.
     */
    public function searchSuggestions(Request $request)
    {
        try {
            $search = $request->get('q', '');

            if (strlen($search) < 2) {
                return response()->json([]);
            }

            $suggestions = Informasi::berita()
                ->published()
                ->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%");
                })
                ->select('judul', 'id')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->judul,
                        'url' => route('berita.show', $item->id)
                    ];
                });

            return response()->json($suggestions);

        } catch (\Exception $e) {
            \Log::error('Error in InformasiController@searchSuggestions: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}
