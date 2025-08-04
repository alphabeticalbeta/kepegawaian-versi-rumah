<?php

namespace App\Http\Requests\Backend\PegawaiUnmul;

use Illuminate\Foundation\Http\FormRequest;

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
    public function rules()
    {
        return [
            // Memastikan periode_usulan_id dikirim dan ada di tabel periode_usulans
            'periode_usulan_id' => ['required', 'exists:periode_usulans,id'],

            // field karya ilmiah / artikel (semua optional)
            'karya_ilmiah'      => ['nullable', 'string', 'max:255'],
            'nama_jurnal'       => ['nullable', 'string', 'max:255'],
            'judul_artikel'     => ['nullable', 'string', 'max:255'],
            'penerbit_artikel'  => ['nullable', 'string', 'max:255'],
            'volume_artikel'    => ['nullable', 'string', 'max:100'],
            'nomor_artikel'     => ['nullable', 'string', 'max:100'],
            'edisi_artikel'     => ['nullable', 'string', 'max:100'],
            'halaman_artikel'   => ['nullable', 'string', 'max:100'],
            'issn_artikel'      => ['nullable', 'string', 'max:100'],
            'link_artikel'      => ['nullable', 'string', 'max:255'],

            // Catatan bersifat opsional (nullable)
            'catatan' => ['nullable', 'string', 'max:2000'],

            // Memastikan 'dokumen' adalah sebuah array (karena kita menggunakan name="dokumen[id]")
            'dokumen' => ['required', 'array'],

            // Validasi untuk setiap item di dalam array 'dokumen'
            // 'dokumen.*' berarti aturan ini berlaku untuk semua file dalam array dokumen.
            'dokumen.*' => ['required', 'file', 'mimes:pdf', 'max:1024'], // max:1024 = 1MB
        ];
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
