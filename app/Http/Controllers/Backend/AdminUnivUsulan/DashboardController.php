<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin univ usulan dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            \Log::info('AdminUnivUsulan Dashboard accessed successfully', ['user_id' => Auth::id()]);

            // Get active periods for different usulan types
            $activePeriods = \App\Models\BackendUnivUsulan\PeriodeUsulan::where('status', 'Buka')
                ->with(['usulans' => function($query) {
                    $query->with('pegawai:id,nama_lengkap,nip')
                          ->latest()
                          ->limit(5);
                }])
                ->get();

            // Get recent usulans for validation (status: Diusulkan ke Universitas)
            $recentUsulans = \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Diusulkan ke Universitas')
                ->with(['pegawai:id,nama_lengkap,nip', 'periodeUsulan'])
                ->latest()
                ->limit(10)
                ->get();

            // Get statistics
            $stats = [
                'total_periods' => $activePeriods->count(),
                'total_usulans_pending' => $recentUsulans->count(),
                'total_usulans_all' => \App\Models\BackendUnivUsulan\Usulan::count(),
                'usulans_by_status' => [
                    'Diajukan' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Diajukan')->count(),
                    'Diusulkan ke Universitas' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Diusulkan ke Universitas')->count(),
                    'Sedang Direview' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Sedang Direview')->count(),
                    'Direkomendasikan' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Direkomendasikan')->count(),
                    'Disetujui' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Disetujui')->count(),
                    'Ditolak' => \App\Models\BackendUnivUsulan\Usulan::where('status_usulan', 'Ditolak')->count(),
                ]
            ];

            return view('backend.layouts.views.admin-univ-usulan.dashboard', [
                'activePeriods' => $activePeriods,
                'recentUsulans' => $recentUsulans,
                'stats' => $stats,
                'user' => Auth::user()
            ]);
        } catch (\Exception $e) {
            // Log the specific error
            \Log::error('AdminUnivUsulan Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // If even the minimal version fails, return a basic error page
            return response()->view('backend.layouts.views.admin-univ-usulan.dashboard', [
                'activePeriods' => collect(),
                'recentUsulans' => collect(),
                'stats' => [],
                'user' => Auth::user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ], 500);
        }
    }
}
