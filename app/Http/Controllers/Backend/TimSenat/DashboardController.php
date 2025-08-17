<?php

namespace App\Http\Controllers\Backend\TimSenat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pegawai')->user();

        // Statistik senat
        $stats = [
            'total_usulan_dosen' => Usulan::whereHas('pegawai', function($q) {
                $q->where('jenis_pegawai', 'Dosen');
            })->count(),
            'usulan_pending_review' => Usulan::where('status_usulan', 'Menunggu Review Senat')->count(),
            'usulan_reviewed' => Usulan::where('status_usulan', 'Sudah Direview Senat')->count(),
            'total_dosen' => Pegawai::where('jenis_pegawai', 'Dosen')->count(),
        ];

        // Usulan berdasarkan jabatan untuk chart
        $usulanByJabatan = Usulan::join('jabatans', 'usulans.jabatan_tujuan_id', '=', 'jabatans.id')
            ->selectRaw('jabatans.jabatan, COUNT(*) as count')
            ->groupBy('jabatans.id', 'jabatans.jabatan')
            ->get()
            ->pluck('count', 'jabatan')
            ->toArray();

        // Recent activities
        $recentUsulans = Usulan::with(['pegawai:id,nama_lengkap,nip', 'jabatanTujuan:id,jabatan'])
            ->whereHas('pegawai', function($q) {
                $q->where('jenis_pegawai', 'Dosen');
            })
            ->latest()
            ->take(10)
            ->get();

        return view('backend.layouts.views.tim-senat.dashboard', compact(
            'user',
            'stats',
            'usulanByJabatan',
            'recentUsulans'
        ));
    }
}
