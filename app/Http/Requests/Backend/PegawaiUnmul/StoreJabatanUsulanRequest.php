<?php

namespace App\Http\Requests\Backend\PegawaiUnmul;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJabatanUsulanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Kita set true karena otorisasi sudah ditangani oleh middleware 'auth'
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Cek aksi apa yang dilakukan pengguna dari tombol yang diklik.
        $isSavingDraft = $this->input('action') === 'save_draft';

        // Ambil data usulan dari route jika sedang dalam mode edit.
        $usulan = $this->route('usulan');

        // =====================================================================
        // ATURAN VALIDASI FINAL
        // =====================================================================

        $rules = [
            // Aturan dasar yang selalu berlaku
            'periode_usulan_id' => ['required', 'exists:periode_usulans,id'],
            'catatan'           => ['nullable', 'string', 'max:5000'],
            'karya_ilmiah'      => ['nullable', 'string'],
            'nama_jurnal'       => ['nullable', 'string'],
            'judul_artikel'     => ['nullable', 'string'],
            'penerbit_artikel'  => ['nullable', 'string'],
            'volume_artikel'    => ['nullable', 'string'],
            'nomor_artikel'     => ['nullable', 'string'],
            'edisi_artikel'     => ['nullable', 'string'],
            'halaman_artikel'   => ['nullable', 'string'],
            'issn_artikel'      => ['nullable', 'string'],
            'link_artikel'      => ['nullable', 'url'],
            'link_sinta'        => ['nullable', 'url'],
            'link_scopus'       => ['nullable', 'url'],
            'link_scimago'      => ['nullable', 'url'],
            'link_wos'          => ['nullable', 'url'],
            'syarat_guru_besar' => ['nullable', 'string'],
        ];

        // Daftar semua dokumen
        $documentFields = [
            'bukti_korespondensi',
            'turnitin',
            'upload_artikel',
            'pakta_integritas',
            'bukti_syarat_guru_besar',
        ];

        // Terapkan aturan file secara dinamis
        foreach ($documentFields as $field) {
            $rules[$field] = [
                // File hanya WAJIB jika:
                // 1. Ini BUKAN Simpan Draft, DAN
                // 2. Ini adalah usulan BARU ATAU filenya memang belum ada
                Rule::requiredIf(
                    !$isSavingDraft &&
                    (!$usulan || empty($usulan->data_usulan[$field]))
                ),
                'file',
                'mimes:pdf',
                'max:2048' // 2MB
            ];
        }

        // Pengecualian: bukti_syarat_guru_besar hanya wajib jika syarat_guru_besar dipilih
        $rules['bukti_syarat_guru_besar'] = [
            Rule::requiredIf(
                !$isSavingDraft && // dan bukan draft
                !empty($this->input('syarat_guru_besar')) && // dan syarat gb dipilih
                (!$usulan || empty($usulan->data_usulan['bukti_syarat_guru_besar'])) // dan filenya belum ada
            ),
            'file',
            'mimes:pdf',
            'max:2048'
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'dokumen.required' => 'Tidak ada dokumen yang diunggah. Anda wajib melengkapi semua dokumen.',
            'dokumen.*.required' => 'Ada dokumen wajib yang belum Anda unggah.',
            'dokumen.*.mimes' => 'Semua dokumen wajib dalam format PDF.',
            'dokumen.*.max' => 'Ukuran setiap dokumen tidak boleh lebih dari 1MB.',
        ];
    }
}
