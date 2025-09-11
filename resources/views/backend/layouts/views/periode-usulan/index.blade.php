@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Periode Usulan')

@section('content')
@php
    $jenisUsulan = $jenisUsulan ?? null;
@endphp

<!-- FontAwesome CDN untuk icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- CSS Fallback untuk icon -->
<style>
    .fas {
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 14px !important;
        width: 16px !important;
        height: 16px !important;
        display: inline-block !important;
        text-align: center !important;
        line-height: 16px !important;
    }

    /* Fallback untuk icon yang tidak terlihat */
    .fa-toggle-on::before { content: "🔓"; }
    .fa-toggle-off::before { content: "🔒"; }
    .fa-edit::before { content: "✏️"; }
    .fa-trash::before { content: "🗑️"; }
    .fa-plus::before { content: "➕"; }
    .fa-filter::before { content: "🔍"; }
    .fa-times::before { content: "❌"; }
    .fa-list::before { content: "📋"; }
    .fa-calendar-times::before { content: "📅"; }
    .fa-spinner::before { content: "⏳"; }

    /* Padding hanya untuk main content (card), bukan header */
    .bg-white.rounded-lg.shadow-sm.border.border-gray-200 {
        margin-left: 1.5rem !important;
        margin-right: 1.5rem !important;
        margin-top: 0 !important;
        margin-bottom: 1.5rem !important;
    }

    /* Header tidak perlu margin/padding dari sisi */
    .bg-white.border-b.border-gray-200 {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 1.5rem !important;
    }

    /* Memastikan icon terlihat dengan baik */
    .action-button i {
        font-size: 14px !important;
        width: 16px !important;
        height: 16px !important;
        display: inline-block !important;
        text-align: center !important;
        line-height: 16px !important;
    }

    /* Memastikan button aktif dan terlihat */
    .action-button {
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        border: none !important;
        outline: none !important;
    }

    .action-button:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }

    .action-button:disabled {
        cursor: not-allowed !important;
        opacity: 0.5 !important;
    }

    /* Memastikan semua button terlihat dan berfungsi */
    button, a {
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    button:hover, a:hover {
        transform: translateY(-1px) !important;
    }

    button:disabled {
        cursor: not-allowed !important;
        opacity: 0.5 !important;
    }

    /* Debug styling untuk memastikan button terlihat */
    #tambahPeriodeBtn, #tambahPeriodeEmptyBtn {
        position: relative !important;
        z-index: 10 !important;
        pointer-events: auto !important;
    }

    /* Memastikan form delete sejajar dengan button lainnya */
    .action-buttons form {
        display: inline-block !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .action-buttons form button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 0;
        max-width: 400px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.7);
        transition: all 0.3s ease;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-overlay.show .modal-content {
        transform: scale(1);
    }

    .modal-header {
        padding: 1.5rem 1.5rem 0.5rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-body {
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        padding: 0.5rem 1.5rem 1.5rem 1.5rem;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .modal-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .modal-message {
        color: #6b7280;
        line-height: 1.5;
        margin: 0;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        outline: none;
    }

    .btn-secondary {
        background-color: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
        background-color: #e5e7eb;
        border-color: #9ca3af;
    }

    .btn-danger {
        background-color: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
    }

    .btn-success {
        background-color: #059669;
        color: white;
    }

    .btn-success:hover {
        background-color: #047857;
    }

    /* Notification Styles */
    .notification {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 10000;
        max-width: 400px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-left: 4px solid;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification.success {
        border-left-color: #059669;
    }

    .notification.error {
        border-left-color: #dc2626;
    }

    .notification.info {
        border-left-color: #3b82f6;
    }

    .notification-content {
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .notification-icon {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-message {
        flex: 1;
        color: #374151;
        font-size: 0.875rem;
    }

    .notification-close {
        flex-shrink: 0;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: color 0.2s ease;
    }

    .notification-close:hover {
        color: #6b7280;
    }
</style>

<!-- Page Header -->
<div class="bg-white border-b border-gray-200 px-6 py-4 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if($jenisUsulan && $jenisUsulan !== 'all')
                    Histori Periode {{ ucwords(str_replace('-', ' ', $jenisUsulan)) }}
                @else
                    Data Periode Usulan
                @endif
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                @if($jenisUsulan && $jenisUsulan !== 'all')
                    Menampilkan semua periode untuk jenis usulan ini
                @else
                    Kelola semua periode usulan kepegawaian
                @endif
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg text-sm font-medium text-white hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-sm">
                <i class="fas fa-plus" style="font-size: 14px;"></i>
                Tambah Periode
            </a>
        </div>
    </div>
</div>

<!-- Filter Section - Only View -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700 flex items-center gap-2">
                    Jenis Usulan:
                </label>
            <div class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700">
                @if($jenisUsulan == 'all' || !$jenisUsulan)
                    Semua Usulan Aktif
                @elseif($jenisUsulan == 'jabatan-dosen-regular')
                    Usulan Jabatan Dosen Reguler
                @elseif($jenisUsulan == 'jabatan-dosen-pengangkatan')
                    Usulan Jabatan Dosen Pengangkatan Pertama
                @elseif($jenisUsulan == 'nuptk')
                    Usulan NUPTK
                @elseif($jenisUsulan == 'laporan-lkd')
                    Usulan Laporan LKD
                @elseif($jenisUsulan == 'presensi')
                    Usulan Presensi
                @elseif($jenisUsulan == 'id-sinta-sister')
                    Usulan ID SINTA ke SISTER
                @elseif($jenisUsulan == 'satyalancana')
                    Usulan Satyalancana
                @elseif($jenisUsulan == 'tugas-belajar')
                    Usulan Tugas Belajar
                @elseif($jenisUsulan == 'pengaktifan-kembali')
                    Usulan Pengaktifan Kembali
                @elseif($jenisUsulan == 'penyesuaian-masa-kerja')
                    Usulan Penyesuaian Masa Kerja
                @elseif($jenisUsulan == 'ujian-dinas-ijazah')
                    Usulan Ujian Dinas Ijazah
                @elseif($jenisUsulan == 'laporan-serdos')
                    Usulan Laporan SERDOS
                @elseif($jenisUsulan == 'pensiun')
                    Usulan Pensiun
                @elseif($jenisUsulan == 'kepangkatan')
                    Usulan Kepangkatan
                @elseif($jenisUsulan == 'pencantuman-gelar')
                    Usulan Pencantuman Gelar
                @else
                    {{ ucwords(str_replace('-', ' ', $jenisUsulan)) }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">

    <!-- Table Header with Add Button -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">Daftar Periode Usulan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-white">
                <tr>
                    <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider">Jenis Usulan</th>
                    <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider">Nama Periode</th>
                    <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-56">Periode</th>
                    <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Status</th>
                    <th class="px-4 py-3 text-xs font-bold text-black text-center tracking-wider w-40">Aksi</th>
                </tr>
            </thead>
            <tbody id="periodeTableBody" class="bg-white divide-y divide-gray-200">
                @forelse ($periodeUsulans as $index => $periode)
                    <tr class="hover:bg-gray-50 transition-colors" data-jenis="{{ $periode->jenis_usulan }}" data-periode-id="{{ $periode->id }}">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                            {{ ($periodeUsulans->currentPage() - 1) * $periodeUsulans->perPage() + $index + 1 }}
                        </td>

                        <!-- Jenis Usulan -->
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @php
                                $badgeClasses = match($periode->jenis_usulan) {
                                    'Semua Usulan Aktif' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'jabatan-dosen-regular' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                    'jabatan-dosen-pengangkatan' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                                    'usulan-nuptk' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'usulan-laporan-lkd' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'usulan-presensi' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
                                    'usulan-id-sinta-sister' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
                                    'usulan-satyalancana' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                    'usulan-tugas-belajar' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                    'usulan-pengaktifan-kembali' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                                    'usulan-penyesuaian-masa-kerja' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
                                    'usulan-ujian-dinas-ijazah' => 'bg-lime-100 text-lime-800 dark:bg-lime-900 dark:text-lime-200',
                                    'usulan-laporan-serdos' => 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200',
                                    'usulan-pensiun' => 'bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200',
                                    'usulan-kepangkatan' => 'bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200',
                                    'usulan-pencantuman-gelar' => 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900 dark:text-fuchsia-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                @if($periode->jenis_usulan == 'Semua Usulan Aktif')
                                    Semua Usulan Aktif
                                @elseif($periode->jenis_usulan == 'jabatan-dosen-regular')
                                    Usulan Jabatan Dosen Reguler
                                @elseif($periode->jenis_usulan == 'jabatan-dosen-pengangkatan')
                                    Usulan Jabatan Dosen Pengangkatan Pertama
                                @elseif($periode->jenis_usulan == 'usulan-nuptk')
                                    Usulan NUPTK
                                @elseif($periode->jenis_usulan == 'usulan-laporan-lkd')
                                    Usulan Laporan LKD
                                @elseif($periode->jenis_usulan == 'usulan-presensi')
                                    Usulan Presensi
                                @elseif($periode->jenis_usulan == 'usulan-id-sinta-sister')
                                    Usulan ID SINTA ke SISTER
                                @elseif($periode->jenis_usulan == 'usulan-satyalancana')
                                    Usulan Satyalancana
                                @elseif($periode->jenis_usulan == 'usulan-tugas-belajar')
                                    Usulan Tugas Belajar
                                @elseif($periode->jenis_usulan == 'usulan-pengaktifan-kembali')
                                    Usulan Pengaktifan Kembali
                                @elseif($periode->jenis_usulan == 'usulan-penyesuaian-masa-kerja')
                                    Usulan Penyesuaian Masa Kerja
                                @elseif($periode->jenis_usulan == 'usulan-ujian-dinas-ijazah')
                                    Usulan Ujian Dinas Ijazah
                                @elseif($periode->jenis_usulan == 'usulan-laporan-serdos')
                                    Usulan Laporan SERDOS
                                @elseif($periode->jenis_usulan == 'usulan-pensiun')
                                    Usulan Pensiun
                                @elseif($periode->jenis_usulan == 'usulan-kepangkatan')
                                    Usulan Kepangkatan
                                @elseif($periode->jenis_usulan == 'usulan-pencantuman-gelar')
                                    Usulan Pencantuman Gelar
                                @else
                                    {{ ucwords(str_replace('-', ' ', $periode->jenis_usulan)) }}
                                @endif
                            </span>
                        </td>

                        <!-- Nama Periode -->
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                            <div class="font-medium">{{ $periode->nama_periode }} ({{ $periode->tahun_periode }})</div>
                        </td>

                        <!-- Periode -->
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                            <div class="font-medium">{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->isoFormat('D MMM YYYY') }} - {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->isoFormat('D MMM YYYY') }}</div>
                            </td>

                        <!-- Status -->
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($periode->status == 'Buka')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                        Buka
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                        Tutup
                                    </span>
                                @endif
                            </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.edit', $periode->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                   title="Edit Data">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <button onclick="deletePeriode({{ $periode->id }})"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                        title="Hapus Data">
                                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus
                                    </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada periode usulan</h3>
                                <p class="text-gray-500 mb-4">Mulai dengan membuat periode usulan baru untuk jenis {{ $jenisUsulan ?? 'jabatan-dosen-regular' }}.</p>
                                <a href="{{ route('backend.kepegawaian-universitas.periode-usulan.create', ['jenis' => $jenisUsulan ?? 'jabatan-dosen-regular']) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Periode
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    @if($periodeUsulans->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if ($periodeUsulans->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $periodeUsulans->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if ($periodeUsulans->hasMorePages())
                        <a href="{{ $periodeUsulans->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $periodeUsulans->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $periodeUsulans->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $periodeUsulans->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                    {{ $periodeUsulans->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript untuk delete functionality -->
<script>
function deletePeriode(periodeId) {
    if (confirm('Apakah Anda yakin ingin menghapus periode ini?')) {
        fetch(`/kepegawaian-universitas/periode-usulan/${periodeId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove row from table
                const row = document.querySelector(`tr[data-periode-id="${periodeId}"]`);
                if (row) {
                    row.remove();
                }

                // Show success message
                showNotification('Periode berhasil dihapus', 'success');
            } else {
                showNotification(data.message || 'Gagal menghapus periode', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menghapus periode', 'error');
        });
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type} show`;
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                ${type === 'success' ? '✅' : '❌'}
            </div>
            <div class="notification-message">${message}</div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
    }, 5000);
}
</script>
@endsection
