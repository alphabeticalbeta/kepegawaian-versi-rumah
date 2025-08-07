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
                           ->with('periodeUsulan')
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

        // Jika semua kondisi terpenuhi, tampilkan form
        $usulan = new Usulan();

        return view('backend.layouts.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
        ]);
    }

    public function storeJabatan(StoreJabatanUsulanRequest $request)
    {
        $validatedData = $request->validated();
        $pegawai       = Auth::user();
        $statusUsulan  = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';
        $jabatanLama   = $pegawai->jabatan;
        $jabatanTujuan = $jabatanLama
            ? Jabatan::where('id', '>', $jabatanLama->id)->orderBy('id', 'asc')->first()
            : null;

        // Data dari form usulan
        $dataUsulanTambahan = [
            'karya_ilmiah'       => $validatedData['karya_ilmiah']    ?? null,
            'nama_jurnal'        => $validatedData['nama_jurnal']     ?? null,
            'judul_artikel'      => $validatedData['judul_artikel']   ?? null,
            'penerbit_artikel'   => $validatedData['penerbit_artikel']?? null,
            'volume_artikel'     => $validatedData['volume_artikel']  ?? null,
            'nomor_artikel'      => $validatedData['nomor_artikel']   ?? null,
            'edisi_artikel'      => $validatedData['edisi_artikel']   ?? null,
            'halaman_artikel'    => $validatedData['halaman_artikel'] ?? null,
            'issn_artikel'       => $validatedData['issn_artikel']    ?? null,
            'link_artikel'       => $validatedData['link_artikel']    ?? null,
            'link_sinta'         => $validatedData['link_sinta']      ?? null,
            'link_scopus'        => $validatedData['link_scopus']     ?? null,
            'link_scimago'       => $validatedData['link_scimago']    ?? null,
            'link_wos'           => $validatedData['link_wos']        ?? null,
            'syarat_guru_besar'  => $validatedData['syarat_guru_besar'] ?? null,
            'catatan_pengusul'   => $validatedData['catatan'] ?? null,
        ];

        // Data snapshot dari profil pegawai
        $dataSnapshotProfil = $request->input('snapshot', []);

        try {
            $usulanCreated = null;

            DB::transaction(function () use ($request, $pegawai, $jabatanLama, $jabatanTujuan, $validatedData, $dataSnapshotProfil, $dataUsulanTambahan, $statusUsulan, &$usulanCreated) {
                $daftarDokumenKeys = [
                    'bukti_korespondensi',
                    'pakta_integritas',
                    'turnitin',
                    'upload_artikel',
                    'bukti_syarat_guru_besar'
                ];
                $filePaths = [];

                foreach ($daftarDokumenKeys as $key) {
                    if ($request->hasFile($key)) {
                        // Simpan ke 'local' disk untuk keamanan
                        $path = $request->file($key)->store('usulan-dokumen/' . $pegawai->id, 'local');
                        $filePaths[$key] = $path;
                    }
                }

                $dataUsulan = array_merge($dataSnapshotProfil, $dataUsulanTambahan, $filePaths);

                $usulan = Usulan::create([
                    'pegawai_id'          => $pegawai->id,
                    'periode_usulan_id'   => $validatedData['periode_usulan_id'],
                    'jenis_usulan'        => 'jabatan',
                    'jabatan_lama_id'     => $jabatanLama?->id,
                    'jabatan_tujuan_id'   => $jabatanTujuan?->id,
                    'status_usulan'       => $statusUsulan,
                    'data_usulan'         => $dataUsulan,
                    'catatan_verifikator' => null,
                ]);

                // Save for queue jobs
                $usulanCreated = $usulan;

                foreach ($filePaths as $key => $path) {
                    UsulanDokumen::create([
                        'usulan_id'        => $usulan->id,
                        'diupload_oleh_id' => $pegawai->id,
                        'nama_dokumen'     => $key,
                        'path'             => $path,
                    ]);
                }

                UsulanLog::create([
                    'usulan_id'         => $usulan->id,
                    'status_sebelumnya' => null,
                    'status_baru'       => $statusUsulan,
                    'catatan'           => $validatedData['catatan'] ?? null,
                    'dilakukan_oleh_id' => $pegawai->id,
                ]);
            });

            // ========================================
            // DISPATCH JOBS TO QUEUE (Outside Transaction)
            // ========================================
            if ($usulanCreated) {
                // 1. Process documents in background (wait 10 seconds)
                ProcessUsulanDocuments::dispatch($usulanCreated)
                    ->delay(now()->addSeconds(10));

                // 2. Send notification email if submitted
                if ($statusUsulan === 'Diajukan') {
                    SendUsulanNotification::dispatch($usulanCreated, 'submitted')
                        ->delay(now()->addSeconds(5));

                    // 3. Generate report after 2 minutes
                    GenerateUsulanReport::dispatch($usulanCreated)
                        ->delay(now()->addMinutes(2));
                }

                Log::info('Jobs dispatched for usulan', [
                    'usulan_id' => $usulanCreated->id,
                    'status' => $statusUsulan
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan usulan jabatan: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan, data usulan tidak tersimpan. Silakan cek log.');
        }

        $message = $statusUsulan === 'Diajukan'
            ? 'Usulan kenaikan jabatan berhasil diajukan. Anda akan menerima email konfirmasi.'
            : 'Usulan Anda berhasil disimpan sebagai Draft.';

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', $message);
    }

    public function editJabatan(Usulan $usulan)
    {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        $pegawai = Auth::user();

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
        $pegawai       = Auth::user();
        $oldStatus     = $usulan->status_usulan;
        $statusUsulan  = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

        try {
            DB::transaction(function () use ($request, $usulan, $pegawai, $statusUsulan, $validatedData, $oldStatus) {

                // TAHAP 1: Siapkan semua data non-file terlebih dahulu
                $dataUsulanLama = $usulan->data_usulan ?? [];

                $dataTeksBaru = [
                    'karya_ilmiah'      => $validatedData['karya_ilmiah'] ?? null,
                    'nama_jurnal'       => $validatedData['nama_jurnal'] ?? null,
                    'judul_artikel'     => $validatedData['judul_artikel'] ?? null,
                    'penerbit_artikel'  => $validatedData['penerbit_artikel'] ?? null,
                    'volume_artikel'    => $validatedData['volume_artikel'] ?? null,
                    'nomor_artikel'     => $validatedData['nomor_artikel'] ?? null,
                    'edisi_artikel'     => $validatedData['edisi_artikel'] ?? null,
                    'halaman_artikel'   => $validatedData['halaman_artikel'] ?? null,
                    'issn_artikel'      => $validatedData['issn_artikel'] ?? null,
                    'link_artikel'      => $validatedData['link_artikel'] ?? null,
                    'link_sinta'        => $validatedData['link_sinta'] ?? null,
                    'link_scopus'       => $validatedData['link_scopus'] ?? null,
                    'link_scimago'      => $validatedData['link_scimago'] ?? null,
                    'link_wos'          => $validatedData['link_wos'] ?? null,
                    'syarat_guru_besar' => $validatedData['syarat_guru_besar'] ?? null,
                    'catatan_pengusul'  => $validatedData['catatan'] ?? null,
                ];

                // Gabungkan data lama dengan data teks baru
                $dataUsulanFinal = array_merge($dataUsulanLama, $dataTeksBaru);

                // TAHAP 2: Proses file HANYA SETELAH data digabung
                $daftarDokumenKeys = [
                    'bukti_korespondensi', 'turnitin', 'upload_artikel',
                    'pakta_integritas', 'bukti_syarat_guru_besar'
                ];

                foreach ($daftarDokumenKeys as $key) {
                    if ($request->hasFile($key)) {
                        // Hapus file lama jika ada
                        if (!empty($dataUsulanFinal[$key]) && Storage::disk('local')->exists($dataUsulanFinal[$key])) {
                            Storage::disk('local')->delete($dataUsulanFinal[$key]);
                        }
                        // Simpan file baru
                        $path = $request->file($key)->store('usulan-dokumen/' . $pegawai->id, 'local');
                        // Perbarui path di data JSON
                        $dataUsulanFinal[$key] = $path;

                        // Perbarui juga path di tabel 'usulan_dokumens'
                        UsulanDokumen::updateOrCreate(
                            ['usulan_id' => $usulan->id, 'nama_dokumen' => $key],
                            ['diupload_oleh_id' => $pegawai->id, 'path' => $path]
                        );
                    }
                }

                // TAHAP 3: Simpan semua perubahan ke database
                $usulan->update([
                    'status_usulan'   => $statusUsulan,
                    'data_usulan'     => $dataUsulanFinal,
                ]);

                // Buat log jika status berubah
                if ($oldStatus !== $statusUsulan) {
                    UsulanLog::create([
                        'usulan_id'         => $usulan->id,
                        'status_sebelumnya' => $oldStatus,
                        'status_baru'       => $statusUsulan,
                        'catatan'           => 'Usulan diperbarui oleh pengusul.',
                        'dilakukan_oleh_id' => $pegawai->id,
                    ]);
                }
            });

            // ========================================
            // DISPATCH JOBS TO QUEUE (Outside Transaction)
            // ========================================

            // If status changed from Draft to Diajukan
            if ($oldStatus !== 'Diajukan' && $statusUsulan === 'Diajukan') {
                // Send notification
                SendUsulanNotification::dispatch($usulan, 'submitted')
                    ->delay(now()->addSeconds(5));

                // Generate report
                GenerateUsulanReport::dispatch($usulan)
                    ->delay(now()->addMinutes(1));

                // Process documents
                ProcessUsulanDocuments::dispatch($usulan)
                    ->delay(now()->addSeconds(10));

                Log::info('Jobs dispatched for updated usulan', [
                    'usulan_id' => $usulan->id,
                    'old_status' => $oldStatus,
                    'new_status' => $statusUsulan
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Gagal memperbarui usulan jabatan: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan, data usulan tidak tersimpan. Silakan cek log.');
        }

        $message = $statusUsulan === 'Diajukan'
            ? 'Usulan kenaikan jabatan berhasil diperbarui dan diajukan. Anda akan menerima email konfirmasi.'
            : 'Perubahan pada usulan Anda berhasil disimpan sebagai Draft.';

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', $message);
    }

    public function showUsulanDocument(Usulan $usulan, $field)
    {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'Anda tidak punya akses ke dokumen ini');
        }

        $filePath = $usulan->data_usulan[$field] ?? null;

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Log document access
        Log::info('Document accessed', [
            'usulan_id' => $usulan->id,
            'field' => $field,
            'user_id' => Auth::id()
        ]);

        return response()->file(Storage::disk('local')->path($filePath));
    }
}
