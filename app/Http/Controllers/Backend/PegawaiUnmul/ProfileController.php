<?php

namespace App\Http\Controllers\Backend\PegawaiUnmul;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\File as FileFacade;

// Model yang dibutuhkan untuk form
use App\Models\BackendUnivUsulan\Jabatan;
use App\Models\BackendUnivUsulan\Pangkat;
use App\Models\BackendUnivUsulan\Pegawai; // <-- PERBAIKAN 1: PASTIKAN BARIS INI ADA
use App\Models\BackendUnivUsulan\SubSubUnitKerja;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna yang sedang login.
     */
    public function show()
    {
        /** @var Pegawai $pegawai */ // <-- PERBAIKAN 2: PETUNJUK UNTUK IDE
        $pegawai = Auth::guard('pegawai')->user();

        // Error 'load' akan hilang
        $pegawai->load(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja']);

        return view('backend.layouts.pegawai-unmul.my-profil', compact('pegawai'));
    }

    /**
     * Menampilkan form untuk mengedit profil.
     */
    public function edit()
    {
        /** @var Pegawai $pegawai */ // <-- PERBAIKAN 2: PETUNJUK UNTUK IDE
        $pegawai = Auth::guard('pegawai')->user();

        $pangkats = Pangkat::orderBy('pangkat')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();
        $unitKerjas = SubSubUnitKerja::with('subUnitKerja.unitKerja')->orderBy('nama')->get();

        return view('backend.layouts.admin-univ-usulan.data-pegawai.form-datapegawai', compact('pegawai', 'pangkats', 'jabatans', 'unitKerjas'));
    }

    /**
     * Memproses dan menyimpan perubahan profil.
     */
    public function update(Request $request)
    {
        /** @var Pegawai $pegawai */ // <-- PERBAIKAN 2: PETUNJUK UNTUK IDE
        $pegawai = Auth::guard('pegawai')->user();

        $validated = $this->validateRequest($request, $pegawai->id);

        $this->handleFileUploads($request, $validated, $pegawai);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Error 'update' akan hilang
        $pegawai->update($validated);

        return redirect()->route('pegawai-unmul.profile.show')
                         ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    // ... (private methods validateRequest dan handleFileUploads tetap sama) ...
    private function validateRequest(Request $request, $pegawaiId = null)
    {
        $rules = [
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'nip' => 'required|numeric|digits:18|unique:pegawais,nip,' . $pegawaiId,
            'gelar_depan' => 'nullable|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawaiId,
            'gelar_belakang' => 'required|string|max:255',
            'password' => 'nullable|min:8',
            'nomor_kartu_pegawai' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'pangkat_terakhir_id' => 'required|exists:pangkats,id',
            'tmt_pangkat' => 'required|date',
            'jabatan_terakhir_id' => 'required|exists:jabatans,id',
            'tmt_jabatan' => 'required|date',
            'pendidikan_terakhir' => 'required|string',
            'predikat_kinerja_tahun_pertama' => 'required|string',
            'predikat_kinerja_tahun_kedua' => 'required|string',
            'unit_kerja_terakhir_id' => 'required|exists:sub_sub_unit_kerjas,id',
            'nomor_handphone' => 'required|string',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'required|date',
            'nuptk' => 'nullable|numeric|digits:16',
            'mata_kuliah_diampu' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'ranting_ilmu_kepakaran' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'url_profil_sinta' => 'nullable|required_if:jenis_pegawai,Dosen|url',
            'nilai_konversi' => 'nullable|numeric',
            'status_kepegawaian' => ['required','string',Rule::in([
                'Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN',
                'Tenaga Kependidikan PNS', 'Tenaga Kependidikan PPPK', 'Tenaga Kependidikan Non ASN'
                ])
            ],
            'sk_pangkat_terakhir' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'sk_jabatan_terakhir' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'ijazah_terakhir' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'transkrip_nilai_terakhir' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_pertama' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_kedua' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'pak_konversi' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'sk_penyetaraan_ijazah' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'disertasi_thesis_terakhir' => ['nullable', File::types(['pdf'])->max(10 * 1024)],
            'sk_cpns' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
            'sk_pns' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
        ];

        return $request->validate($rules);
    }

    private function handleFileUploads(Request $request, &$validatedData, $pegawai = null)
    {
        $fileColumns = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir', 'ijazah_terakhir', 'transkrip_nilai_terakhir',
            'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir', 'pak_konversi', 'skp_tahun_pertama',
            'skp_tahun_kedua', 'sk_cpns', 'sk_pns', 'foto'
        ];

        foreach ($fileColumns as $column) {
            if ($request->hasFile($column)) {
                if ($pegawai && $pegawai->$column) {
                    Storage::disk('public')->delete($pegawai->$column);
                }
                $path = $request->file($column)->store('pegawai-files/' . $column, 'public');
                $validatedData[$column] = $path;
            }
        }
    }
}
