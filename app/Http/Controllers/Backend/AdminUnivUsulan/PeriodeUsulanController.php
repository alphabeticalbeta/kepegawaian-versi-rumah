<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use Illuminate\Http\Request;
use App\Rules\NoDateRangeOverlap;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $validated = $request->validate([
            'nama_periode'            => ['required', 'string', 'max:255'],
            'jenis_usulan'            => ['required', 'string', 'max:255'],
            'tanggal_mulai'           => [
                'required', 'date',
                new NoDateRangeOverlap(
                    table: 'periode_usulans',
                    startColumn: 'tanggal_mulai',
                    endColumn: 'tanggal_selesai',
                    filters: ['jenis_usulan' => $request->input('jenis_usulan')],
                    excludeId: null
                ),
            ],
            'tanggal_selesai'         => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tanggal_mulai_perbaikan' => ['nullable', 'date', 'after_or_equal:tanggal_selesai'],
            'tanggal_selesai_perbaikan' => ['nullable', 'date', 'after_or_equal:tanggal_mulai_perbaikan'],
            'status'                  => ['required', 'in:Buka,Tutup'], // jika mau otomatis buka: hapus dari form dan set manual di bawah
            'senat_min_setuju'        => ['nullable', 'integer', 'min:0'],
        ]);

        // Hitung tahun_periode dari tanggal_mulai
        $validated['tahun_periode'] = Carbon::parse($validated['tanggal_mulai'])->year;

        // (Opsional) Jika ingin selalu buka saat buat:
        // $validated['status'] = 'Buka';

        // Defaultkan senat_min_setuju bila kosong
        $validated['senat_min_setuju'] = (int) ($validated['senat_min_setuju'] ?? 0);

        try {
            DB::transaction(function () use ($validated) {
                $periode = new PeriodeUsulan();
                $periode->fill($validated);   // boleh fill karena kita kontrol $validated
                $periode->save();
            });

            return back()->with('success', 'Periode usulan berhasil dibuat.');
        } catch (\Throwable $e) {
            report($e);
            // sementara untuk debug, boleh tampilkan pesan asli (hapus di produksi):
            return back()->withInput()->with('error', 'Gagal membuat periode: '.$e->getMessage());
        }
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
    public function update(Request $request, PeriodeUsulan $periode_usulan)
    {
        $request->validate([
            'tanggal_mulai' => [
                'required',
                'date',
                new NoDateRangeOverlap(
                    table: 'periode_usulans',
                    startColumn: 'tanggal_mulai',
                    endColumn: 'tanggal_selesai',
                    filters: ['jenis_usulan' => $request->input('jenis_usulan')],
                    excludeId: (int) $periode_usulan->id
                ),
            ],
            'tanggal_selesai'  => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_usulan'     => ['required', 'string', 'max:255'],
            'senat_min_setuju' => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($request, $periode_usulan) {
                $periode_usulan->tanggal_mulai     = $request->input('tanggal_mulai');
                $periode_usulan->tanggal_selesai   = $request->input('tanggal_selesai');
                $periode_usulan->jenis_usulan      = $request->input('jenis_usulan');
                $periode_usulan->senat_min_setuju  = (int) $request->input('senat_min_setuju', $periode_usulan->senat_min_setuju ?? 0);
                // TODO: set field lain yang kamu punya
                $periode_usulan->save();
            });

            // return redirect()->route('periode-usulan.index')->with('success', 'Periode usulan berhasil diperbarui.');
            return back()->with('success', 'Periode usulan berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Gagal memperbarui periode usulan. Coba lagi.');
        }
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
