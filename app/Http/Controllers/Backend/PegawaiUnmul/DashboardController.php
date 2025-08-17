<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

class DashboardController extends Controller
{
    /**
     * Display the pegawai dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pegawaiId = Auth::id();

        // Get pegawai data
        $pegawai = Pegawai::findOrFail($pegawaiId);

        // Get all usulans with pagination
        $usulans = Usulan::where('pegawai_id', $pegawaiId)
            ->with(['periodeUsulan', 'jabatan'])
            ->latest()
            ->paginate(10);

        // Get usulan statistics
        $usulanStats = $this->getUsulanStatistics($pegawaiId);

        // Get recent usulans
        $recentUsulans = $this->getRecentUsulans($pegawaiId);

        // Get active periods
        $activePeriods = $this->getActivePeriods();

        // Get chart data
        $chartData = $this->getChartData($pegawaiId);

        return view('backend.layouts.views.pegawai-unmul.dashboard', [
            'pegawai' => $pegawai,
            'usulans' => $usulans,
            'usulanStats' => $usulanStats,
            'recentUsulans' => $recentUsulans,
            'activePeriods' => $activePeriods,
            'chartData' => $chartData,
            'user' => Auth::user()
        ]);
    }

    /**
     * Get usulan statistics for the pegawai.
     *
     * @param int $pegawaiId
     * @return array
     */
    private function getUsulanStatistics($pegawaiId)
    {
        return [
            'total_usulan' => Usulan::where('pegawai_id', $pegawaiId)->count(),
            'usulan_pending' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', 'Diajukan')->count(),
            'usulan_approved' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', 'Direkomendasikan')->count(),
            'usulan_rejected' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', 'Ditolak')->count(),
            'usulan_returned' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', 'Perlu Perbaikan')->count(),
            'usulan_draft' => Usulan::where('pegawai_id', $pegawaiId)->where('status_usulan', 'Draft')->count(),
        ];
    }

    /**
     * Get recent usulans for the pegawai.
     *
     * @param int $pegawaiId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentUsulans($pegawaiId)
    {
        return Usulan::where('pegawai_id', $pegawaiId)
            ->with(['periodeUsulan', 'jabatan'])
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Get active periods.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getActivePeriods()
    {
        return PeriodeUsulan::where('status', 'Buka')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->get();
    }

    /**
     * Get chart data for dashboard.
     *
     * @param int $pegawaiId
     * @return array
     */
    private function getChartData($pegawaiId)
    {
        // Monthly usulan submission data for this pegawai
        $monthlyData = Usulan::where('pegawai_id', $pegawaiId)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Status distribution for this pegawai
        $statusData = Usulan::where('pegawai_id', $pegawaiId)
            ->selectRaw('status_usulan, COUNT(*) as count')
            ->groupBy('status_usulan')
            ->pluck('count', 'status_usulan')
            ->toArray();

        return [
            'monthly_submissions' => $monthlyData,
            'status_distribution' => $statusData,
        ];
    }
}
