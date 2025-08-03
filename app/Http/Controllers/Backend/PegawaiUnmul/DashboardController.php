<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Tambahkan ini

class DashboardController extends Controller
{
    public function index()
    {
        // Dapatkan ID pegawai yang sedang login
        $pegawaiId = Auth::id();

        // Ambil data usulan HANYA untuk pegawai tersebut
        $usulans = \App\Models\BackendUnivUsulan\Usulan::where('pegawai_id', $pegawaiId)
                                                     ->with('periodeUsulan')
                                                     ->latest()
                                                     ->paginate(10);

        // Kirim data ke view dashboard
        return view('backend.layouts.pegawai-unmul.dashboard', [
            'usulans' => $usulans
        ]);
    }
}
