@extends('backend.layouts.roles.admin-universitas.app')
@section('title', 'Dashboard Usulan - Admin Universitas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-blue-50">
    <!-- Header Section -->
    <div class="admin-universitas-bg text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Dashboard Usulan per Periode</h1>
                <p class="text-indigo-100">Statistik dan ringkasan usulan untuk setiap periode</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-indigo-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-indigo-200 text-xs">{{ now()->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Total Periode</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $overallStats['total_periodes'] }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stats-card rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Periode Aktif</p>
                    <p class="text-3xl font-bold text-green-600">{{ $overallStats['periodes_aktif'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stats-card rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Total Usulan</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $overallStats['total_usulan'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stats-card rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Usulan Bulan Ini</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $overallStats['usulan_bulan_ini'] }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-slate-800">Akses Cepat</h2>
            <a href="{{ route('admin-universitas.periode-usulan.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Periode
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin-universitas.periode-usulan.index') }}"
               class="bg-white/90 backdrop-blur-xl p-4 rounded-xl shadow-lg border border-white/30 hover:shadow-xl transition-all duration-300 flex items-center">
                <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Kelola Periode</h3>
                    <p class="text-sm text-slate-600">Edit, hapus, dan atur periode</p>
                </div>
            </a>

            <a href="#"
               class="bg-white/90 backdrop-blur-xl p-4 rounded-xl shadow-lg border border-white/30 hover:shadow-xl transition-all duration-300 flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Laporan Usulan</h3>
                    <p class="text-sm text-slate-600">Export dan analisis data</p>
                </div>
            </a>

            <a href="#"
               class="bg-white/90 backdrop-blur-xl p-4 rounded-xl shadow-lg border border-white/30 hover:shadow-xl transition-all duration-300 flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Pengaturan</h3>
                    <p class="text-sm text-slate-600">Konfigurasi sistem</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Periods List -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-xl font-semibold text-slate-800">Dashboard Periode Usulan</h3>
            <p class="text-slate-600 mt-1">Klik pada periode untuk melihat detail dashboard</p>
        </div>

        <div class="p-6">
            @if($periodes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($periodes as $periode)
                        <div class="periode-card bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-white/50 shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-slate-800 text-lg">{{ $periode->nama_periode }}</h4>
                                    <p class="text-sm text-slate-600">{{ $periode->jenis_usulan }} • {{ $periode->tahun_periode }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $periode->status === 'Buka' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $periode->status }}
                                </span>
                            </div>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600">Total Usulan:</span>
                                    <span class="font-semibold text-slate-800">{{ $periode->usulans_count }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600">Disetujui:</span>
                                    <span class="font-semibold text-green-600">{{ $periode->usulan_disetujui_count }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600">Ditolak:</span>
                                    <span class="font-semibold text-red-600">{{ $periode->usulan_ditolak_count }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600">Pending:</span>
                                    <span class="font-semibold text-yellow-600">{{ $periode->usulan_pending_count }}</span>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs text-slate-500 mb-4">
                                <div class="flex justify-between">
                                    <span>Periode:</span>
                                    <span>{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}</span>
                                </div>
                                @if($periode->tanggal_mulai_perbaikan && $periode->tanggal_selesai_perbaikan)
                                    <div class="flex justify-between">
                                        <span>Perbaikan:</span>
                                        <span>{{ \Carbon\Carbon::parse($periode->tanggal_mulai_perbaikan)->format('d M') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai_perbaikan)->format('d M Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('admin-universitas.dashboard-usulan.show', $periode) }}"
                               class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors text-center block text-sm font-medium">
                                Lihat Dashboard
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada periode</h3>
                    <p class="mt-1 text-sm text-slate-500">Mulai dengan membuat periode usulan pertama.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin-universitas.periode-usulan.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Periode
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
