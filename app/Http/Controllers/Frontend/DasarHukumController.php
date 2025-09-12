<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DasarHukum;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DasarHukumController extends Controller
{
    /**
     * Display a listing of keputusan with search and pagination
     */
    public function index(Request $request): View
    {
        try {
            // Build query with eager loading for better performance
            $query = DasarHukum::where('jenis_dasar_hukum', 'keputusan')
                ->where('status', 'published')
                ->orderBy('is_pinned', 'desc') // Pinned items first
                ->orderBy('is_featured', 'desc') // Featured items second
                ->orderBy('tanggal_dokumen', 'desc'); // Then by date

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('judul', 'like', "%{$searchTerm}%")
                  ->orWhere('konten', 'like', "%{$searchTerm}%")
                  ->orWhere('nomor_dokumen', 'like', "%{$searchTerm}%")
                  ->orWhere('penulis', 'like', "%{$searchTerm}%");
            });
        }

        // Apply additional filters
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_dokumen', $request->tahun);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('pinned')) {
            $query->where('is_pinned', true);
        }

        // Paginate with custom per page
        $perPage = $request->get('per_page', 8);
        $keputusan = $query->paginate($perPage)
            ->withQueryString(); // Preserve query parameters in pagination links

        // Get statistics for sidebar (optional)
        $stats = [
            'total' => DasarHukum::where('jenis_dasar_hukum', 'keputusan')->where('status', 'published')->count(),
            'featured' => DasarHukum::where('jenis_dasar_hukum', 'keputusan')->where('status', 'published')->where('is_featured', true)->count(),
            'pinned' => DasarHukum::where('jenis_dasar_hukum', 'keputusan')->where('status', 'published')->where('is_pinned', true)->count(),
            'this_year' => DasarHukum::where('jenis_dasar_hukum', 'keputusan')->where('status', 'published')->whereYear('tanggal_dokumen', date('Y'))->count(),
        ];

        // Get available years for filter
        $availableYears = DasarHukum::where('jenis_dasar_hukum', 'keputusan')
            ->where('status', 'published')
            ->selectRaw('YEAR(tanggal_dokumen) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

            return view('frontend.layouts.dasar-hukum.keputusan', compact(
                'keputusan',
                'stats',
                'availableYears'
            ));
        } catch (\Exception $e) {
            // Log error for debugging in Docker
            \Log::error('Error in DasarHukumController@index: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $keputusan = collect()->paginate(8);
            $stats = ['total' => 0, 'featured' => 0, 'pinned' => 0, 'this_year' => 0];
            $availableYears = collect();

            return view('frontend.layouts.dasar-hukum.keputusan', compact(
                'keputusan',
                'stats',
                'availableYears'
            ));
        }
    }

    /**
     * Display the specified keputusan
     */
    public function show(DasarHukum $keputusan): View
    {
        // Ensure it's a keputusan and published
        if ($keputusan->jenis_dasar_hukum !== 'keputusan' || $keputusan->status !== 'published') {
            abort(404);
        }

        // Get related keputusan (same year, different items)
        $relatedKeputusan = DasarHukum::where('jenis_dasar_hukum', 'keputusan')
            ->where('status', 'published')
            ->where('id', '!=', $keputusan->id)
            ->whereYear('tanggal_dokumen', $keputusan->tanggal_dokumen->year)
            ->orderBy('tanggal_dokumen', 'desc')
            ->limit(4)
            ->get();

        return view('frontend.layouts.dasar-hukum.keputusan-detail', compact(
            'keputusan',
            'relatedKeputusan'
        ));
    }

    /**
     * Download document file
     */
    public function download(DasarHukum $keputusan, string $filename)
    {
        // Ensure it's a keputusan and published
        if ($keputusan->jenis_dasar_hukum !== 'keputusan' || $keputusan->status !== 'published') {
            abort(404);
        }

        // Find the file in lampiran
        $lampiran = $keputusan->lampiran ?? [];
        $fileFound = false;
        $filePath = null;

        foreach ($lampiran as $file) {
            $fileInfo = is_array($file) ? $file : ['path' => $file, 'name' => basename($file)];
            if (basename($fileInfo['path']) === $filename) {
                $fileFound = true;
                $filePath = $fileInfo['path'];
                break;
            }
        }

        if (!$fileFound) {
            abort(404, 'File not found');
        }

        // Check if file exists in storage (Docker compatible)
        $fullPath = storage_path('app/public/' . $filePath);
        if (!file_exists($fullPath)) {
            // Try alternative path for Docker
            $altPath = public_path('storage/' . $filePath);
            if (!file_exists($altPath)) {
                abort(404, 'File not found on server');
            }
            $fullPath = $altPath;
        }

        return response()->download($fullPath, basename($filePath));
    }

    /**
     * Get search suggestions (for autocomplete - optional enhancement)
     */
    public function searchSuggestions(Request $request)
    {
        if (!$request->has('q') || strlen($request->q) < 2) {
            return response()->json([]);
        }

        $suggestions = DasarHukum::where('jenis_dasar_hukum', 'keputusan')
            ->where('status', 'published')
            ->where(function($q) use ($request) {
                $q->where('judul', 'like', "%{$request->q}%")
                  ->orWhere('nomor_dokumen', 'like', "%{$request->q}%");
            })
            ->select('judul', 'nomor_dokumen')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'text' => $item->judul,
                    'value' => $item->judul,
                    'subtext' => $item->nomor_dokumen
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Display a listing of peraturan.
     */
    public function peraturan(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'peraturan' type
            $query = DasarHukum::where('jenis_dasar_hukum', 'peraturan')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_dokumen', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->where('status', 'published');
            }

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%")
                      ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_dokumen', $year);
            }

            // Get paginated results
            $peraturan = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.dasar-hukum.peraturan', compact('peraturan'));

        } catch (\Exception $e) {
            \Log::error('Error in DasarHukumController@peraturan: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $peraturan = collect()->paginate(8);

            return view('frontend.layouts.dasar-hukum.peraturan', compact('peraturan'));
        }
    }

    /**
     * Display a listing of surat edaran.
     */
    public function suratEdaran(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'surat_edaran' type
            $query = DasarHukum::where('jenis_dasar_hukum', 'surat_edaran')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_dokumen', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->where('status', 'published');
            }

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%")
                      ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_dokumen', $year);
            }

            // Get paginated results
            $surat_edaran = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.dasar-hukum.surat-edaran', compact('surat_edaran'));

        } catch (\Exception $e) {
            \Log::error('Error in DasarHukumController@suratEdaran: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $surat_edaran = collect()->paginate(8);

            return view('frontend.layouts.dasar-hukum.surat-edaran', compact('surat_edaran'));
        }
    }

    /**
     * Display a listing of surat kementerian.
     */
    public function suratKementerian(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'surat_kementerian' type
            $query = DasarHukum::where('jenis_dasar_hukum', 'surat_kementerian')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_dokumen', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->where('status', 'published');
            }

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%")
                      ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_dokumen', $year);
            }

            // Get paginated results
            $surat_kementerian = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.dasar-hukum.surat-kementerian', compact('surat_kementerian'));

        } catch (\Exception $e) {
            \Log::error('Error in DasarHukumController@suratKementerian: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $surat_kementerian = collect()->paginate(8);

            return view('frontend.layouts.dasar-hukum.surat-kementerian', compact('surat_kementerian'));
        }
    }

    /**
     * Display a listing of surat rektor unmul.
     */
    public function suratRektorUnmul(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'surat_rektor' type
            $query = DasarHukum::where('jenis_dasar_hukum', 'surat_rektor')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_dokumen', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->where('status', 'published');
            }

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%")
                      ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_dokumen', $year);
            }

            // Get paginated results
            $surat_rektor_unmul = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.dasar-hukum.surat-rektor-unmul', compact('surat_rektor_unmul'));

        } catch (\Exception $e) {
            \Log::error('Error in DasarHukumController@suratRektorUnmul: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $surat_rektor_unmul = collect()->paginate(8);

            return view('frontend.layouts.dasar-hukum.surat-rektor-unmul', compact('surat_rektor_unmul'));
        }
    }

    /**
     * Display a listing of undang-undang.
     */
    public function undangUndang(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'undang_undang' type
            $query = DasarHukum::where('jenis_dasar_hukum', 'undang_undang')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_dokumen', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->where('status', 'published');
            }

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%")
                      ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_dokumen', $year);
            }

            // Get paginated results
            $undang_undang = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.dasar-hukum.undang-undang', compact('undang_undang'));

        } catch (\Exception $e) {
            \Log::error('Error in DasarHukumController@undangUndang: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $undang_undang = collect()->paginate(8);

            return view('frontend.layouts.dasar-hukum.undang-undang', compact('undang_undang'));
        }
    }

    /**
     * Display a listing of pedoman.
     */
    public function pedoman(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 8);
            $search = $request->get('search');
            $year = $request->get('tahun');
            $status = $request->get('status');

            // Build query for 'pedoman' type
            $query = DasarHukum::where('jenis_dasar_hukum', 'pedoman')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy('tanggal_dokumen', 'desc');

            // Apply status filter
            if ($status) {
                $query->where('status', $status);
            } else {
                // Default to published only
                $query->where('status', 'published');
            }

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%")
                      ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%");
                });
            }

            // Apply year filter
            if ($year) {
                $query->whereYear('tanggal_dokumen', $year);
            }

            // Get paginated results
            $pedoman = $query->paginate($perPage)->withQueryString();

            return view('frontend.layouts.dasar-hukum.pedoman', compact('pedoman'));

        } catch (\Exception $e) {
            \Log::error('Error in DasarHukumController@pedoman: ' . $e->getMessage());

            // Return empty data to prevent page crash
            $pedoman = collect()->paginate(12);

            return view('frontend.layouts.dasar-hukum.pedoman', compact('pedoman'));
        }
    }
}
