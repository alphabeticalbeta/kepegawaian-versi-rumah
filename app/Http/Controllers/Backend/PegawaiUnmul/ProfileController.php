<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\DocumentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Validation\Rules\File;
use App\Models\BackendUnivUsulan\Pangkat;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\SubSubUnitKerja;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request)
    {
        // 1. Ambil data pegawai
        $pegawai = Auth::guard('pegawai')->user();
        $pegawai = Pegawai::with(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja'])
                        ->find($pegawai->id);

        // 2. Ambil daftar field dokumen
        $documentFields = $this->getDocumentFields();

        // 3. Tentukan apakah sedang dalam mode edit atau tidak
        $isEditing = $request->has('edit') && $request->get('edit') == '1';

        // 4. Ambil data untuk dropdown HANYA JIKA sedang mode edit
        $pangkats = $isEditing ? \App\Models\BackendUnivUsulan\Pangkat::orderBy('pangkat')->get() : [];
        $jabatans = $isEditing ? \App\Models\BackendUnivUsulan\Jabatan::orderBy('jabatan')->get() : [];
        $unitKerjas = $isEditing ? \App\Models\BackendUnivUsulan\SubSubUnitKerja::with('subUnitKerja.unitKerja')->orderBy('nama')->get() : [];

        // 5. Kirim SEMUA variabel ke view di akhir metode
        return view('backend.layouts.pegawai-unmul.profile.show', compact(
            'pegawai',
            'documentFields',
            'isEditing',
            'pangkats',
            'jabatans',
            'unitKerjas'
        ));
    }
    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        return redirect()->route('pegawai-unmul.profile.show', ['edit' => '1']);
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('pegawai')->user();
        $pegawai = Pegawai::find($user->id);


        $validated = $request->validate([
            // Dari Tab: Data Pribadi
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawai->id,
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang' => 'nullable|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'nomor_handphone' => 'required|string|max:20',
            'pendidikan_terakhir' => 'required|string',
            'nuptk' => 'nullable|string|max:16',
            'ranting_ilmu_kepakaran' => 'nullable|string',
            'mata_kuliah_diampu' => 'nullable|string',
            'url_profil_sinta' => 'nullable|url',

            // Dari Tab: Kepegawaian
            'nomor_kartu_pegawai' => 'nullable|string|max:255',
            'pangkat_terakhir_id' => 'required|exists:pangkats,id',
            'tmt_pangkat' => 'required|date',
            'jabatan_terakhir_id' => 'required|exists:jabatans,id',
            'tmt_jabatan' => 'required|date',
            'unit_kerja_terakhir_id' => 'required|exists:sub_sub_unit_kerjas,id',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'required|date',

            // Dari Tab: PAK & SKP
            'predikat_kinerja_tahun_pertama' => 'required|string',
            'predikat_kinerja_tahun_kedua' => 'required|string',
            'nilai_konversi' => 'nullable|numeric',


            // Dari Tab: Keamanan (untuk ganti password)
            'current_password' => 'nullable|sometimes|required_with:new_password',
            'new_password' => 'nullable|sometimes|min:8|confirmed',
        ]);

        // 3. Handle file uploads
        $this->handleFileUploads($request, $validated, $pegawai);

        // 4. Handle update password
        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $pegawai->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        // 5. Perintah update dijalankan pada SATU objek pegawai. Ini sekarang akan berhasil.
        $pegawai->update($validated);

        return redirect()->route('pegawai-unmul.profile.show')
                        ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Display a document with access control and logging.
     *
     * Route: /pegawai-unmul/profil/dokumen/{field}
     * Same parameter structure as admin route for consistency
     */
    public function showDocument($field)
    {
        $pegawai = Auth::guard('pegawai')->user();

        // 1. Validasi field yang diizinkan
        $allowedFields = array_keys($this->getDocumentFields());

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // 2. Cek apakah file ada
        $filePath = $pegawai->$field;
        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // 3. **ACCESS CONTROL** - Pegawai hanya bisa akses dokumen sendiri
        // Untuk sementara skip permission check atau buat logic sederhana
        // if (!$pegawai->can('view_own_documents')) {
        //     abort(403, 'Anda tidak memiliki akses untuk halaman atau dokumen ini.');
        // }

        // 4. **LOGGING** - Catat akses dokumen
        $this->logDocumentAccess($pegawai->id, $pegawai->id, $field, request());

        // 5. Serve file
        $fullPath = Storage::disk('local')->path($filePath);

        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $mimeType = FileFacade::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
        ]);
    }

    /**
     * Handle file uploads untuk profile update
     */
    private function handleFileUploads(Request $request, &$validatedData, $pegawai)
    {
        $fileColumns = array_keys($this->getDocumentFields());

        foreach ($fileColumns as $column) {
            if ($request->hasFile($column)) {
                // Validasi file
                $request->validate([
                    $column => ['required', File::types(['pdf'])->max(2 * 1024)]
                ]);

                // Hapus file lama jika ada
                if ($pegawai->$column) {
                    Storage::disk('local')->delete($pegawai->$column);
                }

                // Upload file baru
                $path = $request->file($column)->store('pegawai-files/' . $column, 'local');
                $validatedData[$column] = $path;
            }
        }
    }

    /**
     * Catat akses dokumen ke log
     */
    private function logDocumentAccess($pegawaiId, $accessorId, $documentField, $request): void
    {
        DocumentAccessLog::create([
            'pegawai_id' => $pegawaiId,
            'accessor_id' => $accessorId,
            'document_field' => $documentField,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent', ''),
            'accessed_at' => now(),
        ]);
    }

    /**
     * Get document fields configuration
     */
    private function getDocumentFields(): array
    {
        return [
            'sk_pangkat_terakhir' => [
                'label' => 'SK Pangkat Terakhir',
                'icon' => 'file-text'
            ],
            'sk_jabatan_terakhir' => [
                'label' => 'SK Jabatan Terakhir',
                'icon' => 'briefcase'
            ],
            'ijazah_terakhir' => [
                'label' => 'Ijazah Terakhir',
                'icon' => 'graduation-cap'
            ],
            'transkrip_nilai_terakhir' => [
                'label' => 'Transkrip Nilai Terakhir',
                'icon' => 'file-spreadsheet'
            ],
            'sk_penyetaraan_ijazah' => [
                'label' => 'SK Penyetaraan Ijazah',
                'icon' => 'file-check'
            ],
            'disertasi_thesis_terakhir' => [
                'label' => 'Disertasi/Thesis Terakhir',
                'icon' => 'book-open'
            ],
            'pak_konversi' => [
                'label' => 'PAK Konversi',
                'icon' => 'calculator'
            ],
            'skp_tahun_pertama' => [
                'label' => 'SKP Tahun Pertama',
                'icon' => 'clipboard-check'
            ],
            'skp_tahun_kedua' => [
                'label' => 'SKP Tahun Kedua',
                'icon' => 'clipboard-check'
            ],
            'sk_cpns' => [
                'label' => 'SK CPNS',
                'icon' => 'file-badge'
            ],
            'sk_pns' => [
                'label' => 'SK PNS',
                'icon' => 'file-badge'
            ],
            'foto' => [
                'label' => 'Foto Pegawai',
                'icon' => 'user'
            ]
        ];
    }
}
