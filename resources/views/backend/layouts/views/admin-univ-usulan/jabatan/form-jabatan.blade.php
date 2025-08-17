@extends('backend.layouts.roles.admin-univ-usulan.app')

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
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
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
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- JENIS JABATAN --}}
            <div class="mb-6">
                <label for="jenis_jabatan" class="block mb-2 font-semibold text-gray-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
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
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- NAMA JABATAN --}}
            <div class="mb-6">
                <label for="jabatan" class="block mb-2 font-semibold text-gray-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Nama Jabatan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="jabatan" id="jabatan"
                    value="{{ old('jabatan', $jabatan->jabatan ?? '') }}"
                    placeholder="Contoh: Lektor, Kepala Bagian, Arsiparis Ahli Muda"
                    class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 @error('jabatan') border-red-500 @enderror"
                    required>
                @error('jabatan')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- HIERARCHY LEVEL - FIELD BARU --}}
            <div class="mb-6">
                <label for="hierarchy_level" class="block mb-2 font-semibold text-gray-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Level Hirarki <span class="text-gray-500">(Opsional)</span>
                </label>
                <div class="relative">
                    <input type="number" name="hierarchy_level" id="hierarchy_level"
                        value="{{ old('hierarchy_level', $jabatan->hierarchy_level ?? '') }}"
                        placeholder="Contoh: 1, 2, 3, 4, 5..."
                        min="1" max="100"
                        class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 @error('hierarchy_level') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
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
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- PREVIEW SECTION --}}
            <div id="previewSection" class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg hidden">
                <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
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
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 transition duration-200 flex items-center font-semibold">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    {{ isset($jabatan) ? 'Update' : 'Simpan' }} Jabatan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/admin-universitas/jabatan.js') }}"></script>
@endpush

@endsection
