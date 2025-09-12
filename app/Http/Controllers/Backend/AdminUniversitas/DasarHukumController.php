<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use App\Models\DasarHukum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DasarHukumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Build query with filters - mengikuti pola aplikasi kepegawaian
        $query = DasarHukum::query()
            ->when($request->search, function ($q, $search) {
                return $q->search($search);
            })
            ->when($request->jenis, function ($q, $jenis) {
                return $q->byJenis($jenis);
            })
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->sub_jenis, function ($q, $sub_jenis) {
                return $q->where('sub_jenis', $sub_jenis);
            })
            ->latest();

        // Apply pagination dengan query string preservation
        $dasarHukum = $query->paginate(10)->withQueryString();

        return view('backend.layouts.views.admin-universitas.dasar-hukum', compact('dasarHukum'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'konten' => 'required|string',
                'jenis_dasar_hukum' => 'required|in:keputusan,pedoman,peraturan,surat_edaran,surat_kementerian,surat_rektor,undang_undang',
                'sub_jenis' => 'nullable|in:peraturan,surat_keputusan,sk_non_pns',
                'nomor_dokumen' => 'required|string|max:100',
                'tanggal_dokumen' => 'required|date',
                'nama_instansi' => 'required|string|max:255',
                'masa_berlaku' => 'nullable|date',
                'penulis' => 'required|string|max:255',
                'tags' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'lampiran' => 'required|array|min:1',
                'lampiran.*' => 'required|file|mimes:pdf|max:10240',
                'status' => 'required|in:draft,published,archived',
                'tanggal_publish' => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date',
                'is_featured' => 'boolean',
                'is_pinned' => 'boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                               ->withErrors($validator)
                               ->withInput();
            }

            // Handle thumbnail upload
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . Str::slug($request->judul) . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnailPath = $thumbnail->storeAs('dasar-hukum/thumbnails', $thumbnailName, 'public');
            }

            // Handle lampiran upload
            $lampiranPaths = [];
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/dasar-hukum/lampiran', $fileName);
                    $lampiranPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $fileName, // Store relative filename only
                        'size' => $file->getSize()
                    ];
                }
            }

            // Prepare data for database
            $data = $request->except(['thumbnail', 'lampiran', 'penulis']);

            // Add file paths to data
            if ($thumbnailPath) {
                $data['thumbnail'] = '/storage/' . $thumbnailPath;
            }

            if (!empty($lampiranPaths)) {
                $data['lampiran'] = $lampiranPaths;
            }

            // Parse tags
            if ($request->tags && trim($request->tags)) {
                $data['tags'] = array_map('trim', explode(',', $request->tags));
            } else {
                $data['tags'] = null;
            }

            // Auto-fill penulis (always from logged in user - ignore form input for security)
            if (Auth::guard('pegawai')->check()) {
                $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
            } else {
                $data['penulis'] = Auth::user()->name ?? 'Administrator';
            }

            // FIFO Logic untuk Featured (max 5)
            if (isset($data['is_featured']) && $data['is_featured']) {
                $currentFeatured = DasarHukum::where('is_featured', true)->count();
                if ($currentFeatured >= 5) {
                    $oldestFeatured = DasarHukum::where('is_featured', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestFeatured) {
                        $oldestFeatured->update(['is_featured' => false]);
                    }
                }
            }

            // FIFO Logic untuk Pinned (max 3)
            if (isset($data['is_pinned']) && $data['is_pinned']) {
                $currentPinned = DasarHukum::where('is_pinned', true)->count();
                if ($currentPinned >= 3) {
                    $oldestPinned = DasarHukum::where('is_pinned', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestPinned) {
                        $oldestPinned->update(['is_pinned' => false]);
                    }
                }
            }

            // Save to database
            $dasarHukum = DasarHukum::create($data);

            Log::info('Dasar Hukum data saved to database', [
                'id' => $dasarHukum->id,
                'jenis_dasar_hukum' => $request->jenis_dasar_hukum,
                'judul' => $request->judul
            ]);

            return redirect()->route('admin-universitas.dasar-hukum.index')
                           ->with('success', 'Data dasar hukum berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('Error saving dasar hukum data to database', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);
            return view('backend.layouts.views.admin-universitas.dasar-hukum-show', compact('dasarHukum'));
        } catch (\Exception $e) {
            return redirect()->route('admin-universitas.dasar-hukum.index')
                           ->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Download a file from dasar hukum.
     */
    public function download(string $id, string $filename)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);

            // Check if file exists in lampiran
            $fileFound = false;
            $filePath = null;

            if ($dasarHukum->lampiran) {
                foreach ($dasarHukum->lampiran as $file) {
                    $filePathToCheck = is_array($file) ? $file['path'] : $file;
                    if ($filePathToCheck === $filename) {
                        $fileFound = true;
                        $filePath = 'public/dasar-hukum/lampiran/' . $filename;
                        break;
                    }
                }
            }

            if (!$fileFound || !Storage::exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            return Storage::download($filePath);

        } catch (\Exception $e) {
            Log::error('Error downloading dasar hukum file', [
                'id' => $id,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Gagal mengunduh file');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'konten' => 'required|string',
                'jenis_dasar_hukum' => 'required|in:keputusan,pedoman,peraturan,surat_edaran,surat_kementerian,surat_rektor,undang_undang',
                'sub_jenis' => 'nullable|in:peraturan,surat_keputusan,sk_non_pns',
                'nomor_dokumen' => 'required|string|max:100',
                'tanggal_dokumen' => 'required|date',
                'nama_instansi' => 'required|string|max:255',
                'masa_berlaku' => 'nullable|date',
                'penulis' => 'required|string|max:255',
                'tags' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'lampiran' => 'nullable|array',
                'lampiran.*' => 'file|mimes:pdf|max:10240',
                'status' => 'required|in:draft,published,archived',
                'tanggal_publish' => 'nullable|date',
                'tanggal_berakhir' => 'nullable|date',
                'is_featured' => 'boolean',
                'is_pinned' => 'boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                               ->withErrors($validator)
                               ->withInput();
            }

            // Handle file uploads
            $data = $request->except(['thumbnail', 'lampiran', 'penulis']);

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                Log::info('Thumbnail file detected for update', [
                    'id' => $id,
                    'filename' => $request->file('thumbnail')->getClientOriginalName(),
                    'size' => $request->file('thumbnail')->getSize(),
                    'mime' => $request->file('thumbnail')->getMimeType()
                ]);

                // Delete old thumbnail
                if ($dasarHukum->thumbnail) {
                    $oldThumbnail = str_replace('/storage/', 'public/', $dasarHukum->thumbnail);
                    Storage::delete($oldThumbnail);
                    Log::info('Old thumbnail deleted', ['path' => $oldThumbnail]);
                }

                $thumbnail = $request->file('thumbnail');
                $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();

                Log::info('Attempting to store thumbnail', [
                    'id' => $id,
                    'filename' => $thumbnailName,
                    'target_path' => 'public/dasar-hukum/thumbnails/' . $thumbnailName
                ]);

                // Use move() method instead of storeAs() for more reliable upload
                $targetDir = storage_path('app/public/dasar-hukum/thumbnails');
                $targetPath = $targetDir . '/' . $thumbnailName;
                $moveResult = $thumbnail->move($targetDir, $thumbnailName);

                if ($moveResult) {
                    $data['thumbnail'] = '/storage/dasar-hukum/thumbnails/' . $thumbnailName;

                    // Verify file exists immediately after move
                    $fileExists = file_exists($targetPath);

                    Log::info('Thumbnail move result', [
                        'id' => $id,
                        'target_path' => $targetPath,
                        'public_path' => $data['thumbnail'],
                        'file_exists_immediately' => $fileExists,
                        'file_size_on_disk' => $fileExists ? filesize($targetPath) : 'N/A'
                    ]);
                } else {
                    Log::error('Thumbnail move failed', [
                        'id' => $id,
                        'filename' => $thumbnailName,
                        'target_path' => $targetPath
                    ]);
                }
            } else {
                Log::info('No thumbnail file uploaded for update', ['id' => $id]);
            }

            // Upload lampiran
            if ($request->hasFile('lampiran')) {
                Log::info('Lampiran files detected for update', [
                    'id' => $id,
                    'count' => count($request->file('lampiran'))
                ]);

                $lampiranFiles = [];
                foreach ($request->file('lampiran') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/dasar-hukum/lampiran', $fileName);
                    $lampiranFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $fileName, // Store relative filename only
                        'size' => $file->getSize()
                    ];
                }
                $data['lampiran'] = $lampiranFiles;

                Log::info('Lampiran files uploaded successfully', [
                    'id' => $id,
                    'files' => $lampiranFiles
                ]);
            } else {
                // Keep existing lampiran if no new files uploaded
                $data['lampiran'] = $dasarHukum->lampiran;
                Log::info('No lampiran files uploaded for update, keeping existing', [
                    'id' => $id,
                    'existing_lampiran' => $dasarHukum->lampiran
                ]);
            }

            // Parse tags
            if ($request->tags && trim($request->tags)) {
                $data['tags'] = array_map('trim', explode(',', $request->tags));
            } else {
                $data['tags'] = null;
            }

            // Auto-fill penulis (always from logged in user - ignore form input for security)
            if (Auth::guard('pegawai')->check()) {
                $data['penulis'] = Auth::guard('pegawai')->user()->nama_lengkap ?? Auth::guard('pegawai')->user()->name ?? 'Pegawai';
            } else {
                $data['penulis'] = Auth::user()->name ?? 'Administrator';
            }

            // FIFO Logic untuk Featured (max 5) - hanya jika item ini belum featured
            if (isset($data['is_featured']) && $data['is_featured'] && !$dasarHukum->is_featured) {
                $currentFeatured = DasarHukum::where('is_featured', true)->count();
                if ($currentFeatured >= 5) {
                    $oldestFeatured = DasarHukum::where('is_featured', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestFeatured) {
                        $oldestFeatured->update(['is_featured' => false]);
                    }
                }
            }

            // FIFO Logic untuk Pinned (max 3) - hanya jika item ini belum pinned
            if (isset($data['is_pinned']) && $data['is_pinned'] && !$dasarHukum->is_pinned) {
                $currentPinned = DasarHukum::where('is_pinned', true)->count();
                if ($currentPinned >= 3) {
                    $oldestPinned = DasarHukum::where('is_pinned', true)
                        ->orderBy('updated_at', 'asc')
                        ->first();
                    if ($oldestPinned) {
                        $oldestPinned->update(['is_pinned' => false]);
                    }
                }
            }

            // Update in database
            $dasarHukum->update($data);

            Log::info('Dasar Hukum data updated in database', [
                'id' => $dasarHukum->id,
                'jenis_dasar_hukum' => $request->jenis_dasar_hukum,
                'judul' => $request->judul
            ]);

            return redirect()->route('admin-universitas.dasar-hukum.index')
                           ->with('success', 'Data dasar hukum berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error updating dasar hukum data in database', [
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
     */
    public function destroy(string $id)
    {
        try {
            $dasarHukum = DasarHukum::findOrFail($id);

            // Delete files
            if ($dasarHukum->thumbnail) {
                $thumbnail = str_replace('/storage/', 'public/', $dasarHukum->thumbnail);
                Storage::delete($thumbnail);
            }

            if ($dasarHukum->lampiran) {
                foreach ($dasarHukum->lampiran as $file) {
                    // Handle both array format and string format
                    if (is_array($file) && isset($file['path'])) {
                        // Array format: ['name' => '...', 'path' => '...', 'size' => ...]
                        $filePath = 'public/dasar-hukum/lampiran/' . $file['path'];
                    } elseif (is_string($file)) {
                        // String format: direct filename or full path
                        if (strpos($file, '/storage/') === 0) {
                            $filePath = str_replace('/storage/', 'public/', $file);
                        } else {
                            $filePath = 'public/dasar-hukum/lampiran/' . $file;
                        }
                    } else {
                        // Skip invalid format
                        continue;
                    }

                    Storage::delete($filePath);
                }
            }

            // Delete from database
            $dasarHukum->delete();

            Log::info('Dasar Hukum data deleted from database', [
                'id' => $id
            ]);

            return redirect()->route('admin-universitas.dasar-hukum.index')
                           ->with('success', 'Data dasar hukum berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Error deleting dasar hukum data from database', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }


    /**
     * Show document file
     */
    public function showDocument($filename)
    {
        // Public access - no authentication required for document viewing

        // Get file path from storage - check both lampiran and thumbnails
        $filePath = 'public/dasar-hukum/lampiran/' . $filename;

        // Check if file exists in lampiran folder
        if (!Storage::disk('local')->exists($filePath)) {
            // Check in thumbnails folder
            $thumbnailPath = 'public/dasar-hukum/thumbnails/' . $filename;
            if (Storage::disk('local')->exists($thumbnailPath)) {
                $filePath = $thumbnailPath;
            } else {
                // File not found - return placeholder image
                return $this->getPlaceholderImage($filename);
            }
        }

        // Get the full path to the file
        $fullPath = Storage::disk('local')->path($filePath);

        // Return the file
        return response()->file($fullPath);
    }

    /**
     * Generate placeholder image for missing files
     */
    private function getPlaceholderImage($filename)
    {
        // Determine file type from extension
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Create a simple placeholder image
        $width = 400;
        $height = 300;

        // Create image resource
        $image = imagecreate($width, $height);

        // Set colors
        $bgColor = imagecolorallocate($image, 240, 240, 240); // Light gray background
        $textColor = imagecolorallocate($image, 100, 100, 100); // Dark gray text
        $borderColor = imagecolorallocate($image, 200, 200, 200); // Border color

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Draw border
        imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);

        // Add text
        $text = "File Tidak Ditemukan";
        $fontSize = 3;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;

        imagestring($image, $fontSize, $x, $y, $text, $textColor);

        // Add filename
        $filenameText = "File: " . $filename;
        $filenameWidth = imagefontwidth($fontSize) * strlen($filenameText);
        $filenameX = ($width - $filenameWidth) / 2;
        $filenameY = $y + $textHeight + 10;

        imagestring($image, $fontSize, $filenameX, $filenameY, $filenameText, $textColor);

        // Output image
        ob_start();
        imagejpeg($image, null, 80);
        $imageData = ob_get_contents();
        ob_end_clean();

        // Clean up
        imagedestroy($image);

        // Return image response
        return response($imageData)
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
