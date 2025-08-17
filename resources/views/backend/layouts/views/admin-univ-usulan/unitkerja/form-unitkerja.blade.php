@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', isset($item) ? 'Edit Unit Kerja' : 'Tambah Unit Kerja')

@section('dashboard-content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">
                            {{ isset($item) ? 'Edit Unit Kerja' : 'Tambah Unit Kerja' }}
                        </h1>
                        <p class="text-slate-600">
                            {{ isset($item) ? 'Perbarui data unit kerja' : 'Tambah unit kerja baru ke sistem' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('backend.admin-univ-usulan.unitkerja.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Form Unit Kerja</h2>
                <p class="text-slate-600">Isi data unit kerja sesuai hierarki yang diinginkan</p>
            </div>

            <form action="{{ isset($item) ? route('backend.admin-univ-usulan.unitkerja.update', ['type' => $type, 'id' => $item->id]) : route('backend.admin-univ-usulan.unitkerja.store') }}"
                  method="POST"
                  class="p-6 space-y-6">
                @csrf
                @if(isset($item))
                    @method('PUT')
                @endif

                <!-- Unit Kerja Field -->
                <div class="space-y-4">
                    <label for="unit_kerja_nama" class="block text-sm lg:text-base font-semibold text-slate-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Unit Kerja
                        </div>
                    </label>
                    <input type="text"
                           name="unit_kerja_nama"
                           id="unit_kerja_nama"
                           value="{{ old('unit_kerja_nama', isset($item) && $type == 'sub_unit_kerja' ? $item->unitKerja->nama ?? '' : '') }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="Masukkan nama unit kerja">
                </div>

                <!-- Sub Unit Kerja Field -->
                <div class="space-y-4">
                    <label for="sub_unit_kerja_nama" class="block text-sm lg:text-base font-semibold text-slate-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Sub Unit Kerja
                        </div>
                    </label>
                    <input type="text"
                           name="sub_unit_kerja_nama"
                           id="sub_unit_kerja_nama"
                           value="{{ old('sub_unit_kerja_nama', isset($item) && $type == 'sub_sub_unit_kerja' ? $item->subUnitKerja->nama ?? '' : '') }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="Masukkan nama sub unit kerja">
                </div>

                <!-- Nama Field -->
                <div class="space-y-4">
                    <label for="nama" class="block text-sm lg:text-base font-semibold text-slate-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Nama <span class="text-red-500">*</span>
                        </div>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama', $item->nama ?? '') }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition-all duration-300 placeholder-slate-400 bg-white/80 backdrop-blur-sm hover:bg-white hover:shadow-md"
                           placeholder="Masukkan nama unit kerja">
                    @error('nama')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4 pt-6 border-t border-slate-200">
                    <a href="{{ route('backend.admin-univ-usulan.unitkerja.index') }}"
                       class="px-6 py-3 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        {{ isset($item) ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
