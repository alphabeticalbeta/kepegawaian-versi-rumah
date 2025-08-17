@extends('backend.layouts.roles.admin-universitas.app')
@section('title', 'Kelola Periode Usulan - Admin Universitas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-blue-50">
    <!-- Header Section -->
    <div class="admin-universitas-bg text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Kelola Periode Usulan</h1>
                <p class="text-indigo-100">Manajemen periode pembukaan dan penutupan usulan kepegawaian</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-indigo-100 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-indigo-200 text-xs">{{ now()->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Action Button -->
    <div class="mb-6">
        <a href="{{ route('admin-universitas.periode-usulan.create') }}"
           class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition-colors duration-200 font-medium flex items-center w-fit">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Periode Baru
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-slate-800">Daftar Periode Usulan</h3>
                    <p class="text-slate-600 mt-1">{{ $periodes->total() }} periode usulan tersedia</p>
                </div>
                <div class="flex gap-2">
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Periode</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tahun Periode</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Pembukaan</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Penutup</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Perbaikan</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total Usulan</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($periodes as $index => $periode)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $periodes->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-slate-900">{{ $periode->nama_periode }}</div>
                                    <div class="text-sm text-slate-500">{{ $periode->jenis_usulan }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $periode->tahun_periode }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                @if($periode->tanggal_mulai_perbaikan && $periode->tanggal_selesai_perbaikan)
                                    <div>
                                        <div>{{ \Carbon\Carbon::parse($periode->tanggal_mulai_perbaikan)->format('d M Y') }}</div>
                                        <div>s.d {{ \Carbon\Carbon::parse($periode->tanggal_selesai_perbaikan)->format('d M Y') }}</div>
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $periode->status === 'Buka' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $periode->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $periode->usulans_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin-universitas.dashboard-usulan.show', $periode) }}"
                                       class="text-blue-600 hover:text-blue-900 px-3 py-1 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('admin-universitas.periode-usulan.edit', $periode) }}"
                                       class="text-indigo-600 hover:text-indigo-900 px-3 py-1 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin-universitas.periode-usulan.destroy', $periode) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900 px-3 py-1 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">Tidak ada periode</h3>
                                    <p class="mt-1 text-sm text-slate-500">Belum ada periode usulan yang dibuat.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('admin-universitas.periode-usulan.create') }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambah Periode Pertama
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($periodes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $periodes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
