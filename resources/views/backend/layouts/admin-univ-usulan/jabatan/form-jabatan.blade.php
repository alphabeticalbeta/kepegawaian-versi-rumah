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

            <div class="mb-4">
                <label class="block mb-1 font-medium">Jenis Jabatan</label>
                <select name="jenis_jabatan" class="w-full border px-3 py-2 rounded @error('jenis_jabatan') border-red-500 @enderror" required>
                    <option value="">-- Pilih Jenis Jabatan --</option>
                    @foreach([
                        'Dosen Fungsional',
                        'Dosen Fungsi Tambahan',
                        'Tenaga Kependidikan Struktural',
                        'Tenaga Kependidikan Fungsional Umum',
                        'Tenaga Kependidikan Fungsional Tertentu',
                        'Tenaga Kependidikan Tugas Tambahan',
                    ] as $option)
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
                    placeholder="Contoh: Kepala Bagian / Staf Administrasi"
                    class="w-full border px-3 py-2 rounded @error('jabatan') border-red-500 @enderror" required>
                @error('jabatan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center items-center space-x-4 mt-6">
                <a href="{{ route('backend.admin-univ-usulan.jabatan.index') }}"
                   class="px-5 py-2.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
