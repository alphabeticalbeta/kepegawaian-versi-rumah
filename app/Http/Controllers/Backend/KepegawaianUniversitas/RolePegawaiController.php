<?php

namespace App\Http\Controllers\Backend\KepegawaianUniversitas;

use App\Http\Controllers\Controller;
use App\Models\KepegawaianUniversitas\Pegawai;
use Illuminate\Http\Request;
// Gunakan model Role dari Spatie
use Spatie\Permission\Models\Role;
use App\Services\FileStorageService;
use App\Services\ValidationService;

class RolePegawaiController extends Controller
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
        // Query builder dengan eager loading yang sama seperti DataPegawaiController
        $query = Pegawai::with(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja', 'roles'])
            ->when($request->filter_jenis_pegawai, function ($q, $jenis_pegawai) {
                return $q->where('jenis_pegawai', $jenis_pegawai);
            })
            ->when($request->search, function ($q, $search) {
                return $q->where(function($query) use ($search) {
                    $query->where('nama_lengkap', 'like', "%{$search}%")
                          ->orWhere('nip', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status_kepegawaian, function ($q, $status) {
                return $q->where('status_kepegawaian', $status);
            })
            ->when($request->unit_kerja, function ($q, $unitKerja) {
                return $q->whereHas('unitKerja.subUnitKerja.unitKerja', function($query) use ($unitKerja) {
                    $query->where('nama', $unitKerja);
                });
            })
            ->when($request->role, function ($q, $role) {
                return $q->whereHas('roles', function($query) use ($role) {
                    $query->where('name', $role);
                });
            })
            ->latest();

        $pegawais = $query->paginate(10)->withQueryString();

        // Static pagination - no AJAX needed

        return view('backend.layouts.views.kepegawaian-universitas.role-pegawai.master-rolepegawai', compact('pegawais'));
    }

    public function edit(Pegawai $pegawai)
    {
        // Ambil semua peran yang ada untuk ditampilkan di form dengan guard 'pegawai'
        $roles = Role::where('guard_name', 'pegawai')->orderBy('name')->get();
        return view('backend.layouts.views.kepegawaian-universitas.role-pegawai.edit', compact('pegawai', 'roles'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'roles' => 'nullable|array' // Memvalidasi bahwa 'roles' adalah sebuah array
        ]);

        // syncRoles() untuk guard 'pegawai'
        // Menggunakan array role names dan guard eksplisit
        $roleNames = $request->input('roles', []);

        // Clear existing roles terlebih dahulu untuk guard pegawai
        $pegawai->roles()->detach();

        // Assign role baru dengan guard yang benar
        if (!empty($roleNames)) {
            foreach ($roleNames as $roleName) {
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)
                    ->where('guard_name', 'pegawai')
                    ->first();

                if ($role) {
                    $pegawai->assignRole($role);
                }
            }
        }

        return redirect()->route('backend.kepegawaian-universitas.role-pegawai.index')
            ->with('success', 'Peran untuk ' . $pegawai->nama_lengkap . ' berhasil diperbarui.');
    }
}
