<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::orderBy('jabatan')->paginate(10);
        return view('backend.layouts.admin-univ-usulan.jabatan.master-data-jabatan', compact('jabatans'));
    }

    public function create()
    {
        return view('backend.layouts.admin-univ-usulan.jabatan.form-jabatan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|unique:jabatans,jabatan',
        ]);

        Jabatan::create([
            'jabatan' => $request->jabatan
        ]);

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                         ->with('success', 'Data Jabatan berhasil ditambahkan.');
    }

    public function edit(Jabatan $jabatan)
    {
        return view('backend.layouts.admin-univ-usulan.jabatan.form-jabatan', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'jabatan' => 'required|unique:jabatans,jabatan,' . $jabatan->id,
        ]);

        $jabatan->update([
            'jabatan' => $request->jabatan
        ]);

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                         ->with('success', 'Data Jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                         ->with('success', 'Data Jabatan berhasil dihapus.');
    }
}
