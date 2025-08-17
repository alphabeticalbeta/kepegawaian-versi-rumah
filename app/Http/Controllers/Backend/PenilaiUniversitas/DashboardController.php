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
        try {
            \Log::info('PenilaiUniversitas Dashboard accessed', ['user_id' => Auth::id()]);
            $user = Auth::user();

            // Handle case where user is not authenticated (for testing)
            if (!$user) {
                return view('backend.layouts.views.penilai-universitas.dashboard', [
                    'assessmentStats' => $this->getDefaultAssessmentStats(),
                    'recentAssessments' => collect(),
                    'pendingAssessments' => collect(),
                    'user' => null
                ]);
            }

            // Get assessment statistics
            $assessmentStats = $this->getAssessmentStatistics();

            // Get recent assessments
            $recentAssessments = $this->getRecentAssessments();

            // Get pending assessments
            $pendingAssessments = $this->getPendingAssessments();

            return view('backend.layouts.views.penilai-universitas.dashboard', [
                'assessmentStats' => $assessmentStats,
                'recentAssessments' => $recentAssessments,
                'pendingAssessments' => $pendingAssessments,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('PenilaiUniversitas Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe fallback view
            return view('backend.layouts.views.penilai-universitas.dashboard', [
                'assessmentStats' => $this->getDefaultAssessmentStats(),
                'recentAssessments' => collect(),
                'pendingAssessments' => collect(),
                'user' => Auth::user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get assessment statistics.
     *
     * @return array
     */
    private function getAssessmentStatistics()
    {
        try {
            return [
                'total_assessments' => Usulan::where('status_usulan', 'Sedang Dinilai')->count(),
                'completed_assessments' => Usulan::where('status_usulan', 'Direkomendasikan')->count(),
                'pending_assessments' => Usulan::where('status_usulan', 'Sedang Dinilai')->count(),
                'average_score' => 0,
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting assessment statistics: ' . $e->getMessage());
            return $this->getDefaultAssessmentStats();
        }
    }

    /**
     * Get default assessment statistics when database is not available.
     *
     * @return array
     */
    private function getDefaultAssessmentStats()
    {
        return [
            'total_assessments' => 0,
            'completed_assessments' => 0,
            'pending_assessments' => 0,
            'average_score' => 0,
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
        try {
            return Usulan::with(['pegawai', 'periodeUsulan', 'jabatan'])
                ->where('status_usulan', 'Sedang Dinilai')
                ->orderBy('created_at', 'asc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error getting pending assessments: ' . $e->getMessage());
            return collect();
        }
    }
}
