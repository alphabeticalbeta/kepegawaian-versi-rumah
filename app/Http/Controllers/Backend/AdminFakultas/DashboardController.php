<?php

namespace App\Http\Controllers\Backend\AdminFakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

class DashboardController extends Controller
{
    /**
     * Display the admin fakultas dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get faculty-specific data
        $facultyData = $this->getFacultyData($user);

        // Get unit kerja data
        $unitKerja = $user->pegawai->unitKerja ?? null;

        // Get periode usulans with statistics
        $periodeUsulans = $this->getPeriodeUsulansWithStats($user);

        // Get usulan statistics for faculty
        $usulanStats = $this->getUsulanStatistics($user);

        // Get recent usulans for faculty
        $recentUsulans = $this->getRecentUsulans($user);

        // Get chart data
        $chartData = $this->getChartData($user);

        return view('backend.layouts.views.admin-fakultas.dashboard', [
            'facultyData' => $facultyData,
            'unitKerja' => $unitKerja,
            'periodeUsulans' => $periodeUsulans,
            'usulanStats' => $usulanStats,
            'recentUsulans' => $recentUsulans,
            'chartData' => $chartData,
            'user' => $user
        ]);
    }

    /**
     * Get faculty-specific data.
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getFacultyData($user)
    {
        // Get faculty information based on user's faculty
        $faculty = $user->pegawai->unitKerja ?? null;

        return [
            'faculty_name' => $faculty ? $faculty->nama_unit : 'Unknown Faculty',
            'faculty_code' => $faculty ? $faculty->kode_unit : 'UNK',
            'total_pegawai_faculty' => Pegawai::whereHas('unitKerja', function($query) use ($faculty) {
                if ($faculty) {
                    $query->where('id', $faculty->id);
                }
            })->count(),
        ];
    }

    /**
     * Get usulan statistics for faculty.
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getUsulanStatistics($user)
    {
        $faculty = $user->pegawai->unitKerja ?? null;

        $query = Usulan::query();

        if ($faculty) {
            $query->whereHas('pegawai.unitKerja', function($q) use ($faculty) {
                $q->where('id', $faculty->id);
            });
        }

        return [
            'total_usulan' => $query->count(),
            'usulan_pending' => (clone $query)->where('status_usulan', 'Diajukan')->count(),
            'usulan_approved' => (clone $query)->where('status_usulan', 'Direkomendasikan')->count(),
            'usulan_rejected' => (clone $query)->where('status_usulan', 'Ditolak')->count(),
            'usulan_returned' => (clone $query)->where('status_usulan', 'Perlu Perbaikan')->count(),
            'usulan_forwarded' => (clone $query)->where('status_usulan', 'Diusulkan ke Universitas')->count(),
        ];
    }

    /**
     * Get recent usulans for faculty.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentUsulans($user)
    {
        $faculty = $user->pegawai->unitKerja ?? null;

        $query = Usulan::with(['pegawai', 'periodeUsulan', 'jabatan']);

        if ($faculty) {
            $query->whereHas('pegawai.unitKerja', function($q) use ($faculty) {
                $q->where('id', $faculty->id);
            });
        }

        return $query->latest()->take(10)->get();
    }

    /**
     * Get periode usulans with statistics for faculty.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPeriodeUsulansWithStats($user)
    {
        $faculty = $user->pegawai->unitKerja ?? null;

        $periodeUsulans = PeriodeUsulan::with(['usulans' => function($query) use ($faculty) {
            if ($faculty) {
                $query->whereHas('pegawai.unitKerja', function($q) use ($faculty) {
                    $q->where('id', $faculty->id);
                });
            }
        }])->get();

        // Add statistics to each periode
        foreach ($periodeUsulans as $periode) {
            $periode->jumlah_pengusul = $periode->usulans->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])->count();
            $periode->total_usulan = $periode->usulans->count();
        }

        return $periodeUsulans;
    }

    /**
     * Get chart data for dashboard.
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getChartData($user)
    {
        $faculty = $user->pegawai->unitKerja ?? null;

        $query = Usulan::query();

        if ($faculty) {
            $query->whereHas('pegawai.unitKerja', function($q) use ($faculty) {
                $q->where('id', $faculty->id);
            });
        }

        // Monthly usulan submission data
        $monthlyData = (clone $query)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Status distribution
        $statusData = (clone $query)
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
