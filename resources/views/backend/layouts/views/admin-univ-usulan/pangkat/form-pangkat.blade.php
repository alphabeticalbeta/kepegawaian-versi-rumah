@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', isset($pangkat) ? 'Edit Pangkat' : 'Tambah Pangkat')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto p-6 rounded-lg shadow-lg bg-gray-300">
        {{-- Header Section --}}
        <div class="mb-6">
            <div class="rounded-lg p-6 text-black shadow-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-2 mr-3">
                                <i data-lucide="award" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <h2 class="text-2xl font-bold leading-tight">
                                {{ isset($pangkat) ? 'Edit' : 'Tambah' }} Pangkat
                            </h2>
                        </div>
                        <p class="text-black text-base leading-relaxed">
                            {{ isset($pangkat)
                                ? 'Perbarui informasi pangkat dengan status dan hirarki yang sesuai dalam sistem usulan.'
                                : 'Tambahkan data pangkat baru dengan status dan hirarki yang tepat untuk sistem usulan.' }}
                        </p>

                        @if(isset($pangkat))
                            <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm">
                                <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                                <span class="text-sm font-medium">
                                    Mengedit: {{ $pangkat->pangkat }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Action Indicator --}}
                    <div class="flex-shrink-0 ml-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                            @if(isset($pangkat))
                                <i data-lucide="edit-3" class="w-8 h-8 text-black"></i>
                            @else
                                <i data-lucide="plus-circle" class="w-8 h-8 text-black"></i>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Breadcrumb Style Navigation --}}
                <div class="mt-4 pt-4 border-t border-white/20">
                    <nav class="flex items-center space-x-2 text-sm">
                        <a href="{{ route('backend.admin-univ-usulan.pangkat.index') }}"
                        class="flex items-center text-black hover:text-white transition-colors duration-200">
                            <i data-lucide="database" class="w-4 h-4 mr-1"></i>
                            Master Pangkat
                        </a>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-black"></i>
                        <span class="text-black font-medium">
                            {{ isset($pangkat) ? 'Edit Pangkat' : 'Tambah Pangkat' }}
                        </span>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ isset($pangkat)
                        ? route('backend.admin-univ-usulan.pangkat.update', $pangkat)
                        : route('backend.admin-univ-usulan.pangkat.store') }}"
              method="POST">
            @csrf
            @if(isset($pangkat))
                @method('PUT')
            @endif

            {{-- Input Pangkat --}}
            <div class="mb-6">
                <label for="pangkat" class="block mb-2 font-semibold text-gray-700">
                    <i data-lucide="award" class="w-4 h-4 inline mr-1"></i>
                    Nama Pangkat <span class="text-red-500">*</span>
                </label>
                <input type="text" name="pangkat" id="pangkat"
                    value="{{ old('pangkat', $pangkat->pangkat ?? '') }}"
                    placeholder="Contoh: Penata Muda (III/a) / Pembina Tingkat I (IV/b)"
                    class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 @error('pangkat') border-red-500 @enderror"
                    required>
                {{-- TAMBAHKAN KODE INI --}}
                @error('pangkat')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Input Status Pangkat --}}
            <div class="mb-6">
                <label for="status_pangkat" class="block mb-2 font-semibold text-gray-700">
                    <i data-lucide="badge-check" class="w-4 h-4 inline mr-1"></i>
                    Status Pangkat <span class="text-red-500">*</span>
                </label>
                <select name="status_pangkat" id="status_pangkat"
                        class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 @error('status_pangkat') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Status Pangkat --</option>
                    <option value="PNS" {{ old('status_pangkat', $pangkat->status_pangkat ?? '') == 'PNS' ? 'selected' : '' }}>
                        PNS (Pegawai Negeri Sipil)
                    </option>
                    <option value="PPPK" {{ old('status_pangkat', $pangkat->status_pangkat ?? '') == 'PPPK' ? 'selected' : '' }}>
                        PPPK (Pegawai Pemerintah dengan Perjanjian Kerja)
                    </option>
                    <option value="Non-ASN" {{ old('status_pangkat', $pangkat->status_pangkat ?? '') == 'Non-ASN' ? 'selected' : '' }}>
                        Non-ASN (Kontrak, Honorer)
                    </option>
                </select>
                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700 font-medium mb-1">💡 Panduan Status Pangkat:</p>
                    <ul class="text-xs text-blue-600 space-y-1">
                        <li><strong>PNS:</strong> Untuk pangkat PNS dengan hirarki promosi (Level 1-17)</li>
                        <li><strong>PPPK:</strong> Untuk pangkat PPPK dengan hirarki yang sama seperti PNS</li>
                        <li><strong>Non-ASN:</strong> Untuk pangkat kontrak/honorer tanpa hirarki</li>
                    </ul>
                </div>
                @error('status_pangkat')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Input Hierarchy Level --}}
            <div class="mb-6">
                <label for="hierarchy_level" class="block mb-2 font-semibold text-gray-700">
                    <i data-lucide="trending-up" class="w-4 h-4 inline mr-1"></i>
                    Level Hirarki <span class="text-gray-500">(Opsional untuk Non-ASN)</span>
                </label>
                <div class="relative">
                    <input type="number" name="hierarchy_level" id="hierarchy_level"
                        value="{{ old('hierarchy_level', $pangkat->hierarchy_level ?? '') }}"
                        placeholder="Contoh: 1, 2, 3, 4, 5..."
                        min="1" max="20"
                        class="w-full border-2 border-gray-300 px-4 py-3 rounded-lg focus:border-indigo-500 focus:ring focus:ring-indigo-200 @error('hierarchy_level') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i data-lucide="hash" class="w-5 h-5 text-gray-400"></i>
                    </div>
                </div>
                <div class="mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <p class="text-sm text-indigo-700 font-medium mb-1">💡 Panduan Level Hirarki Pangkat:</p>
                    <ul class="text-xs text-indigo-600 space-y-1">
                        <li><strong>Kosongkan</strong> untuk pangkat Non-ASN (Kontrak, Honorer)</li>
                        <li><strong>1-4:</strong> Golongan I (Juru)</li>
                        <li><strong>5-8:</strong> Golongan II (Pengatur)</li>
                        <li><strong>9-12:</strong> Golongan III (Penata)</li>
                        <li><strong>13-17:</strong> Golongan IV (Pembina)</li>
                        <li><strong>Contoh:</strong> Penata Muda (III/a) = Level 9</li>
                    </ul>
                </div>
                @error('hierarchy_level')
                    <p class="text-red-600 text-sm mt-1 flex items-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Preview Section --}}
            <div id="previewSection" class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg hidden">
                <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                    Preview Pangkat
                </h3>
                <div id="previewContent" class="text-sm text-gray-600">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-center items-center space-x-4 mt-8">
                <a href="{{ route('backend.admin-univ-usulan.pangkat.index') }}"
                   class="px-6 py-3 rounded-lg border-2 border-gray-300 text-white bg-blue-500 hover:bg-gray-100 hover:text-gray-900 transition duration-200 flex items-center">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 rounded-lg border-2 border-gray-300 text-white bg-blue-500 hover:bg-gray-400 hover:text-gray-900 transition duration-200 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    {{ isset($pangkat) ? 'Update' : 'Simpan' }} Pangkat
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
