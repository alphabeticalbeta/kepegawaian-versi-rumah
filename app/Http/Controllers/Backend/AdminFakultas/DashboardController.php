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
        try {
            \Log::info('AdminFakultas Dashboard accessed', ['user_id' => Auth::guard('pegawai')->id()]);
            $user = Auth::guard('pegawai')->user();

            // Handle case where user is not authenticated (for testing)
            if (!$user) {
                return view('backend.layouts.views.admin-fakultas.dashboard', [
                    'unitKerja' => null,
                    'periodeUsulans' => collect(),
                    'user' => null
                ]);
            }

            // Check if user has unit kerja data
            if (!$user->unitKerjaPengelola) {
                return view('backend.layouts.views.admin-fakultas.dashboard', [
                    'unitKerja' => null,
                    'periodeUsulans' => collect(),
                    'user' => $user,
                    'error' => 'Unit kerja tidak ditemukan. Periksa pengaturan akun Anda.'
                ]);
            }

            // Get unit kerja data safely
            $unitKerja = $user->unitKerjaPengelola;

            // Get periode usulans with statistics
            $periodeUsulans = $this->getPeriodeUsulansWithStats($user);

            return view('backend.layouts.views.admin-fakultas.dashboard', [
                'unitKerja' => $unitKerja,
                'periodeUsulans' => $periodeUsulans,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('AdminFakultas Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::guard('pegawai')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return safe fallback view
            return view('backend.layouts.views.admin-fakultas.dashboard', [
                'unitKerja' => null,
                'periodeUsulans' => collect(),
                'user' => Auth::guard('pegawai')->user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Get periode usulans with statistics for faculty.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
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
        }])->paginate(10);

        // Add statistics to each periode
        foreach ($periodeUsulans as $periode) {
            $periode->jumlah_pengusul = $periode->usulans->whereIn('status_usulan', ['Diajukan', 'Sedang Direview'])->count();
            $periode->total_usulan = $periode->usulans->count();
        }

        return $periodeUsulans;
    }
}
