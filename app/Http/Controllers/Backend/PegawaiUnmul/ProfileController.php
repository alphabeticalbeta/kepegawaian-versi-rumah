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
        $pegawai = Pegawai::with(['pangkat', 'jabatan', 'unitKerja'])->find(Auth::id());
        return view('backend.layouts.pegawai-unmul.my-profil', ['pegawai' => $pegawai, 'isEditing' => false]);
    }

    public function edit()
    {
        $pegawai = Auth::guard('pegawai')->user();
        $pangkats = Pangkat::orderBy('pangkat')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();
        $unitKerjas = SubSubUnitKerja::orderBy('nama')->get();

        return view('backend.layouts.pegawai-unmul.my-profil', [
            'pegawai'    => $pegawai,
            'pangkats'   => $pangkats,
            'jabatans'   => $jabatans,
            'unitKerjas' => $unitKerjas,
            'isEditing'  => true
        ]);
    }

    public function update(Request $request)
    {
        /** @var Pegawai $pegawai */
        $pegawai = Auth::guard('pegawai')->user();
        $validated = $this->validateRequest($request, $pegawai->id);
        $this->handleFileUploads($request, $validated, $pegawai);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $pegawai->update($validated);
        return redirect()->route('pegawai-unmul.profile.show')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    private function validateRequest(Request $request, $pegawaiId = null)
    {
        $rules = [
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'nip' => 'required|numeric|digits:18|unique:pegawais,nip,' . $pegawaiId,
            'gelar_depan' => 'nullable|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawaiId,
            'gelar_belakang' => 'required|string|max:255',
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
            'nuptk' => 'nullable|string',
            'nilai_konversi' => 'nullable|string',
            'unit_kerja_terakhir_id' => 'required|exists:sub_sub_unit_kerjas,id',
            'nomor_handphone' => 'required|string',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'required|date',
            'status_kepegawaian' => 'required|string|in:Dosen PNS,Dosen PPPK,Dosen Non ASN,Tenaga Kependidikan PNS,Tenaga Kependidikan PPPK,Tenaga Kependidikan Non ASN',
            'mata_kuliah_diampu' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'ranting_ilmu_kepakaran' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'url_profil_sinta' => 'nullable|required_if:jenis_pegawai,Dosen|url',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
