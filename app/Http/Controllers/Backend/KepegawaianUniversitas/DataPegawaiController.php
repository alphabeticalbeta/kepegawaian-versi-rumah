<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\KepegawaianUniversitas\Jabatan;
use App\Models\KepegawaianUniversitas\Pangkat;
use App\Models\KepegawaianUniversitas\Pegawai;
use App\Models\KepegawaianUniversitas\SubSubUnitKerja;
use App\Models\KepegawaianUniversitas\DocumentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File as FileFacade;
use App\Services\FileStorageService;
use App\Exports\PegawaiExport;
use App\Exports\PegawaiTemplate;
use App\Imports\PegawaiImport;
use Maatwebsite\Excel\Facades\Excel;

class DataPegawaiController extends Controller
{
    private $fileStorage;

    public function __construct(FileStorageService $fileStorage)
    {
        $this->fileStorage = $fileStorage;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // OPTIMASI: Gunakan query builder dengan eager loading yang optimal
        $query = Pegawai::with(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja'])
            ->when($request->filter_jenis_pegawai, function ($q, $jenis_pegawai) {
                return $q->where('jenis_pegawai', $jenis_pegawai);
            })
            ->when($request->search, function ($q, $search) {
                return $q->where(function($query) use ($search) {
                    $query->where('nama_lengkap', 'like', "%{$search}%")
                          ->orWhere('nip', 'like', "%{$search}%");
                });
            })
            ->latest();

        $pegawais = $query->paginate(10)->withQueryString();

        return view('backend.layouts.views.kepegawaian-universitas.data-pegawai.master-datapegawai', compact('pegawais'));
    }

    // Method create dan store dihapus karena menggunakan shared ProfileController

    // Method edit dihapus karena menggunakan shared profile page

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        // Handle AJAX auto-save requests
        if ($request->ajax() && $request->isMethod('POST')) {
            return $this->handleAutoSave($request, $pegawai);
        }

        $validated = $this->validateRequest($request, $pegawai->id);
        $this->handleFileUploads($request, $validated, $pegawai);

        // Handle password update
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        } else {
            unset($validated['password']);
        }

        // Handle unit_kerja_id berdasarkan unit_kerja_id
        if ($request->filled('unit_kerja_id')) {
            $subSubUnitKerja = \App\Models\KepegawaianUniversitas\SubSubUnitKerja::with(['subUnitKerja', 'subUnitKerja.unitKerja'])
                ->find($request->unit_kerja_id);

            if ($subSubUnitKerja && $subSubUnitKerja->subUnitKerja && $subSubUnitKerja->subUnitKerja->unitKerja) {
                // Set unit_kerja_id berdasarkan parent dari Sub-sub Unit Kerja
                $validated['unit_kerja_id'] = $subSubUnitKerja->subUnitKerja->unitKerja->id;
            }
        }

        $pegawai->update($validated);

        return redirect()->route('backend.kepegawaian-universitas.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    /**
     * Handle auto-save functionality for AJAX requests
     */
    private function handleAutoSave(Request $request, Pegawai $pegawai)
    {
        try {
            // Only allow updating specific dosen fields for auto-save
            $allowedFields = [
                'mata_kuliah_diampu',
                'ranting_ilmu_kepakaran',
                'url_profil_sinta'
            ];

            $updateData = [];
            foreach ($allowedFields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->input($field);
                }
            }

            // Validate URL if present
            if (isset($updateData['url_profil_sinta']) && !empty($updateData['url_profil_sinta'])) {
                $urlPattern = '/^https?:\/\/sinta\.kemdikbud\.go\.id\/.*$/';
                if (!preg_match($urlPattern, $updateData['url_profil_sinta'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'URL Sinta tidak valid'
                    ], 422);
                }
            }

            // Update only the allowed fields
            if (!empty($updateData)) {
                $pegawai->update($updateData);

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan otomatis',
                    'updated_at' => $pegawai->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang perlu diperbarui'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Auto-save error: ' . $e->getMessage(), [
                'pegawai_id' => $pegawai->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Pegawai $pegawai)
    {

        // Existing delete logic
        $fileColumns = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir',
            'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        foreach ($fileColumns as $column) {
            if ($pegawai->$column) {
                $disk = $this->getFileDisk($column);
                Storage::disk($disk)->delete($pegawai->$column);
            }
        }

        $pegawai->delete();

        return redirect()->route('backend.kepegawaian-universitas.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil dihapus.');
    }

    // Method show dihapus karena menggunakan shared profile page

    /**
     * Reusable validation logic.
     */
    private function validateRequest(Request $request, $pegawaiId = null)
    {
        $rules = [
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|in:Dosen Fungsional,Dosen dengan Tugas Tambahan,Tenaga Kependidikan Fungsional Umum,Tenaga Kependidikan Fungsional Tertentu,Tenaga Kependidikan Struktural,Tenaga Kependidikan Tugas Tambahan',
            'nip' => 'required|numeric|digits:18|unique:pegawais,nip,' . $pegawaiId,
            'gelar_depan' => 'nullable|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawaiId,
            'gelar_belakang' => 'required|string|max:255',
            'nomor_kartu_pegawai' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'pangkat_terakhir_id' => 'required|exists:pangkats,id',
            'tmt_pangkat' => 'required|date',
            'jabatan_terakhir_id' => 'required|exists:jabatans,id',
            'tmt_jabatan' => 'required|date',
            'pendidikan_terakhir' => 'required|string',
            'nama_universitas_sekolah' => 'nullable|string|max:255',
            'nama_prodi_jurusan_s2' => 'nullable|string|max:255',
            'predikat_kinerja_tahun_pertama' => 'required|string',
            'predikat_kinerja_tahun_kedua' => 'required|string',
            'unit_kerja_id' => 'required|exists:sub_sub_unit_kerjas,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'nomor_handphone' => 'required|string',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'required|date',
            'nuptk' => 'nullable|numeric|digits:16',
            'mata_kuliah_diampu' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'ranting_ilmu_kepakaran' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'url_profil_sinta' => 'nullable|required_if:jenis_pegawai,Dosen|url',
            'nilai_konversi' => 'nullable|numeric',
            'status_kepegawaian' => ['required','string',Rule::in([
                'Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN',
                'Tenaga Kependidikan PNS', 'Tenaga Kependidikan PPPK', 'Tenaga Kependidikan Non ASN'
                ])
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ];

        $fileRules = [
            'sk_pangkat_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'sk_jabatan_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'ijazah_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'transkrip_nilai_terakhir' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_pertama' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_kedua' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'pak_konversi' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'sk_penyetaraan_ijazah' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'disertasi_thesis_terakhir' => ['nullable', File::types(['pdf'])->max(10 * 1024)],
            'sk_cpns' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'sk_pns' => ['required', File::types(['pdf'])->max(2 * 1024)],
        ];

        if ($pegawaiId) {
            foreach ($fileRules as $key => $value) {
                $rules[$key] = ['nullable', ...array_slice($value, 1)];
            }
        } else {
             $rules = array_merge($rules, $fileRules);
        }

        return $request->validate($rules);
    }

    /**
     * Reusable file upload logic - REFACTORED with FileStorageService.
     */
    private function handleFileUploads(Request $request, &$validatedData, $pegawai = null)
    {
        $fileColumns = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        foreach ($fileColumns as $column) {
            if ($request->hasFile($column)) {
                // Delete old file if exists using FileStorageService
                if ($pegawai && $pegawai->$column) {
                    $this->fileStorage->deleteFile($pegawai->$column);
                }

                // Store new file using FileStorageService
                $uploadPath = 'pegawai-files/' . $column;
                $file = $request->file($column);
                $path = $this->fileStorage->uploadFile($file, $uploadPath, $column);
                $validatedData[$column] = $path;

                \Log::info("File uploaded using FileStorageService", [
                    'column' => $column,
                    'file_path' => $path,
                    'pegawai_id' => $pegawai ? $pegawai->id : 'new'
                ]);
            }
        }
    }

    /**
     * Display a document with access control and logging.
     */
        public function showDocument(Pegawai $pegawai, $field)
    {
        try {
            // Debug info
            \Log::info('showDocument called', [
                'pegawai_id' => $pegawai->id,
                'field' => $field,
                'url' => request()->url()
            ]);
        } catch (\Exception $e) {
            // Handle database connection error
            \Log::error('Database connection error in showDocument', [
                'error' => $e->getMessage(),
                'field' => $field,
                'url' => request()->url()
            ]);

            return response()->json([
                'error' => 'Database connection error',
                'message' => 'Tidak dapat terhubung ke database. Pastikan MySQL server berjalan.',
                'details' => $e->getMessage(),
                'field' => $field,
                'suggestion' => 'Start MySQL server atau gunakan SQLite'
            ], 503);
        }

        // 1. Validasi field yang diizinkan
        $allowedFields = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        if (!in_array($field, $allowedFields)) {
            \Log::warning('Invalid field requested', ['field' => $field]);
            return response()->json([
                'error' => 'Jenis dokumen tidak valid',
                'field' => $field,
                'allowed_fields' => $allowedFields
            ], 404);
        }

        // 2. Cek apakah file ada
        $filePath = $pegawai->$field;

        if (!$filePath) {
            \Log::warning('File path is empty', ['field' => $field, 'pegawai_id' => $pegawai->id]);
            return response()->json([
                'error' => 'File tidak ditemukan',
                'message' => 'Pegawai tidak memiliki file ' . $field,
                'pegawai_id' => $pegawai->id,
                'field' => $field
            ], 404);
        }

        // Determine correct disk based on field type
        $disk = $this->getFileDisk($field);

        if (!Storage::disk($disk)->exists($filePath)) {
            \Log::warning('File not found in storage', [
                'field' => $field,
                'filePath' => $filePath,
                'disk' => $disk
            ]);
            return response()->json([
                'error' => 'File tidak ditemukan di storage',
                'message' => 'File ada di database tapi tidak ditemukan di storage',
                'file_path' => $filePath,
                'disk' => $disk,
                'pegawai_id' => $pegawai->id,
                'field' => $field
            ], 404);
        }

        // 3. **ACCESS CONTROL** - Cek permission berdasarkan role
        // Coba ambil user dari berbagai guard yang mungkin
        $currentUser = Auth::guard('pegawai')->user() ?? Auth::guard('web')->user() ?? Auth::user();

        if (!$currentUser) {
            abort(403, 'Anda harus login untuk mengakses dokumen ini.');
        }

        if (!$this->canAccessDocument($currentUser, $pegawai)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman atau dokumen ini.');
        }

        // 4. **LOGGING** - Catat akses dokumen
        $this->logDocumentAccess($pegawai->id, $currentUser->id ?? 0, $field, request());

        // 5. **HANDLE FILE RESPONSE** - Gunakan disk yang sesuai
        try {
            // Untuk foto, gunakan URL langsung jika menggunakan disk public
            if ($field === 'foto' && $disk === 'public') {
                $url = Storage::disk($disk)->url($filePath);
                return redirect($url);
            }

            // Untuk file lainnya, gunakan response()->file
            $fullPath = Storage::disk($disk)->path($filePath);

            if (!file_exists($fullPath)) {
                \Log::error('File not found in filesystem', [
                    'field' => $field,
                    'filePath' => $filePath,
                    'disk' => $disk,
                    'fullPath' => $fullPath
                ]);
                abort(404, 'File tidak ditemukan di storage');
            }

            $mimeType = FileFacade::mimeType($fullPath);

            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error serving file', [
                'field' => $field,
                'filePath' => $filePath,
                'disk' => $disk,
                'error' => $e->getMessage()
            ]);
            abort(404, 'Error loading file: ' . $e->getMessage());
        }
    }

    /**
     * Enhanced access control dengan security terbaik
     */
    private function canAccessDocument($currentUser, $targetPegawai): bool
    {
        // 1. SUPER ADMIN: Kepegawaian Universitas - full access
        if ($currentUser->hasRole('Kepegawaian Universitas') ||
            $currentUser->hasPermissionTo('view_all_pegawai_documents')) {
            return true;
        }

        // 2. ADMIN FAKULTAS: Hanya bisa akses dokumen pegawai di fakultasnya
        if ($currentUser->hasRole('Admin Fakultas')) {
            // Double check: pastikan ada unit_kerja_id
            if (!$currentUser->unit_kerja_id) {
                \Log::warning('Admin Fakultas tanpa unit_kerja_id mencoba akses dokumen', [
                    'admin_id' => $currentUser->id,
                    'target_pegawai_id' => $targetPegawai->id
                ]);
                return false;
            }

            // Cek apakah pegawai target berada di fakultas yang sama
            $targetFakultasId = $targetPegawai->unitKerja?->subUnitKerja?->unit_kerja_id;

            if ($currentUser->unit_kerja_id === $targetFakultasId) {
                \Log::info('Admin Fakultas akses dokumen pegawai di fakultasnya', [
                    'admin_id' => $currentUser->id,
                    'admin_fakultas_id' => $currentUser->unit_kerja_id,
                    'target_pegawai_id' => $targetPegawai->id,
                    'target_fakultas_id' => $targetFakultasId
                ]);
                return true;
            }

            \Log::warning('Admin Fakultas mencoba akses dokumen pegawai dari fakultas lain', [
                'admin_id' => $currentUser->id,
                'admin_fakultas_id' => $currentUser->unit_kerja_id,
                'target_pegawai_id' => $targetPegawai->id,
                'target_fakultas_id' => $targetFakultasId
            ]);
            return false;
        }

        // 3. PEGAWAI: Hanya dokumen sendiri
        if ($currentUser->hasPermissionTo('view_own_documents') ||
            $currentUser->hasRole('Pegawai')) {
            return $currentUser->id === $targetPegawai->id;
        }

        // 4. PENILAI: Akses terbatas (implementasi future)
        if ($currentUser->hasRole('Penilai Universitas')) {
            // TODO: Implementasi logic penilai berdasarkan usulan yang sedang dinilai
            return false;
        }

        // 5. DEFAULT DENY: Tidak ada akses
        \Log::warning('Unauthorized document access attempt', [
            'user_id' => $currentUser->id,
            'user_roles' => $currentUser->getRoleNames()->toArray(),
            'target_pegawai_id' => $targetPegawai->id,
            'field' => request()->route('field')
        ]);

        return false;
    }

    /**
     * Enhanced fakultas checking dengan error handling
     */
    private function isInSameFakultas($user1, $user2): bool
    {
        try {
            // Load relasi dengan error handling
            if (!$user1->relationLoaded('unitKerja')) {
                $user1->load('unitKerja');
            }
            if (!$user2->relationLoaded('unitKerja')) {
                $user2->load('unitKerja');
            }

            // Method 1: Gunakan unit_kerja_id langsung (lebih efisien)
            if ($user1->unit_kerja_id && $user2->unit_kerja_id) {
                return $user1->unit_kerja_id === $user2->unit_kerja_id;
            }

            // Method 2: Fallback dengan relasi langsung
            $fakultas1 = $user1->unitKerja?->id;
            $fakultas2 = $user2->unitKerja?->id;

            return $fakultas1 && $fakultas2 && $fakultas1 === $fakultas2;

        } catch (\Exception $e) {
            \Log::error('Error checking fakultas relationship', [
                'user1_id' => $user1->id,
                'user2_id' => $user2->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Simplified logging tanpa advanced fields
     */
    private function logDocumentAccess($pegawaiId, $accessorId, $documentField, $request): void
    {
        try {
            DocumentAccessLog::create([
                'pegawai_id' => $pegawaiId,
                'accessor_id' => $accessorId,
                'document_field' => $documentField,
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->header('User-Agent', ''), 0, 500),
                'accessed_at' => now(),
            ]);

            // Log role info separately untuk debugging
            $accessor = Auth::guard('pegawai')->user() ?? Auth::guard('web')->user() ?? Auth::user();
            if ($accessor) {
                \Log::info('Document accessed', [
                    'pegawai_id' => $pegawaiId,
                    'accessor_id' => $accessorId,
                    'document_field' => $documentField,
                    'accessor_has_roles' => $accessor->roles ? $accessor->roles->count() : 0,
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to log document access', [
                'pegawai_id' => $pegawaiId,
                'accessor_id' => $accessorId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get the appropriate disk for a given field
     */
    private function getFileDisk($field): string
    {
        $sensitiveFiles = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'pak_integrasi', 'skp_tahun_pertama', 'skp_tahun_kedua', 'sk_cpns', 'sk_pns'
        ];

        // Foto menggunakan disk public untuk akses langsung
        if ($field === 'foto') {
            return 'public';
        }

        return in_array($field, $sensitiveFiles) ? 'local' : 'public';
    }

    /**
     * Export data pegawai to Excel
     */
    public function export(Request $request)
    {
        try {
            // Get filters from request
            $filters = [
                'jenis_pegawai' => $request->get('jenis_pegawai'),
                'status_kepegawaian' => $request->get('status_kepegawaian'),
                'unit_kerja' => $request->get('unit_kerja'),
            ];

            // Generate filename with timestamp
            $filename = 'data_pegawai_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Export to Excel
            return Excel::download(new PegawaiExport($filters), $filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting pegawai data: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString(),
                'filters' => $filters ?? []
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }

    /**
     * Import data pegawai from Excel
     */
    public function import(Request $request)
    {
        try {
            // Validate file
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
                'import_mode' => 'required|in:create_only,update_only,create_update'
            ]);

            $file = $request->file('file');
            $importMode = $request->get('import_mode', 'create_update');

            // Create import instance
            $import = new PegawaiImport($importMode);

            // Import the file
            Excel::import($import, $file);

            // Get statistics
            $stats = $import->getStatistics();
            $failures = $import->failures();

            // Prepare success message
            $message = "Import berhasil! ";
            $message .= "Data baru: {$stats['created']}, ";
            $message .= "Data diupdate: {$stats['updated']}";

            if ($stats['failures'] > 0) {
                $message .= ", Gagal: {$stats['failures']}";
            }

            return redirect()->back()
                           ->with('success', $message)
                           ->with('import_failures', $failures);

        } catch (\Exception $e) {
            \Log::error('Error importing pegawai data: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString(),
                'file' => $request->file('file')?->getClientOriginalName()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel for import
     */
    public function downloadTemplate()
    {
        try {
            // Create template with example data
            $template = new PegawaiTemplate();

            // Generate filename
            $filename = 'template_import_pegawai_' . date('Y-m-d') . '.xlsx';

            // Download template
            return Excel::download($template, $filename);

        } catch (\Exception $e) {
            \Log::error('Error downloading template: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat mengunduh template: ' . $e->getMessage());
        }
    }

    /**
     * Preview import data before actual import
     */
    public function previewImport(Request $request)
    {
        try {
            // Validate file
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:10240'
            ]);

            $file = $request->file('file');
            $importMode = $request->get('import_mode', 'create_update');

            // Create import instance
            $import = new PegawaiImport($importMode);

            // Read first 10 rows for preview
            $data = Excel::toArray($import, $file);
            $previewData = array_slice($data[0], 0, 10); // First 10 rows

            return response()->json([
                'success' => true,
                'preview_data' => $previewData,
                'total_rows' => count($data[0]),
                'headers' => array_keys($previewData[0] ?? [])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error previewing import: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat preview data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete pegawai
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'selected_ids' => 'required|array|min:1',
                'selected_ids.*' => 'exists:pegawais,id'
            ]);

            $selectedIds = $request->selected_ids;
            $deletedCount = 0;

            foreach ($selectedIds as $id) {
                $pegawai = Pegawai::find($id);
                if ($pegawai) {
                    // Delete associated files
                    $this->deletePegawaiFiles($pegawai);

                    // Delete pegawai
                    $pegawai->delete();
                    $deletedCount++;
                }
            }

            \Log::info('Bulk delete pegawai completed', [
                'deleted_count' => $deletedCount,
                'selected_ids' => $selectedIds
            ]);

            return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} pegawai.");

        } catch (\Exception $e) {
            \Log::error('Error in bulk delete: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update pegawai
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $request->validate([
                'selected_ids' => 'required|string',
                'status_kepegawaian' => 'nullable|string|in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN',
                'jenis_pegawai' => 'nullable|string|in:Dosen,Tenaga Kependidikan',
                'pangkat_terakhir_id' => 'nullable|exists:pangkats,id',
                'jabatan_terakhir_id' => 'nullable|exists:jabatans,id',
                'unit_kerja_id' => 'nullable|exists:sub_sub_unit_kerjas,id',
            ]);

            $selectedIds = explode(',', $request->selected_ids);
            $updatedCount = 0;
            $updateData = [];

            // Prepare update data (only non-empty fields)
            if ($request->filled('status_kepegawaian')) {
                $updateData['status_kepegawaian'] = $request->status_kepegawaian;
            }
            if ($request->filled('jenis_pegawai')) {
                $updateData['jenis_pegawai'] = $request->jenis_pegawai;
            }
            if ($request->filled('pangkat_terakhir_id')) {
                $updateData['pangkat_terakhir_id'] = $request->pangkat_terakhir_id;
            }
            if ($request->filled('jabatan_terakhir_id')) {
                $updateData['jabatan_terakhir_id'] = $request->jabatan_terakhir_id;
            }
            if ($request->filled('unit_kerja_id')) {
                $updateData['unit_kerja_id'] = $request->unit_kerja_id;
            }

            if (empty($updateData)) {
                return redirect()->back()->with('error', 'Tidak ada data yang diupdate.');
            }

            foreach ($selectedIds as $id) {
                $pegawai = Pegawai::find($id);
                if ($pegawai) {
                    $pegawai->update($updateData);
                    $updatedCount++;
                }
            }

            \Log::info('Bulk update pegawai completed', [
                'updated_count' => $updatedCount,
                'selected_ids' => $selectedIds,
                'update_data' => $updateData
            ]);

            return redirect()->back()->with('success', "Berhasil mengupdate {$updatedCount} pegawai.");

        } catch (\Exception $e) {
            \Log::error('Error in bulk update: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage());
        }
    }

    /**
     * Delete pegawai files
     */
    private function deletePegawaiFiles($pegawai)
    {
        $fileFields = [
            'foto', 'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai', 'sk_cpns', 'sk_pns', 'sk_penyesuaian_pangkat',
            'sk_penyesuaian_jabatan', 'sk_mutasi', 'sk_pensiun', 'sk_hukuman_disiplin',
            'sk_penghargaan', 'sk_tugas_belajar', 'sk_izin_belajar', 'sk_cuti',
            'sk_tugas_tambahan', 'sk_pelatihan', 'sk_sertifikasi', 'sk_penilaian_kinerja',
            'sk_pak_integrasi', 'sk_skp', 'sk_penelitian', 'sk_pengabdian_masyarakat',
            'sk_publikasi', 'sk_hki', 'sk_penghargaan_akademik', 'sk_organisasi_profesi'
        ];

        foreach ($fileFields as $field) {
            if ($pegawai->$field) {
                try {
                    // Use Storage facade directly
                    \Storage::delete($pegawai->$field);
                } catch (\Exception $e) {
                    \Log::warning("Failed to delete file for field {$field}: " . $e->getMessage());
                }
            }
        }
    }
}
