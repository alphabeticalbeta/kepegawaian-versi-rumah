@extends('backend.layouts.admin-univ-usulan.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-6">
            <h3 class="text-lg font-semibold text-white">
                {{ isset($periode) ? 'Edit Periode Usulan' : 'Tambah Periode Usulan Baru' }}
            </h3>
            <p class="text-indigo-100 text-sm mt-1">
                Lengkapi formulir di bawah dengan informasi yang tepat.
            </p>
        </div>

        <div class="px-6 py-8">
            <form action="{{ isset($periode) ? route('backend.admin-univ-usulan.periode-usulan.update', $periode->id) : route('backend.admin-univ-usulan.periode-usulan.store') }}" method="POST" class="space-y-8">
                @csrf
                @if(isset($periode))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="nama_periode" class="block text-sm font-semibold text-gray-800">
                            Nama Periode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_periode" id="nama_periode"
                               class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:border-gray-300"
                               placeholder="Contoh: Periode Semester Ganjil 2024"
                               value="{{ old('nama_periode', $periode->nama_periode ?? '') }}" required>
                        @error('nama_periode')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-800">Jenis Usulan</label>
                        <div class="block w-full px-4 py-3 border-2 border-gray-200 bg-gray-50 rounded-xl shadow-sm">
                            <span class="text-gray-800 font-semibold capitalize">{{ $jenis_usulan_otomatis }}</span>
                        </div>
                        <input type="hidden" name="jenis_usulan" value="{{ $jenis_usulan_otomatis }}">
                    </div>
                </div>

                <div class="p-6 border-2 border-gray-200 rounded-xl space-y-4">
                    <h4 class="text-md font-semibold text-gray-800">Jadwal Pengajuan Usulan</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-800">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm"
                                   value="{{ old('tanggal_mulai', $periode->tanggal_mulai ?? '') }}" required>
                            @error('tanggal_mulai')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-800">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm"
                                   value="{{ old('tanggal_selesai', $periode->tanggal_selesai ?? '') }}" required>
                            @error('tanggal_selesai')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="p-6 border-2 border-gray-200 rounded-xl space-y-4">
                     <h4 class="text-md font-semibold text-gray-800">Jadwal Perbaikan (Opsional)</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="tanggal_mulai_perbaikan" class="block text-sm font-semibold text-gray-800">
                                Tanggal Mulai Perbaikan
                            </label>
                            <input type="date" name="tanggal_mulai_perbaikan" id="tanggal_mulai_perbaikan"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm"
                                   value="{{ old('tanggal_mulai_perbaikan', $periode->tanggal_mulai_perbaikan ?? '') }}">
                            @error('tanggal_mulai_perbaikan')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="tanggal_selesai_perbaikan" class="block text-sm font-semibold text-gray-800">
                                Tanggal Selesai Perbaikan
                            </label>
                            <input type="date" name="tanggal_selesai_perbaikan" id="tanggal_selesai_perbaikan"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm"
                                   value="{{ old('tanggal_selesai_perbaikan', $periode->tanggal_selesai_perbaikan ?? '') }}">
                            @error('tanggal_selesai_perbaikan')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="status" class="block text-sm font-semibold text-gray-800">
                        Status Periode <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 bg-white rounded-xl shadow-sm appearance-none cursor-pointer">
                        <option value="Tutup" {{ old('status', $periode->status ?? 'Tutup') == 'Tutup' ? 'selected' : '' }}>Tutup</option>
                        <option value="Buka" {{ old('status', $periode->status ?? '') == 'Buka' ? 'selected' : '' }}>Buka</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('backend.admin-univ-usulan.pusat-usulan.index') }}" class="px-6 py-3 border-2 border-gray-300 text-sm font-semibold text-gray-700 bg-white rounded-xl hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg">
                        {{ isset($periode) ? 'Update Periode' : 'Simpan Periode' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
