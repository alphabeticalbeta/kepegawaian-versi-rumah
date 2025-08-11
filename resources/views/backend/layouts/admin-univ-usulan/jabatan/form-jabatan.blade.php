@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($jabatan) ? 'Edit Jabatan' : 'Tambah Jabatan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto p-6 rounded-lg shadow-lg bg-white">
        <div class="mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg p-4 text-white">
                <h2 class="text-2xl font-bold leading-tight">
                    {{ isset($jabatan) ? 'Edit' : 'Tambah' }} Jabatan
                </h2>
                <p class="text-blue-100 mt-1">
                    Kelola data jabatan dengan hirarki yang tepat untuk sistem usulan.
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

            {{-- JENIS PEGAWAI --}}
            <div class="mb-6">
                <label for="jenis_pegawai" class="block mb-2 font-semibold text-gray-700">
                    <i data-lucide="user-check" class="w-4 h-4 inline mr-1"></i>
                    Jenis Pegawai <span class="text-red-500">*</span>
                </label>
                <select name="jenis_pegawai" id="jenis_pegawai"
                        class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 @error('jenis_pegawai') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Jenis Pegawai --</option>
                    <option value="Dosen" {{ old('jenis_pegawai', $jabatan->jenis_pegawai ?? '') == 'Dosen' ? 'selected' : '' }}>
                        👨‍🎓 Dosen
                    </option>
                    <option value="Tenaga Kependidikan" {{ old('jenis_pegawai', $jabatan->jenis_pegawai ?? '') == 'Tenaga Kependidikan' ? 'selected' : '' }}>
                        👥 Tenaga Kependidikan
                    </option>
                </select>
                @error('jenis_pegawai')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- JENIS JABATAN --}}
            <div class="mb-6">
                <label for="jenis_jabatan" class="block mb-2 font-semibold text-gray-700">
                    <i data-lucide="briefcase" class="w-4 h-4 inline mr-1"></i>
                    Jenis Jabatan <span class="text-red-500">*</span>
                </label>
                <select name="jenis_jabatan" id="jenis_jabatan"
                        class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 @error('jenis_jabatan') border-red-500 @enderror"
                        data-old-value="{{ old('jenis_jabatan', $jabatan->jenis_jabatan ?? '') }}"
                        required>
                    <option value="">-- Pilih Jenis Jabatan --</option>
                </select>
                @error('jenis_jabatan')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- NAMA JABATAN --}}
            <div class="mb-6">
                <label for="jabatan" class="block mb-2 font-semibold text-gray-700">
                    <i data-lucide="award" class="w-4 h-4 inline mr-1"></i>
                    Nama Jabatan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="jabatan" id="jabatan"
                    value="{{ old('jabatan', $jabatan->jabatan ?? '') }}"
                    placeholder="Contoh: Lektor, Kepala Bagian, Arsiparis Ahli Muda"
                    class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 @error('jabatan') border-red-500 @enderror"
                    required>
                @error('jabatan')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- HIERARCHY LEVEL - FIELD BARU --}}
            <div class="mb-6">
                <label for="hierarchy_level" class="block mb-2 font-semibold text-gray-700">
                    <i data-lucide="trending-up" class="w-4 h-4 inline mr-1"></i>
                    Level Hirarki <span class="text-gray-500">(Opsional)</span>
                </label>
                <div class="relative">
                    <input type="number" name="hierarchy_level" id="hierarchy_level"
                        value="{{ old('hierarchy_level', $jabatan->hierarchy_level ?? '') }}"
                        placeholder="Contoh: 1, 2, 3, 4, 5..."
                        min="1" max="100"
                        class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 @error('hierarchy_level') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i data-lucide="hash" class="w-5 h-5 text-gray-400"></i>
                    </div>
                </div>
                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700 font-medium mb-1">💡 Panduan Level Hirarki:</p>
                    <ul class="text-xs text-blue-600 space-y-1">
                        <li><strong>Kosongkan</strong> jika jabatan tidak memiliki hirarki (flat/setara)</li>
                        <li><strong>1, 2, 3, 4, 5...</strong> untuk jabatan berurutan (level 1 = terendah)</li>
                        <li><strong>Contoh Dosen:</strong> Tenaga Pengajar (1) → Asisten Ahli (2) → Lektor (3) → dst</li>
                        <li><strong>Contoh TK:</strong> Arsiparis Pertama (1) → Arsiparis Muda (2) → dst</li>
                    </ul>
                </div>
                @error('hierarchy_level')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- PREVIEW SECTION --}}
            <div id="previewSection" class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg hidden">
                <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                    Preview Jabatan
                </h3>
                <div id="previewContent" class="text-sm text-gray-600">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex justify-center items-center space-x-4 mt-8">
                <a href="{{ route('backend.admin-univ-usulan.jabatan.index') }}"
                   class="px-6 py-3 rounded-lg border-2 border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition duration-200 flex items-center">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 transition duration-200 flex items-center font-semibold">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    {{ isset($jabatan) ? 'Update' : 'Simpan' }} Jabatan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jenisJabatanSelect = document.getElementById('jenis_jabatan');
        const jabatanInput = document.getElementById('jabatan');
        const hierarchyLevelInput = document.getElementById('hierarchy_level');
        const previewSection = document.getElementById('previewSection');
        const previewContent = document.getElementById('previewContent');

        // Mapping jenis pegawai -> jenis jabatan
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

        // Update jenis jabatan options
        function updateJenisJabatanOptions() {
            const selectedPegawai = jenisPegawaiSelect.value;
            const oldValue = jenisJabatanSelect.dataset.oldValue;

            jenisJabatanSelect.innerHTML = '<option value="">-- Pilih Jenis Jabatan --</option>';

            if (selectedPegawai && jabatanMapping[selectedPegawai]) {
                const options = jabatanMapping[selectedPegawai];

                options.forEach(function(optionText) {
                    const option = document.createElement('option');
                    option.value = optionText;
                    option.textContent = optionText;

                    if (optionText === oldValue) {
                        option.selected = true;
                    }

                    jenisJabatanSelect.appendChild(option);
                });
            }
            updatePreview();
        }

        // Update preview
        function updatePreview() {
            const jenisPegawai = jenisPegawaiSelect.value;
            const jenisJabatan = jenisJabatanSelect.value;
            const jabatan = jabatanInput.value;
            const hierarchyLevel = hierarchyLevelInput.value;

            if (jenisPegawai || jenisJabatan || jabatan) {
                previewSection.classList.remove('hidden');

                let preview = '<div class="space-y-2">';
                preview += `<div><strong>Jenis Pegawai:</strong> ${jenisPegawai || '-'}</div>`;
                preview += `<div><strong>Jenis Jabatan:</strong> ${jenisJabatan || '-'}</div>`;
                preview += `<div><strong>Nama Jabatan:</strong> ${jabatan || '-'}</div>`;

                if (hierarchyLevel) {
                    preview += `<div><strong>Level Hirarki:</strong> <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">${hierarchyLevel}</span> (Berurutan)</div>`;
                } else {
                    preview += `<div><strong>Level Hirarki:</strong> <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">Tidak ada</span> (Flat/Setara)</div>`;
                }

                // Tampilkan info tentang eligibility untuk usulan
                if (jenisJabatan === 'Tenaga Kependidikan Struktural') {
                    preview += `<div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                        <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                        <strong>Catatan:</strong> Jabatan Struktural tidak dapat mengajukan usulan
                    </div>`;
                } else {
                    preview += `<div class="mt-2 p-2 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                        <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                        <strong>Status:</strong> Dapat mengajukan usulan jabatan
                    </div>`;
                }

                preview += '</div>';
                previewContent.innerHTML = preview;
            } else {
                previewSection.classList.add('hidden');
            }
        }

        // Event listeners
        jenisPegawaiSelect.addEventListener('change', function() {
            jenisJabatanSelect.dataset.oldValue = '';
            updateJenisJabatanOptions();
        });

        jenisJabatanSelect.addEventListener('change', updatePreview);
        jabatanInput.addEventListener('input', updatePreview);
        hierarchyLevelInput.addEventListener('input', updatePreview);

        // Initialize
        updateJenisJabatanOptions();
    });
</script>

@endsection
