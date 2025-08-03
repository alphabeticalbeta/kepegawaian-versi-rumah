<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Jabatan;

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
        // 1. Dapatkan ID pengguna yang sedang login
        $pegawaiId = Auth::id();

        // 2. Ambil data lengkap Pegawai dari database menggunakan ID tersebut
        // Ini memastikan kita mendapatkan model Eloquent yang benar.
        $pegawai = \App\Models\BackendUnivUsulan\Pegawai::with('jabatan')->find($pegawaiId);

        // Pastikan data pegawai ditemukan
        if (!$pegawai) {
            return redirect()->route('login')->with('error', 'Data pegawai tidak ditemukan.');
        }

        // 3. Ambil daftar periode yang statusnya "Buka" & jenisnya "jabatan"
        $daftarPeriode = PeriodeUsulan::where('status', 'Buka')
                                      ->where('jenis_usulan', 'jabatan')
                                      ->orderBy('tahun_periode', 'desc')
                                      ->get();

        // 4. Logika untuk menentukan jabatan tujuan
        $hirarkiJabatan = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];
        $jabatanSaatIni = $pegawai->jabatan->jabatan;
        $indexSaatIni = array_search($jabatanSaatIni, $hirarkiJabatan);

        $jabatanTujuan = null;
        if ($indexSaatIni !== false && $indexSaatIni < count($hirarkiJabatan) - 1) {
            $namaJabatanTujuan = $hirarkiJabatan[$indexSaatIni + 1];
            $jabatanTujuan = Jabatan::where('jabatan', $namaJabatanTujuan)->first();
        }

        // 5. Tampilkan view form dan kirim semua data yang dibutuhkan
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
