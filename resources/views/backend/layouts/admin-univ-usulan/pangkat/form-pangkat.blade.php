@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($pangkat) ? 'Edit Pangkat' : 'Tambah Pangkat')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-lg mx-auto p-6 rounded-md shadow bg-white">
        {{-- Bagian header form --}}
        <div class="mb-6">
            <div class="bg-blue-50 rounded-md p-4">
                <h2 class="text-xl font-bold text-blue-800 leading-tight">
                    {{ isset($pangkat) ? 'Edit' : 'Tambah' }} Pangkat
                </h2>
                <p class="text-sm text-blue-700 mt-1">
                    Silakan lengkapi data pangkat dengan benar.
                </p>
            </div>
        </div>

        {{-- Form input --}}
        <form action="{{ isset($pangkat)
                        ? route('backend.admin-univ-usulan.pangkat.update', $pangkat)
                        : route('backend.admin-univ-usulan.pangkat.store') }}"
              method="POST">
            @csrf
            @if(isset($pangkat))
                @method('PUT')
            @endif

            {{-- Input Pangkat --}}
            <div class="mb-4">
                <label class="block mb-1 font-medium">Nama Pangkat</label>
                <input type="text" name="pangkat"
                    value="{{ old('pangkat', $pangkat->pangkat ?? '') }}"
                    placeholder="Contoh: Penata Muda / Pembina"
                    class="w-full border px-3 py-2 rounded @error('pangkat') border-red-500 @enderror" required>
                @error('pangkat')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol aksi --}}
            <div class="flex justify-center items-center space-x-4 mt-6">
                <a href="{{ route('backend.admin-univ-usulan.pangkat.index') }}"
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
