@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Master Data Unit Kerja')

@push('styles')
<style>
    /* Fade in animation for new content */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
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
                    Master Data Unit Kerja
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola hierarki Unit Kerja, Sub Unit Kerja, dan Sub-sub Unit Kerja
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 py-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">
            <!-- Action Buttons -->
            <div class="mb-4 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('backend.kepegawaian-universitas.unitkerja.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Unit Kerja
                </a>
            </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="mb-4 bg-white rounded-2xl shadow-xl p-3 sm:p-4 transition-all duration-300 hover:shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Search Input -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <input type="text"
                           id="searchInput"
                           placeholder="Cari unit kerja, sub unit kerja, atau sub-sub unit kerja..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                </div>

                <!-- Level Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Level Unit Kerja</label>
                    <select id="filterLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                        <option value="">Semua Level</option>
                        <option value="unit_kerja">Unit Kerja Saja</option>
                        <option value="sub_unit_kerja">Sub Unit Kerja Saja</option>
                        <option value="sub_sub_unit_kerja">Sub-sub Unit Kerja Saja</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Total Unit Kerja</p>
                        <p class="text-lg font-bold text-blue-600" id="totalUnitKerja">{{ $unitKerjas->count() }}</p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-xl">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Total Sub Unit Kerja</p>
                        <p class="text-lg font-bold text-green-600" id="totalSubUnitKerja">{{ $unitKerjas->sum(function($uk) { return $uk->subUnitKerjas->count(); }) }}</p>
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
                        <p class="text-xs text-gray-600">Total Sub-sub Unit Kerja</p>
                        <p class="text-lg font-bold text-purple-600" id="totalSubSubUnitKerja">{{ $unitKerjas->sum(function($uk) { return $uk->subUnitKerjas->sum(function($suk) { return $suk->subSubUnitKerjas->count(); }); }) }}</p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-xl">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-600">Total Semua Level</p>
                        <p class="text-lg font-bold text-indigo-600" id="totalAllLevels">{{ $unitKerjas->count() + $unitKerjas->sum(function($uk) { return $uk->subUnitKerjas->count(); }) + $unitKerjas->sum(function($uk) { return $uk->subUnitKerjas->sum(function($suk) { return $suk->subSubUnitKerjas->count(); }); }) }}</p>
                    </div>
                    <div class="p-2 bg-indigo-100 rounded-xl">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hierarchical Data Display -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/30 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/30">
                <h2 class="text-xl font-bold text-slate-800">Hierarki Unit Kerja</h2>
                <p class="text-slate-600">Struktur organisasi Unit Kerja Universitas Mulawarman</p>
            </div>

            <div class="p-6">
                <!-- Loading State -->
                <div id="loadingState" class="hidden text-center py-8">
                    <div class="inline-flex items-center gap-3 text-blue-600">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memuat data...</span>
                    </div>
                </div>

                <!-- Data Container -->
                <div id="unitKerjaDataContainer">
                    @if($unitKerjas->count() > 0)
                        <div class="space-y-6">
                            @foreach($unitKerjas as $unitKerja)
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200/50 overflow-hidden">
                            <!-- Unit Kerja Header -->
                            <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <h3 class="text-lg font-bold">{{ $unitKerja->nama }}</h3>
                                        <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium">Unit Kerja</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'unit_kerja', 'id' => $unitKerja->id]) }}"
                                           class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'unit_kerja', 'id' => $unitKerja->id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Unit Kerja ini?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Sub Unit Kerja -->
                            @if($unitKerja->subUnitKerjas->count() > 0)
                                <div class="p-4 space-y-3">
                                    @foreach($unitKerja->subUnitKerjas as $subUnitKerja)
                                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                                        <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <h4 class="font-semibold">{{ $subUnitKerja->nama }}</h4>
                                                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium">Sub Unit Kerja</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'sub_unit_kerja', 'id' => $subUnitKerja->id]) }}"
                                                       class="p-1.5 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'sub_unit_kerja', 'id' => $subUnitKerja->id]) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub Unit Kerja ini?')"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub-sub Unit Kerja -->
                                        @if($subUnitKerja->subSubUnitKerjas->count() > 0)
                                            <div class="p-3 space-y-2">
                                                @foreach($subUnitKerja->subSubUnitKerjas as $subSubUnitKerja)
                                                <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg">
                                                    <div class="flex items-center gap-3">
                                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                        <span class="font-medium text-slate-700">{{ $subSubUnitKerja->nama }}</span>
                                                        <span class="px-2 py-1 bg-slate-200 text-slate-600 rounded-lg text-xs font-medium">Sub-sub Unit Kerja</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'sub_sub_unit_kerja', 'id' => $subSubUnitKerja->id]) }}"
                                                           class="p-1 bg-slate-200 rounded hover:bg-slate-300 transition-colors">
                                                            <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'sub_sub_unit_kerja', 'id' => $subSubUnitKerja->id]) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub-sub Unit Kerja ini?')"
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 bg-slate-200 rounded hover:bg-slate-300 transition-colors">
                                                                <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-slate-900">Belum Ada Unit Kerja</h3>
                                <p class="mt-2 text-slate-500">Mulai dengan menambahkan unit kerja pertama Anda.</p>
                                <div class="mt-6">
                                    <a href="{{ route('backend.kepegawaian-universitas.unitkerja.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Tambah Unit Kerja Pertama
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Loading animation */

    /* Table transition animations */
    #unitKerjaDataContainer {
        transition: opacity 0.3s ease-in-out;
    }

    .table-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .table-loaded {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
// Global variables
let unitKerjaData = [];
let currentSearch = '';
let currentLevel = '';
let currentStatus = '';

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
    initializeForm();
});

// Initialize form
function initializeForm() {
    // Initialize search input with real-time filtering
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            currentSearch = e.target.value.trim();
            loadUnitKerjaData();
        }, 300));
    }

    // Initialize filter dropdowns with real-time filtering
    const filterLevel = document.getElementById('filterLevel');
    const filterStatus = document.getElementById('filterStatus');

    if (filterLevel) {
        filterLevel.addEventListener('change', function() {
            currentLevel = this.value;
            loadUnitKerjaData();
        });
    }

    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            currentStatus = this.value;
            loadUnitKerjaData();
        });
    }
}

// Load unit kerja data
async function loadUnitKerjaData() {
    try {
        showLoading();

        const params = new URLSearchParams({
            search: currentSearch,
            level: currentLevel,
            status: currentStatus
        });

        const response = await fetch(`{{ route('backend.kepegawaian-universitas.unitkerja.index') }}?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const result = await response.json();

            if (result.success) {
                unitKerjaData = result.data;
                setTimeout(() => {
                    displayUnitKerjaData();
                    updateStatistics(result);
                    hideLoading();
                }, 200);
            } else {
                hideLoading();
                showError('Gagal memuat data unit kerja');
            }
        } else {
            hideLoading();
            showError('Terjadi kesalahan saat memuat data');
        }
    } catch (error) {
        hideLoading();
        console.error('Error loading unit kerja data:', error);
        showError('Terjadi kesalahan jaringan');
    }
}

// Display unit kerja data
function displayUnitKerjaData() {
    const container = document.getElementById('unitKerjaDataContainer');

    if (!unitKerjaData || unitKerjaData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-slate-900">Tidak Ada Data Ditemukan</h3>
                <p class="mt-2 text-slate-500">Coba ubah kriteria pencarian atau filter Anda.</p>
            </div>
        `;
        return;
    }

    let html = '<div class="space-y-6">';

    unitKerjaData.forEach((unitKerja, index) => {
        html += generateUnitKerjaHTML(unitKerja, index);
    });

    html += '</div>';
    container.innerHTML = html;
}

// Generate HTML for unit kerja
function generateUnitKerjaHTML(unitKerja, index) {
    let html = `
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200/50 overflow-hidden animate-fade-in" style="animation-delay: ${index * 100}ms">
            <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="text-lg font-bold">${escapeHtml(unitKerja.nama)}</h3>
                        <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium">Unit Kerja</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'unit_kerja', 'id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', unitKerja.id)
                           class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'unit_kerja', 'id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', unitKerja.id)
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Unit Kerja ini?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    `;

    // Add sub unit kerjas
    if (unitKerja.sub_unit_kerjas && unitKerja.sub_unit_kerjas.length > 0) {
        html += '<div class="p-4 space-y-3">';
        unitKerja.sub_unit_kerjas.forEach((subUnitKerja, subIndex) => {
            html += generateSubUnitKerjaHTML(subUnitKerja, subIndex);
        });
        html += '</div>';
    }

    html += '</div>';
    return html;
}

// Generate HTML for sub unit kerja
function generateSubUnitKerjaHTML(subUnitKerja, index) {
    let html = `
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden animate-fade-in" style="animation-delay: ${(index + 1) * 100}ms">
            <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h4 class="font-semibold">${escapeHtml(subUnitKerja.nama)}</h4>
                        <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium">Sub Unit Kerja</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'sub_unit_kerja', 'id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', subUnitKerja.id)
                           class="p-1.5 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'sub_unit_kerja', 'id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', subUnitKerja.id)
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub Unit Kerja ini?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    `;

    // Add sub-sub unit kerjas
    if (subUnitKerja.sub_sub_unit_kerjas && subUnitKerja.sub_sub_unit_kerjas.length > 0) {
        html += '<div class="p-3 space-y-2">';
        subUnitKerja.sub_sub_unit_kerjas.forEach((subSubUnitKerja, subSubIndex) => {
            html += generateSubSubUnitKerjaHTML(subSubUnitKerja, subSubIndex);
        });
        html += '</div>';
    }

    html += '</div>';
    return html;
}

// Generate HTML for sub-sub unit kerja
function generateSubSubUnitKerjaHTML(subSubUnitKerja, index) {
    return `
        <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg animate-fade-in" style="animation-delay: ${(index + 2) * 100}ms">
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="font-medium text-slate-700">${escapeHtml(subSubUnitKerja.nama)}</span>
                <span class="px-2 py-1 bg-slate-200 text-slate-600 rounded-lg text-xs font-medium">Sub-sub Unit Kerja</span>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('backend.kepegawaian-universitas.unitkerja.edit', ['type' => 'sub_sub_unit_kerja', 'id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', subSubUnitKerja.id)
                   class="p-1 bg-slate-200 rounded hover:bg-slate-300 transition-colors">
                    <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                <form action="{{ route('backend.kepegawaian-universitas.unitkerja.destroy', ['type' => 'sub_sub_unit_kerja', 'id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', subSubUnitKerja.id)
                      method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus Sub-sub Unit Kerja ini?')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1 bg-slate-200 rounded hover:bg-slate-300 transition-colors">
                        <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    `;
}

// Update statistics
function updateStatistics(result) {
    // Update total counts
    const totalUnitKerja = result.data.length;
    const totalSubUnitKerja = result.data.reduce((sum, uk) => sum + (uk.sub_unit_kerjas ? uk.sub_unit_kerjas.length : 0), 0);
    const totalSubSubUnitKerja = result.data.reduce((sum, uk) =>
        sum + (uk.sub_unit_kerjas ? uk.sub_unit_kerjas.reduce((subSum, suk) =>
            subSum + (suk.sub_sub_unit_kerjas ? suk.sub_sub_unit_kerjas.length : 0), 0) : 0), 0);
    const totalAllLevels = totalUnitKerja + totalSubUnitKerja + totalSubSubUnitKerja;

    document.getElementById('totalUnitKerja').textContent = totalUnitKerja;
    document.getElementById('totalSubUnitKerja').textContent = totalSubUnitKerja;
    document.getElementById('totalSubSubUnitKerja').textContent = totalSubSubUnitKerja;
    document.getElementById('totalAllLevels').textContent = totalAllLevels;
}

// Show loading state
function showLoading() {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('unitKerjaDataContainer').classList.add('table-loading');
}

// Hide loading state
function hideLoading() {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('unitKerjaDataContainer').classList.remove('table-loading');
    document.getElementById('unitKerjaDataContainer').classList.add('table-loaded');
}

// Show error message
function showError(message) {
    // You can implement SweetAlert or other notification system here
    alert(message);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
