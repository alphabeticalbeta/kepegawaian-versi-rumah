<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nip'      => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('pegawai')->attempt($credentials)) {
            $request->session()->regenerate();

            $pegawai = Auth::guard('pegawai')->user();

            // === PERBAIKAN DI SINI: Gunakan 'contains' untuk memeriksa role ===
            // Cek jika koleksi 'roles' milik pegawai mengandung role dengan nama tertentu.
            if ($pegawai->roles->contains('name', 'Admin Universitas Usulan')) {
                return redirect()->route('backend.admin-univ-usulan.dashboard');
            }

            if ($pegawai->roles->contains('name', 'Admin Fakultas')) {
                return redirect()->route('admin-fakultas.dashboard-fakultas');
            }
            // =============================================================

            // Redirect default jika tidak memiliki role admin di atas
            return redirect()->route('pegawai-unmul.dashboard-pegawai-unmul');
        }

        return back()->withErrors([
            'nip' => 'NIP atau Password yang Anda masukkan salah.',
        ])->onlyInput('nip');
    }




    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('pegawai')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function username()
    {
        return 'nip';
    }
}
