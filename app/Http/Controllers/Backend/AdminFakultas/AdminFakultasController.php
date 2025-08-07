<?php

namespace App\Http\Controllers\Backend\AdminFakultas;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminFakultasController extends Controller
{
    /**
     * Menampilkan dasbor untuk Admin Fakultas.
     * Dasbor ini berisi daftar usulan yang perlu diverifikasi.
     */
    public function index()
    {
        /** @var \App\Models\BackendUnivUsulan\Pegawai $admin */
        $admin = Auth::user();

        // Asumsi: Anda perlu menyesuaikan logika ini jika struktur DB berbeda.
        // Ini adalah contoh jika admin terhubung ke satu fakultas/unit kerja.
        // Jika admin bisa menangani banyak unit kerja, logikanya perlu diubah.
        $unitKerjaId = $admin->unit_kerja_terakhir_id; // Pastikan kolom ini benar

        $usulans = Usulan::with('pegawai', 'pegawai.unitKerja', 'jabatanLama', 'jabatanTujuan')
            ->where('status_usulan', 'Diajukan') // Atau status lain yang sesuai
            ->whereHas('pegawai', function ($query) use ($unitKerjaId) {
                $query->where('unit_kerja_terakhir_id', $unitKerjaId);
            })
            ->latest()
            ->paginate(10);

        // [AKTIFKAN BARIS INI]
        // Mengirim data $usulans ke view Anda
        return view('backend.layouts.admin-fakultas.dashboard', compact('usulans'));
    }
}
