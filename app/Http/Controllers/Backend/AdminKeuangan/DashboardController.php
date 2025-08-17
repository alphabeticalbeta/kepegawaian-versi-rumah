<?php

namespace App\Http\Controllers\Backend\AdminKeuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\Usulan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pegawai')->user();

        // Statistik keuangan
        $stats = [
            'total_usulan' => Usulan::count(),
            'usulan_pending' => Usulan::where('status_usulan', 'Menunggu Verifikasi')->count(),
            'usulan_approved' => Usulan::where('status_usulan', 'Disetujui')->count(),
            'usulan_rejected' => Usulan::where('status_usulan', 'Ditolak')->count(),
        ];

        // Data usulan berdasarkan status untuk chart
        $usulanByStatus = Usulan::selectRaw('status_usulan, COUNT(*) as count')
            ->groupBy('status_usulan')
            ->get()
            ->pluck('count', 'status_usulan')
            ->toArray();

        // Recent activities
        $recentUsulans = Usulan::with(['pegawai:id,nama_lengkap,nip'])
            ->latest()
            ->take(10)
            ->get();

        return view('backend.layouts.views.admin-keuangan.dashboard', compact(
            'user',
            'stats',
            'usulanByStatus',
            'recentUsulans'
        ));
    }
}
