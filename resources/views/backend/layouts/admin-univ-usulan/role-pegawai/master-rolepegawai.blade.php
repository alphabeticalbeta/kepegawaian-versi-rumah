@extends('backend.layouts.admin-univ-usulan.app')

@section('title', 'Manajemen Role Pegawai')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- HEADER --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Role Pegawai</h1>
        <p class="text-sm text-gray-500 mt-1">Tetapkan satu atau lebih peran untuk setiap pegawai.</p>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 text-center rounded-md shadow-sm">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- TABEL PEGAWAI --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama Pegawai</th>
                    <th scope="col" class="px-6 py-3">NIP</th>
                    <th scope="col" class="px-6 py-3">Role yang Dimiliki</th>
                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $pegawai)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap flex items-center">
                            <img class="h-10 w-10 rounded-full object-cover mr-4 border" src="{{ $pegawai->foto ? Storage::url($pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) }}" alt="Foto">
                            {{ $pegawai->nama_lengkap }}
                        </th>
                        <td class="px-6 py-4">{{ $pegawai->nip }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($pegawai->roles as $role)
                                    <span class="px-2 py-1 text-xs font-medium text-indigo-800 bg-indigo-100 rounded-full">{{ $role->name }}</span>
                                @empty
                                    <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">Belum ada role</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{-- PERUBAHAN: Tombol diubah menjadi link biasa --}}
                            <a href="{{ route('backend.admin-univ-usulan.role-pegawai.edit', $pegawai) }}" class="font-medium text-indigo-600 hover:text-indigo-900 hover:underline">
                                Kelola Role
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-8 text-gray-500">Tidak ada data pegawai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $pegawais->links() }}
    </div>
</div>
@endsection
