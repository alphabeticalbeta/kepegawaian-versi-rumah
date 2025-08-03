<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Http\Request;

class PusatUsulanController extends Controller
{
    /**
     * Menampilkan halaman utama Pusat Usulan (daftar semua periode).
     * (INI FUNGSI YANG BENAR UNTUK INDEX)
     */
    public function index()
    {
        $periodeUsulans = PeriodeUsulan::withCount('usulans')
                                       ->latest()
                                       ->paginate(10);

        return view('backend.layouts.admin-univ-usulan.pusat-usulan.index', [
            'periodeUsulans' => $periodeUsulans
        ]);
    }

    /**
     * Menampilkan daftar pendaftar untuk periode usulan tertentu.
     * (INI FUNGSI BARU YANG KITA BUAT)
     */
    public function showPendaftar(PeriodeUsulan $periodeUsulan)
    {
        $usulans = $periodeUsulan->usulans()
                                ->with('pegawai', 'jabatanLama', 'jabatanTujuan')
                                ->latest()
                                ->paginate(15);

        return view('backend.layouts.admin-univ-usulan.pusat-usulan.show-pendaftar', [
            'periode' => $periodeUsulan,
            'usulans' => $usulans,
        ]);
    }
}
