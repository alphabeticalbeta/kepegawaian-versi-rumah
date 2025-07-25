@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($unitKerja) ? 'Edit Unit Kerja' : 'Tambah Unit Kerja')

@section('content')
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-lg mx-auto p-6 rounded-md shadow bg-white">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3 w-full">
                        <div class="w-full bg-gray-400 rounded-md p-4">
                            <h2 class="text-xl font-bold text-black leading-tight">
                                {{ isset($unitKerja) ? 'Edit' : 'Tambah' }} Unit Kerja
                            </h2>
                            <p class="text-sm text-black mt-1">
                                Silakan lengkapi data unit kerja secara lengkap dan benar.
                            </p>
                        </div>
                    </div>
                </div>

        <form action="{{ isset($unitKerja)
                        ? route('backend.admin-univ-usulan.unitkerja.update', $unitKerja)
                        : route('backend.admin-univ-usulan.unitkerja.store') }}"
            method="POST">
            @csrf
            @if(isset($unitKerja))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label class="block mb-1 font-medium">Nama Unit Kerja</label>
                <input placeholder="Biro Umum dan Keuangan / Fakultas Teknik" type="text" name="nama"
                    value="{{ old('nama', $unitKerja->nama ?? '') }}"
                    class="w-full border px-3 py-2 rounded" required>
                @error('nama')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center items-center space-x-4 mt-6">
                <a href="{{ route('backend.admin-univ-usulan.unitkerja.index') }}"
                class="px-5 py-2.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 rounded-md bg-gray-500 text-white hover:bg-gray-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
