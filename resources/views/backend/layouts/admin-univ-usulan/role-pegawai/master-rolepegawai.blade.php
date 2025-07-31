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
            {{-- PERBAIKAN 1: Header Tabel --}}
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    {{-- Header "Nama Pegawai" dibuat rata kiri tapi tengah vertikal --}}
                    <th scope="col" class="px-6 py-4 align-middle">Nama Pegawai</th>
                    {{-- Header lainnya dibuat rata tengah horizontal dan vertikal --}}
                    <th scope="col" class="px-6 py-4 text-center align-middle">NIP</th>
                    <th scope="col" class="px-6 py-4 text-center align-middle">Role yang Dimiliki</th>
                    <th scope="col" class="px-6 py-4 text-center align-middle">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $pegawai)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        {{-- PERBAIKAN 2: Isi Kolom Nama --}}
                        {{-- Kelas 'align-middle' memastikan sel ini berada di tengah secara vertikal --}}
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap align-middle">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full object-cover mr-4 border" src="{{ $pegawai->foto ? Storage::url($pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) }}" alt="Foto">
                                {{ $pegawai->nama_lengkap }}
                            </div>
                        </th>

                        {{-- PERBAIKAN 3: Isi Kolom NIP, Role, dan Aksi --}}
                        {{-- Kelas 'text-center align-middle' membuat konten berada di tengah horizontal & vertikal --}}
                        <td class="px-6 py-4 text-center align-middle">{{ $pegawai->nip }}</td>
                        <td class="px-6 py-4 align-middle">
                            {{-- Wrapper ini untuk menjaga tag role tetap di tengah jika ada banyak --}}
                            <div class="flex flex-wrap justify-center gap-1">
                                @forelse($pegawai->roles as $role)
                                    <span class="px-2 py-1 text-xs font-medium text-indigo-800 bg-indigo-100 rounded-full">{{ $role->name }}</span>
                                @empty
                                    <span class="px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">Belum ada role</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            <a href="{{ route('backend.admin-univ-usulan.role-pegawai.edit', $pegawai) }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-100 text-indigo-700 font-semibold text-sm rounded-lg hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                                <i data-lucide="users" class="w-4 h-4"></i>
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
