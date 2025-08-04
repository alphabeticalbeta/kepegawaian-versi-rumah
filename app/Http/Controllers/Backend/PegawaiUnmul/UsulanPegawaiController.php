<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Http\Requests\Backend\PegawaiUnmul\StoreJabatanUsulanRequest;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\UsulanDokumen;
use App\Models\BackendUnivUsulan\UsulanLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UsulanPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        // 1. Dapatkan ID pegawai yang sedang login
        $pegawaiId = Auth::id();

        // 2. Ambil data usulan HANYA untuk pegawai tersebut dari database
        $usulans = \App\Models\BackendUnivUsulan\Usulan::where('pegawai_id', $pegawaiId)
                                                     ->with('periodeUsulan') // Ambil juga data periode terkait
                                                     ->latest()
                                                     ->paginate(10);

        // 3. Tampilkan view dan kirim data usulan
        return view('backend.layouts.pegawai-unmul.usulan-pegawai.dashboard', [
            'usulans' => $usulans
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createJabatan()
    {
        $pegawai = Pegawai::with(['jabatan', 'pangkat', 'unitKerja'])
                        ->findOrFail(Auth::id());

        if (is_null($pegawai->pangkat_terakhir_id) || is_null($pegawai->jabatan_terakhir_id) || is_null($pegawai->unit_kerja_terakhir_id)) {
            return redirect()->route('pegawai-unmul.profile.edit')
                ->with('warning', 'Profil Anda belum lengkap. Silakan lengkapi data Pangkat, Jabatan, dan Unit Kerja sebelum membuat usulan.');
        }

        $daftarPeriode = PeriodeUsulan::where('jenis_usulan', 'jabatan')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->where('status', 'Buka')
            ->first();

        if (!$daftarPeriode) {
            return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                ->with('error', 'Saat ini tidak ada periode pengajuan usulan jabatan yang aktif atau dibuka.');
        }

        $jabatanTujuanId = ($pegawai->jabatan->id ?? 0) + 1;
        $jabatanTujuan = Jabatan::find($jabatanTujuanId);

        // Baris di bawah ini tidak akan pernah dijalankan untuk sementara
        return view('backend.layouts.pegawai-unmul.usul-jabatan.create-jabatan', [
            'pegawai' => $pegawai,
            'daftarPeriode' => $daftarPeriode,
            'jabatanTujuan' => $jabatanTujuan,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function storeJabatan(StoreJabatanUsulanRequest $request)
    {
        $validatedData = $request->validated();
        $pegawai       = Auth::user();

        // Tentukan jabatan tujuan
        $jabatanLama   = $pegawai->jabatan;
        $jabatanTujuan = $jabatanLama
            ? Jabatan::where('id', '>', $jabatanLama->id)->orderBy('id', 'asc')->first()
            : null;

        // Snapshot lengkap data pegawai
        $dataSnapshotProfil = [
            'jenis_pegawai'                    => $pegawai->jenis_pegawai,
            'status_kepegawaian'               => $pegawai->status_kepegawaian,
            'nip'                              => $pegawai->nip,
            'nuptk'                            => $pegawai->nuptk,
            'gelar_depan'                      => $pegawai->gelar_depan,
            'nama_lengkap'                     => $pegawai->nama_lengkap,
            'gelar_belakang'                   => $pegawai->gelar_belakang,
            'email'                            => $pegawai->email,
            'tempat_lahir'                     => $pegawai->tempat_lahir,
            'tanggal_lahir'                    => optional($pegawai->tanggal_lahir)->toDateString(),
            'jenis_kelamin'                    => $pegawai->jenis_kelamin,
            'agama'                            => $pegawai->agama,
            'status_perkawinan'                => $pegawai->status_perkawinan,
            'nomor_handphone'                  => $pegawai->nomor_handphone,
            'alamat'                           => $pegawai->alamat,
            'pangkat_saat_usul'                => $pegawai->pangkat?->pangkat,
            'golongan_saat_usul'               => $pegawai->pangkat?->golongan,
            'tmt_pangkat'                      => optional($pegawai->tmt_pangkat)->toDateString(),
            'jabatan_saat_usul'                => $pegawai->jabatan?->jabatan,
            'tmt_jabatan'                      => optional($pegawai->tmt_jabatan)->toDateString(),
            'tmt_cpns'                         => optional($pegawai->tmt_cpns)->toDateString(),
            'tmt_pns'                          => optional($pegawai->tmt_pns)->toDateString(),
            'unit_kerja_saat_usul'             => $pegawai->unitKerja?->nama,
            'pendidikan_terakhir'              => $pegawai->pendidikan_terakhir,
            'mata_kuliah_diampu'               => $pegawai->mata_kuliah_diampu,
            'ranting_ilmu_kepakaran'           => $pegawai->ranting_ilmu_kepakaran,
            'url_profil_sinta'                 => $pegawai->url_profil_sinta,
            'predikat_kinerja_tahun_pertama'    => $pegawai->predikat_kinerja_tahun_pertama,
            'predikat_kinerja_tahun_kedua'     => $pegawai->predikat_kinerja_tahun_kedua,
            'nilai_konversi'                   => $pegawai->nilai_konversi,
        ];

        // Tambah data artikel dari formulir
        $dataUsulanTambahan = [
            'karya_ilmiah'    => $validatedData['karya_ilmiah']    ?? null,
            'nama_jurnal'     => $validatedData['nama_jurnal']     ?? null,
            'judul_artikel'   => $validatedData['judul_artikel']   ?? null,
            'penerbit_artikel'=> $validatedData['penerbit_artikel']?? null,
            'volume_artikel'  => $validatedData['volume_artikel']  ?? null,
            'nomor_artikel'   => $validatedData['nomor_artikel']   ?? null,
            'edisi_artikel'   => $validatedData['edisi_artikel']   ?? null,
            'halaman_artikel' => $validatedData['halaman_artikel'] ?? null,
            'issn_artikel'    => $validatedData['issn_artikel']    ?? null,
            'link_artikel'    => $validatedData['link_artikel']    ?? null,
            'catatan_pengusul'=> $validatedData['catatan']         ?? null,
        ];

        // Gabungkan ke kolom JSON data_usulan
        $dataUsulan = array_merge($dataSnapshotProfil, $dataUsulanTambahan);

        try {
            DB::transaction(function () use ($pegawai, $jabatanLama, $jabatanTujuan, $validatedData, $dataUsulan) {
                // Buat usulan baru
                $usulan = Usulan::create([
                    'pegawai_id'         => $pegawai->id,
                    'periode_usulan_id'  => $validatedData['periode_usulan_id'],
                    'jenis_usulan'       => 'jabatan',
                    'jabatan_lama_id'    => $jabatanLama?->id,
                    'jabatan_tujuan_id'  => $jabatanTujuan?->id,
                    'status_usulan'      => 'Draft',
                    'data_usulan'        => $dataUsulan,
                    'catatan_verifikator'=> null,
                ]);

                // Simpan semua dokumen pendukung
                foreach ($validatedData['dokumen'] as $key => $file) {
                    $path = $file->store('usulan-dokumen/' . $pegawai->id, 'public');
                    UsulanDokumen::create([
                        'usulan_id'   => $usulan->id,
                        'pegawai_id'  => $pegawai->id,
                        'nama_dokumen'=> $key,
                        'path'        => $path,
                    ]);
                }

                // Catat log awal
                UsulanLog::create([
                    'usulan_id'       => $usulan->id,
                    'status_sebelumnya' => null,
                    'status_baru'     => 'Draft',
                    'catatan'         => $validatedData['catatan'] ?? null,
                    'dilakukan_oleh_id'=> $pegawai->id,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan usulan jabatan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan, data usulan tidak tersimpan.');
        }

        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
            ->with('success', 'Usulan kenaikan jabatan berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
