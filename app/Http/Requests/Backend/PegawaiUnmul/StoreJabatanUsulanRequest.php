<?php

namespace App\Http\Requests\Backend\PegawaiUnmul;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\BackendUnivUsulan\PeriodeUsulan;
use App\Models\BackendUnivUsulan\Usulan;
// JANGAN TAMBAHKAN: use Exception; ← INI TIDAK PERLU

class StoreJabatanUsulanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            // PERIODE USULAN
            'periode_usulan_id' => [
                'required',
                'exists:periode_usulans,id',
                function ($attribute, $value, $fail) {
                    $periode = PeriodeUsulan::find($value);
                    if (!$periode || $periode->status !== 'Buka' || $periode->jenis_usulan !== 'jabatan') {
                        $fail('Periode usulan tidak valid atau sudah tidak aktif.');
                    }
                    if ($periode->tanggal_mulai > now() || $periode->tanggal_selesai < now()) {
                        $fail('Periode usulan belum dimulai atau sudah berakhir.');
                    }
                }
            ],

            // KARYA ILMIAH
            'karya_ilmiah' => 'required|string|in:Jurnal Nasional Bereputasi,Jurnal Internasional Bereputasi',

            // INFORMASI ARTIKEL
            'nama_jurnal' => 'required|string|max:255',
            'judul_artikel' => 'required|string|max:500',
            'penerbit_artikel' => 'required|string|max:255',
            'volume_artikel' => 'required|string|max:50',
            'nomor_artikel' => 'required|string|max:50',
            'edisi_artikel' => 'required|string|max:50',
            'halaman_artikel' => 'required|string|max:50',

            // LINKS
            'link_artikel' => 'required|url|max:500',
            'link_sinta' => 'nullable|url|max:500',
            'link_scopus' => 'nullable|url|max:500',
            'link_scimago' => 'nullable|url|max:500',
            'link_wos' => 'nullable|url|max:500',

            // DOKUMEN WAJIB
            'pakta_integritas' => $this->getFileValidation('pakta_integritas'),
            'bukti_korespondensi' => $this->getFileValidation('bukti_korespondensi'),
            'turnitin' => $this->getFileValidation('turnitin'),
            'upload_artikel' => $this->getFileValidation('upload_artikel'),

            // SYARAT GURU BESAR (opsional)
            'syarat_guru_besar' => 'nullable|string|in:hibah,bimbingan,pengujian,reviewer',
            'bukti_syarat_guru_besar' => 'nullable|file|mimes:pdf|max:1024',

            // CATATAN
            'catatan' => 'nullable|string|max:1000',

            // ACTION
            'action' => 'required|string|in:save_draft,submit_final',

            // VALIDASI USULAN AKTIF (pindahkan ke sini)
            'usulan_constraint' => [
                function ($attribute, $value, $fail) {
                    $pegawai = Auth::user();
                    if (!$pegawai) return;

                    // Cek usulan aktif lainnya hanya untuk create (bukan update)
                    if (!$this->route('usulan')) {
                        $usulanAktif = Usulan::where('pegawai_id', $pegawai->id)
                            ->where('jenis_usulan', 'jabatan')
                            ->whereNotIn('status_usulan', ['Direkomendasikan', 'Ditolak'])
                            ->exists();

                        if ($usulanAktif) {
                            $fail('Anda masih memiliki usulan jabatan yang sedang aktif.');
                        }
                    }
                }
            ]
        ];

        // Validasi syarat guru besar jika dipilih
        if ($this->filled('syarat_guru_besar')) {
            $rules['bukti_syarat_guru_besar'] = 'required|file|mimes:pdf|max:1024';
        }

        return $rules;
    }

    private function getFileValidation(string $fieldName): string
    {
        $isUpdating = $this->route('usulan') !== null;

        if ($isUpdating) {
            // Untuk update: cek apakah file sudah ada
            $usulan = $this->route('usulan');
            if ($usulan) {
                // Cek struktur baru dan lama
                $fileExists = !empty($usulan->data_usulan['dokumen_usulan'][$fieldName]['path']) ||
                             !empty($usulan->data_usulan[$fieldName]);

                // Jika file sudah ada, tidak wajib upload ulang
                return $fileExists ? 'nullable|file|mimes:pdf|max:1024' : 'required|file|mimes:pdf|max:1024';
            }
            return 'nullable|file|mimes:pdf|max:1024';
        }

        // Untuk create: wajib upload
        return 'required|file|mimes:pdf|max:1024';
    }

    public function messages(): array
    {
        return [
            'periode_usulan_id.required' => 'Periode usulan harus dipilih.',
            'periode_usulan_id.exists' => 'Periode usulan tidak valid.',

            'karya_ilmiah.required' => 'Jenis karya ilmiah harus dipilih.',
            'karya_ilmiah.in' => 'Jenis karya ilmiah tidak valid.',

            'nama_jurnal.required' => 'Nama jurnal harus diisi.',
            'judul_artikel.required' => 'Judul artikel harus diisi.',
            'penerbit_artikel.required' => 'Penerbit artikel harus diisi.',
            'volume_artikel.required' => 'Volume artikel harus diisi.',
            'nomor_artikel.required' => 'Nomor artikel harus diisi.',
            'edisi_artikel.required' => 'Edisi artikel harus diisi.',
            'halaman_artikel.required' => 'Halaman artikel harus diisi.',

            'link_artikel.required' => 'Link artikel harus diisi.',
            'link_artikel.url' => 'Format link artikel tidak valid.',

            'pakta_integritas.required' => 'File pakta integritas harus diunggah.',
            'bukti_korespondensi.required' => 'File bukti korespondensi harus diunggah.',
            'turnitin.required' => 'File turnitin harus diunggah.',
            'upload_artikel.required' => 'File artikel harus diunggah.',

            '*.file' => 'File harus berupa file yang valid.',
            '*.mimes' => 'File harus berformat PDF.',
            '*.max' => 'Ukuran file maksimal 1MB.',

            'syarat_guru_besar.in' => 'Syarat guru besar tidak valid.',
            'bukti_syarat_guru_besar.required' => 'Bukti syarat guru besar harus diunggah.',

            'action.required' => 'Aksi harus dipilih.',
            'action.in' => 'Aksi tidak valid.',

            // Pesan untuk constraint validation
            'usulan_constraint' => 'Anda masih memiliki usulan jabatan yang sedang aktif.',
        ];
    }

    public function attributes(): array
    {
        return [
            'periode_usulan_id' => 'periode usulan',
            'karya_ilmiah' => 'jenis karya ilmiah',
            'nama_jurnal' => 'nama jurnal',
            'judul_artikel' => 'judul artikel',
            'penerbit_artikel' => 'penerbit artikel',
            'volume_artikel' => 'volume artikel',
            'nomor_artikel' => 'nomor artikel',
            'edisi_artikel' => 'edisi artikel',
            'halaman_artikel' => 'halaman artikel',
            'link_artikel' => 'link artikel',
            'pakta_integritas' => 'pakta integritas',
            'bukti_korespondensi' => 'bukti korespondensi',
            'turnitin' => 'turnitin',
            'upload_artikel' => 'upload artikel',
            'syarat_guru_besar' => 'syarat guru besar',
            'bukti_syarat_guru_besar' => 'bukti syarat guru besar',
            'action' => 'aksi',
        ];
    }
}
