<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request)
    {
        // Build query dengan filter
        $query = Jabatan::query();

        // Filter berdasarkan jenis pegawai
        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        // Filter berdasarkan jenis jabatan
        if ($request->filled('jenis_jabatan')) {
            $query->where('jenis_jabatan', $request->jenis_jabatan);
        }

        // Search berdasarkan nama jabatan
        if ($request->filled('search')) {
            $query->where('jabatan', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan hirarki
        if ($request->filled('has_hierarchy')) {
            if ($request->has_hierarchy === 'yes') {
                $query->whereNotNull('hierarchy_level');
            } elseif ($request->has_hierarchy === 'no') {
                $query->whereNull('hierarchy_level');
            }
        }

        // Filter berdasarkan eligibility untuk usulan
        if ($request->filled('eligible_usulan')) {
            if ($request->eligible_usulan === 'yes') {
                $query->where('jenis_jabatan', '!=', 'Tenaga Kependidikan Struktural');
            } elseif ($request->eligible_usulan === 'no') {
                $query->where('jenis_jabatan', 'Tenaga Kependidikan Struktural');
            }
        }

        // SORTING HIERARCHY: Terendah ke Tertinggi
        $jabatans = $query->orderBy('jenis_pegawai', 'asc')
                          ->orderBy('jenis_jabatan', 'asc')
                          ->orderByRaw('ISNULL(hierarchy_level), hierarchy_level ASC') // NULL di akhir, angka dari kecil ke besar
                          ->orderBy('jabatan', 'asc')
                          ->paginate(20)
                          ->appends($request->query()); // Maintain filter params di pagination

        // Data untuk dropdown filter
        $filterData = [
            'jenis_pegawai_options' => Jabatan::distinct()->pluck('jenis_pegawai')->sort(),
            'jenis_jabatan_options' => Jabatan::distinct()->pluck('jenis_jabatan')->sort(),
        ];

        // Statistik untuk dashboard
        $stats = [
            'total' => Jabatan::count(),
            'dengan_hirarki' => Jabatan::whereNotNull('hierarchy_level')->count(),
            'tanpa_hirarki' => Jabatan::whereNull('hierarchy_level')->count(),
            'dapat_usulan' => Jabatan::where('jenis_jabatan', '!=', 'Tenaga Kependidikan Struktural')->count(),
        ];

        return view('backend.layouts.admin-univ-usulan.jabatan.master-data-jabatan', compact('jabatans', 'filterData', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.admin-univ-usulan.jabatan.form-jabatan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pegawai' => 'required|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255|unique:jabatans,jabatan',
            'hierarchy_level' => 'nullable|integer|min:1|max:100',
        ], [
            'hierarchy_level.integer' => 'Level hirarki harus berupa angka.',
            'hierarchy_level.min' => 'Level hirarki minimal 1.',
            'hierarchy_level.max' => 'Level hirarki maksimal 100.',
        ]);

        Jabatan::create([
            'jenis_pegawai' => $request->jenis_pegawai,
            'jenis_jabatan' => $request->jenis_jabatan,
            'jabatan' => $request->jabatan,
            'hierarchy_level' => $request->hierarchy_level,
        ]);

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                        ->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jabatan $jabatan)
    {
        return view('backend.layouts.admin-univ-usulan.jabatan.show', compact('jabatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan)
    {
        return view('backend.layouts.admin-univ-usulan.jabatan.form-jabatan', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'jenis_pegawai' => 'required|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255|unique:jabatans,jabatan,' . $jabatan->id,
            'hierarchy_level' => 'nullable|integer|min:1|max:100',
        ], [
            'hierarchy_level.integer' => 'Level hirarki harus berupa angka.',
            'hierarchy_level.min' => 'Level hirarki minimal 1.',
            'hierarchy_level.max' => 'Level hirarki maksimal 100.',
        ]);

        $jabatan->update([
            'jenis_pegawai' => $request->jenis_pegawai,
            'jenis_jabatan' => $request->jenis_jabatan,
            'jabatan' => $request->jabatan,
            'hierarchy_level' => $request->hierarchy_level,
        ]);

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                        ->with('success', 'Data jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        // Check if jabatan is being used
        $usedInUsulan = $jabatan->usulanJabatanLama()->exists() || $jabatan->usulanJabatanTujuan()->exists();
        $usedByPegawai = $jabatan->pegawais()->exists();

        if ($usedInUsulan || $usedByPegawai) {
            return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                            ->with('error', 'Jabatan tidak dapat dihapus karena sedang digunakan oleh pegawai atau usulan.');
        }

        $jabatan->delete();

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                        ->with('success', 'Data jabatan berhasil dihapus.');
    }

    /**
     * Export data jabatan (bonus feature)
     */
    public function export(Request $request)
    {
        // Apply same filters as index
        $query = Jabatan::query();

        if ($request->filled('jenis_pegawai')) {
            $query->where('jenis_pegawai', $request->jenis_pegawai);
        }

        if ($request->filled('jenis_jabatan')) {
            $query->where('jenis_jabatan', $request->jenis_jabatan);
        }

        if ($request->filled('search')) {
            $query->where('jabatan', 'like', '%' . $request->search . '%');
        }

        $jabatans = $query->orderBy('jenis_pegawai', 'asc')
                          ->orderBy('jenis_jabatan', 'asc')
                          ->orderByRaw('ISNULL(hierarchy_level), hierarchy_level ASC')
                          ->orderBy('jabatan', 'asc')
                          ->get();

        // Simple CSV export
        $filename = 'jabatan_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($jabatans) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['No', 'Jenis Pegawai', 'Jenis Jabatan', 'Nama Jabatan', 'Level Hirarki', 'Dapat Usulan']);

            // Data
            foreach ($jabatans as $index => $jabatan) {
                fputcsv($file, [
                    $index + 1,
                    $jabatan->jenis_pegawai,
                    $jabatan->jenis_jabatan,
                    $jabatan->jabatan,
                    $jabatan->hierarchy_level ?? '-',
                    $jabatan->isEligibleForUsulan() ? 'Ya' : 'Tidak'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
