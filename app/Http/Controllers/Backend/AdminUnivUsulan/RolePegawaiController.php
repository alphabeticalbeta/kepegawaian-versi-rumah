<?php
namespace App\Http\Controllers\Backend\AdminUnivUsulan;
use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePegawaiController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Pegawai::class);
        $pegawais = Pegawai::with('roles')->orderBy('nama_lengkap')->paginate(15);
        return view('backend.layouts.admin-univ-usulan.role-pegawai.master-rolepegawai', compact('pegawais'));
    }

    /**
     * PERUBAHAN: Method ini sekarang memuat halaman edit terpisah.
     */
    public function edit(Pegawai $pegawai)
    {
        $this->authorize('update', $pegawai);
        $roles = Role::all();
        return view('backend.layouts.admin-univ-usulan.role-pegawai.edit', compact('pegawai', 'roles'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $this->authorize('update', $pegawai);
        $request->validate(['roles' => 'nullable|array']);
        $pegawai->roles()->sync($request->input('roles', []));
        return redirect()->route('backend.admin-univ-usulan.role-pegawai.index')->with('success', 'Role untuk ' . $pegawai->nama_lengkap . ' berhasil diperbarui.');
    }
}
