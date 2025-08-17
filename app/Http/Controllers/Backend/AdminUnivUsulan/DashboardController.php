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
            
            // Return a minimal dashboard without any database queries
            return view('backend.layouts.views.admin-univ-usulan.dashboard', [
                'recentUsulans' => collect(),
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
                'recentUsulans' => collect(),
                'user' => Auth::user(),
                'error' => 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.'
            ], 500);
        }
    }
}
