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
                <select name="jenis_jabatan" id="jenis_jabatan" class="w-full border px-3 py-2 rounded @error('jenis_jabatan') border-red-500 @enderror" required>
                    <option value="">-- Pilih Jenis Jabatan --</option>
                    @php
                        // Daftar lengkap semua kemungkinan jenis jabatan
                        $allJenisJabatan = [
                            'Dosen Fungsional',
                            'Dosen Fungsi Tambahan',
                            'Tenaga Kependidikan Struktural',
                            'Tenaga Kependidikan Fungsional Umum',
                            'Tenaga Kependidikan Fungsional Tertentu',
                            'Tenaga Kependidikan Tugas Tambahan',
                        ];
                    @endphp
                    @foreach($allJenisJabatan as $option)
                        <option value="{{ $option }}" {{ old('jenis_jabatan', $jabatan->jenis_jabatan ?? '') == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                @error('jenis_jabatan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

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
        // --- Selektor Elemen ---
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jabatanSelect = document.getElementById('jabatan_terakhir_id');
        const jenisJabatanDisplay = document.getElementById('jenis_jabatan_display');

        // Field yang visibilitasnya akan diubah
        const nuptkField = document.getElementById('field_nuptk');
        const nilaiKonversiField = document.getElementById('field_nilai_konversi');
        const pakKonversiField = document.getElementById('field_pak_konversi');
        const dosenFieldsWrapper = document.getElementById('dosen_fields_wrapper');

        // Elemen lainnya
        const unitKerjaSelect = document.getElementById('unit_kerja_terakhir_id');
        const pathDisplay = document.getElementById('unit_kerja_path_display');

        // --- Fungsi Inti ---

        function filterJabatan() {
            const selectedJenisPegawai = jenisPegawaiSelect.value;
            for (const option of jabatanSelect.options) {
                if (option.value === "") {
                    option.style.display = "block";
                    continue;
                }
                const optionJenisPegawai = option.dataset.jenisPegawai;
                if (!selectedJenisPegawai || optionJenisPegawai === selectedJenisPegawai) {
                    option.style.display = "block";
                } else {
                    option.style.display = "none";
                }
            }
            if (jabatanSelect.options[jabatanSelect.selectedIndex]?.style.display === 'none') {
                jabatanSelect.value = "";
            }
        }

        function updateJenisJabatanLabel() {
            const selectedOption = jabatanSelect.options[jabatanSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const jenisJabatan = selectedOption.dataset.jenisJabatan;
                jenisJabatanDisplay.textContent = `(${jenisJabatan})`;
            } else {
                jenisJabatanDisplay.textContent = '';
            }
        }

        /**
         * =========================================================================
         * FUNGSI YANG DIPERBARUI DENGAN DEBUGGING
         * =========================================================================
         */
        function toggleConditionalFields() {
            const selectedJenisPegawai = jenisPegawaiSelect.value;
            const selectedJabatanOption = jabatanSelect.options[jabatanSelect.selectedIndex];
            const selectedJenisJabatan = selectedJabatanOption ? selectedJabatanOption.dataset.jenisJabatan : '';

            // --- DEBUGGING: Cetak nilai ke console browser ---
            console.log("--- Memeriksa Kondisi Visibilitas ---");
            console.log("Jenis Pegawai Dipilih:", `'${selectedJenisPegawai}'`);
            console.log("Jenis Jabatan Dipilih:", `'${selectedJenisJabatan}'`);
            // -------------------------------------------

            // --- KONDISI 1: Untuk field NUPTK, Nilai Konversi, PAK Konversi ---
            const showSharedFields = (
                (selectedJenisPegawai === 'Dosen' && ['Dosen Fungsional', 'Dosen Fungsi Tambahan'].includes(selectedJenisJabatan)) ||
                (selectedJenisPegawai === 'Tenaga Kependidikan' && selectedJenisJabatan === 'Tenaga Kependidikan Fungsional Tertentu')
            );

            console.log("Tampilkan NUPTK, Konversi, PAK?", showSharedFields);

            nuptkField.classList.toggle('hidden', !showSharedFields);
            nilaiKonversiField.classList.toggle('hidden', !showSharedFields);
            pakKonversiField.classList.toggle('hidden', !showSharedFields);

            // --- KONDISI 2: Untuk field spesifik Dosen lainnya (URL Sinta, dll) ---
            const showDosenSpecificFields = selectedJenisPegawai === 'Dosen';
            dosenFieldsWrapper.classList.toggle('hidden', !showDosenSpecificFields);
        }

        function displayUnitKerjaPath() {
            const selectedOption = unitKerjaSelect.options[unitKerjaSelect.selectedIndex];
            pathDisplay.innerHTML = (selectedOption && selectedOption.value) ? selectedOption.dataset.path : '';
        }

        // --- Panggilan Awal saat Halaman Dimuat ---
        displayUnitKerjaPath();
        filterJabatan();
        updateJenisJabatanLabel();
        toggleConditionalFields();

        // --- Event Listeners ---
        jenisPegawaiSelect.addEventListener('change', function() {
            filterJabatan();
            jabatanSelect.value = '';
            updateJenisJabatanLabel();
            toggleConditionalFields();
        });

        jabatanSelect.addEventListener('change', function() {
            updateJenisJabatanLabel();
            toggleConditionalFields();
        });

        unitKerjaSelect.addEventListener('change', displayUnitKerjaPath);
    });
</script>

@endsection
