<?php

namespace App\Http\Controllers\Backend\AdminUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

class DashboardController extends Controller
{
    /**
     * Display the admin universitas dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics for dashboard
        $statistics = $this->getDashboardStatistics();

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get chart data
        $chartData = $this->getChartData();

        return view('backend.layouts.views.admin-universitas.dashboard', [
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData,
            'user' => Auth::user()
        ]);
    }

    /**
     * Get dashboard statistics.
     *
     * @return array
     */
    private function getDashboardStatistics()
    {
        return [
            'total_pegawai' => Pegawai::count(),
            'total_usulan' => Usulan::count(),
            'usulan_pending' => Usulan::where('status_usulan', 'Diajukan')->count(),
            'usulan_approved' => Usulan::where('status_usulan', 'Direkomendasikan')->count(),
            'usulan_rejected' => Usulan::where('status_usulan', 'Ditolak')->count(),
            'periode_aktif' => PeriodeUsulan::where('status', 'Buka')->count(),
        ];
    }

    /**
     * Get recent activities.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentActivities()
    {
        return Usulan::with(['pegawai', 'periodeUsulan'])
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Get chart data for dashboard.
     *
     * @return array
     */
    private function getChartData()
    {
        // Monthly usulan submission data
        $monthlyData = Usulan::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Status distribution
        $statusData = Usulan::selectRaw('status_usulan, COUNT(*) as count')
            ->groupBy('status_usulan')
            ->pluck('count', 'status_usulan')
            ->toArray();

        return [
            'monthly_submissions' => $monthlyData,
            'status_distribution' => $statusData,
        ];
    }
}
