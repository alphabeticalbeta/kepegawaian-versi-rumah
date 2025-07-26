<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\Pangkat;
use Illuminate\Http\Request;

class PangkatController extends Controller
{
    public function index()
    {
        $pangkats = Pangkat::orderBy('pangkat')->paginate(10);
        return view('backend.layouts.admin-univ-usulan.pangkat.master-data-pangkat', compact('pangkats'));
    }

    public function create()
    {
        return view('backend.layouts.admin-univ-usulan.pangkat.form-pangkat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pangkat' => 'required|unique:pangkats,pangkat',
        ]);

        Pangkat::create([
            'pangkat' => $request->input('pangkat')
        ]);

        return redirect()->route('backend.admin-univ-usulan.pangkat.index')
                         ->with('success', 'Data Pangkat berhasil ditambahkan.');
    }

    public function edit(Pangkat $pangkat)
    {
        return view('backend.layouts.admin-univ-usulan.pangkat.form-pangkat', compact('pangkat'));
    }

    public function update(Request $request, Pangkat $pangkat)
    {
        $request->validate([
            'pangkat' => 'required|unique:pangkats,pangkat,' . $pangkat->id,
        ]);

        $pangkat->update([
            'pangkat' => $request->input('pangkat')
        ]);

        return redirect()->route('backend.admin-univ-usulan.pangkat.index')
                         ->with('success', 'Data Pangkat berhasil diperbarui.');
    }

    public function destroy(Pangkat $pangkat)
    {
        $pangkat->delete();

        return redirect()->route('backend.admin-univ-usulan.pangkat.index')
                         ->with('success', 'Data Pangkat berhasil dihapus.');
    }
}
