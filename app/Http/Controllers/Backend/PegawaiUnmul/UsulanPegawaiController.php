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

        // Pastikan path view ini benar sesuai lokasi file Anda
        return view('backend.layouts.pegawai-unmul.dashboard', [
            'usulans' => $usulans
        ]);
    }

    public function createJabatan()
    {
        $pegawai = Pegawai::with(['jabatan', 'pangkat', 'unitKerja'])
                        ->findOrFail(Auth::id());

        // 1. Cek Kelengkapan Profil
        if (is_null($pegawai->pangkat_terakhir_id) || is_null($pegawai->jabatan_terakhir_id) || is_null($pegawai->unit_kerja_terakhir_id)) {
            return redirect()->route('pegawai-unmul.profile.edit')
                ->with('warning', 'Profil Anda belum lengkap. Silakan lengkapi data Pangkat, Jabatan, dan Unit Kerja sebelum membuat usulan.');
        }

        // 2. Cek apakah ada usulan jabatan yang masih aktif
        $usulanAktif = Usulan::where('pegawai_id', Auth::id())
            ->where('jenis_usulan', 'jabatan')
            ->whereNotIn('status_usulan', ['Diterima', 'Ditolak']) // Sesuaikan status final jika perlu
            ->exists();

        if ($usulanAktif) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Anda sudah memiliki usulan jabatan yang sedang aktif. Anda tidak dapat membuat usulan baru sampai usulan sebelumnya selesai diproses.');
        }

        // 3. Cek apakah ada periode yang sedang buka
        $daftarPeriode = PeriodeUsulan::where('jenis_usulan', 'jabatan')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->where('status', 'Buka')
            ->first();

        if (!$daftarPeriode) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Saat ini tidak ada periode pengajuan usulan jabatan yang aktif atau dibuka.');
        }

            $jabatanLama = $pegawai->jabatan;
            $jabatanTujuan = $jabatanLama
                ? Jabatan::where('id', '>', $jabatanLama->id)->orderBy('id', 'asc')->first()
                : null;

            return view('backend.layouts.pegawai-unmul.usul-jabatan.create-jabatan', [
                'pegawai' => $pegawai,
                'daftarPeriode' => $daftarPeriode,
                'jabatanTujuan' => $jabatanTujuan,
            ]);
    }

    public function storeJabatan(StoreJabatanUsulanRequest $request)
    {

        // dd('ERROR: Method STORE yang berjalan, seharusnya UPDATE.');

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
            DB::transaction(function () use ($request, $pegawai, $jabatanLama, $jabatanTujuan, $validatedData, $dataSnapshotProfil, $dataUsulanTambahan, $statusUsulan) {
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
                        $path = $request->file($key)->store('usulan-dokumen/' . $pegawai->id, 'public');
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

                foreach ($filePaths as $key => $path) {
                    UsulanDokumen::create([
                        'usulan_id'   => $usulan->id,
                        'pegawai_id'  => $pegawai->id,
                        'nama_dokumen'=> $key,
                        'path'        => $path,
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
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan usulan jabatan: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan, data usulan tidak tersimpan. Silakan cek log.');
        }

        $message = $statusUsulan === 'Diajukan'
            ? 'Usulan kenaikan jabatan berhasil diajukan.'
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

        // [FIXED] Mengganti get() menjadi first() untuk mengambil satu objek
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

// In file: app/Http/Controllers/Backend/PegawaiUnmul/UsulanPegawaiController.php

    public function updateJabatan(StoreJabatanUsulanRequest $request, Usulan $usulan)
    {
        // Pastikan hanya pemilik yang bisa mengedit
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        $validatedData = $request->validated();
        $pegawai       = Auth::user();
        $statusUsulan  = ($request->input('action') === 'submit_final') ? 'Diajukan' : 'Draft';

        try {
            DB::transaction(function () use ($request, $usulan, $pegawai, $statusUsulan, $validatedData) {

                // =====================================================================
                // [!!!] PERBAIKAN UTAMA ADA DI SINI [!!!]
                // Kita akan memanipulasi kolom JSON 'data_usulan'
                // =====================================================================

                // 1. Ambil data JSON yang sudah ada dari database.
                //    Kita berikan array kosong sebagai default jika datanya null.
                $dataUsulanLama = $usulan->data_usulan ?? [];

                // 2. Siapkan array data baru dari form yang sudah divalidasi.
                $dataUsulanBaru = [
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
                ];

                // 3. Gabungkan data lama dengan data baru.
                //    Data baru akan menimpa data lama jika key-nya sama.
                $dataUsulanFinal = array_merge($dataUsulanLama, $dataUsulanBaru);

                // 4. Proses dan perbarui path file dokumen di dalam data JSON
                $daftarDokumenKeys = [
                    'bukti_korespondensi', 'turnitin', 'upload_artikel', 'pakta_integritas', 'bukti_syarat_guru_besar'
                ];
                foreach ($daftarDokumenKeys as $key) {
                    if ($request->hasFile($key)) {
                        // Hapus file lama jika ada path-nya di data JSON
                        if (!empty($dataUsulanFinal[$key]) && Storage::disk('public')->exists($dataUsulanFinal[$key])) {
                            Storage::disk('public')->delete($dataUsulanFinal[$key]);
                        }
                        // Simpan file baru dan perbarui path di data JSON
                        $path = $request->file($key)->store('usulan-dokumen/' . $pegawai->id, 'public');
                        $dataUsulanFinal[$key] = $path;
                    }
                }

                // 5. Simpan semua perubahan ke database
                $usulan->update([
                    'status_usulan'   => $statusUsulan,
                    'catatan_pegawai' => $validatedData['catatan'] ?? null,
                    'data_usulan'     => $dataUsulanFinal, // Simpan kolom JSON yang sudah diperbarui
                ]);

                // 6. Catat log perubahan
                UsulanLog::create([
                    'usulan_id'         => $usulan->id,
                    'status_sebelumnya' => $usulan->getOriginal('status_usulan'),
                    'status_baru'       => $statusUsulan,
                    'catatan'           => 'Usulan diperbarui oleh pengusul.',
                    'dilakukan_oleh_id' => $pegawai->id,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Gagal memperbarui usulan jabatan: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan, data usulan tidak tersimpan. Silakan cek log.');
        }

        $message = $statusUsulan === 'Diajukan'
            ? 'Usulan kenaikan jabatan berhasil diperbarui dan diajukan.'
            : 'Perubahan pada usulan Anda berhasil disimpan sebagai Draft.';

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', $message);
    }

    public function showUsulanDocument(Usulan $usulan, $field)
    {
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403, 'TIDAK DIIZINKAN');
        }

        $filePath = $usulan->data_usulan[$field] ?? null;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($filePath));
    }
}
