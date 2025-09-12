@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Manajemen Role Pegawai')

@push('styles')
<style>
    /* Hide scrollbar for table container */
    .overflow-x-auto::-webkit-scrollbar {
        display: none;
    }

    .overflow-x-auto {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Table transition animations */
    #pegawaiTableBody {
        transition: opacity 0.3s ease-in-out;
    }

    .table-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .table-loaded {
        opacity: 1;
    }

    /* Force scrollbar to always show */
    .force-scrollbar {
        overflow: auto !important;
        scrollbar-width: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .force-scrollbar::-webkit-scrollbar {
        width: 12px !important;
        height: 12px !important;
    }

    .force-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1 !important;
        border-radius: 6px !important;
    }

    .force-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1 !important;
        border-radius: 6px !important;
    }

    .force-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8 !important;
    }

    /* Fade in animation for new content */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Header Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 py-8 sm:px-6 sm:py-10">
            <div class="mx-auto max-w-full text-center">
                <div class="mb-4 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-20 w-auto object-contain">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-black sm:text-3xl mb-2">
                    Manajemen Role Pegawai
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola role dan permission untuk setiap pegawai Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">

            <!-- Filter and Search -->
            <div class="mb-4 bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Pegawai</label>
                        <input type="text" id="searchInput" placeholder="Nama, NIP, atau Email..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                </div>
                <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status Kepegawaian</label>
                        <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua Status</option>
                            @php
                                $uniqueStatus = $pegawais->pluck('status_kepegawaian')->filter()->unique()->sort();
                            @endphp
                            @foreach($uniqueStatus as $status)
                                <option value="{{ $status }}" {{ request('status_kepegawaian') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                    </select>
                </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Unit Kerja</label>
                        <select id="filterUnitKerja" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua Unit Kerja</option>
                            @php
                                $uniqueUnitKerjas = $pegawais->pluck('unitKerja.subUnitKerja.unitKerja.nama')->filter()->unique()->sort();
                            @endphp
                            @foreach($uniqueUnitKerjas as $unitKerja)
                                <option value="{{ $unitKerja }}" {{ request('unit_kerja') == $unitKerja ? 'selected' : '' }}>{{ $unitKerja }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                        <select id="filterRole" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua Role</option>
                            @php
                                $uniqueRoles = $pegawais->pluck('roles')->flatten()->pluck('name')->unique()->sort();
                            @endphp
                            @foreach($uniqueRoles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
        </div>

        <!-- Role Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-gray-600">Admin Univ</p>
                        <p class="text-lg font-bold text-indigo-600">{{ $pegawais->where('roles.0.name', 'Kepegawaian Universitas')->count() }}</p>
                    </div>
                    <div class="p-2 bg-indigo-100 rounded-xl">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                    </div>
                </div>
            </div>

                <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-gray-600">Admin Fakultas</p>
                        <p class="text-lg font-bold text-green-600">{{ $pegawais->where('roles.0.name', 'Admin Fakultas')->count() }}</p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-xl">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                    </div>
                </div>
            </div>

                <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-gray-600">Admin Keuangan</p>
                        <p class="text-lg font-bold text-yellow-600">{{ $pegawais->where('roles.0.name', 'Admin Keuangan')->count() }}</p>
                    </div>
                    <div class="p-2 bg-yellow-100 rounded-xl">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                    </div>
                </div>
            </div>

                <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-gray-600">Tim Senat</p>
                        <p class="text-lg font-bold text-orange-600">{{ $pegawais->where('roles.0.name', 'Tim Senat')->count() }}</p>
                    </div>
                    <div class="p-2 bg-orange-100 rounded-xl">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                    </div>
                </div>
            </div>

                <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-gray-600">Penilai</p>
                        <p class="text-lg font-bold text-purple-600">{{ $pegawais->where('roles.0.name', 'Penilai Universitas')->count() }}</p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-xl">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                    </div>
                </div>
            </div>

                <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-gray-600">Pegawai</p>
                        <p class="text-lg font-bold text-blue-600">{{ $pegawais->where('roles.0.name', 'Pegawai Unmul')->count() }}</p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-xl">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                    </div>
                </div>
            </div>

                <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                            <p class="text-xs text-gray-600">Tanpa Role</p>
                        <p class="text-lg font-bold text-gray-600">{{ $pegawais->where('roles', '[]')->count() }}</p>
                    </div>
                    <div class="p-2 bg-gray-100 rounded-xl">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header with Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 gap-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Daftar Role Pegawai</h3>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <!-- Refresh Button -->
                        <button onclick="window.location.reload()"
                                class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-gray-700 hover:to-gray-800 focus:from-gray-700 focus:to-gray-800 active:from-gray-900 active:to-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                            <svg class="h-4 w-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="hidden sm:inline">Refresh</span>
                            <span class="sm:hidden">Refresh</span>
                        </button>
        </div>
            </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-16">No</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-48">Nama & NIP</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Status Kepegawaian</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-40">Unit Kerja</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-40">Role</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pegawaiTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($pegawais as $index => $pegawai)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                    {{ ($pegawais->currentPage() - 1) * $pegawais->perPage() + $index + 1 }}
                                </td>
                                <td class="px-2 sm:px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full mr-2 sm:mr-4 border border-gray-200 overflow-hidden">
                                            @if($pegawai->foto && !empty($pegawai->foto))
                                                <img class="h-full w-full object-cover"
                                                     src="{{ route('backend.kepegawaian-universitas.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => 'foto']) }}"
                                                     alt="{{ $pegawai->nama_lengkap }}"
                                                     onerror="this.parentElement.innerHTML='<div class=\'h-full w-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs\'>{{ substr($pegawai->nama_lengkap, 0, 2) }}</div>'">
                                            @else
                                                <div class="h-full w-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                                                    {{ substr($pegawai->nama_lengkap, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <div class="text-xs sm:text-sm font-medium text-gray-900">{{ $pegawai->nama_lengkap }}</div>
                                            <div class="text-xs text-gray-500">{{ $pegawai->nip }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    @if($pegawai->status_kepegawaian == 'PNS')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            PNS
                                        </span>
                                    @elseif($pegawai->status_kepegawaian == 'PPPK')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            PPPK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Non-ASN
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <div class="font-medium">{{ $pegawai->unitKerja?->subUnitKerja?->unitKerja?->nama ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    @if($pegawai->roles->count() > 0)
                                        <div class="flex flex-wrap gap-1 justify-center">
                                            @foreach($pegawai->roles as $role)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Tanpa Role
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('backend.kepegawaian-universitas.role-pegawai.edit', $pegawai) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                           title="Edit Role">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900">Tidak ada data pegawai</h3>
                                        <p class="text-gray-500 mt-1">Data pegawai akan ditampilkan di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>

                <!-- Pagination -->
            @if($pegawais->hasPages())
            <div id="paginationContainer" class="bg-white rounded-2xl shadow-xl overflow-hidden mt-6">
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-700">
                        <span id="paginationInfo">Menampilkan {{ $pegawais->firstItem() ?? 0 }} - {{ $pegawais->lastItem() ?? 0 }} dari {{ $pegawais->total() }} data</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($pegawais->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $pegawais->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                        @endif
                        <div id="pageNumbers" class="flex space-x-1">
                            @foreach($pegawais->getUrlRange(1, $pegawais->lastPage()) as $page => $url)
                                @if($page == $pegawais->currentPage())
                                    <span class="px-3 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg shadow-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-200">{{ $page }}</a>
                                @endif
                            @endforeach
                </div>
                        @if($pegawais->hasMorePages())
                            <a href="{{ $pegawais->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


@push('styles')
<style>
    /* Pagination container styling */
    #paginationContainer {
        display: block;
    }

    /* Table transition animations */
    #pegawaiTableBody {
        transition: opacity 0.3s ease-in-out;
    }

    .table-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .table-loaded {
        opacity: 1;
    }

    /* Pagination button animations */
    #paginationContainer button {
        transition: all 0.2s ease-in-out;
    }

    #paginationContainer button:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #paginationContainer button:active:not(:disabled) {
        transform: translateY(0);
    }

    /* Page number button animations */
    #pageNumbers button {
        transition: all 0.2s ease-in-out;
    }

    #pageNumbers button:hover {
        transform: scale(1.05);
    }

    #pageNumbers button:active {
        transform: scale(0.95);
    }

    /* Loading spinner animation */
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #6366f1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Fade in animation for new content */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<script>
// Global variables
let pegawaiData = [];
let currentPage = 1;
let totalPages = 1;
let totalData = 0;
let currentSearch = '';

// Escape HTML function for security
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Simple form initialization - no AJAX needed
});
</script>
@endpush
@endsection
