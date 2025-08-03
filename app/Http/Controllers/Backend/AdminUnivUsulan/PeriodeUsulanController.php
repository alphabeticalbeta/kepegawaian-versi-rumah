<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Http\Request;
use App\Rules\NoDateRangeOverlap;

class PeriodeUsulanController extends Controller
{
    /**
     * Menampilkan daftar semua resource.
     * (Metode ini tidak lagi digunakan karena daftar ditampilkan di PusatUsulanController)
     */
    public function index()
    {
        return redirect()->route('backend.admin-univ-usulan.pusat-usulan.index');
    }

    /**
     * Menampilkan form untuk membuat resource baru.
     */
    public function create(Request $request)
    {
        $jenisUsulan = $request->query('jenis', 'jabatan');

        return view('backend.layouts.periode-usulan.form', [
            'jenis_usulan_otomatis' => $jenisUsulan
        ]);
    }

    /**
     * Menyimpan resource yang baru dibuat.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_periode'              => 'required|string|max:255',
            'jenis_usulan'              => 'required|string',
            'tanggal_mulai'             => ['required', 'date', new NoDateRangeOverlap($request)],
            'tanggal_selesai'           => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_perbaikan'   => 'nullable|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_perbaikan' => 'nullable|date|after_or_equal:tanggal_mulai_perbaikan',
            'status'                    => 'required|in:Buka,Tutup',
        ]);

        $validatedData['tahun_periode'] = \Carbon\Carbon::parse($validatedData['tanggal_mulai'])->year;

        PeriodeUsulan::create($validatedData);

        return redirect()->route('backend.admin-univ-usulan.pusat-usulan.index')
                         ->with('success', 'Periode Usulan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit resource.
     */
    public function edit(PeriodeUsulan $periodeUsulan)
    {
        return view('backend.layouts.periode-usulan.form', [
            'periode'                 => $periodeUsulan,
            'jenis_usulan_otomatis'   => $periodeUsulan->jenis_usulan,
        ]);
    }

    /**
     * Memperbarui resource yang ada di storage.
     */
    public function update(Request $request, PeriodeUsulan $periodeUsulan)
    {
        $validatedData = $request->validate([
            'nama_periode'              => 'required|string|max:255',
            'jenis_usulan'              => 'required|string',
            'tanggal_mulai'             => ['required', 'date', new NoDateRangeOverlap($request)],
            'tanggal_selesai'           => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_perbaikan'   => 'nullable|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_perbaikan' => 'nullable|date|after_or_equal:tanggal_mulai_perbaikan',
            'status'                    => 'required|in:Buka,Tutup',
        ]);

        $validatedData['tahun_periode'] = \Carbon\Carbon::parse($validatedData['tanggal_mulai'])->year;

        $periodeUsulan->update($validatedData);

        return redirect()->route('backend.admin-univ-usulan.pusat-usulan.index')
                         ->with('success', 'Periode Usulan berhasil diperbarui!');
    }

    /**
     * Menghapus resource dari storage.
     */
    public function destroy(PeriodeUsulan $periodeUsulan)
    {
        if ($periodeUsulan->usulans()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Periode ini sudah memiliki pendaftar.');
        }

        $periodeUsulan->delete();

        return back()->with('success', 'Periode Usulan berhasil dihapus.');
    }
}
