@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', 'Master Data Jabatan')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card mb-6">
            <div class="bg-gray-200 text-black p-6 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">Master Data Jabatan</h1>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('backend.admin-univ-usulan.jabatan.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Tambah Jabatan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card mb-6">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
                    Filter & Pencarian Data
                </h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('backend.admin-univ-usulan.jabatan.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                        {{-- Filter Jenis Pegawai --}}
                        <div>
                            <label for="jenis_pegawai" class="block text-sm font-medium text-gray-700 mb-2">Jenis Pegawai</label>
                            <select name="jenis_pegawai" id="jenis_pegawai"
                                    class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Jenis</option>
                                @foreach($filterData['jenis_pegawai_options'] as $option)
                                    <option value="{{ $option }}" {{ request('jenis_pegawai') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Jenis Jabatan --}}
                        <div>
                            <label for="jenis_jabatan" class="block text-sm font-medium text-gray-700 mb-2">Jenis Jabatan</label>
                            <select name="jenis_jabatan" id="jenis_jabatan"
                                    class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Jenis</option>
                                @foreach($filterData['jenis_jabatan_options'] as $option)
                                    <option value="{{ $option }}" {{ request('jenis_jabatan') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Nama Jabatan --}}
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Nama Jabatan</label>
                            <input type="text" name="search" id="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama jabatan..."
                                class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Filter Hirarki --}}
                        <div>
                            <label for="has_hierarchy" class="block text-sm font-medium text-gray-700 mb-2">Status Hirarki</label>
                            <select name="has_hierarchy" id="has_hierarchy"
                                    class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="yes" {{ request('has_hierarchy') == 'yes' ? 'selected' : '' }}>Dengan Hirarki</option>
                                <option value="no" {{ request('has_hierarchy') == 'no' ? 'selected' : '' }}>Tanpa Hirarki</option>
                            </select>
                        </div>

                        {{-- Filter Eligible Usulan --}}
                        <div>
                            <label for="eligible_usulan" class="block text-sm font-medium text-gray-700 mb-2">Dapat Usulan</label>
                            <select name="eligible_usulan" id="eligible_usulan"
                                    class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="yes" {{ request('eligible_usulan') == 'yes' ? 'selected' : '' }}>Ya</option>
                                <option value="no" {{ request('eligible_usulan') == 'no' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                    </div>

                    {{-- Filter Action Buttons --}}
                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-3">
                            <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                                <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                                <span>Cari</span>
                            </button>
                            <a href="{{ route('backend.admin-univ-usulan.jabatan.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Reset
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $jabatans->count() }} dari {{ $jabatans->total() }} data
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="card">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-800">Data Jabatan (Diurutkan berdasarkan Hirarki)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Jabatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jabatan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($jabatans as $index => $jabatan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ($jabatans->currentPage() - 1) * $jabatans->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($jabatan->jenis_pegawai === 'Dosen')
                                            <i data-lucide="graduation-cap" class="w-4 h-4 text-blue-500 mr-2"></i>
                                        @else
                                            <i data-lucide="users" class="w-4 h-4 text-green-500 mr-2"></i>
                                        @endif
                                        <span class="text-sm text-gray-900">{{ $jabatan->jenis_pegawai }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $jabatan->jenis_jabatan_badge_class }}">
                                        {{ $jabatan->jenis_jabatan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $jabatan->jabatan }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($jabatan->hierarchy_level)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                                            Level {{ $jabatan->hierarchy_level }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i data-lucide="minus" class="w-3 h-3 mr-1"></i>
                                            Flat
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($jabatan->isEligibleForUsulan())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                            Dapat Usulan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                                            Tidak Usulan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="{{ route('backend.admin-univ-usulan.jabatan.edit', $jabatan) }}"
                                        class="text-indigo-600 hover:text-indigo-900 transition">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('backend.admin-univ-usulan.jabatan.destroy', $jabatan) }}"
                                            method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Hapus jabatan {{ $jabatan->jabatan }}?')"
                                                    class="text-red-600 hover:text-red-900 transition">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="search-x" class="w-12 h-12 text-gray-400 mb-4"></i>
                                        <p class="text-gray-500 font-medium">Tidak ada data jabatan yang ditemukan</p>
                                        <p class="text-gray-400 text-sm mt-1">Coba ubah filter atau tambah data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($jabatans->hasPages())
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    {{ $jabatans->links() }}
                </div>
            @endif
        </div>
    </div>


@endsection
