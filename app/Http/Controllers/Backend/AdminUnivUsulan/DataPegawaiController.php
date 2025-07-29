<?php

namespace App\Http\Controllers\Backend\AdminUnivUsulan;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Pangkat;
use App\Models\Pegawai;
use App\Models\SubSubUnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class DataPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load relasi untuk optimasi query
        $pegawais = Pegawai::with(['pangkat', 'jabatan', 'unitKerja'])
            ->orderBy('gelar_depan')
            ->orderBy('gelar_belakang')
            ->paginate(10);

        return view('backend.layouts.admin-univ-usulan.data-pegawai.master-datapegawai', compact('pegawais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pangkats = Pangkat::orderBy('pangkat')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();
        $unitKerjas = SubSubUnitKerja::orderBy('nama')->get();

        return view('backend.layouts.admin-univ-usulan.data-pegawai.form-datapegawai', compact('pangkats', 'jabatans', 'unitKerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $this->handleFileUploads($request, $validated);

        Pegawai::create($validated);

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        $pangkats = Pangkat::orderBy('pangkat')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();
        $unitKerjas = SubSubUnitKerja::orderBy('nama')->get();

        return view('backend.layouts.admin-univ-usulan.data-pegawai.form-datapegawai', compact('pegawai', 'pangkats', 'jabatans', 'unitKerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $this->validateRequest($request, $pegawai->id);
        $this->handleFileUploads($request, $validated, $pegawai);

        $pegawai->update($validated);

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        // Hapus semua file terkait sebelum menghapus data dari database
        $fileColumns = [
            'sk_cpns_terakhir', 'sk_pns_terakhir', 'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'sk_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua'
        ];

        foreach ($fileColumns as $column) {
            if ($pegawai->$column) {
                Storage::disk('public')->delete($pegawai->$column);
            }
        }

        $pegawai->delete();

        return redirect()->route('backend.admin-univ-usulan.data-pegawai.index')
                         ->with('success', 'Data Pegawai berhasil dihapus.');
    }

    /**
     *Reusable validation logic.
     */
    private function validateRequest(Request $request, $pegawaiId = null)
    {
        $rules = [
            'role' => 'required|array',
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'nip' => 'required|numeric|digits:18|unique:pegawais,nip,' . $pegawaiId,
            'gelar_depan' => 'required|string|max:255',
            'gelar_belakang' => 'required|string|max:255',
            'nomor_kartu_pegawai' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tmt_cpns' => 'required|date',
            'tmt_pns' => 'required|date',
            'pangkat_terakhir_id' => 'required|exists:pangkats,id',
            'tmt_pangkat' => 'required|date',
            'jabatan_terakhir_id' => 'required|exists:jabatans,id',
            'tmt_jabatan' => 'required|date',
            'pendidikan_terakhir' => 'required|string',
            'predikat_kinerja_tahun_pertama' => 'required|string',
            'predikat_kinerja_tahun_kedua' => 'required|string',
            'unit_kerja_terakhir_id' => 'required|exists:sub_sub_unit_kerjas,id',
            'nomor_handphone' => 'required|string',
            // --- Conditional & Optional Rules ---
            'nuptk' => 'nullable|required_if:jenis_pegawai,Dosen|numeric|digits:16',
            'mata_kuliah_diampu' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'ranting_ilmu_kepakaran' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'url_profil_sinta' => 'nullable|required_if:jenis_pegawai,Dosen|url',
            'sk_konversi' => 'nullable', // Logika required_if bisa lebih kompleks jika perlu
            // --- File Upload Rules ---
            'sk_penyetaraan_ijazah' => ['nullable', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'disertasi_thesis_terakhir' => ['nullable', File::types(['pdf'])->max(10 * 1024)],
        ];

        // Aturan file yang wajib saat create, tapi opsional saat update
        $fileRules = [
            'sk_cpns_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'sk_pns_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'sk_pangkat_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'sk_jabatan_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'ijazah_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'transkrip_nilai_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'skp_tahun_pertama' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_kedua' => ['required', File::types(['pdf'])->max(2 * 1024)],
        ];

        if ($pegawaiId) { // Jika ini adalah update, file tidak wajib diisi ulang
            foreach ($fileRules as $key => $value) {
                $rules[$key] = ['nullable', ...array_slice($value, 1)];
            }
        } else { // Jika ini adalah create, semua file wajib
             $rules = array_merge($rules, $fileRules);
        }

        return $request->validate($rules);
    }

    /**
     * Reusable file upload logic.
     */
    private function handleFileUploads(Request $request, &$validatedData, $pegawai = null)
    {
        $fileColumns = [
            'sk_cpns_terakhir', 'sk_pns_terakhir', 'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'sk_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua'
        ];

        foreach ($fileColumns as $column) {
            if ($request->hasFile($column)) {
                // Hapus file lama jika ada (saat update)
                if ($pegawai && $pegawai->$column) {
                    Storage::disk('public')->delete($pegawai->$column);
                }
                // Simpan file baru dan update path di data yang akan divalidasi
                $path = $request->file($column)->store('pegawai-files/' . $column, 'public');
                $validatedData[$column] = $path;
            }
        }
    }
}
