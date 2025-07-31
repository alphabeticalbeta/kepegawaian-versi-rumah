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
use Illuminate\Support\Facades\File as FileFacade;

class DataPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // PERUBAHAN 1: Tambahkan Request $request
    {
        // PERUBAHAN 2: Ganti logika pengambilan data dengan query builder
        $query = Pegawai::with(['pangkat', 'jabatan', 'unitKerja'])->latest();

        // Terapkan filter berdasarkan jenis pegawai jika ada
        $query->when($request->filter_jenis_pegawai, function ($q, $jenis_pegawai) {
            return $q->where('jenis_pegawai', $jenis_pegawai);
        });

        // Terapkan filter pencarian berdasarkan nama atau NIP jika ada
        $query->when($request->search, function ($q, $search) {
            return $q->where(function ($subQuery) use ($search) {
                $subQuery->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('nip', 'like', "%{$search}%");
            });
        });

        // Ambil data dengan paginasi
        $pegawais = $query->paginate(10)->withQueryString();

        return view('backend.layouts.admin-univ-usulan.data-pegawai.master-datapegawai', compact('pegawais'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pangkats = Pangkat::orderBy('pangkat')->get();
        $jabatans = Jabatan::orderBy('jabatan')->get();
        $unitKerjas = SubSubUnitKerja::with('subUnitKerja.unitKerja')->orderBy('nama')->get();

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
        $unitKerjas = SubSubUnitKerja::with('subUnitKerja.unitKerja')->orderBy('nama')->get();

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
        $fileColumns = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
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

    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['pangkat', 'jabatan', 'unitKerja.subUnitKerja.unitKerja']);

        return view('backend.layouts.admin-univ-usulan.data-pegawai.show-datapegawai', compact('pegawai'));
    }

    /**
     * Reusable validation logic.
     */
    private function validateRequest(Request $request, $pegawaiId = null)
    {
        $rules = [
            'jenis_pegawai' => 'required|string|in:Dosen,Tenaga Kependidikan',
            'nip' => 'required|numeric|digits:18|unique:pegawais,nip,' . $pegawaiId,
            'gelar_depan' => 'nullable|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:pegawais,email,' . $pegawaiId,
            'gelar_belakang' => 'nullable|string|max:255',
            'nomor_kartu_pegawai' => 'nullable|string|max:255',
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
            'tmt_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'nuptk' => 'nullable|numeric|digits:16',
            'mata_kuliah_diampu' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'ranting_ilmu_kepakaran' => 'nullable|required_if:jenis_pegawai,Dosen|string',
            'url_profil_sinta' => 'nullable|required_if:jenis_pegawai,Dosen|url',
            'nilai_konversi' => 'nullable|numeric',
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'sk_penyetaraan_ijazah' => ['nullable', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'disertasi_thesis_terakhir' => ['nullable', File::types(['pdf'])->max(10 * 1024)],
            'sk_cpns' => ['nullable', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'sk_pns' => ['nullable', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
        ];

        $fileRules = [
            'sk_pangkat_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'sk_jabatan_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'ijazah_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'transkrip_nilai_terakhir' => ['required', File::types(['pdf', 'jpg', 'png'])->max(2 * 1024)],
            'skp_tahun_pertama' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'skp_tahun_kedua' => ['required', File::types(['pdf'])->max(2 * 1024)],
            'pak_konversi' => ['nullable', File::types(['pdf'])->max(2 * 1024)],
        ];

        if ($pegawaiId) {
            foreach ($fileRules as $key => $value) {
                $rules[$key] = ['nullable', ...array_slice($value, 1)];
            }
        } else {
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
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
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

    /**
     * Display a document.
     */
    public function showDocument(Pegawai $pegawai, $field)
    {
        $allowedFields = [
            'sk_pangkat_terakhir', 'sk_jabatan_terakhir',
            'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_penyetaraan_ijazah', 'disertasi_thesis_terakhir',
            'pak_konversi', 'skp_tahun_pertama', 'skp_tahun_kedua',
            'sk_cpns', 'sk_pns', 'foto'
        ];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        $filePath = $pegawai->$field;
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($filePath);
        $mimeType = FileFacade::mimeType($path);
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ];

        return response()->file($path, $headers);
    }
}
