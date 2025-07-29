<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    /**
     * Menampilkan daftar jabatan dengan fungsionalitas filter.
     */
    public function index(Request $request)
    {
        $query = Jabatan::query();

        $query->when($request->filter_jenis_jabatan, function ($q, $jenis_jabatan) {
            return $q->where('jenis_jabatan', $jenis_jabatan);
        });

        $jabatans = $query->orderBy('jabatan')->paginate(10)->appends($request->all());

        return view('backend.layouts.admin-univ-usulan.jabatan.master-data-jabatan', compact('jabatans'));
    }

    /**
     * Menampilkan form untuk membuat jabatan baru.
     */
    public function create()
    {
        return view('backend.layouts.admin-univ-usulan.jabatan.form-jabatan');
    }

    /**
     * Menyimpan jabatan baru ke database.
     */
    public function store(Request $request)
    {
        // Penambahan validasi untuk jenis_pegawai
        $request->validate([
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255|unique:jabatans,jabatan',
        ]);

        // Penambahan jenis_pegawai saat create
        Jabatan::create($request->only('jenis_pegawai', 'jenis_jabatan', 'jabatan'));

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                         ->with('success', 'Data Jabatan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jabatan.
     */
    public function edit(Jabatan $jabatan)
    {
        return view('backend.layouts.admin-univ-usulan.jabatan.form-jabatan', compact('jabatan'));
    }

    /**
     * Memperbarui data jabatan di database.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        // Penambahan validasi untuk jenis_pegawai
        $request->validate([
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'jenis_jabatan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255|unique:jabatans,jabatan,' . $jabatan->id,
        ]);

        // Penambahan jenis_pegawai saat update
        $jabatan->update($request->only('jenis_pegawai', 'jenis_jabatan', 'jabatan'));

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                         ->with('success', 'Data Jabatan berhasil diperbarui.');
    }

    /**
     * Menghapus data jabatan dari database.
     */
    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();

        return redirect()->route('backend.admin-univ-usulan.jabatan.index')
                         ->with('success', 'Data Jabatan berhasil dihapus.');
    }
}
