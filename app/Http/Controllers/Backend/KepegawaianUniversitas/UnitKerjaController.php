<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\UnitKerja;
use App\Models\KepegawaianUniversitas\SubUnitKerja;
use App\Models\KepegawaianUniversitas\SubSubUnitKerja;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class UnitKerjaController extends Controller
{
    private $fileStorage;
    private $validationService;

    public function __construct(FileStorageService $fileStorage, ValidationService $validationService)
    {
        $this->fileStorage = $fileStorage;
        $this->validationService = $validationService;
    }

    public function index(Request $request)
    {
        // Query builder dengan search dan filter
        $query = UnitKerja::with(['subUnitKerjas.subSubUnitKerjas'])
            ->when($request->search, function ($q, $search) {
                return $q->where(function($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                          ->orWhereHas('subUnitKerjas', function($subQuery) use ($search) {
                              $subQuery->where('nama', 'like', "%{$search}%")
                                       ->orWhereHas('subSubUnitKerjas', function($subSubQuery) use ($search) {
                                           $subSubQuery->where('nama', 'like', "%{$search}%");
                                       });
                          });
                });
            })
            ->when($request->level, function ($q, $level) {
                switch ($level) {
                    case 'unit_kerja':
                        return $q->whereDoesntHave('subUnitKerjas');
                    case 'sub_unit_kerja':
                        return $q->whereHas('subUnitKerjas', function($query) {
                            $query->whereDoesntHave('subSubUnitKerjas');
                        });
                    case 'sub_sub_unit_kerja':
                        return $q->whereHas('subUnitKerjas.subSubUnitKerjas');
                }
            })
            ->orderBy('nama');

        $unitKerjas = $query->get();

        // Handle AJAX request
        if ($request->ajax()) {
            $data = $unitKerjas->map(function($unitKerja) {
                return [
                    'id' => $unitKerja->id,
                    'nama' => $unitKerja->nama,
                    'level' => 'unit_kerja',
                    'sub_unit_kerjas' => $unitKerja->subUnitKerjas->map(function($subUnitKerja) {
                        return [
                            'id' => $subUnitKerja->id,
                            'nama' => $subUnitKerja->nama,
                            'level' => 'sub_unit_kerja',
                            'sub_sub_unit_kerjas' => $subUnitKerja->subSubUnitKerjas->map(function($subSubUnitKerja) {
                                return [
                                    'id' => $subSubUnitKerja->id,
                                    'nama' => $subSubUnitKerja->nama,
                                    'level' => 'sub_sub_unit_kerja'
                                ];
                            })
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $unitKerjas->count()
            ]);
        }

        return view('backend.layouts.views.kepegawaian-universitas.unitkerja.master-data-unitkerja', compact('unitKerjas'));
    }

    public function create()
    {
        return view('backend.layouts.views.kepegawaian-universitas.unitkerja.form-unitkerja');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit_kerja_nama' => 'nullable|string|max:255',
            'sub_unit_kerja_nama' => 'nullable|string|max:255',
        ]);

        // Determine type based on filled fields
        $type = 'unit_kerja'; // Default
        if ($request->filled('sub_unit_kerja_nama')) {
            $type = 'sub_sub_unit_kerja';
        } elseif ($request->filled('unit_kerja_nama')) {
            $type = 'sub_unit_kerja';
        }

        switch ($type) {
            case 'unit_kerja':
                UnitKerja::create(['nama' => $request->nama]);
                $message = 'Unit Kerja berhasil ditambahkan.';
                break;

            case 'sub_unit_kerja':
                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                SubUnitKerja::create([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub Unit Kerja berhasil ditambahkan.';
                break;

            case 'sub_sub_unit_kerja':
                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                // Find or create sub unit kerja
                $subUnitKerja = SubUnitKerja::firstOrCreate([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->sub_unit_kerja_nama
                ]);

                SubSubUnitKerja::create([
                    'sub_unit_kerja_id' => $subUnitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub-sub Unit Kerja berhasil ditambahkan.';
                break;
        }

        return redirect()->route('backend.kepegawaian-universitas.unitkerja.index')
                         ->with('success', $message);
    }

    public function edit($type, $id)
    {
        switch ($type) {
            case 'unit_kerja':
                $item = UnitKerja::findOrFail($id);
                break;

            case 'sub_unit_kerja':
                $item = SubUnitKerja::with('unitKerja')->findOrFail($id);
                break;

            case 'sub_sub_unit_kerja':
                $item = SubSubUnitKerja::with(['subUnitKerja', 'unitKerja'])->findOrFail($id);
                break;

            default:
                abort(404);
        }

        return view('backend.layouts.views.kepegawaian-universitas.unitkerja.form-unitkerja', compact('item', 'type'));
    }

    public function update(Request $request, $type, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit_kerja_nama' => 'nullable|string|max:255',
            'sub_unit_kerja_nama' => 'nullable|string|max:255',
        ]);

        // Use the type parameter from URL since we're editing an existing item
        switch ($type) {
            case 'unit_kerja':
                $item = UnitKerja::findOrFail($id);
                $item->update(['nama' => $request->nama]);
                $message = 'Unit Kerja berhasil diperbarui.';
                break;

            case 'sub_unit_kerja':
                $item = SubUnitKerja::findOrFail($id);

                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                $item->update([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub Unit Kerja berhasil diperbarui.';
                break;

            case 'sub_sub_unit_kerja':
                $item = SubSubUnitKerja::findOrFail($id);

                // Find or create unit kerja
                $unitKerja = UnitKerja::firstOrCreate(['nama' => $request->unit_kerja_nama]);

                // Find or create sub unit kerja
                $subUnitKerja = SubUnitKerja::firstOrCreate([
                    'unit_kerja_id' => $unitKerja->id,
                    'nama' => $request->sub_unit_kerja_nama
                ]);

                $item->update([
                    'sub_unit_kerja_id' => $subUnitKerja->id,
                    'nama' => $request->nama
                ]);
                $message = 'Sub-sub Unit Kerja berhasil diperbarui.';
                break;

            default:
                abort(404);
        }

        return redirect()->route('backend.kepegawaian-universitas.unitkerja.index')
                         ->with('success', $message);
    }

    public function destroy($type, $id)
    {
        switch ($type) {
            case 'unit_kerja':
                $item = UnitKerja::findOrFail($id);
                // Check if has sub units
                if ($item->subUnitKerjas()->count() > 0) {
                    return redirect()->route('backend.kepegawaian-universitas.unitkerja.index')
                                     ->with('error', 'Unit Kerja tidak dapat dihapus karena masih memiliki Sub Unit Kerja.');
                }
                $item->delete();
                $message = 'Unit Kerja berhasil dihapus.';
                break;

            case 'sub_unit_kerja':
                $item = SubUnitKerja::findOrFail($id);
                // Check if has sub sub units
                if ($item->subSubUnitKerjas()->count() > 0) {
                    return redirect()->route('backend.kepegawaian-universitas.unitkerja.index')
                                     ->with('error', 'Sub Unit Kerja tidak dapat dihapus karena masih memiliki Sub-sub Unit Kerja.');
                }
                $item->delete();
                $message = 'Sub Unit Kerja berhasil dihapus.';
                break;

            case 'sub_sub_unit_kerja':
                $item = SubSubUnitKerja::findOrFail($id);
                $item->delete();
                $message = 'Sub-sub Unit Kerja berhasil dihapus.';
                break;

            default:
                abort(404);
        }

        return redirect()->route('backend.kepegawaian-universitas.unitkerja.index')
                         ->with('success', $message);
    }

    // API method untuk mendapatkan sub unit kerja berdasarkan unit kerja
    public function getSubUnitKerja($unitKerjaId)
    {
        $subUnitKerjas = SubUnitKerja::where('unit_kerja_id', $unitKerjaId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($subUnitKerjas);
    }

    // API method untuk mendapatkan sub sub unit kerja berdasarkan sub unit kerja
    public function getSubSubUnitKerja($subUnitKerjaId)
    {
        $subSubUnitKerjas = SubSubUnitKerja::where('sub_unit_kerja_id', $subUnitKerjaId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($subSubUnitKerjas);
    }
}
