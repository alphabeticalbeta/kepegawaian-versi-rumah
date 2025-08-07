<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\BackendUnivUsulan\Pegawai;
use Illuminate\Http\Request;
// Gunakan model Role dari Spatie
use Spatie\Permission\Models\Role;

class RolePegawaiController extends Controller
{
    public function index()
    {
        // with('roles') akan mengambil relasi peran dari Spatie
        $pegawais = Pegawai::with('roles')->orderBy('nama_lengkap')->paginate(15);
        return view('backend.layouts.admin-univ-usulan.role-pegawai.master-rolepegawai', compact('pegawais'));
    }

    public function edit(Pegawai $pegawai)
    {
        // Ambil semua peran yang ada untuk ditampilkan di form
        $roles = Role::all();
        return view('backend.layouts.admin-univ-usulan.role-pegawai.edit', compact('pegawai', 'roles'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'roles' => 'nullable|array' // Memvalidasi bahwa 'roles' adalah sebuah array
        ]);

        // syncRoles() akan menerima array berisi NAMA-NAMA peran dari form
        // dan menyinkronkannya ke pengguna.
        $pegawai->syncRoles($request->input('roles', []));

        return redirect()->route('backend.admin-univ-usulan.role-pegawai.index')
            ->with('success', 'Peran untuk ' . $pegawai->nama_lengkap . ' berhasil diperbarui.');
    }
}
