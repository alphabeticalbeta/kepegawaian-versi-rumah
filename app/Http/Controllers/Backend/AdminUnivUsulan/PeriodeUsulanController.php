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

        // Tentukan view berdasarkan jenis usulan
        $viewMapping = [
            'usulan-jabatan-dosen' => 'backend.layouts.periode-usulan.form-jabatan-dosen',
            'usulan-jabatan-tendik' => 'backend.layouts.periode-usulan.form-jabatan-tendik',
        ];

        $view = $viewMapping[$jenisUsulan] ?? 'backend.layouts.periode-usulan.form';

        return view($view, [
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
            'jenis_usulan'              => 'required|string|in:usulan-jabatan-dosen,usulan-jabatan-tendik',
            'tanggal_mulai'             => ['required', 'date', new NoDateRangeOverlap($request)],
            'tanggal_selesai'           => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_perbaikan'   => 'nullable|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_perbaikan' => 'nullable|date|after_or_equal:tanggal_mulai_perbaikan',
            'status'                    => 'required|in:Buka,Tutup',
        ]);

        // Validasi tambahan: cek overlap berdasarkan jenis usulan yang sama
        $this->validateOverlapByJenisUsulan($request);

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
            'periode' => $periodeUsulan
        ]);
    }

    /**
     * Memperbarui resource yang ada di storage.
     */
    public function update(Request $request, PeriodeUsulan $periodeUsulan)
    {
        $validatedData = $request->validate([
            'nama_periode'              => 'required|string|max:255',
            'jenis_usulan'              => 'required|string|in:usulan-jabatan-dosen,usulan-jabatan-tendik',
            'tanggal_mulai'             => 'required|date',
            'tanggal_selesai'           => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_perbaikan'   => 'nullable|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_perbaikan' => 'nullable|date|after_or_equal:tanggal_mulai_perbaikan',
            'status'                    => 'required|in:Buka,Tutup',
        ]);

        // Validasi tambahan: cek overlap berdasarkan jenis usulan yang sama (kecuali periode yang sedang di-edit)
        $this->validateOverlapByJenisUsulan($request, $periodeUsulan->id);

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

    /**
     * Validasi overlap berdasarkan jenis usulan yang sama
     */
    private function validateOverlapByJenisUsulan(Request $request, $excludeId = null)
    {
        $jenisUsulan = $request->jenis_usulan;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        $query = PeriodeUsulan::where('jenis_usulan', $jenisUsulan)
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                  ->orWhere(function ($subQ) use ($tanggalMulai, $tanggalSelesai) {
                      $subQ->where('tanggal_mulai', '<=', $tanggalMulai)
                           ->where('tanggal_selesai', '>=', $tanggalSelesai);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $overlappingPeriode = $query->first();

        if ($overlappingPeriode) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal_mulai' => [
                    "Tanggal periode overlapping dengan periode '{$overlappingPeriode->nama_periode}' yang memiliki jenis usulan yang sama ({$jenisUsulan})"
                ]
            ]);
        }
    }
}
