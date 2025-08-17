@extends('backend.layouts.roles.admin-univ-usulan.app')
@section('title', 'Dashboard Periode: ' . $periode->nama_periode . ' - Admin Universitas Usulan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-4">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Dashboard: {{ $periode->nama_periode }}</h1>
                <p class="text-blue-100">{{ $periode->jenis_usulan }} • Tahun {{ $periode->tahun_periode }}</p>
                <div class="flex items-center mt-2 space-x-4 text-blue-200 text-sm">
                    <span>📅 {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $periode->status === 'Buka' ? 'bg-green-500/20 text-green-100' : 'bg-red-500/20 text-red-100' }}">
                        {{ $periode->status }}
                    </span>
                </div>
            </div>
            <div class="hidden md:block">
                <a href="{{ route('backend.admin-univ-usulan.dashboard-periode.index', ['jenis' => request()->get('jenis', 'jabatan')]) }}"
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Total Usulan</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_usulan'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Disetujui</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['usulan_disetujui'] }}</p>
                    @if($stats['total_usulan'] > 0)
                        <p class="text-xs text-slate-500">{{ number_format(($stats['usulan_disetujui'] / $stats['total_usulan']) * 100, 1) }}%</p>
                    @endif
                </div>
                <div class="p-3 bg-green-100 rounded-xl">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Ditolak</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['usulan_ditolak'] }}</p>
                    @if($stats['total_usulan'] > 0)
                        <p class="text-xs text-slate-500">{{ number_format(($stats['usulan_ditolak'] / $stats['total_usulan']) * 100, 1) }}%</p>
                    @endif
                </div>
                <div class="p-3 bg-red-100 rounded-xl">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-white/30">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['usulan_pending'] }}</p>
                    @if($stats['total_usulan'] > 0)
                        <p class="text-xs text-slate-500">{{ number_format(($stats['usulan_pending'] / $stats['total_usulan']) * 100, 1) }}%</p>
                    @endif
                </div>
                <div class="p-3 bg-yellow-100 rounded-xl">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Status Distribution Chart -->
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Distribusi Status Usulan</h3>
            <div class="space-y-4">
                @foreach($usulanByStatus as $status => $count)
                    @php
                        $percentage = $stats['total_usulan'] > 0 ? ($count / $stats['total_usulan']) * 100 : 0;
                        $colorClass = match($status) {
                            'Disetujui' => 'bg-green-500',
                            'Ditolak' => 'bg-red-500',
                            'Menunggu Verifikasi' => 'bg-yellow-500',
                            'Dalam Proses' => 'bg-blue-500',
                            default => 'bg-gray-500'
                        };
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">{{ $status }}</span>
                            <span class="font-semibold">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Employee Type Distribution -->
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Usulan per Jenis Pegawai</h3>
            <div class="space-y-4">
                @forelse($usulanByJenisPegawai as $jenis => $count)
                    @php
                        $percentage = $stats['total_usulan'] > 0 ? ($count / $stats['total_usulan']) * 100 : 0;
                        $colorClass = match($jenis) {
                            'Dosen' => 'bg-indigo-500',
                            'Tenaga Kependidikan' => 'bg-purple-500',
                            default => 'bg-gray-500'
                        };
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">{{ $jenis }}</span>
                            <span class="font-semibold">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center">Belum ada data usulan</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Timeline Chart -->
    @if($timelineData->count() > 0)
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-6 mb-8">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Timeline Usulan per Bulan</h3>
            <div class="flex items-end space-x-2 h-40">
                @foreach($timelineData as $data)
                    @php
                        $maxCount = $timelineData->max('count');
                        $height = $maxCount > 0 ? ($data['count'] / $maxCount) * 100 : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-indigo-500 rounded-t hover:bg-indigo-600 transition-colors relative group"
                             style="height: {{ $height }}%">
                            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $data['count'] }} usulan
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 mt-2 text-center">{{ $data['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-4 mb-6">
        <a href="{{ route('backend.admin-univ-usulan.periode-usulan.edit', $periode) }}"
           class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition-colors duration-200 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Periode
        </a>

        <button class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-colors duration-200 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Data
        </button>

        <form action="{{ route('backend.admin-univ-usulan.usulan.toggle-periode') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
            <button type="submit"
                    class="px-6 py-3 rounded-xl font-medium flex items-center transition-colors duration-200
                    {{ $periode->status === 'Buka' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($periode->status === 'Buka')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    @endif
                </svg>
                {{ $periode->status === 'Buka' ? 'Tutup Periode' : 'Buka Periode' }}
            </button>
        </form>
    </div>

    <!-- Recent Usulans -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Usulan Terbaru</h3>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    Lihat Semua
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Usulan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($recentUsulans as $usulan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-slate-900">{{ $usulan->pegawai->nama_lengkap }}</div>
                                    <div class="text-sm text-slate-500">NIP: {{ $usulan->pegawai->nip }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->pegawai->jenis_pegawai }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $usulan->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $usulan->status_usulan === 'Disetujui' ? 'bg-green-100 text-green-800' :
                                       ($usulan->status_usulan === 'Ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $usulan->status_usulan }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                Belum ada usulan untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

