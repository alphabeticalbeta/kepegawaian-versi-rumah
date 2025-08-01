<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $unitkerjas = UnitKerja::orderBy('nama')->paginate(10);
        return view('backend.layouts.admin-univ-usulan.unitkerja.master-data-unitkerja', compact('unitkerjas'));
    }

    public function create()
    {
        return view('backend.layouts.admin-univ-usulan.unitkerja.form-unitkerja');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'       => 'required|unique:unit_kerjas,nama',
            'keterangan' => 'nullable|string',
        ]);

        UnitKerja::create($data);

        return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                         ->with('success', 'Unit Kerja berhasil ditambahkan.');
    }

    public function edit(UnitKerja $unitKerja)
    {
        return view('backend.layouts.admin-univ-usulan.unitkerja.form-unitkerja', compact('unitKerja'));
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        $data = $request->validate([
            'nama'       => 'required|unique:unit_kerjas,nama,'.$unitKerja->id,
            'keterangan' => 'nullable|string',
        ]);

        $unitKerja->update($data);

        return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                         ->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    public function destroy(UnitKerja $unitKerja)
    {
        $unitKerja->delete();

        return redirect()->route('backend.admin-univ-usulan.unitkerja.index')
                         ->with('success', 'Unit Kerja berhasil dihapus.');
    }
}
