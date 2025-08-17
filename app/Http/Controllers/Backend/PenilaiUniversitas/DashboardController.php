<?php

namespace App\Http\Controllers\Backend\PenilaiUniversitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\Pegawai;
use App\Models\BackendUnivUsulan\PeriodeUsulan;

class DashboardController extends Controller
{
    /**
     * Display the penilai universitas dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get assessment statistics
        $assessmentStats = $this->getAssessmentStatistics();

        // Get recent assessments
        $recentAssessments = $this->getRecentAssessments();

        // Get pending assessments
        $pendingAssessments = $this->getPendingAssessments();

        // Get chart data
        $chartData = $this->getChartData();

        return view('backend.layouts.views.penilai-universitas.dashboard', [
            'assessmentStats' => $assessmentStats,
            'recentAssessments' => $recentAssessments,
            'pendingAssessments' => $pendingAssessments,
            'chartData' => $chartData,
            'user' => $user
        ]);
    }

    /**
     * Get assessment statistics.
     *
     * @return array
     */
    private function getAssessmentStatistics()
    {
        return [
            'total_assessments' => Usulan::where('status_usulan', 'Sedang Dinilai')->count(),
            'completed_assessments' => Usulan::where('status_usulan', 'Direkomendasikan')->count(),
            'pending_assessments' => Usulan::where('status_usulan', 'Sedang Dinilai')->count(),
            'high_priority' => Usulan::where('status_usulan', 'Sedang Dinilai')
                ->where('priority', 'high')
                ->count(),
            'average_score' => Usulan::where('status_usulan', 'Direkomendasikan')
                ->whereNotNull('assessment_score')
                ->avg('assessment_score') ?? 0,
        ];
    }

    /**
     * Get recent assessments.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentAssessments()
    {
        return Usulan::with(['pegawai', 'periodeUsulan', 'jabatan'])
            ->whereIn('status_usulan', ['Direkomendasikan', 'Sedang Dinilai'])
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Get pending assessments.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingAssessments()
    {
        return Usulan::with(['pegawai', 'periodeUsulan', 'jabatan'])
            ->where('status_usulan', 'Sedang Dinilai')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();
    }

    /**
     * Get chart data for dashboard.
     *
     * @return array
     */
    private function getChartData()
    {
        // Monthly assessment completion data
        $monthlyData = Usulan::where('status_usulan', 'Direkomendasikan')
            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
            ->whereYear('updated_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Score distribution
        $scoreData = Usulan::where('status_usulan', 'Direkomendasikan')
            ->whereNotNull('assessment_score')
            ->selectRaw('FLOOR(assessment_score/10)*10 as score_range, COUNT(*) as count')
            ->groupBy('score_range')
            ->orderBy('score_range')
            ->get()
            ->pluck('count', 'score_range')
            ->toArray();

        // Assessment by periode
        $periodeData = Usulan::with('periodeUsulan')
            ->whereIn('status_usulan', ['Direkomendasikan', 'Sedang Dinilai'])
            ->selectRaw('periode_usulan_id, COUNT(*) as count')
            ->groupBy('periode_usulan_id')
            ->get()
            ->pluck('count', 'periodeUsulan.nama_periode')
            ->toArray();

        return [
            'monthly_completions' => $monthlyData,
            'score_distribution' => $scoreData,
            'periode_distribution' => $periodeData,
        ];
    }
}
