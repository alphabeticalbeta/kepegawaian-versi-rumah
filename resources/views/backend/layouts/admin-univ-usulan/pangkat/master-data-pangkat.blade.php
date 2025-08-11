@extends('backend.layouts.admin-univ-usulan.app')

@section('title', 'Master Data Pangkat')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header Section --}}
    <div class="card mb-6">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-t-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">Master Data Pangkat</h1>
                    <p class="text-indigo-100 mt-1">Kelola hirarki pangkat PNS, PPPK, dan Non-ASN untuk sistem usulan</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('backend.admin-univ-usulan.pangkat.create') }}"
                       class="bg-white text-indigo-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Tambah Pangkat
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-400 mr-2"></i>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 mr-2"></i>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="card">
        <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-800">Data Pangkat (Diurutkan berdasarkan Status & Hirarki)</h3>
            <p class="text-sm text-gray-600 mt-1">Pangkat PNS & PPPK diurutkan dari level terendah ke tertinggi, Non-ASN di bagian akhir</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pangkat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pangkat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Golongan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pangkats as $index => $pangkat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ($pangkats->currentPage() - 1) * $pangkats->perPage() + $index + 1 }}
                            </td>

                            {{-- Status Pangkat --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $pangkat->status_pangkat_badge_class ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                    <i data-lucide="{{ $pangkat->status_icon ?? 'user' }}" class="w-3 h-3 mr-1"></i>
                                    {{ $pangkat->status_pangkat ?? 'PNS' }}
                                </span>
                            </td>

                            {{-- Pangkat --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($pangkat->hierarchy_level)
                                        <div class="flex-shrink-0 w-8 h-8 {{ $pangkat->hierarchy_badge_class ?? 'bg-gray-100 text-gray-800' }} rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                            {{ $pangkat->hierarchy_level }}
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="minus" class="w-4 h-4"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $pangkat->pangkat }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $pangkat->hierarchy_level ? 'Dengan Hirarki' : 'Tanpa Hirarki' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Golongan --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pangkat->golongan)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pangkat->hierarchy_badge_class ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $pangkat->golongan }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        -
                                    </span>
                                @endif
                            </td>

                            {{-- Level --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pangkat->hierarchy_level)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pangkat->hierarchy_badge_class ?? 'bg-gray-100 text-gray-800' }}">
                                        <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                                        Level {{ $pangkat->hierarchy_level }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i data-lucide="minus" class="w-3 h-3 mr-1"></i>
                                        Non-Hierarki
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center space-x-3">
                                    <a href="{{ route('backend.admin-univ-usulan.pangkat.edit', $pangkat) }}"
                                       class="text-indigo-600 hover:text-indigo-900 transition">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('backend.admin-univ-usulan.pangkat.destroy', $pangkat) }}"
                                          method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Hapus pangkat {{ $pangkat->pangkat }}?')"
                                                class="text-red-600 hover:text-red-900 transition">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="award" class="w-12 h-12 text-gray-400 mb-4"></i>
                                    <p class="text-gray-500 font-medium">Tidak ada data pangkat</p>
                                    <p class="text-gray-400 text-sm mt-1">Tambah data pangkat untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pangkats->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $pangkats->links() }}
            </div>
        @endif
    </div>

    {{-- Summary Statistics --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $totalPangkats = $pangkats->total();
            $pnsCount = $pangkats->where('status_pangkat', 'PNS')->count();
            $pppkCount = $pangkats->where('status_pangkat', 'PPPK')->count();
            $nonAsnCount = $pangkats->where('status_pangkat', 'Non-ASN')->count();
        @endphp

        <div class="bg-white rounded-lg border p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $totalPangkats }}</div>
            <div class="text-sm text-gray-600">Total Pangkat</div>
        </div>

        <div class="bg-green-50 rounded-lg border border-green-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-700">{{ $pnsCount }}</div>
            <div class="text-sm text-green-600">Pangkat PNS</div>
        </div>

        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-700">{{ $pppkCount }}</div>
            <div class="text-sm text-blue-600">Pangkat PPPK</div>
        </div>

        <div class="bg-orange-50 rounded-lg border border-orange-200 p-4 text-center">
            <div class="text-2xl font-bold text-orange-700">{{ $nonAsnCount }}</div>
            <div class="text-sm text-orange-600">Pangkat Non-ASN</div>
        </div>
    </div>
</div>
@endsection
