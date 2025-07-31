@extends('backend.layouts.admin-univ-usulan.app')

@section('title', 'Edit Role Pegawai')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Role Pegawai</h2>
            <p class="text-gray-600">untuk: <span class="font-semibold">{{ $pegawai->nama_lengkap }}</span></p>
        </div>
        <a href="{{ route('backend.admin-univ-usulan.role-pegawai.index') }}" class="text-sm text-gray-600 hover:text-indigo-600"> &larr; Kembali ke Daftar</a>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <form action="{{ route('backend.admin-univ-usulan.role-pegawai.update', $pegawai) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-base font-medium text-gray-800 mb-3">Tetapkan Role</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 border rounded-md bg-gray-50">
                    @foreach ($roles as $role)
                        <label for="role-{{ $role->id }}" class="flex items-center space-x-3 p-3 hover:bg-gray-100 rounded-md cursor-pointer">
                            <input id="role-{{ $role->id }}" name="roles[]" type="checkbox" value="{{ $role->id }}"
                                   class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                   {{-- Cek apakah pegawai sudah memiliki role ini --}}
                                   @if($pegawai->roles->contains($role)) checked @endif
                                   >
                            <span class="text-gray-700 text-sm font-medium">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('backend.admin-univ-usulan.role-pegawai.index') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
