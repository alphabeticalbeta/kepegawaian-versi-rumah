<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InformasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Informasi::query();

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Featured/Pinned
        if ($request->filled('special')) {
            $special = $request->special;
            if ($special === 'featured') {
                $query->where('is_featured', true);
            } elseif ($special === 'pinned') {
                $query->where('is_pinned', true);
            } elseif ($special === 'both') {
                $query->where('is_featured', true)->where('is_pinned', true);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $informasi = $query->paginate(15);

        return view('backend.layouts.views.admin-universitas.informasi', [
            'user' => Auth::user(),
            'informasi' => $informasi,
            'filters' => $request->only(['jenis', 'status', 'special', 'search', 'sort_by', 'sort_order'])
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            $informasi = Informasi::findOrFail($id);
            return view('backend.layouts.views.admin-universitas.informasi-show', compact('informasi'));
        } catch (\Exception $e) {
            return redirect()->route('admin-universitas.informasi.index')
                           ->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $informasi = Informasi::findOrFail($id);
            return view('backend.layouts.views.admin-universitas.informasi', compact('informasi'));
        } catch (\Exception $e) {
            return redirect()->route('admin-universitas.informasi.index')
                           ->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'judul' => 'required|string|max:255',
                'konten' => 'required|string',
                'jenis' => 'required|in:berita,pengumuman',
                'status' => 'required|in:draft,published,archived',
                'penulis' => $request->jenis === 'berita' ? 'required|string|max:255' : 'nullable|string|max:255',
                'tags' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'lampiran' => 'nullable|array',
                'lampiran.*' => 'file|mimes:pdf|max:10240',
                'tanggal_publish' => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date|after:today',
                'is_featured' => 'boolean',
                'is_pinned' => 'boolean'
            ];

            // Conditional validation untuk pengumuman
            if ($request->jenis === 'pengumuman') {
                $rules['nomor_surat'] = 'required|string|max:100|unique:informasi,nomor_surat';
                $rules['tanggal_surat'] = 'required|date|before_or_equal:today';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()
                               ->withErrors($validator)
                               ->withInput();
            }

            // Handle file uploads
            $data = $request->except(['thumbnail', 'lampiran']);

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
                $thumbnailPath = $thumbnail->storeAs('public/informasi/thumbnails', $thumbnailName);
                $data['thumbnail'] = '/storage/' . str_replace('public/', '', $thumbnailPath);
            }

            // Upload lampiran
            if ($request->hasFile('lampiran')) {
                $lampiranFiles = [];
                foreach ($request->file('lampiran') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/informasi/lampiran', $fileName);
                    $lampiranFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $fileName, // Store relative filename only
                        'size' => $file->getSize()
                    ];
                }
                $data['lampiran'] = $lampiranFiles;
            }

            // Parse tags
            if ($request->tags) {
                $data['tags'] = array_map('trim', explode(',', $request->tags));
            }

            // Auto-fill penulis untuk pengumuman
            if ($request->jenis === 'pengumuman') {
                if (Auth::guard('pegawai')->check()) {
                    $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
                } else {
                    $data['penulis'] = Auth::user()->name ?? 'Administrator';
                }
            } elseif (empty($data['penulis'])) {
                if (Auth::guard('pegawai')->check()) {
                    $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
                } else {
                    $data['penulis'] = Auth::user()->name ?? 'Administrator';
                }
            }

            // FIFO Logic untuk Featured (max 5)
            if (isset($data['is_featured']) && $data['is_featured']) {
                $currentFeatured = Informasi::where('is_featured', true)->count();
                if ($currentFeatured >= 5) {
                    $oldestFeatured = Informasi::where('is_featured', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestFeatured) {
                        $oldestFeatured->update(['is_featured' => false]);
                    }
                }
            }

            // FIFO Logic untuk Pinned (max 3)
            if (isset($data['is_pinned']) && $data['is_pinned']) {
                $currentPinned = Informasi::where('is_pinned', true)->count();
                if ($currentPinned >= 3) {
                    $oldestPinned = Informasi::where('is_pinned', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestPinned) {
                        $oldestPinned->update(['is_pinned' => false]);
                    }
                }
            }


            // Save to database
            $informasi = Informasi::create($data);

            Log::info('Informasi data saved to database', [
                'id' => $informasi->id,
                'jenis' => $request->jenis,
                'judul' => $request->judul
            ]);

            return redirect()->route('admin-universitas.informasi.index')
                           ->with('success', 'Data berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('Error saving informasi data to database', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $informasi = Informasi::findOrFail($id);

            // Base validation rules
            $rules = [
                'judul' => 'required|string|max:255',
                'konten' => 'required|string',
                'jenis' => 'required|in:berita,pengumuman',
                'status' => 'required|in:draft,published,archived',
                'penulis' => $request->jenis === 'berita' ? 'required|string|max:255' : 'nullable|string|max:255',
                'tags' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'lampiran' => 'nullable|array',
                'lampiran.*' => 'file|mimes:pdf|max:10240',
                'tanggal_publish' => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date|after:today',
                'is_featured' => 'boolean',
                'is_pinned' => 'boolean'
            ];

            // Conditional validation untuk pengumuman
            if ($request->jenis === 'pengumuman') {
                $rules['nomor_surat'] = 'required|string|max:100|unique:informasi,nomor_surat,' . $id;
                $rules['tanggal_surat'] = 'required|date|before_or_equal:today';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()
                               ->withErrors($validator)
                               ->withInput();
            }

            // Handle file uploads
            $data = $request->except(['thumbnail', 'lampiran']);

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($informasi->thumbnail) {
                    $oldThumbnail = str_replace('/storage/', 'public/', $informasi->thumbnail);
                    Storage::delete($oldThumbnail);
                }

                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
                $thumbnailPath = $thumbnail->storeAs('public/informasi/thumbnails', $thumbnailName);
                $data['thumbnail'] = '/storage/' . str_replace('public/', '', $thumbnailPath);
            }

            // Upload lampiran
            if ($request->hasFile('lampiran')) {
                $lampiranFiles = [];
                foreach ($request->file('lampiran') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/informasi/lampiran', $fileName);
                    $lampiranFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $fileName, // Store relative filename only
                        'size' => $file->getSize()
                    ];
                }
                $data['lampiran'] = $lampiranFiles;
            }

            // Parse tags
            if ($request->tags) {
                $data['tags'] = array_map('trim', explode(',', $request->tags));
            }

            // Auto-fill penulis untuk pengumuman
            if ($request->jenis === 'pengumuman') {
                if (Auth::guard('pegawai')->check()) {
                    $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
                } else {
                    $data['penulis'] = Auth::user()->name ?? 'Administrator';
                }
            } elseif (empty($data['penulis'])) {
                if (Auth::guard('pegawai')->check()) {
                    $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
                } else {
                    $data['penulis'] = Auth::user()->name ?? 'Administrator';
                }
            }

            // FIFO Logic untuk Featured (max 5) - hanya jika item ini belum featured
            if (isset($data['is_featured']) && $data['is_featured'] && !$informasi->is_featured) {
                $currentFeatured = Informasi::where('is_featured', true)->count();
                if ($currentFeatured >= 5) {
                    $oldestFeatured = Informasi::where('is_featured', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestFeatured) {
                        $oldestFeatured->update(['is_featured' => false]);
                    }
                }
            }

            // FIFO Logic untuk Pinned (max 3) - hanya jika item ini belum pinned
            if (isset($data['is_pinned']) && $data['is_pinned'] && !$informasi->is_pinned) {
                $currentPinned = Informasi::where('is_pinned', true)->count();
                if ($currentPinned >= 3) {
                    $oldestPinned = Informasi::where('is_pinned', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestPinned) {
                        $oldestPinned->update(['is_pinned' => false]);
                    }
                }
            }


            // Update in database
            $informasi->update($data);

            Log::info('Informasi data updated in database', [
                'id' => $informasi->id,
                'jenis' => $request->jenis,
                'judul' => $request->judul
            ]);

            return redirect()->route('admin-universitas.informasi.index')
                           ->with('success', 'Data berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating informasi data in database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $informasi = Informasi::findOrFail($id);

            // Delete files
            if ($informasi->thumbnail) {
                $thumbnail = str_replace('/storage/', 'public/', $informasi->thumbnail);
                Storage::delete($thumbnail);
            }

            if ($informasi->lampiran) {
                foreach ($informasi->lampiran as $file) {
                    $filePath = str_replace('/storage/', 'public/', $file['path']);
                    Storage::delete($filePath);
                }
            }

            // Delete from database
            $informasi->delete();

            Log::info('Informasi data deleted from database', [
                'id' => $id
            ]);

            return redirect()->route('admin-universitas.informasi.index')
                           ->with('success', 'Data berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting informasi data from database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor surat otomatis
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateNomorSurat(Request $request)
    {
        try {
            $unit = $request->get('unit', 'KEU');
            $tahun = $request->get('tahun', date('Y'));

            $nomorSurat = Informasi::generateNomorSurat($unit, $tahun);

            return response()->json([
                'success' => true,
                'nomor_surat' => $nomorSurat
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show document for informasi lampiran
     * Based on NUPTK showDocument method with authentication check
     */
    public function showDocument($filename)
    {
        // Authentication check - user must be logged in
        if (!Auth::check()) {
            abort(403, 'Unauthorized. Anda harus login untuk mengakses dokumen ini.');
        }

        // Get file path from storage - check both lampiran and thumbnails
        $filePath = 'public/informasi/lampiran/' . $filename;

        // Check if file exists in lampiran folder
        if (!Storage::disk('local')->exists($filePath)) {
            // Check in thumbnails folder
            $thumbnailPath = 'public/informasi/thumbnails/' . $filename;
            if (Storage::disk('local')->exists($thumbnailPath)) {
                $filePath = $thumbnailPath;
            } else {
                abort(404, 'File tidak ditemukan: ' . $filename);
            }
        }

        // Log document access for security
        Log::info('Informasi document accessed', [
            'filename' => $filename,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Unknown',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Check if this is a download request
        if (request()->has('download') && request()->get('download') == '1') {
            return Storage::disk('local')->download($filePath, $filename);
        }

        // Return file for viewing
        return Storage::disk('local')->response($filePath);
    }

    /**
     * Download document for informasi lampiran
     */
    public function download($id, $filename)
    {
        try {
            // Authentication check - user must be logged in
            if (!Auth::check()) {
                abort(403, 'Unauthorized. Anda harus login untuk mengakses dokumen ini.');
            }

            // Find the informasi record
            $informasi = Informasi::findOrFail($id);

            // Get file path from storage - check both lampiran and thumbnails
            $filePath = 'public/informasi/lampiran/' . $filename;

            // Check if file exists in lampiran folder
            if (!Storage::disk('local')->exists($filePath)) {
                // Check in thumbnails folder
                $thumbnailPath = 'public/informasi/thumbnails/' . $filename;
                if (Storage::disk('local')->exists($thumbnailPath)) {
                    $filePath = $thumbnailPath;
                } else {
                    abort(404, 'File tidak ditemukan: ' . $filename);
                }
            }

            // Log document access for security
            Log::info('Informasi document downloaded', [
                'id' => $id,
                'filename' => $filename,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Unknown',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            // Return file for download
            return Storage::disk('local')->download($filePath, $filename);

        } catch (\Exception $e) {
            Log::error('Error downloading informasi document', [
                'id' => $id,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            abort(404, 'File tidak ditemukan atau tidak dapat diakses.');
        }
    }
}
