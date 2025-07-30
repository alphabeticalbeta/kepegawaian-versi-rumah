@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($jabatan) ? 'Edit Jabatan' : 'Tambah Jabatan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-lg mx-auto p-6 rounded-md shadow bg-white">
        <div class="mb-6">
            <div class="bg-blue-50 rounded-md p-4">
                <h2 class="text-xl font-bold text-blue-800 leading-tight">
                    {{ isset($jabatan) ? 'Edit' : 'Tambah' }} Jabatan
                </h2>
                <p class="text-sm text-blue-700 mt-1">
                    Silakan lengkapi data jabatan dengan benar.
                </p>
            </div>
        </div>

        <form action="{{ isset($jabatan)
                        ? route('backend.admin-univ-usulan.jabatan.update', $jabatan)
                        : route('backend.admin-univ-usulan.jabatan.store') }}"
              method="POST">
            @csrf
            @if(isset($jabatan))
                @method('PUT')
            @endif

            {{-- DROPDOWN BARU: JENIS PEGAWAI --}}
            <div class="mb-4">
                <label for="jenis_pegawai" class="block mb-1 font-medium">Jenis Pegawai <span class="text-red-500">*</span></label>
                <select name="jenis_pegawai" id="jenis_pegawai" class="w-full border px-3 py-2 rounded @error('jenis_pegawai') border-red-500 @enderror" required>
                    <option value="">-- Pilih Jenis Pegawai --</option>
                    <option value="Dosen" {{ old('jenis_pegawai', $jabatan->jenis_pegawai ?? '') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="Tenaga Kependidikan" {{ old('jenis_pegawai', $jabatan->jenis_pegawai ?? '') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                </select>
                @error('jenis_pegawai')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="jenis_jabatan" class="block mb-1 font-medium">Jenis Jabatan <span class="text-red-500">*</span></label>
                <select name="jenis_jabatan" id="jenis_jabatan"
                        class="w-full border px-3 py-2 rounded @error('jenis_jabatan') border-red-500 @enderror"
                        data-old-value="{{ old('jenis_jabatan', $jabatan->jenis_jabatan ?? '') }}"
                        required>
                    <option value="">-- Pilih Jenis Jabatan --</option>
                </select>
                @error('jenis_jabatan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- DROPDOWN BARU: JABATAN --}}

            <div class="mb-4">
                <label class="block mb-1 font-medium">Nama Jabatan</label>
                <input type="text" name="jabatan"
                    value="{{ old('jabatan', $jabatan->jabatan ?? '') }}"
                    placeholder="Contoh: Lektor / Kepala Bagian"
                    class="w-full border px-3 py-2 rounded @error('jabatan') border-red-500 @enderror" required>
                @error('jabatan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center items-center space-x-4 mt-6">
                <a href="{{ route('backend.admin-univ-usulan.jabatan.index') }}"
                   class="px-5 py-2.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition duration-150 ease-in-out">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition duration-150 ease-in-out">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Definisikan elemen select yang akan kita gunakan
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');

        // 2. Buat pemetaan (mapping) antara Jenis Pegawai dan Jenis Jabatan
        const jabatanMapping = {
            'Dosen': [
                'Dosen Fungsional',
                'Dosen Fungsi Tambahan'
            ],
            'Tenaga Kependidikan': [
                'Tenaga Kependidikan Struktural',
                'Tenaga Kependidikan Fungsional Umum',
                'Tenaga Kependidikan Fungsional Tertentu',
                'Tenaga Kependidikan Tugas Tambahan'
            ]
        };

        // 3. Buat fungsi untuk memperbarui opsi Jenis Jabatan
        function updateJenisJabatanOptions() {
            // Ambil nilai jenis pegawai yang sedang dipilih
            const selectedPegawai = jenisPegawaiSelect.value;
            // Ambil nilai lama (jika ada, untuk form edit)
            const oldValue = jenisJabatanSelect.dataset.oldValue;

            // Kosongkan opsi jenis jabatan yang ada saat ini
            jenisJabatanSelect.innerHTML = '<option value="">-- Pilih Jenis Jabatan --</option>';

            // Jika jenis pegawai telah dipilih
            if (selectedPegawai && jabatanMapping[selectedPegawai]) {
                // Ambil daftar opsi yang sesuai dari mapping
                const options = jabatanMapping[selectedPegawai];

                // Tambahkan setiap opsi ke dalam select
                options.forEach(function(optionText) {
                    const option = document.createElement('option');
                    option.value = optionText;
                    option.textContent = optionText;

                    // Jika nilai ini adalah nilai lama, buat opsi ini terpilih
                    if (optionText === oldValue) {
                        option.selected = true;
                    }

                    jenisJabatanSelect.appendChild(option);
                });
            }
        }

        // 4. Tambahkan event listener ke dropdown Jenis Pegawai
        jenisPegawaiSelect.addEventListener('change', function() {
            // Hapus data-old-value agar tidak mengganggu pilihan baru
            jenisJabatanSelect.dataset.oldValue = '';
            // Panggil fungsi untuk memperbarui dropdown
            updateJenisJabatanOptions();
        });

        // 5. Panggil fungsi sekali saat halaman dimuat (penting untuk form edit)
        // Ini akan mengisi dropdown 'Jenis Jabatan' berdasarkan nilai 'Jenis Pegawai' yang sudah ada
        updateJenisJabatanOptions();
    });
</script>

@endsection
