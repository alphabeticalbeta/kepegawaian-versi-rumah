@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Berita & Pengumuman')

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

    /* Rich Text Editor Styles - Minimal custom CSS for editor functionality */
    #editor {
        min-height: 200px;
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        color: #374151;
    }

    #editor:focus {
        outline: none;
    }

    #editor p {
        margin-bottom: 1rem;
    }

    #editor ul, #editor ol {
        margin: 1rem 0;
        padding-left: 2rem;
    }

    #editor ul {
        list-style-type: disc;
    }

    #editor ol {
        list-style-type: decimal;
    }

    #editor li {
        margin-bottom: 0.5rem;
    }

    #editor strong {
        font-weight: 600;
    }

    #editor em {
        font-style: italic;
    }

    #editor u {
        text-decoration: underline;
    }

    #editor h1, #editor h2, #editor h3 {
        font-weight: 600;
        margin: 1.5rem 0 1rem 0;
    }

    #editor h1 {
        font-size: 1.875rem;
    }

    #editor h2 {
        font-size: 1.5rem;
    }

    #editor h3 {
        font-size: 1.25rem;
    }

    #editor blockquote {
        border-left: 4px solid #E5E7EB;
        padding-left: 1rem;
        margin: 1rem 0;
        font-style: italic;
        color: #6B7280;
    }

    #editor::-webkit-scrollbar {
        width: 6px;
    }

    #editor::-webkit-scrollbar-track {
        background: #F3F4F6;
        border-radius: 3px;
    }

    #editor::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 3px;
    }

    #editor::-webkit-scrollbar-thumb:hover {
        background: #9CA3AF;
    }

    /* Hide pagination by default */
    #paginationContainer {
        display: none;
    }

    /* Show pagination when needed */
    #paginationContainer.show {
        display: block;
    }

    /* Table transition animations */
    #informasiTableBody {
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

    /* Modern Notification Styles */
    #notificationContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .notification {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        margin-bottom: 12px;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid;
        overflow: hidden;
    }

    .notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification.success {
        border-left-color: #10b981;
    }

    .notification.error {
        border-left-color: #ef4444;
    }

    .notification-content {
        display: flex;
        align-items: center;
        padding: 16px;
        gap: 12px;
    }

    .notification-icon {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
    }

    .notification.success .notification-icon {
        color: #10b981;
    }

    .notification.error .notification-icon {
        color: #ef4444;
    }

    .notification-text {
        flex: 1;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        line-height: 1.4;
    }

    .notification-close {
        flex-shrink: 0;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .notification-close:hover {
        color: #6b7280;
        background: #f3f4f6;
    }
</style>
@endpush

@section('content')
<!-- Notification Container -->
<div id="notificationContainer"></div>

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
                    Berita & Pengumuman
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola berita dan pengumuman Universitas Mulawarman
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis</label>
                        <select id="filterJenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="berita">Berita</option>
                            <option value="pengumuman">Pengumuman</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Featured/Pinned</label>
                        <select id="filterSpecial" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="featured">Featured</option>
                            <option value="pinned">Pinned</option>
                            <option value="both">Featured & Pinned</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Cari judul/konten..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header with Add Button -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Berita & Pengumuman</h3>
                    <button onclick="openModal()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                        <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                        Tambah Informasi
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-16 text-center">No</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-24 text-center">Jenis</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider text-center">Judul</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-32 text-center">Nomor Surat</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-24 text-center">Status</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-32 text-center">Featured/Pinned</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-28 text-center">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-24 text-center">Dokumen</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($informasi as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $informasi->firstItem() + $index }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item->jenis === 'berita' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->jenis === 'berita' ? 'Berita' : 'Pengumuman' }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 text-sm text-gray-900 max-w-xs">
                                    <div class="font-medium text-gray-900 truncate" title="{{ $item->judul }}">
                                        {{ $item->judul }}
                                    </div>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $item->nomor_surat ?? '-' }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : ($item->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $item->status === 'published' ? 'Published' : ($item->status === 'draft' ? 'Draft' : 'Archived') }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    <div class="flex flex-col space-y-1">
                                        @if($item->is_featured)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Featured</span>
                                        @endif
                                        @if($item->is_pinned)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Pinned</span>
                                        @endif
                                        @if(!$item->is_featured && !$item->is_pinned)
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    @if($item->lampiran && count($item->lampiran) > 0)
                                        @php
                                            $firstFile = is_array($item->lampiran[0]) ? $item->lampiran[0]['path'] : $item->lampiran[0];
                                        @endphp
                                        <a href="{{ route('admin-universitas.informasi.download', ['id' => $item->id, 'filename' => $firstFile]) }}" target="_blank"
                                           class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                           title="Lihat Lampiran">
                                            <i data-lucide="eye" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                            <span class="hidden sm:inline">Lihat</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="editInformasi({{ $item->id }})" class="edit-btn inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg" data-id="{{ $item->id }}" style="position: relative; z-index: 10;"
                                                title="Edit"
                                                data-judul="{{ htmlspecialchars($item->judul, ENT_QUOTES, 'UTF-8') }}"
                                                data-konten="{{ htmlspecialchars($item->konten, ENT_QUOTES, 'UTF-8') }}"
                                                data-jenis="{{ htmlspecialchars($item->jenis, ENT_QUOTES, 'UTF-8') }}"
                                                data-nomor-surat="{{ htmlspecialchars($item->nomor_surat, ENT_QUOTES, 'UTF-8') }}"
                                                data-tanggal-surat="{{ $item->tanggal_surat }}"
                                                data-penulis="{{ htmlspecialchars($item->penulis, ENT_QUOTES, 'UTF-8') }}"
                                                data-status="{{ htmlspecialchars($item->status, ENT_QUOTES, 'UTF-8') }}"
                                                data-tanggal-publish="{{ $item->tanggal_publish }}"
                                                data-tanggal-berakhir="{{ $item->tanggal_berakhir }}"
                                                data-is-featured="{{ $item->is_featured ? 'true' : 'false' }}"
                                                data-is-pinned="{{ $item->is_pinned ? 'true' : 'false' }}"
                                                data-thumbnail="{{ htmlspecialchars($item->thumbnail, ENT_QUOTES, 'UTF-8') }}"
                                                data-lampiran="{{ base64_encode(json_encode($item->lampiran)) }}">
                                            <i data-lucide="edit" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                            <span class="hidden sm:inline">Edit</span>
                                        </button>
                                        <button onclick="deleteInformasi({{ $item->id }})" class="delete-btn inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg" data-id="{{ $item->id }}" style="position: relative; z-index: 10;"
                                                title="Hapus">
                                            <i data-lucide="trash-2" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                            <span class="hidden sm:inline">Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="file-text" class="h-10 w-10 text-gray-300 mb-3"></i>
                                        <p class="text-base font-medium">Belum ada data</p>
                                        <p class="text-sm">Data berita dan pengumuman akan ditampilkan di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($informasi->hasPages())
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mt-6">
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-700">
                        <span>Menampilkan {{ $informasi->firstItem() ?? 0 }} - {{ $informasi->lastItem() ?? 0 }} dari {{ $informasi->total() }} data</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($informasi->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                            <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            </span>
                        @else
                            <a href="{{ $informasi->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            </a>
                        @endif

                        <div class="flex space-x-1">
                            @foreach($informasi->getUrlRange(1, $informasi->lastPage()) as $page => $url)
                                @if($page == $informasi->currentPage())
                                    <span class="px-3 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg shadow-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-200">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>

                        @if($informasi->hasMorePages())
                            <a href="{{ $informasi->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal -->
<div id="informasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-2xl bg-white transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Informasi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="informasiForm" class="mt-6" enctype="multipart/form-data" action="{{ route('admin-universitas.informasi.store') }}" method="POST">
                <input type="hidden" id="editId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Jenis -->
                    <div>
                        <label for="jenis" class="block text-sm font-semibold text-gray-700 mb-3">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis" name="jenis" required onchange="togglePengumumanFields()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Pilih Jenis</option>
                            <option value="berita">Berita</option>
                            <option value="pengumuman">Pengumuman</option>
                        </select>
                    </div>

                    <!-- Fields khusus untuk Pengumuman -->
                    <div id="pengumumanFields" class="hidden grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Surat -->
                        <div>
                            <label for="nomor_surat" class="block text-sm font-semibold text-gray-700 mb-3">
                                Nomor Surat <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" id="nomor_surat" name="nomor_surat"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                       placeholder="001/UNMUL/KEU/2024">
                                <button type="button" onclick="generateNomorSurat()"
                                        class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Format: [No]/[Instansi]/[Unit]/[Tahun]
                            </p>
                        </div>

                        <!-- Tanggal Surat -->
                        <div>
                            <label for="tanggal_surat" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Surat <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_surat" name="tanggal_surat"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                    </div>

                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-3">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                               placeholder="Masukkan judul berita/pengumuman">
                    </div>

                    <!-- Konten -->
                    <div>
                        <label for="konten" class="block text-sm font-semibold text-gray-700 mb-3">
                            Konten <span class="text-red-500">*</span>
                        </label>
                        <!-- Rich Text Editor Toolbar -->
                        <div class="border border-gray-300 rounded-t-lg bg-gray-50 p-2">
                            <div class="flex flex-wrap gap-1">
                                <button type="button" class="ql-bold p-2 hover:bg-gray-200 rounded" title="Bold">
                                    <i data-lucide="bold" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-italic p-2 hover:bg-gray-200 rounded" title="Italic">
                                    <i data-lucide="italic" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-underline p-2 hover:bg-gray-200 rounded" title="Underline">
                                    <i data-lucide="underline" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-list p-2 hover:bg-gray-200 rounded" value="ordered" title="Numbered List">
                                    <i data-lucide="list-ordered" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-list p-2 hover:bg-gray-200 rounded" value="bullet" title="Bullet List">
                                    <i data-lucide="list" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="" title="Align Left">
                                    <i data-lucide="align-left" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="center" title="Align Center">
                                    <i data-lucide="align-center" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="right" title="Align Right">
                                    <i data-lucide="align-right" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-indent p-2 hover:bg-gray-200 rounded" value="-1" title="Decrease Indent">
                                    <i data-lucide="indent-decrease" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-indent p-2 hover:bg-gray-200 rounded" value="+1" title="Increase Indent">
                                    <i data-lucide="indent-increase" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                        <div id="editor" contenteditable="true" class="w-full px-4 py-3 border border-gray-300 border-t-0 rounded-b-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                             placeholder="Masukkan konten berita/pengumuman..."></div>
                        <input type="hidden" id="konten" name="konten">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <!-- Penulis dan Tags -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="penulis" class="block text-sm font-semibold text-gray-700 mb-3">
                                Penulis <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="penulis" name="penulis" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400 disabled:bg-gray-100 disabled:text-gray-600 disabled:cursor-not-allowed"
                                   placeholder="Nama penulis">
                        </div>
                        <div>
                            <label for="tags" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tags
                            </label>
                            <input type="text" id="tags" name="tags"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                   placeholder="kepegawaian, pendidikan, umum (pisahkan dengan koma)">
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-3">
                            Thumbnail
                        </label>
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        <p class="text-xs text-gray-500 mt-1">
                            Format: JPEG, PNG, JPG, GIF. Max: 2MB
                        </p>
                        <!-- Current Thumbnail Display -->
                        <div id="currentThumbnail" class="mt-3 hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">File saat ini:</p>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border">
                                <div class="flex-shrink-0">
                                    <img id="thumbnailPreview" src="" alt="Thumbnail" class="h-12 w-12 object-cover rounded">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p id="thumbnailName" class="text-sm font-medium text-gray-900 truncate"></p>
                                    <p class="text-xs text-gray-500">Klik untuk download</p>
                                </div>
                                <a id="thumbnailDownload" href="#" target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i data-lucide="download" class="h-3 w-3 mr-1"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran -->
                    <div>
                        <label for="lampiran" class="block text-sm font-semibold text-gray-700 mb-3">
                            Lampiran <span id="lampiranRequired" class="text-red-500">*</span>
                        </label>
                        <input type="file" id="lampiran" name="lampiran[]" multiple accept=".pdf"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        <p class="text-xs text-gray-500 mt-1">
                            Format: PDF saja. Max: 10MB per file
                            <span id="lampiranNote" class="text-blue-600 font-medium">(Wajib untuk Pengumuman)</span>
                        </p>
                        <!-- Current Attachments Display -->
                        <div id="currentAttachments" class="mt-3 hidden">
                            <p class="text-sm font-medium text-gray-700 mb-2">File saat ini:</p>
                            <div id="attachmentsList" class="space-y-2">
                                <!-- Attachments will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Publish dan Berakhir -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tanggal_publish" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Publish
                            </label>
                            <input type="datetime-local" id="tanggal_publish" name="tanggal_publish"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                        <div>
                            <label for="tanggal_berakhir" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Berakhir
                            </label>
                            <input type="datetime-local" id="tanggal_berakhir" name="tanggal_berakhir"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                Featured (Tampil di halaman utama)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_pinned" class="ml-2 block text-sm text-gray-700">
                                Pinned (Selalu tampil di atas)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <span id="submitText">Simpan</span>
                        <div id="submitLoader" class="hidden ml-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
let currentEditId = null;
let editor = null;

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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    initializeRichTextEditor();
    initializeSearchAndFilter();
    lucide.createIcons();
});

// Initialize form
function initializeForm() {
    const form = document.getElementById('informasiForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Event listeners are now handled via onclick attributes
}

// Initialize search and filter with auto-submit
function initializeSearchAndFilter() {
    const searchInput = document.getElementById('searchInput');
    const filterJenis = document.getElementById('filterJenis');
    const filterStatus = document.getElementById('filterStatus');
    const filterSpecial = document.getElementById('filterSpecial');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            document.getElementById('filterForm').submit();
        }, 500));
    }

    if (filterJenis) {
        filterJenis.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }

    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }

    if (filterSpecial) {
        filterSpecial.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
}

// Initialize rich text editor
function initializeRichTextEditor() {
    const editorElement = document.getElementById('editor');
    if (editorElement) {
        // Initialize toolbar buttons
        const toolbarButtons = document.querySelectorAll('.ql-bold, .ql-italic, .ql-underline, .ql-list, .ql-align, .ql-indent');

        toolbarButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const command = this.classList[0].replace('ql-', '');
                const value = this.getAttribute('value') || null;

                if (command === 'list') {
                    document.execCommand('insertOrderedList', false, null);
                } else if (command === 'align') {
                    document.execCommand('justify' + (value || 'Left'), false, null);
                } else if (command === 'indent') {
                    if (value === '+1') {
                        document.execCommand('indent', false, null);
                    } else {
                        document.execCommand('outdent', false, null);
                    }
                } else {
                    document.execCommand(command, false, null);
                }

                // Update hidden input
                updateHiddenContent();
            });
        });

        // Update hidden input when content changes
        editorElement.addEventListener('input', updateHiddenContent);
        editorElement.addEventListener('paste', function(e) {
            setTimeout(updateHiddenContent, 100);
        });
    }
}

// Update hidden content input
function updateHiddenContent() {
    const editor = document.getElementById('editor');
    const hiddenInput = document.getElementById('konten');
    if (editor && hiddenInput) {
        hiddenInput.value = editor.innerHTML;
    }
}

// Toggle pengumuman fields
function togglePengumumanFields(isEdit = false) {
    const jenis = document.getElementById('jenis').value;
    const pengumumanFields = document.getElementById('pengumumanFields');
    const nomorSurat = document.getElementById('nomor_surat');
    const tanggalSurat = document.getElementById('tanggal_surat');
    const penulisField = document.getElementById('penulis');
    const penulisLabel = document.querySelector('label[for="penulis"]');
    const lampiranField = document.getElementById('lampiran');
    const lampiranRequired = document.getElementById('lampiranRequired');
    const lampiranNote = document.getElementById('lampiranNote');

    if (jenis === 'pengumuman') {
        // Show pengumuman fields
        pengumumanFields.classList.remove('hidden');
        nomorSurat.required = true;
        tanggalSurat.required = true;

        // Lampiran wajib untuk pengumuman baru, tapi tidak wajib untuk edit jika sudah ada lampiran
        if (isEdit) {
            // Saat edit, cek apakah sudah ada lampiran
            const currentAttachments = document.getElementById('currentAttachments');
            const hasExistingAttachments = currentAttachments && !currentAttachments.classList.contains('hidden');

            if (hasExistingAttachments) {
                // Jika sudah ada lampiran, tidak wajib upload baru
                lampiranField.required = false;
                lampiranRequired.style.display = 'none';
                lampiranNote.textContent = '(Opsional - gunakan file yang sudah ada atau upload file baru)';
                lampiranNote.className = 'text-blue-600 font-medium';
            } else {
                // Jika belum ada lampiran, tetap wajib
                lampiranField.required = true;
                lampiranRequired.style.display = 'inline';
                lampiranNote.textContent = '(Wajib untuk Pengumuman)';
                lampiranNote.className = 'text-red-600 font-medium';
            }
        } else {
            // Untuk pengumuman baru, lampiran wajib
            lampiranField.required = true;
            lampiranRequired.style.display = 'inline';
            lampiranNote.textContent = '(Wajib untuk Pengumuman)';
            lampiranNote.className = 'text-red-600 font-medium';
        }

        // Disable penulis field and set to current user
        penulisField.disabled = true;
        @if(auth()->guard('pegawai')->check())
            penulisField.value = '{{ auth()->guard("pegawai")->user()->nama_lengkap ?? auth()->guard("pegawai")->user()->name ?? "Pegawai" }}';
        @else
            penulisField.value = '{{ auth()->user()->name ?? "Administrator" }}';
        @endif
        penulisField.classList.add('bg-gray-100', 'text-gray-600');
        penulisLabel.innerHTML = 'Penulis <span class="text-gray-500">(Otomatis dari pegawai login)</span>';
    } else {
        // Hide pengumuman fields
        pengumumanFields.classList.add('hidden');
        nomorSurat.required = false;
        tanggalSurat.required = false;
        nomorSurat.value = '';
        tanggalSurat.value = '';

        // Lampiran tidak wajib untuk berita
        lampiranField.required = false;
        lampiranRequired.style.display = 'none';
        lampiranNote.textContent = '(Opsional untuk Berita)';
        lampiranNote.className = 'text-blue-600 font-medium';

        // Enable penulis field for berita
        penulisField.disabled = false;
        penulisField.value = '';
        penulisField.classList.remove('bg-gray-100', 'text-gray-600');
        penulisLabel.innerHTML = 'Penulis <span class="text-red-500">*</span>';
    }
}

// Generate nomor surat - now uses form submission
function generateNomorSurat() {
    // This will be handled by the form submission to the controller
    // The controller will generate the nomor surat and redirect back with the value
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin-universitas.informasi.generate-nomor-surat") }}';

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const unitInput = document.createElement('input');
    unitInput.type = 'hidden';
    unitInput.name = 'unit';
    unitInput.value = 'KEU';

    const tahunInput = document.createElement('input');
    tahunInput.type = 'hidden';
    tahunInput.name = 'tahun';
    tahunInput.value = new Date().getFullYear();

    form.appendChild(csrfToken);
    form.appendChild(unitInput);
    form.appendChild(tahunInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Data loading is now handled by server-side rendering
// No need for AJAX data loading functions

// Data display is now handled by server-side rendering
// No need for client-side data display functions


// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Old openModal function removed - using new one below

// Close modal
function closeModal() {
    const modal = document.getElementById('informasiModal');
    const modalContent = document.getElementById('modalContent');

    // Trigger close animation
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentEditId = null;
        resetForm();
    }, 300);
}

function resetForm() {
    const form = document.getElementById('informasiForm');
    if (form) {
        form.reset();
        form.action = '{{ route("admin-universitas.informasi.store") }}';
        form.method = 'POST';
    }

    document.getElementById('editId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Informasi';
    document.getElementById('submitText').textContent = 'Simpan';
    document.getElementById('formMethod').value = 'POST';

    // Clear rich text editor
    if (document.getElementById('editor')) {
        document.getElementById('editor').innerHTML = '';
    }

    // Hide current file displays
    document.getElementById('currentThumbnail').classList.add('hidden');
    document.getElementById('currentAttachments').classList.add('hidden');

    // Reset pengumuman fields
    const nomorSuratField = document.getElementById('nomor_surat');
    const tanggalSuratField = document.getElementById('tanggal_surat');
    if (nomorSuratField) nomorSuratField.value = '';
    if (tanggalSuratField) tanggalSuratField.value = '';

    // Reset toggle fields
    togglePengumumanFields();
}

// Old displayCurrentFiles function removed - using new one below

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Old editInformasi function removed - using new one below

// Delete informasi with modern confirmation modal
function deleteInformasi(id) {
    // Create confirmation modal
    const modal = document.createElement('div');
    modal.id = 'confirmationModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="confirmationModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500 text-center mb-6">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex space-x-3">
                    <button type="button" id="cancelDelete" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="button" id="confirmDelete" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Animate modal in
    setTimeout(() => {
        const modalContent = document.getElementById('confirmationModalContent');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);

    // Handle cancel
    document.getElementById('cancelDelete').addEventListener('click', () => {
        closeConfirmationModal();
    });

    // Handle confirm
    document.getElementById('confirmDelete').addEventListener('click', (e) => {
        // Add loading animation to button
        const confirmButton = e.target;
        const originalText = confirmButton.textContent;
        confirmButton.disabled = true;
        confirmButton.classList.add('opacity-75', 'cursor-not-allowed', 'animate-pulse');
        confirmButton.innerHTML = '<i class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></i>Menghapus...';

        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin-universitas.informasi.destroy', ':id') }}`.replace(':id', id);

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        closeConfirmationModal();
    });

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeConfirmationModal();
        }
    });
}

function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    if (modal) {
        const modalContent = document.getElementById('confirmationModalContent');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            document.body.removeChild(modal);
        }, 300);
    }
}

// Handle form submission - now uses server-side rendering
function handleFormSubmit(event) {
    // Update hidden content before submit
    updateHiddenContent();

    // Show loading state with animation
    const submitButton = event.target.querySelector('button[type="submit"]');
    const submitText = submitButton.querySelector('#submitText');
    const submitLoader = submitButton.querySelector('#submitLoader');

    if (submitButton && submitText && submitLoader) {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-75', 'cursor-not-allowed');
        submitText.textContent = 'Menyimpan...';
        submitLoader.classList.remove('hidden');

        // Add pulse animation
        submitButton.classList.add('animate-pulse');
    }

    // Let the form submit naturally (server-side)
    // The form will redirect back with success/error messages
}

// Show submit loading
function showSubmitLoading(show) {
    const text = document.getElementById('submitText');
    const loader = document.getElementById('submitLoader');

    if (show) {
        text.textContent = 'Menyimpan...';
        loader.classList.remove('hidden');
    } else {
        text.textContent = 'Simpan';
        loader.classList.add('hidden');
    }
}

// Show success message
function showInformasiSuccess(message) {
    Swal.fire({
        title: 'Berhasil!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'bg-gray-800 text-white',
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-green-600 hover:bg-green-700 text-white'
        }
    });
}

// Show error message
function showInformasiError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'bg-gray-800 text-white',
            title: 'text-white',
            content: 'text-gray-300',
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white'
        }
    });
}

// Loading animation functions
function showTableLoading() {
    const tbody = document.getElementById('informasiTableBody');
    if (tbody) {
        tbody.classList.add('table-loading');
        tbody.classList.remove('table-loaded');
    }
}

function hideTableLoading() {
    const tbody = document.getElementById('informasiTableBody');
    if (tbody) {
        tbody.classList.remove('table-loading');
        tbody.classList.add('table-loaded');
    }
}

// Pagination is now handled by server-side rendering
// No need for client-side pagination functions

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

// Open modal function
function openModal(isEdit = false) {
    console.log('openModal called with isEdit:', isEdit);

    const modal = document.getElementById('informasiModal');
    const modalContent = document.getElementById('modalContent');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('informasiForm');
    const formMethod = document.getElementById('formMethod');

    console.log('Modal elements found:', {
        modal: !!modal,
        modalContent: !!modalContent,
        modalTitle: !!modalTitle,
        form: !!form,
        formMethod: !!formMethod
    });

    if (modal && modalContent && modalTitle && form && formMethod) {
        if (isEdit) {
            modalTitle.textContent = 'Edit Informasi';
            formMethod.value = 'PUT';
        } else {
            modalTitle.textContent = 'Tambah Informasi';
            formMethod.value = 'POST';
            form.action = '{{ route("admin-universitas.informasi.store") }}';
            form.method = 'POST';
            form.reset();

            // Reset editor content
            const editorElement = document.getElementById('editor');
            const kontenInput = document.getElementById('konten');
            if (editorElement) editorElement.innerHTML = '';
            if (kontenInput) kontenInput.value = '';

            // Hide current files for new entry
            hideCurrentFiles();
        }

        modal.classList.remove('hidden');
        console.log('Modal hidden class removed');
        setTimeout(() => {
            modalContent.classList.add('scale-100', 'opacity-100');
            console.log('Modal animation classes added');
        }, 10);
    } else {
        console.error('Modal elements not found!');
    }
}

// Close modal function
function closeModal() {
    const modal = document.getElementById('informasiModal');
    const modalContent = document.getElementById('modalContent');

    if (modal && modalContent) {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
            modal.classList.add('hidden');
            currentEditId = null;
        }, 300);
    }
}

// Edit informasi function
function editInformasi(id) {
    console.log('editInformasi called with id:', id);

    // Get the button that was clicked
    const button = document.querySelector(`.edit-btn[data-id="${id}"]`);
    console.log('Button found:', button);

    if (!button) {
        console.error('Edit button not found for id:', id);
        return;
    }

    // Get data from data attributes with proper null handling
    const judul = button.getAttribute('data-judul') || '';
    const konten = button.getAttribute('data-konten') || '';
    const jenis = button.getAttribute('data-jenis') || '';
    const nomorSurat = button.getAttribute('data-nomor-surat') || '';
    const tanggalSurat = button.getAttribute('data-tanggal-surat') || '';
    const penulis = button.getAttribute('data-penulis') || '';
    const status = button.getAttribute('data-status') || '';
    const tanggalPublish = button.getAttribute('data-tanggal-publish') || '';
    const tanggalBerakhir = button.getAttribute('data-tanggal-berakhir') || '';
    const isFeatured = button.getAttribute('data-is-featured') === 'true';
    const isPinned = button.getAttribute('data-is-pinned') === 'true';
    const thumbnail = button.getAttribute('data-thumbnail') || '';

    // Parse lampiran data safely
    let lampiran = [];
    try {
        const lampiranData = button.getAttribute('data-lampiran');
        if (lampiranData && lampiranData.trim() !== '' && lampiranData !== 'bnVsbA==') {
            // Decode base64 and parse JSON
            const decodedData = atob(lampiranData);
            if (decodedData !== 'null') {
                lampiran = JSON.parse(decodedData);
            }
        }
    } catch (e) {
        console.error('Error parsing lampiran data:', e);
        lampiran = [];
    }

    // Show modal first (don't reset form)
    openModal(true);

    // Set form data
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.textContent = 'Edit Informasi';

    // Check if formMethod element exists before setting it
    const formMethodElement = document.getElementById('formMethod');
    if (formMethodElement) {
        formMethodElement.value = 'PUT';
    }

    const form = document.getElementById('informasiForm');
    if (form) {
        form.action = `{{ route('admin-universitas.informasi.update', ':id') }}`.replace(':id', id);
        form.method = 'POST'; // Laravel method spoofing
    }

    // Fill form fields with null checks
    const judulElement = document.getElementById('judul');
    if (judulElement) judulElement.value = judul;

    const kontenElement = document.getElementById('konten');
    if (kontenElement) kontenElement.value = konten;

    // Also update the rich text editor
    const editorElement = document.getElementById('editor');
    if (editorElement) editorElement.innerHTML = konten;

    const jenisElement = document.getElementById('jenis');
    if (jenisElement) jenisElement.value = jenis;

    // Call togglePengumumanFields to show/hide pengumuman fields based on jenis
    // Pass isEdit = true to handle lampiran validation properly
    togglePengumumanFields(true);

    const nomorSuratElement = document.getElementById('nomor_surat');
    if (nomorSuratElement) nomorSuratElement.value = nomorSurat || '';

    const tanggalSuratElement = document.getElementById('tanggal_surat');
    if (tanggalSuratElement) {
        // Convert ISO date to YYYY-MM-DD format for input field
        let formattedTanggalSurat = '';
        if (tanggalSurat) {
            const date = new Date(tanggalSurat);
            if (!isNaN(date.getTime())) {
                formattedTanggalSurat = date.toISOString().split('T')[0]; // Get YYYY-MM-DD part
            }
        }
        tanggalSuratElement.value = formattedTanggalSurat;
    }

    const penulisElement = document.getElementById('penulis');
    if (penulisElement) penulisElement.value = penulis || '';

    const statusElement = document.getElementById('status');
    if (statusElement) statusElement.value = status;

    const tanggalPublishElement = document.getElementById('tanggal_publish');
    if (tanggalPublishElement) {
        // Convert ISO datetime to YYYY-MM-DDTHH:MM format for datetime-local input field
        let formattedTanggalPublish = '';
        if (tanggalPublish) {
            const date = new Date(tanggalPublish);
            if (!isNaN(date.getTime())) {
                // Format as YYYY-MM-DDTHH:MM for datetime-local input
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                formattedTanggalPublish = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        }
        tanggalPublishElement.value = formattedTanggalPublish;
    }

    const tanggalBerakhirElement = document.getElementById('tanggal_berakhir');
    if (tanggalBerakhirElement) {
        // Convert ISO datetime to YYYY-MM-DDTHH:MM format for datetime-local input field
        let formattedTanggalBerakhir = '';
        if (tanggalBerakhir) {
            const date = new Date(tanggalBerakhir);
            if (!isNaN(date.getTime())) {
                // Format as YYYY-MM-DDTHH:MM for datetime-local input
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                formattedTanggalBerakhir = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        }
        tanggalBerakhirElement.value = formattedTanggalBerakhir;
    }

    const isFeaturedElement = document.getElementById('is_featured');
    if (isFeaturedElement) {
        isFeaturedElement.checked = isFeatured;
    }

    const isPinnedElement = document.getElementById('is_pinned');
    if (isPinnedElement) {
        isPinnedElement.checked = isPinned;
    }

    currentEditId = id;

    // Display current files
    displayCurrentFiles(thumbnail, lampiran, id);

    // Update lampiran validation after displaying current files
    setTimeout(() => {
        togglePengumumanFields(true);
    }, 100);
}

// Function to display current files
function displayCurrentFiles(thumbnail, lampiran, id) {
    console.log('displayCurrentFiles called with:', { thumbnail, lampiran, id });

    // Display current thumbnail
    const currentThumbnailDiv = document.getElementById('currentThumbnail');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const thumbnailName = document.getElementById('thumbnailName');
    const thumbnailDownload = document.getElementById('thumbnailDownload');

    if (thumbnail && thumbnail.trim() !== '') {
        console.log('Displaying thumbnail:', thumbnail);
        if (currentThumbnailDiv) currentThumbnailDiv.classList.remove('hidden');
        if (thumbnailPreview) thumbnailPreview.src = thumbnail;
        if (thumbnailName) thumbnailName.textContent = 'Thumbnail saat ini';
        if (thumbnailDownload) {
            // For thumbnail, use direct path since it's stored in /storage/
            thumbnailDownload.href = thumbnail;
            thumbnailDownload.download = 'thumbnail.jpg'; // Set download filename
        }
    } else {
        console.log('No thumbnail to display');
        if (currentThumbnailDiv) currentThumbnailDiv.classList.add('hidden');
    }

    // Display current attachments
    const currentAttachmentsDiv = document.getElementById('currentAttachments');
    const attachmentsList = document.getElementById('attachmentsList');

    console.log('Lampiran data:', lampiran);
    if (lampiran && Array.isArray(lampiran) && lampiran.length > 0) {
        console.log('Displaying attachments:', lampiran.length, 'files');
        if (currentAttachmentsDiv) currentAttachmentsDiv.classList.remove('hidden');
        if (attachmentsList) {
            attachmentsList.innerHTML = '';
            lampiran.forEach((file, index) => {
                const fileInfo = typeof file === 'string' ? { name: file, path: file } : file;
                const fileName = fileInfo.name || fileInfo.path.split('/').pop();
                const filePath = fileInfo.path || fileInfo.name;

                const attachmentItem = document.createElement('div');
                attachmentItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border mb-2';
                attachmentItem.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i data-lucide="file-text" class="h-5 w-5 text-red-500"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 ml-3">
                            <p class="text-sm font-medium text-gray-900 truncate">${fileName}</p>
                            <p class="text-xs text-gray-500">Klik untuk download</p>
                        </div>
                    </div>
                    <a href="{{ route('admin-universitas.informasi.download', ['id' => ':id', 'filename' => ':filename']) }}".replace(':id', id).replace(':filename', filePath)"
                       target="_blank"
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i data-lucide="download" class="h-3 w-3 mr-1"></i>
                        Download
                    </a>
                `;
                attachmentsList.appendChild(attachmentItem);
            });
            // Re-initialize Lucide icons for new elements
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    } else {
        console.log('No attachments to display');
        if (currentAttachmentsDiv) currentAttachmentsDiv.classList.add('hidden');
    }
}

// Function to hide current files
function hideCurrentFiles() {
    const currentThumbnailDiv = document.getElementById('currentThumbnail');
    const currentAttachmentsDiv = document.getElementById('currentAttachments');

    if (currentThumbnailDiv) currentThumbnailDiv.classList.add('hidden');
    if (currentAttachmentsDiv) currentAttachmentsDiv.classList.add('hidden');
}

// Modern notification function
function showNotification(message, type = 'success') {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');

    const icon = type === 'success'
        ? '<svg class="notification-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
        : '<svg class="notification-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

    notification.className = `notification ${type} show`;
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                ${icon}
            </div>
            <div class="notification-text">
                ${message}
            </div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(notification);

    // Auto remove after 5 seconds
        setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Check for flash messages and show notifications
@if(session('success'))
    showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showNotification('{{ session('error') }}', 'error');
@endif

</script>
@endpush
@endsection
