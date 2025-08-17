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

        // Mapping dari parameter URL ke jenis usulan yang benar
        $jenisMapping = [
            'jabatan' => 'Usulan Jabatan',
            'nuptk' => 'Usulan NUPTK',
            'laporan-lkd' => 'Usulan Laporan LKD',
            'presensi' => 'Usulan Presensi',
            'penyesuaian-masa-kerja' => 'Usulan Penyesuaian Masa Kerja',
            'ujian-dinas-ijazah' => 'Usulan Ujian Dinas & Ijazah',
            'laporan-serdos' => 'Usulan Laporan Serdos',
            'pensiun' => 'Usulan Pensiun',
            'kepangkatan' => 'Usulan Kepangkatan',
            'pencantuman-gelar' => 'Usulan Pencantuman Gelar',
            'id-sinta-sister' => 'Usulan ID SINTA ke SISTER',
            'satyalancana' => 'Usulan Satyalancana',
            'tugas-belajar' => 'Usulan Tugas Belajar',
            'pengaktifan-kembali' => 'Usulan Pengaktifan Kembali'
        ];

        $jenisUsulanOtomatis = $jenisMapping[$jenisUsulan] ?? 'Usulan Jabatan';

        // Tentukan view berdasarkan jenis usulan
        $viewMapping = [
            'usulan-jabatan-dosen' => 'backend.layouts.views.periode-usulan.form-jabatan-dosen',
            'usulan-jabatan-tendik' => 'backend.layouts.views.periode-usulan.form-jabatan-tendik',
        ];

        $view = $viewMapping[$jenisUsulan] ?? 'backend.layouts.views.periode-usulan.form';

        return view($view, [
            'jenis_usulan_otomatis' => $jenisUsulanOtomatis,
            'jenis_usulan_key' => $jenisUsulan,
            'nama_usulan' => $jenisUsulanOtomatis
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

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'Usulan Jabatan' => 'jabatan',
                'usulan-jabatan-dosen' => 'jabatan',
                'usulan-jabatan-tendik' => 'jabatan',
                'Usulan NUPTK' => 'nuptk',
                'Usulan Laporan LKD' => 'laporan-lkd',
                'Usulan Presensi' => 'presensi',
                'Usulan Penyesuaian Masa Kerja' => 'penyesuaian-masa-kerja',
                'Usulan Ujian Dinas & Ijazah' => 'ujian-dinas-ijazah',
                'Usulan Laporan Serdos' => 'laporan-serdos',
                'Usulan Pensiun' => 'pensiun',
                'Usulan Kepangkatan' => 'kepangkatan',
                'Usulan Pencantuman Gelar' => 'pencantuman-gelar',
                'Usulan ID SINTA ke SISTER' => 'id-sinta-sister',
                'Usulan Satyalancana' => 'satyalancana',
                'Usulan Tugas Belajar' => 'tugas-belajar',
                'Usulan Pengaktifan Kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$validated['jenis_usulan']] ?? 'jabatan';

            return redirect()->route('backend.admin-univ-usulan.dashboard-periode.index', ['jenis' => $jenisKey])
                ->with('success', 'Periode usulan berhasil dibuat!');
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
        return view('backend.layouts.views.periode-usulan.form', [
            'periode' => $periodeUsulan,
            'nama_usulan' => $periodeUsulan->jenis_usulan
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
                    excludeId: $periode_usulan->id
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

            // Redirect ke dashboard periode dengan jenis usulan yang sesuai
            $jenisMapping = [
                'Usulan Jabatan' => 'jabatan',
                'usulan-jabatan-dosen' => 'jabatan',
                'usulan-jabatan-tendik' => 'jabatan',
                'Usulan NUPTK' => 'nuptk',
                'Usulan Laporan LKD' => 'laporan-lkd',
                'Usulan Presensi' => 'presensi',
                'Usulan Penyesuaian Masa Kerja' => 'penyesuaian-masa-kerja',
                'Usulan Ujian Dinas & Ijazah' => 'ujian-dinas-ijazah',
                'Usulan Laporan Serdos' => 'laporan-serdos',
                'Usulan Pensiun' => 'pensiun',
                'Usulan Kepangkatan' => 'kepangkatan',
                'Usulan Pencantuman Gelar' => 'pencantuman-gelar',
                'Usulan ID SINTA ke SISTER' => 'id-sinta-sister',
                'Usulan Satyalancana' => 'satyalancana',
                'Usulan Tugas Belajar' => 'tugas-belajar',
                'Usulan Pengaktifan Kembali' => 'pengaktifan-kembali'
            ];

            $jenisKey = $jenisMapping[$periode_usulan->jenis_usulan] ?? 'jabatan';

            return redirect()->route('backend.admin-univ-usulan.dashboard-periode.index', ['jenis' => $jenisKey])
                ->with('success', 'Periode usulan berhasil diperbarui!');
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

        // Simpan jenis usulan sebelum dihapus untuk redirect
        $jenisUsulan = $periodeUsulan->jenis_usulan;

        $periodeUsulan->delete();

        // Redirect ke dashboard periode dengan jenis usulan yang sesuai
        $jenisMapping = [
            'Usulan Jabatan' => 'jabatan',
            'usulan-jabatan-dosen' => 'jabatan',
            'usulan-jabatan-tendik' => 'jabatan',
            'Usulan NUPTK' => 'nuptk',
            'Usulan Laporan LKD' => 'laporan-lkd',
            'Usulan Presensi' => 'presensi',
            'Usulan Penyesuaian Masa Kerja' => 'penyesuaian-masa-kerja',
            'Usulan Ujian Dinas & Ijazah' => 'ujian-dinas-ijazah',
            'Usulan Laporan Serdos' => 'laporan-serdos',
            'Usulan Pensiun' => 'pensiun',
            'Usulan Kepangkatan' => 'kepangkatan',
            'Usulan Pencantuman Gelar' => 'pencantuman-gelar',
            'Usulan ID SINTA ke SISTER' => 'id-sinta-sister',
            'Usulan Satyalancana' => 'satyalancana',
            'Usulan Tugas Belajar' => 'tugas-belajar',
            'Usulan Pengaktifan Kembali' => 'pengaktifan-kembali'
        ];

        $jenisKey = $jenisMapping[$jenisUsulan] ?? 'jabatan';

        return redirect()->route('backend.admin-univ-usulan.dashboard-periode.index', ['jenis' => $jenisKey])
            ->with('success', 'Periode Usulan berhasil dihapus!');
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
