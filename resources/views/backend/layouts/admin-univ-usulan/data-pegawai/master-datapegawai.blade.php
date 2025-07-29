@extends('backend.layouts.admin-univ-usulan.app')

@section('title', 'Master Data Pegawai')

@section('content')
<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Master Data Pegawai</h2>
        </div>
        <div class="my-2 flex sm:flex-row flex-col">
            <div class="flex flex-row mb-1 sm:mb-0">
                <a href="{{ route('backend.admin-univ-usulan.data-pegawai.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Tambah Data Pegawai
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIP</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jabatan</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit Kerja</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pegawais as $pegawai)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $loop->iteration + $pegawais->firstItem() - 1 }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $pegawai->nip }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }} {{ $pegawai->gelar_belakang }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $pegawai->jabatan->jabatan ?? 'N/A' }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $pegawai->unitKerja->nama ?? 'N/A' }}</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <a href="{{ route('backend.admin-univ-usulan.data-pegawai.edit', $pegawai->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('backend.admin-univ-usulan.data-pegawai.destroy', $pegawai->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Data Pegawai tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                    {{ $pegawais->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
