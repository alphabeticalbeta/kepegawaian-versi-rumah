<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePegawaiController extends Controller
{
    public function __construct()
    {
        // Lindungi semua method di controller ini dengan Gate 'manage-role-pegawai'
        $this->middleware('can:manage-pegawai');
    }


    /**
     * Menampilkan halaman utama manajemen role.
     */
    public function index()
    {
        // Ambil semua pegawai beserta role yang sudah dimiliki (eager loading)
        $pegawais = Pegawai::with('roles')->orderBy('nama_lengkap')->paginate(15);

        return view('backend.layouts.admin-univ-usulan.role-pegawai.master-rolepegawai', compact('pegawais'));
    }

    /**
     * Mengambil data untuk form edit di modal.
     * Mengembalikan data dalam format JSON.
     */
    public function edit(Pegawai $pegawai)
    {
        // Ambil semua role yang tersedia di sistem
        $allRoles = Role::all();

        // Ambil ID dari role yang sudah dimiliki pegawai saat ini
        $pegawaiRoleIds = $pegawai->roles->pluck('id')->toArray();

        return response()->json([
            'pegawai' => $pegawai,
            'allRoles' => $allRoles,
            'pegawaiRoleIds' => $pegawaiRoleIds,
        ]);
    }

    /**
     * Memperbarui role untuk seorang pegawai.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        // Validasi bahwa 'roles' yang dikirim adalah array
        $request->validate([
            'roles' => 'nullable|array'
        ]);

        // Gunakan sync() untuk sinkronisasi role.
        // Eloquent akan otomatis menambah/menghapus relasi di pivot table.
        $pegawai->roles()->sync($request->input('roles', []));

        return redirect()->route('backend.admin-univ-usulan.role-pegawai.index')
                         ->with('success', 'Role untuk ' . $pegawai->nama_lengkap . ' berhasil diperbarui.');
    }
}
