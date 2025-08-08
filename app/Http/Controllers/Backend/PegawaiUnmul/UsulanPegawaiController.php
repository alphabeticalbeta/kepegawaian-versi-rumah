<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PegawaiUnmul\StoreJabatanUsulanRequest;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanDokumen;
use App\Models\BackendUnivUsulan\UsulanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessUsulanDocuments;
use App\Jobs\SendUsulanNotification;
use App\Jobs\GenerateUsulanReport;

class UsulanPegawaiController extends Controller
{
    public function index()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $pegawai */
        $pegawai = Auth::user();

        $usulans = $pegawai->usulans()
                        ->with([
                            'periodeUsulan',
                            'logs' => function($query) {
                                $query->with('dilakukanOleh')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5); // Ambil 5 log terbaru untuk preview
                            }
                        ])
                        ->latest()
                        ->paginate(10);

        return view('backend.layouts.pegawai-unmul.dashboard', [
            'usulans' => $usulans
        ]);
    }

    public function createJabatan()
    {
        $pegawai = Pegawai::with(['jabatan', 'pangkat', 'unitKerja'])
                        ->findOrFail(Auth::id());

        // =====================================================
        // KONDISI #1: CEK APAKAH SUDAH DI JABATAN TERTINGGI
        // =====================================================
        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = null;

        if ($jabatanLama) {
            // Cari jabatan yang lebih tinggi
            $jabatanTujuan = Jabatan::where('id', '>', $jabatanLama->id)
                                    ->orderBy('id', 'asc')
                                    ->first();

            // Jika tidak ada jabatan yang lebih tinggi, berarti sudah di puncak
            if (!$jabatanTujuan) {
                return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                    ->with('warning', 'Anda sudah berada di jabatan fungsional tertinggi. Tidak ada jabatan yang lebih tinggi untuk diajukan.');
            }
        }

        // =====================================================
        // KONDISI #2: CEK KELENGKAPAN PROFIL (SEMUA FIELD)
        // =====================================================
        $requiredFields = [
            'nip', 'nama_lengkap', 'email', 'jenis_pegawai', 'status_kepegawaian',
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'nomor_handphone',
            'nomor_kartu_pegawai', 'pangkat_terakhir_id', 'tmt_pangkat',
            'jabatan_terakhir_id', 'tmt_jabatan', 'unit_kerja_terakhir_id',
            'pendidikan_terakhir', 'tmt_cpns', 'tmt_pns',
            'predikat_kinerja_tahun_pertama', 'predikat_kinerja_tahun_kedua',
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir',
            'transkrip_nilai_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns'
        ];

        // Additional fields for Dosen
        if ($pegawai->jenis_pegawai == 'Dosen') {
            $requiredFields[] = 'mata_kuliah_diampu';
            $requiredFields[] = 'ranting_ilmu_kepakaran';
        }

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($pegawai->$field)) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            Log::warning('Profil tidak lengkap', [
                'pegawai_id' => $pegawai->id,
                'missing_fields' => $missingFields
            ]);

            return redirect()->route('pegawai-unmul.profile.edit')
                ->with('warning', 'Profil Anda belum lengkap. Silakan lengkapi semua data profil sebelum membuat usulan. Data yang belum lengkap: ' . count($missingFields) . ' field.');
        }

        // =====================================================
        // KONDISI #3: CEK USULAN YANG MASIH AKTIF
        // =====================================================
        $usulanAktif = Usulan::where('pegawai_id', Auth::id())
            ->where('jenis_usulan', 'jabatan')
            ->whereNotIn('status_usulan', ['Direkomendasikan', 'Ditolak'])
            ->exists();

        if ($usulanAktif) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Anda sudah memiliki usulan jabatan yang sedang aktif. Anda tidak dapat membuat usulan baru sampai usulan sebelumnya selesai diproses.');
        }

        // =====================================================
        // KONDISI #4: CEK PERIODE YANG SEDANG BUKA
        // =====================================================
        $daftarPeriode = PeriodeUsulan::where('jenis_usulan', 'jabatan')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->where('status', 'Buka')
            ->first();

        if (!$daftarPeriode) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Saat ini tidak ada periode pengajuan usulan jabatan yang aktif atau dibuka.');
        }

        // PERBAIKAN: Buat dummy usulan untuk compatibility dengan blade
        $usulan = new Usulan();

        return view('backend.layouts.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'usulan' => $usulan, // TAMBAHKAN INI
        ]);
    }

    public function storeJabatan(StoreJabatanUsulanRequest $request)
    {
        $validatedData = $request->validated();
        $pegawai = Auth::user();
        $statusUsulan = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

        // =====================================================
        // VALIDASI AWAL & PENGAMBILAN DATA
        // =====================================================

        // 1. Validasi Periode Usulan
        $periodeUsulan = PeriodeUsulan::where('id', $validatedData['periode_usulan_id'])
            ->where('status', 'Buka')
            ->where('jenis_usulan', 'jabatan')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->first();

        if (!$periodeUsulan) {
            return redirect()->back()
                ->withErrors(['periode_usulan_id' => 'Periode usulan tidak valid atau sudah tidak aktif.'])
                ->withInput();
        }

        // 2. Validasi Jabatan
        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = null;

        if ($jabatanLama) {
            $jabatanTujuan = Jabatan::where('id', '>', $jabatanLama->id)
                                    ->orderBy('id', 'asc')
                                    ->first();

            if (!$jabatanTujuan) {
                return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                    ->with('error', 'Anda sudah berada di jabatan fungsional tertinggi.');
            }
        }

        // 3. Validasi Usulan Aktif
        $usulanAktif = Usulan::where('pegawai_id', $pegawai->id)
            ->where('jenis_usulan', 'jabatan')
            ->whereNotIn('status_usulan', ['Direkomendasikan', 'Ditolak'])
            ->exists();

        if ($usulanAktif) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Anda sudah memiliki usulan jabatan yang sedang aktif.');
        }

        // =====================================================
        // PERSIAPAN DATA USULAN
        // =====================================================

        // Data Snapshot Pegawai saat usulan dibuat
        $pegawaiSnapshot = $this->createPegawaiSnapshot($pegawai);

        // Data Karya Ilmiah
        $karyaIlmiahData = $this->extractKaryaIlmiahData($validatedData);

        // Data Syarat Khusus
        $syaratKhususData = $this->extractSyaratKhususData($validatedData);

        try {
            $usulanCreated = null;

            DB::transaction(function () use (
                $request, $pegawai, $periodeUsulan, $jabatanLama, $jabatanTujuan,
                $statusUsulan, $pegawaiSnapshot, $karyaIlmiahData, $syaratKhususData,
                $validatedData, &$usulanCreated
            ) {

                // Upload dan simpan dokumen
                $dokumenPaths = $this->handleDocumentUploads($request, $pegawai);

                // Struktur data usulan yang terorganisir
                $dataUsulan = [
                    'metadata' => [
                        'created_at_snapshot' => now()->toISOString(),
                        'version' => '1.0',
                        'submission_type' => $statusUsulan,
                    ],
                    'pegawai_snapshot' => $pegawaiSnapshot,
                    'karya_ilmiah' => $karyaIlmiahData,
                    'dokumen_usulan' => $dokumenPaths,
                    'syarat_khusus' => $syaratKhususData,
                    'catatan_pengusul' => $validatedData['catatan'] ?? null,
                ];

                // Buat usulan
                $usulan = Usulan::create([
                    'pegawai_id' => $pegawai->id,
                    'periode_usulan_id' => $periodeUsulan->id,
                    'jenis_usulan' => 'jabatan',
                    'jabatan_lama_id' => $jabatanLama?->id,
                    'jabatan_tujuan_id' => $jabatanTujuan?->id,
                    'status_usulan' => $statusUsulan,
                    'data_usulan' => $dataUsulan,
                    'catatan_verifikator' => null,
                ]);

                // Simpan dokumen ke tabel terpisah
                $this->saveUsulanDocuments($usulan, $dokumenPaths, $pegawai);

                // Buat log awal
                $this->createUsulanLog($usulan, null, $statusUsulan, $pegawai, $validatedData);

                $usulanCreated = $usulan;

                Log::info('Usulan jabatan berhasil dibuat', [
                    'usulan_id' => $usulan->id,
                    'pegawai_id' => $pegawai->id,
                    'status' => $statusUsulan,
                    'jabatan_tujuan' => $jabatanTujuan?->jabatan
                ]);
            });

            // =====================================================
            // DISPATCH BACKGROUND JOBS
            // =====================================================
            if ($usulanCreated) {
                $this->dispatchUsulanJobs($usulanCreated, $statusUsulan);
            }

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error saat membuat usulan jabatan', [
                'error' => $e->getMessage(),
                'pegawai_id' => $pegawai->id
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan database. Silakan coba lagi.')
                ->withInput();

        } catch (\Throwable $e) { // PERBAIKAN: Gunakan \Throwable
            Log::error('Error saat membuat usulan jabatan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pegawai_id' => $pegawai->id
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')
                ->withInput();
        }

        // =====================================================
        // RESPONSE SUCCESS
        // =====================================================
        $message = $statusUsulan === 'Diajukan'
            ? 'Usulan kenaikan jabatan berhasil diajukan. Tim verifikasi akan meninjau usulan Anda.'
            : 'Usulan jabatan berhasil disimpan sebagai draft. Anda dapat melanjutkan pengisian nanti.';

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', $message);
    }

    public function editJabatan(Usulan $usulan)
    {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        $pegawai = Auth::user();

        $isReadOnly = in_array($usulan->status_usulan, [
            'Diajukan',
            'Sedang Direview',
            'Disetujui',
            'Direkomendasikan'
        ]);

        $canEdit = in_array($usulan->status_usulan, [
            'Draft',
            'Perlu Perbaikan',
            'Dikembalikan'
        ]);

        $daftarPeriode = PeriodeUsulan::where('status', 'Buka')
                                      ->where('jenis_usulan', 'jabatan')
                                      ->where('tanggal_mulai', '<=', now())
                                      ->where('tanggal_selesai', '>=', now())
                                      ->orderBy('tahun_periode', 'desc')
                                      ->first();

        $jabatanLama = $pegawai->jabatan;
        $jabatanTujuan = $jabatanLama
            ? Jabatan::where('id', '>', $jabatanLama->id)->orderBy('id', 'asc')->first()
            : null;

        return view('backend.layouts.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai'       => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
            'usulan'        => $usulan,
        ]);
    }

    public function updateJabatan(StoreJabatanUsulanRequest $request, Usulan $usulan)
    {
        // Pastikan hanya pemilik yang bisa mengedit
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        $validatedData = $request->validated();
        $pegawai = Auth::user();
        $oldStatus = $usulan->status_usulan;
        $statusUsulan = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

        try {
            DB::transaction(function () use ($request, $usulan, $pegawai, $statusUsulan, $validatedData, $oldStatus) {

                // PERBAIKAN: Gunakan struktur data yang konsisten
                $dataUsulanLama = $usulan->data_usulan ?? [];

                // Update data karya ilmiah
                $karyaIlmiahData = $this->extractKaryaIlmiahData($validatedData);
                $syaratKhususData = $this->extractSyaratKhususData($validatedData);

                // Perbarui struktur data
                $dataUsulanLama['karya_ilmiah'] = $karyaIlmiahData;
                $dataUsulanLama['syarat_khusus'] = $syaratKhususData;
                $dataUsulanLama['catatan_pengusul'] = $validatedData['catatan'] ?? null;

                // Update metadata
                if (!isset($dataUsulanLama['metadata'])) {
                    $dataUsulanLama['metadata'] = [];
                }
                $dataUsulanLama['metadata']['last_updated'] = now()->toISOString();
                $dataUsulanLama['metadata']['submission_type'] = $statusUsulan;

                // PERBAIKAN: Handle dokumen dengan struktur yang benar
                $daftarDokumenKeys = [
                    'bukti_korespondensi', 'turnitin', 'upload_artikel',
                    'pakta_integritas', 'bukti_syarat_guru_besar'
                ];

                foreach ($daftarDokumenKeys as $key) {
                    if ($request->hasFile($key)) {
                        // Hapus file lama jika ada
                        $oldFilePath = null;

                        // Cek struktur baru dulu
                        if (isset($dataUsulanLama['dokumen_usulan'][$key]['path'])) {
                            $oldFilePath = $dataUsulanLama['dokumen_usulan'][$key]['path'];
                        }
                        // Fallback ke struktur lama
                        elseif (isset($dataUsulanLama[$key])) {
                            $oldFilePath = $dataUsulanLama[$key];
                        }

                        if ($oldFilePath && Storage::disk('local')->exists($oldFilePath)) {
                            Storage::disk('local')->delete($oldFilePath);
                        }

                        // Upload file baru dengan struktur yang benar
                        $file = $request->file($key);
                        $uploadPath = 'usulan-dokumen/' . $pegawai->id . '/' . date('Y/m');
                        $fileName = $key . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs($uploadPath, $fileName, 'local');

                        // Simpan dengan struktur baru
                        if (!isset($dataUsulanLama['dokumen_usulan'])) {
                            $dataUsulanLama['dokumen_usulan'] = [];
                        }

                        $dataUsulanLama['dokumen_usulan'][$key] = [
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            'uploaded_at' => now()->toISOString(),
                        ];

                        // Perbarui juga tabel usulan_dokumens
                        UsulanDokumen::updateOrCreate(
                            ['usulan_id' => $usulan->id, 'nama_dokumen' => $key],
                            ['diupload_oleh_id' => $pegawai->id, 'path' => $path]
                        );

                        Log::info("File $key berhasil diupdate", [
                            'usulan_id' => $usulan->id,
                            'path' => $path,
                            'pegawai_id' => $pegawai->id
                        ]);
                    }
                }

                // Simpan perubahan
                $usulan->update([
                    'status_usulan' => $statusUsulan,
                    'data_usulan' => $dataUsulanLama,
                ]);

                // Buat log jika status berubah
                if ($oldStatus !== $statusUsulan) {
                    $this->createUsulanLog($usulan, $oldStatus, $statusUsulan, $pegawai, $validatedData);
                }
            });

            // Dispatch jobs jika status berubah ke Diajukan
            if ($oldStatus !== 'Diajukan' && $statusUsulan === 'Diajukan') {
                $this->dispatchUsulanJobs($usulan, $statusUsulan);
            }

        } catch (\Throwable $e) { // PERBAIKAN: Gunakan \Throwable
            Log::error('Gagal memperbarui usulan jabatan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'usulan_id' => $usulan->id
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan, data usulan tidak tersimpan. Silakan coba lagi.');
        }

        $message = $statusUsulan === 'Diajukan'
            ? 'Usulan kenaikan jabatan berhasil diperbarui dan diajukan. Tim verifikasi akan meninjau usulan Anda.'
            : 'Perubahan pada usulan Anda berhasil disimpan sebagai Draft.';

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', $message);
    }

    public function showUsulanDocument(Usulan $usulan, $field)
    {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'Anda tidak punya akses ke dokumen ini');
        }

        // PERBAIKAN: Sesuaikan dengan struktur data baru
        $filePath = null;

        // Coba struktur baru dulu (dengan nested structure)
        if (isset($usulan->data_usulan['dokumen_usulan'][$field]['path'])) {
            $filePath = $usulan->data_usulan['dokumen_usulan'][$field]['path'];
        }
        // Fallback ke struktur lama (untuk backward compatibility)
        elseif (isset($usulan->data_usulan[$field])) {
            $filePath = $usulan->data_usulan[$field];
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Log document access
        Log::info('Document accessed', [
            'usulan_id' => $usulan->id,
            'field' => $field,
            'user_id' => Auth::id(),
            'file_path' => $filePath
        ]);

        $fullPath = Storage::disk('local')->path($filePath);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    public function getLogs(Usulan $usulan)
    {
        // Pastikan hanya pemilik usulan yang bisa melihat log
        if ($usulan->pegawai_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $logs = $usulan->logs()
                ->with('dilakukanOleh')
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedLogs = $logs->map(function($log) {
                return [
                    'id' => $log->id,
                    'status' => $log->status_baru ?? $log->status_sebelumnya,
                    'status_sebelumnya' => $log->status_sebelumnya,
                    'status_baru' => $log->status_baru,
                    'keterangan' => $log->catatan,
                    'user_name' => $log->dilakukanOleh ? $log->dilakukanOleh->nama_lengkap : 'System',
                    'formatted_date' => $log->created_at->isoFormat('D MMMM YYYY, HH:mm'),
                    'created_at' => $log->created_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'logs' => $formattedLogs
            ]);

        } catch (\Throwable $e) { // PERBAIKAN: Gunakan \Throwable
            Log::error('Error getting usulan logs: ' . $e->getMessage());

            return response()->json([
                'error' => 'Gagal mengambil data log'
            ], 500);
        }
    }

    // Helper methods tetap sama...

    private function createPegawaiSnapshot($pegawai): array
    {
        return [
            // Data Pribadi
            'nip' => $pegawai->nip,
            'nuptk' => $pegawai->nuptk,
            'nama_lengkap' => $pegawai->nama_lengkap,
            'gelar_depan' => $pegawai->gelar_depan,
            'gelar_belakang' => $pegawai->gelar_belakang,
            'email' => $pegawai->email,
            'tempat_lahir' => $pegawai->tempat_lahir,
            'tanggal_lahir' => $pegawai->tanggal_lahir?->toDateString(),
            'jenis_kelamin' => $pegawai->jenis_kelamin,
            'nomor_handphone' => $pegawai->nomor_handphone,
            'jenis_pegawai' => $pegawai->jenis_pegawai,
            'status_kepegawaian' => $pegawai->status_kepegawaian,

            // Data Kepegawaian
            'pangkat_saat_usul' => $pegawai->pangkat?->pangkat,
            'pangkat_id' => $pegawai->pangkat_terakhir_id,
            'tmt_pangkat' => $pegawai->tmt_pangkat?->toDateString(),
            'jabatan_saat_usul' => $pegawai->jabatan?->jabatan,
            'jabatan_id' => $pegawai->jabatan_terakhir_id,
            'tmt_jabatan' => $pegawai->tmt_jabatan?->toDateString(),
            'unit_kerja_saat_usul' => $pegawai->unitKerja?->nama,
            'unit_kerja_id' => $pegawai->unit_kerja_terakhir_id,
            'tmt_cpns' => $pegawai->tmt_cpns?->toDateString(),
            'tmt_pns' => $pegawai->tmt_pns?->toDateString(),

            // Data Pendidikan & Fungsional
            'pendidikan_terakhir' => $pegawai->pendidikan_terakhir,
            'mata_kuliah_diampu' => $pegawai->mata_kuliah_diampu,
            'ranting_ilmu_kepakaran' => $pegawai->ranting_ilmu_kepakaran,
            'url_profil_sinta' => $pegawai->url_profil_sinta,

            // Data Kinerja
            'predikat_kinerja_tahun_pertama' => $pegawai->predikat_kinerja_tahun_pertama,
            'predikat_kinerja_tahun_kedua' => $pegawai->predikat_kinerja_tahun_kedua,
            'nilai_konversi' => $pegawai->nilai_konversi,

            // Dokumen Profil (path saja)
            'ijazah_terakhir' => $pegawai->ijazah_terakhir,
            'transkrip_nilai_terakhir' => $pegawai->transkrip_nilai_terakhir,
            'sk_pangkat_terakhir' => $pegawai->sk_pangkat_terakhir,
            'sk_jabatan_terakhir' => $pegawai->sk_jabatan_terakhir,
            'skp_tahun_pertama' => $pegawai->skp_tahun_pertama,
            'skp_tahun_kedua' => $pegawai->skp_tahun_kedua,
            'pak_konversi' => $pegawai->pak_konversi,
            'sk_cpns' => $pegawai->sk_cpns,
            'sk_pns' => $pegawai->sk_pns,
            'sk_penyetaraan_ijazah' => $pegawai->sk_penyetaraan_ijazah,
            'disertasi_thesis_terakhir' => $pegawai->disertasi_thesis_terakhir,
        ];
    }

    /**
     * Extract data karya ilmiah dari validated data
     */
    private function extractKaryaIlmiahData(array $validatedData): array
    {
        return [
            'jenis_karya' => $validatedData['karya_ilmiah'],
            'nama_jurnal' => $validatedData['nama_jurnal'],
            'judul_artikel' => $validatedData['judul_artikel'],
            'penerbit_artikel' => $validatedData['penerbit_artikel'],
            'volume_artikel' => $validatedData['volume_artikel'],
            'nomor_artikel' => $validatedData['nomor_artikel'],
            'edisi_artikel' => $validatedData['edisi_artikel'],
            'halaman_artikel' => $validatedData['halaman_artikel'],
            'links' => [
                'artikel' => $validatedData['link_artikel'],
                'sinta' => $validatedData['link_sinta'] ?? null,
                'scopus' => $validatedData['link_scopus'] ?? null,
                'scimago' => $validatedData['link_scimago'] ?? null,
                'wos' => $validatedData['link_wos'] ?? null,
            ]
        ];
    }

    /**
     * Extract data syarat khusus (untuk Guru Besar)
     */
    private function extractSyaratKhususData(array $validatedData): array
    {
        return [
            'syarat_guru_besar' => $validatedData['syarat_guru_besar'] ?? null,
            'deskripsi_syarat' => $this->getSyaratGuruBesarDescription($validatedData['syarat_guru_besar'] ?? null),
        ];
    }

    /**
     * Get deskripsi syarat guru besar
     */
    private function getSyaratGuruBesarDescription(?string $syarat): ?string
    {
        $descriptions = [
            'hibah' => 'Pernah mendapatkan hibah penelitian',
            'bimbingan' => 'Pernah membimbing program doktor',
            'pengujian' => 'Pernah menguji mahasiswa doktor',
            'reviewer' => 'Sebagai reviewer jurnal internasional'
        ];

        return $syarat ? $descriptions[$syarat] ?? null : null;
    }

    /**
     * Handle upload dokumen
     */
    private function handleDocumentUploads($request, $pegawai): array
    {
        $dokumentKeys = [
            'pakta_integritas',
            'bukti_korespondensi',
            'turnitin',
            'upload_artikel',
            'bukti_syarat_guru_besar'
        ];

        $filePaths = [];
        $uploadPath = 'usulan-dokumen/' . $pegawai->id . '/' . date('Y/m');

        foreach ($dokumentKeys as $key) {
            if ($request->hasFile($key)) {
                try {
                    $file = $request->file($key);
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $key . '_' . time() . '_' . uniqid() . '.' . $extension;

                    $path = $file->storeAs($uploadPath, $fileName, 'local');

                    $filePaths[$key] = [
                        'path' => $path,
                        'original_name' => $originalName,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                    ];

                    Log::info("File $key berhasil diupload", [
                        'path' => $path,
                        'size' => $file->getSize(),
                        'pegawai_id' => $pegawai->id
                    ]);

                } catch (\Throwable $e) { // PERBAIKAN: Gunakan \Throwable
                    Log::error("Gagal upload file $key", [
                        'error' => $e->getMessage(),
                        'pegawai_id' => $pegawai->id
                    ]);
                    throw new \RuntimeException("Gagal mengupload file $key: " . $e->getMessage());
                }
            }
        }

        return $filePaths;
    }

    /**
     * Simpan dokumen ke tabel usulan_dokumens
     */
    private function saveUsulanDocuments($usulan, array $dokumenPaths, $pegawai): void
    {
        foreach ($dokumenPaths as $nama => $fileData) {
            UsulanDokumen::create([
                'usulan_id' => $usulan->id,
                'diupload_oleh_id' => $pegawai->id,
                'nama_dokumen' => $nama,
                'path' => $fileData['path'],
            ]);
        }
    }

    /**
     * Buat log usulan
     */
    private function createUsulanLog($usulan, $statusLama, $statusBaru, $pegawai, $validatedData): void
    {
        $catatan = match($statusBaru) {
            'Draft' => 'Usulan disimpan sebagai draft oleh pegawai',
            'Diajukan' => 'Usulan diajukan oleh pegawai untuk review',
            default => 'Status usulan diubah'
        };

        if (!empty($validatedData['catatan'])) {
            $catatan .= '. Catatan: ' . $validatedData['catatan'];
        }

        UsulanLog::create([
            'usulan_id' => $usulan->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => $statusBaru,
            'catatan' => $catatan,
            'dilakukan_oleh_id' => $pegawai->id,
        ]);
    }

    /**
     * Dispatch background jobs
     */
    private function dispatchUsulanJobs($usulan, string $status): void
    {
        try {
            // Process documents (always)
            ProcessUsulanDocuments::dispatch($usulan)
                ->delay(now()->addSeconds(10));

            // Send notifications and generate reports (only for submitted)
            if ($status === 'Diajukan') {
                SendUsulanNotification::dispatch($usulan, 'submitted')
                    ->delay(now()->addSeconds(5));

                GenerateUsulanReport::dispatch($usulan)
                    ->delay(now()->addMinutes(2));
            }

            Log::info('Background jobs dispatched', [
                'usulan_id' => $usulan->id,
                'status' => $status
            ]);

        } catch (\Throwable $e) { // PERBAIKAN: Gunakan \Throwable
            Log::error('Gagal dispatch background jobs', [
                'usulan_id' => $usulan->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception, just log it
        }
    }
}
