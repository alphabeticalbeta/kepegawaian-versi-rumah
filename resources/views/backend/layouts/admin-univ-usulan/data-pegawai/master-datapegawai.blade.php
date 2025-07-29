@extends('backend.layouts.admin-univ-usulan.app')

@section('title', 'Master Data Pegawai')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="card">
        <div class="card-header text-center">
            <h1 class="text-2xl font-semibold text-gray-900">Master Data Pegawai</h1>
            <div class="mt-4">
                <a href="{{ route('backend.admin-univ-usulan.data-pegawai.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                    Tambah Data Pegawai
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mx-4 mt-4 text-center">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="p-4">
            <form method="GET" action="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}">
                <div class="flex items-end space-x-4">
                    <div>
                        <label for="filter_jenis_pegawai" class="block text-sm font-medium text-gray-700">Filter Berdasarkan Jenis Pegawai</label>
                        <select id="filter_jenis_pegawai" name="filter_jenis_pegawai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Jenis Pegawai</option>
                            <option value="Dosen" {{ request('filter_jenis_pegawai') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="Tenaga Kependidikan" {{ request('filter_jenis_pegawai') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700 transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="w-full text-sm text-gray-700 border-collapse">
                <thead class="bg-gray-100 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-2 text-center">No</th>
                        <th class="px-4 py-2 text-center">Nama</th>
                        <th class="px-4 py-2 text-center">NIP</th>
                        <th class="px-4 py-2 text-center">Jenis Pegawai</th>
                        <th class="px-4 py-2 text-center">Unit Kerja</th>
                        <th class="px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawais as $index => $pegawai)
                        <tr>
                            <td class="px-4 py-2 text-center">{{ $index + $pegawais->firstItem() }}</td>
                            <td class="px-4 py-2 text-left font-medium">{{ $pegawai->gelar_depan }} {{ $pegawai->nama }} {{ $pegawai->gelar_belakang }}</td>
                            <td class="px-4 py-2 text-center">{{ $pegawai->nip }}</td>
                            <td class="px-4 py-2 text-center">{{ $pegawai->jenis_pegawai }}</td>
                            <td class="px-4 py-2 text-center">{{ $pegawai->unitKerja->nama ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <a href="{{ route('backend.admin-univ-usulan.data-pegawai.edit', $pegawai) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 hover:bg-indigo-600 hover:text-white rounded-md text-sm transition">
                                        <i data-lucide="edit" class="w-4 h-4 mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('backend.admin-univ-usulan.data-pegawai.destroy', $pegawai) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-600 hover:text-white rounded-md text-sm transition">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-gray-500 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="database-x" class="w-12 h-12 text-gray-300 mb-2"></i>
                                    <p>Tidak ada data pegawai yang cocok dengan filter</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-white border-t border-gray-200 text-center">
            {{ $pegawais->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
