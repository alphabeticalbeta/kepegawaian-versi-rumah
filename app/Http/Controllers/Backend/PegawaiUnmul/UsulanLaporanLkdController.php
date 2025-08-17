<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Usulan;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsulanLaporanLkdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawai = Auth::user();

        $usulans = $pegawai->usulans()
                          ->where('jenis_usulan', 'usulan-laporan-lkd')
                          ->with(['periodeUsulan'])
                          ->latest()
                          ->paginate(10);

        return view('backend.layouts.views.pegawai-unmul.usulan-laporan-lkd.index', compact('usulans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Redirect ke halaman yang sesuai atau tampilkan form
        return redirect()->route('pegawai-unmul.usulan-pegawai.dashboard')
                         ->with('info', 'Fitur Usulan Laporan LKD akan segera tersedia.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation akan ditambahkan nanti
        return redirect()->route('pegawai-unmul.usulan-laporan-lkd.index')
                         ->with('success', 'Usulan Laporan LKD berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        return view('backend.layouts.views.pegawai-unmul.usulan-laporan-lkd.show', compact('usulan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        return view('backend.layouts.views.pegawai-unmul.usulan-laporan-lkd.edit', compact('usulan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Implementation akan ditambahkan nanti
        return redirect()->route('pegawai-unmul.usulan-laporan-lkd.index')
                         ->with('success', 'Usulan Laporan LKD berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usulan $usulan)
    {
        // Pastikan usulan milik user yang sedang login
        if ($usulan->pegawai_id !== Auth::id()) {
            abort(403);
        }

        // Implementation akan ditambahkan nanti
        return redirect()->route('pegawai-unmul.usulan-laporan-lkd.index')
                         ->with('success', 'Usulan Laporan LKD berhasil dihapus.');
    }
}
