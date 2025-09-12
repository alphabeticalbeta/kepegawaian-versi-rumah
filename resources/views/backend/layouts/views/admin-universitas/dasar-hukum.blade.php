@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Dasar Hukum')

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

    /* Line clamp utility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Hide pagination by default */
    #paginationContainer {
        display: none;
    }

    /* Show pagination when needed */
    #paginationContainer.show {
        display: block;
    }

    /* Show pagination info even when pagination controls are hidden */
    #paginationContainer:not(.show) {
        display: block;
    }

    /* Hide pagination controls when not needed */
    #paginationContainer:not(.show) .flex.items-center.space-x-2 {
        display: none;
    }

    /* Table transition animations */
    #dasarHukumTableBody {
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
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        backdrop-filter: blur(10px);
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification.success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-left: 4px solid #047857;
    }

    .notification.error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border-left: 4px solid #b91c1c;
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .notification-icon {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    .notification-text {
        flex: 1;
        font-weight: 500;
        line-height: 1.4;
    }

    .notification-close {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .notification-close:hover {
        background-color: rgba(255, 255, 255, 0.2);
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
                    Dasar Hukum
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola dokumen dasar hukum Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">
            <!-- Filter and Search -->
            <form method="GET" action="{{ route('admin-universitas.dasar-hukum.index') }}" class="mb-4 bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis</label>
                        <select name="jenis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="keputusan" {{ request('jenis') == 'keputusan' ? 'selected' : '' }}>Keputusan</option>
                            <option value="pedoman" {{ request('jenis') == 'pedoman' ? 'selected' : '' }}>Pedoman</option>
                            <option value="peraturan" {{ request('jenis') == 'peraturan' ? 'selected' : '' }}>Peraturan</option>
                            <option value="surat_edaran" {{ request('jenis') == 'surat_edaran' ? 'selected' : '' }}>Surat Edaran</option>
                            <option value="surat_kementerian" {{ request('jenis') == 'surat_kementerian' ? 'selected' : '' }}>Surat Kementerian</option>
                            <option value="surat_rektor" {{ request('jenis') == 'surat_rektor' ? 'selected' : '' }}>Surat Rektor</option>
                            <option value="undang_undang" {{ request('jenis') == 'undang_undang' ? 'selected' : '' }}>Undang-Undang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="">Semua</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul/nomor dokumen..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                    </div>
                </div>
            </form>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header with Add Button -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Dasar Hukum</h3>
                    <button onclick="openModal()"
                            class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                        <i data-lucide="plus" class="h-4 w-4 mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Tambah Dasar Hukum</span>
                        <span class="sm:hidden">Tambah</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-16 text-center">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-24 text-center">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-48 text-center">Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-28 text-center">Nomor Dokumen</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-28 text-center">Instansi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-20 text-center">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-24 text-center">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-24 text-center">Dokumen</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black font-bold uppercase tracking-wider w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($dasarHukum as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $dasarHukum->firstItem() + $index }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $item->jenis_label }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 text-sm text-gray-900 max-w-xs">
                                    <div class="truncate" title="{{ $item->judul }}">
                                        {{ $item->judul }}
                </div>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $item->nomor_dokumen }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                    <div class="truncate max-w-24" title="{{ $item->nama_instansi }}">
                                        {{ $item->nama_instansi }}
            </div>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{
                                        $item->status === 'published' ? 'bg-green-100 text-green-800' :
                                        ($item->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                    }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $item->formatted_tanggal_dokumen }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    @if($item->lampiran && count($item->lampiran) > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ count($item->lampiran) }} file
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                    <div class="flex justify-center items-center gap-1 sm:gap-2">
                                        @if($item->lampiran && count($item->lampiran) > 0)
                                            @php
                                                $firstFile = is_array($item->lampiran[0]) ? $item->lampiran[0]['path'] : $item->lampiran[0];
                                            @endphp
                                            <a href="{{ route('admin-universitas.dasar-hukum.download', ['id' => $item->id, 'filename' => $firstFile]) }}" target="_blank"
                                               class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                               title="Lihat Lampiran">
                                                <i data-lucide="eye" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                                <span class="hidden sm:inline">Lihat</span>
                                            </a>
                                        @endif
                                        <button onclick="editDasarHukum({{ $item->id }}, {{ json_encode($item->judul) }}, {{ json_encode($item->konten) }}, {{ json_encode($item->jenis_dasar_hukum) }}, {{ json_encode($item->sub_jenis) }}, {{ json_encode($item->nomor_dokumen) }}, {{ json_encode($item->tanggal_dokumen) }}, {{ json_encode($item->nama_instansi) }}, {{ json_encode($item->masa_berlaku) }}, {{ json_encode($item->penulis) }}, {{ json_encode($item->tags ? implode(',', $item->tags) : '') }}, {{ json_encode($item->status) }}, {{ json_encode($item->tanggal_publish) }}, {{ json_encode($item->tanggal_berakhir) }}, {{ $item->is_featured ? 'true' : 'false' }}, {{ $item->is_pinned ? 'true' : 'false' }}, {{ json_encode($item->thumbnail) }}, {{ json_encode($item->lampiran) }})"
                                                class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                                title="Edit Data">
                                            <i data-lucide="edit" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                            <span class="hidden sm:inline">Edit</span>
                                        </button>
                                        <form action="{{ route('admin-universitas.dasar-hukum.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                                    title="Hapus Data">
                                                <i data-lucide="trash-2" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                                <span class="hidden sm:inline">Hapus</span>
                                            </button>
                                        </form>
        </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="file-x" class="h-12 w-12 text-gray-300 mb-2"></i>
                                        <p class="text-sm">Tidak ada data dasar hukum ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
    </div>

    <!-- Pagination -->
                @if($dasarHukum->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
            <div class="flex items-center text-sm text-gray-700">
                            <span>Menampilkan {{ $dasarHukum->firstItem() ?? 0 }} - {{ $dasarHukum->lastItem() ?? 0 }} dari {{ $dasarHukum->total() }} data</span>
            </div>
            <div class="flex items-center space-x-2">
                            @if($dasarHukum->onFirstPage())
                                <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                </span>
                            @else
                                <a href="{{ $dasarHukum->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                </a>
                            @endif

                            <div class="flex space-x-1">
                                @foreach($dasarHukum->getUrlRange(1, $dasarHukum->lastPage()) as $page => $url)
                                    @if($page == $dasarHukum->currentPage())
                                        <span class="px-3 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg shadow-md">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-200">{{ $page }}</a>
                                    @endif
                                @endforeach
                </div>

                            @if($dasarHukum->hasMorePages())
                                <a href="{{ $dasarHukum->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
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

</div>

<!-- Modal -->
<div id="dasarHukumModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-2xl bg-white transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Dasar Hukum</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="dasarHukumForm" method="POST" class="mt-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <input type="hidden" id="editId" name="id">

                <div class="space-y-6">
                    <!-- Jenis Dasar Hukum dan Sub Jenis -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="jenis_dasar_hukum" class="block text-sm font-semibold text-gray-700 mb-3">
                                Jenis Dasar Hukum <span class="text-red-500">*</span>
                            </label>
                            <select id="jenis_dasar_hukum" name="jenis_dasar_hukum" required onchange="toggleSubJenis()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                                <option value="">Pilih Jenis Dasar Hukum</option>
                                <option value="keputusan">Keputusan</option>
                                <option value="pedoman">Pedoman</option>
                                <option value="peraturan">Peraturan</option>
                                <option value="surat_edaran">Surat Edaran</option>
                                <option value="surat_kementerian">Surat Kementerian</option>
                                <option value="surat_rektor">Surat Rektor Universitas Mulawarman</option>
                                <option value="undang_undang">Undang-Undang</option>
                            </select>
                        </div>

                        <!-- Sub Jenis - Conditional -->
                        <div id="subJenisField" class="hidden">
                            <label for="sub_jenis" class="block text-sm font-semibold text-gray-700 mb-3">
                                Sub Jenis <span class="text-red-500">*</span>
                            </label>
                            <select id="sub_jenis" name="sub_jenis"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                                <option value="">Pilih Sub Jenis</option>
                                <option value="peraturan">Peraturan</option>
                                <option value="surat_keputusan">Surat Keputusan</option>
                                <option value="sk_non_pns">Surat Keputusan (SK) Non PNS</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nomor Dokumen dan Tanggal Dokumen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nomor_dokumen" class="block text-sm font-semibold text-gray-700 mb-3">
                                Nomor Dokumen <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" id="nomor_dokumen" name="nomor_dokumen" required
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                       placeholder="001/UNMUL/REKTOR/2024">
                                <button type="button" onclick="generateNomorDokumen()"
                                        class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="tanggal_dokumen" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Dokumen <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_dokumen" name="tanggal_dokumen" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                    </div>

                    <!-- Nama Instansi -->
                    <div class="mb-6">
                        <div>
                            <label for="nama_instansi" class="block text-sm font-semibold text-gray-700 mb-3">
                                Nama Instansi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_instansi" name="nama_instansi" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                                   placeholder="Universitas Mulawarman">
                        </div>

                    <!-- Judul -->
                    <div class="mt-6">
                        <label for="judul" class="block text-sm font-semibold text-gray-700 mb-3">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                               placeholder="Masukkan judul dokumen dasar hukum">
                    </div>

                    <!-- Konten -->
                    <div class="mb-6 mt-6">
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
                             placeholder="Masukkan konten dokumen dasar hukum..."></div>
                        <input type="hidden" id="konten" name="konten">
                    </div>

                    <!-- Penulis dan Tags -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="penulis" class="block text-sm font-semibold text-gray-700 mb-3">
                                Penulis
                            </label>
                            <input type="text" id="penulis" name="penulis" readonly
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed"
                                   placeholder="Nama penulis"
                                   @if(auth()->guard('pegawai')->check())
                                       value="{{ auth()->guard('pegawai')->user()->nama_lengkap ?? auth()->guard('pegawai')->user()->name ?? 'Pegawai' }}"
                                   @else
                                       value="{{ auth()->user()->name ?? 'Administrator' }}"
                                   @endif>
                            <p class="text-xs text-gray-500 mt-1">
                                <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                                Penulis otomatis dari pegawai yang login
                            </p>
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

                    <!-- Thumbnail dan Lampiran -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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
                        <div>
                            <label for="lampiran" class="block text-sm font-semibold text-gray-700 mb-3">
                                Lampiran <span class="text-red-500" id="lampiranRequired">*</span>
                            </label>
                            <input type="file" id="lampiran" name="lampiran[]" multiple accept=".pdf"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <p class="text-xs text-gray-500 mt-1">
                                Format: PDF saja. Max: 10MB per file
                            </p>
                            <!-- Current Attachments Display -->
                            <div id="currentAttachments" class="mt-3 hidden">
                                <p class="text-sm font-medium text-gray-700 mb-2">File saat ini:</p>
                                <div id="attachmentsList" class="space-y-2">
                                    <!-- Attachments will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Publish dan Masa Berlaku -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="tanggal_publish" class="block text-sm font-semibold text-gray-700 mb-3">
                                Tanggal Publish
                            </label>
                            <input type="datetime-local" id="tanggal_publish" name="tanggal_publish"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
                        <div>
                            <label for="masa_berlaku" class="block text-sm font-semibold text-gray-700 mb-3">
                                Masa Berlaku
                            </label>
                            <input type="date" id="masa_berlaku" name="masa_berlaku"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        </div>
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

                    <!-- Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                Featured (Tampil di halaman utama)
                            </label>
                        </div>
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="is_pinned" name="is_pinned" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_pinned" class="ml-2 block text-sm text-gray-700">
                                Pinned (Selalu tampil di atas)
                            </label>
                        </div>
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

// Page initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeSearchAndFilter();
});

// Initialize search and filter functionality
function initializeSearchAndFilter() {
    // Initialize search input with auto-submit
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            // Auto-submit form when user stops typing
            e.target.closest('form').submit();
        }, 500));
    }

    // Initialize filter dropdowns with auto-submit
    const filterJenis = document.querySelector('select[name="jenis"]');
    const filterStatus = document.querySelector('select[name="status"]');

    if (filterJenis) {
        filterJenis.addEventListener('change', function() {
            // Auto-submit form when jenis changes
            this.closest('form').submit();
        });
    }

    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            // Auto-submit form when status changes
            this.closest('form').submit();
        });
    }
}

// Global variables for modal handling
let currentEditId = null;

// Modal functions
function openModal(isEdit = false) {
    const modal = document.getElementById('dasarHukumModal');
    const modalContent = document.getElementById('modalContent');

    // Only reset form if not editing
    if (!isEdit) {
        const form = document.getElementById('dasarHukumForm');
        if (form) form.reset();

        // Reset contenteditable editor
        const editorElement = document.getElementById('editor');
        if (editorElement) {
            editorElement.innerHTML = '';
        }

        // Reset hidden konten input
        const kontenInput = document.getElementById('konten');
        if (kontenInput) {
            kontenInput.value = '';
        }

        const modalTitle = document.getElementById('modalTitle');
        if (modalTitle) modalTitle.textContent = 'Tambah Dasar Hukum';

        // Check if formMethod element exists before setting it
        const formMethodElement = document.getElementById('formMethod');
        if (formMethodElement) {
            formMethodElement.value = 'POST';
        }

        if (form) form.action = '{{ route("admin-universitas.dasar-hukum.store") }}';
        currentEditId = null;

        // Hide current files for new data
        hideCurrentFiles();
    }

    // Show modal with animation
    if (modal) modal.classList.remove('hidden');
    setTimeout(() => {
        if (modalContent) {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
        }
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('dasarHukumModal');
    const modalContent = document.getElementById('modalContent');

    // Hide modal with animation
    if (modalContent) {
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    }

    setTimeout(() => {
        if (modal) modal.classList.add('hidden');
    }, 300);
}

function editDasarHukum(id, judul, konten, jenis, subJenis, nomor, tanggal, instansi, masaBerlaku, penulis, tags, status, tanggalPublish, tanggalBerakhir, isFeatured, isPinned, thumbnail, lampiran) {
    // Show modal first (don't reset form)
    openModal(true);

    // Set form data
    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.textContent = 'Edit Dasar Hukum';

    // Check if formMethod element exists before setting it
    const formMethodElement = document.getElementById('formMethod');
    if (formMethodElement) {
        formMethodElement.value = 'PUT';
    }

    const form = document.getElementById('dasarHukumForm');
    if (form) form.action = `{{ route('admin-universitas.dasar-hukum.update', ':id') }}`.replace(':id', id);

    // Fill form fields with null checks
    const judulElement = document.getElementById('judul');
    if (judulElement) judulElement.value = judul;

    const kontenElement = document.getElementById('konten');
    if (kontenElement) kontenElement.value = konten;

    // Also update the rich text editor
    const editorElement = document.getElementById('editor');
    if (editorElement) editorElement.innerHTML = konten;

    const jenisElement = document.getElementById('jenis_dasar_hukum');
    if (jenisElement) jenisElement.value = jenis;

    // Call toggleSubJenis to show/hide sub jenis field based on jenis
    toggleSubJenis();

    const subJenisElement = document.getElementById('sub_jenis');
    if (subJenisElement) subJenisElement.value = subJenis || '';

    const nomorElement = document.getElementById('nomor_dokumen');
    if (nomorElement) nomorElement.value = nomor;

    const tanggalElement = document.getElementById('tanggal_dokumen');
    if (tanggalElement) {
        // Convert ISO date to YYYY-MM-DD format for input field
        let formattedTanggal = '';
        if (tanggal) {
            const date = new Date(tanggal);
            if (!isNaN(date.getTime())) {
                formattedTanggal = date.toISOString().split('T')[0]; // Get YYYY-MM-DD part
            }
        }
        tanggalElement.value = formattedTanggal;
    }

    const instansiElement = document.getElementById('nama_instansi');
    if (instansiElement) instansiElement.value = instansi;

    const masaBerlakuElement = document.getElementById('masa_berlaku');
    if (masaBerlakuElement) {
        // Convert ISO date to YYYY-MM-DD format for input field
        let formattedMasaBerlaku = '';
        if (masaBerlaku) {
            const date = new Date(masaBerlaku);
            if (!isNaN(date.getTime())) {
                formattedMasaBerlaku = date.toISOString().split('T')[0]; // Get YYYY-MM-DD part
            }
        }
        masaBerlakuElement.value = formattedMasaBerlaku;
    }

    const penulisElement = document.getElementById('penulis');
    if (penulisElement) penulisElement.value = penulis;

    const tagsElement = document.getElementById('tags');
    if (tagsElement) tagsElement.value = tags || '';

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

    // Check if tanggal_berakhir element exists before setting it
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
        // Handle both boolean and string values
        const isChecked = isFeatured === true || isFeatured === 'true' || isFeatured === 1 || isFeatured === '1';
        isFeaturedElement.checked = isChecked;
    }

    const isPinnedElement = document.getElementById('is_pinned');
    if (isPinnedElement) {
        // Handle both boolean and string values
        const isChecked = isPinned === true || isPinned === 'true' || isPinned === 1 || isPinned === '1';
        isPinnedElement.checked = isChecked;
    }

    currentEditId = id;

    // Display current files
    displayCurrentFiles(thumbnail, lampiran, id);
}

// Function to display current files
function displayCurrentFiles(thumbnail, lampiran, id) {
    // Display current thumbnail
    const currentThumbnailDiv = document.getElementById('currentThumbnail');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const thumbnailName = document.getElementById('thumbnailName');
    const thumbnailDownload = document.getElementById('thumbnailDownload');

    if (thumbnail && thumbnail.trim() !== '') {
        if (currentThumbnailDiv) currentThumbnailDiv.classList.remove('hidden');
        if (thumbnailPreview) thumbnailPreview.src = thumbnail;
        if (thumbnailName) thumbnailName.textContent = 'Thumbnail saat ini';
        if (thumbnailDownload) {
            // For thumbnail, use direct path since it's stored in /storage/
            thumbnailDownload.href = thumbnail;
            thumbnailDownload.download = 'thumbnail.jpg'; // Set download filename
        }
    } else {
        if (currentThumbnailDiv) currentThumbnailDiv.classList.add('hidden');
    }

    // Display current attachments
    const currentAttachmentsDiv = document.getElementById('currentAttachments');
    const attachmentsList = document.getElementById('attachmentsList');

    if (lampiran && lampiran.length > 0) {
        if (currentAttachmentsDiv) currentAttachmentsDiv.classList.remove('hidden');
        if (attachmentsList) {
            attachmentsList.innerHTML = '';
            lampiran.forEach((file, index) => {
                const fileInfo = typeof file === 'string' ? { name: file, path: file } : file;
                const attachmentItem = document.createElement('div');
                attachmentItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border';
                attachmentItem.innerHTML = `
                    <div class="flex items-center">
                        <i data-lucide="file-text" class="h-6 w-6 text-red-500 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${fileInfo.name || fileInfo.path}</p>
                            ${fileInfo.size ? `<p class="text-xs text-gray-500">${(fileInfo.size / 1024).toFixed(2)} KB</p>` : ''}
                        </div>
                    </div>
                    <a href="/admin-universitas/dasar-hukum/${id}/download/${fileInfo.path}"
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                       target="_blank">
                        <i data-lucide="download" class="h-3 w-3 mr-1"></i>
                        Download
                    </a>
                `;
                attachmentsList.appendChild(attachmentItem);
            });
            // Re-initialize Lucide icons for new elements
            lucide.createIcons();
        }
    } else {
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

// Toggle sub jenis field
function toggleSubJenis() {
    const jenis = document.getElementById('jenis_dasar_hukum').value;
    const subJenisField = document.getElementById('subJenisField');
    const subJenisSelect = document.getElementById('sub_jenis');

    if (jenis === 'surat_rektor') {
        if (subJenisField) subJenisField.classList.remove('hidden');
        if (subJenisSelect) subJenisSelect.required = true;
            } else {
        if (subJenisField) subJenisField.classList.add('hidden');
        if (subJenisSelect) {
            subJenisSelect.required = false;
            subJenisSelect.value = '';
        }
    }
}

// Initialize editor
function initializeEditor() {
    // Simple rich text editor initialization
    const editor = document.getElementById('editor');
    if (editor) {
        // Basic contenteditable functionality
        editor.setAttribute('contenteditable', 'true');
    }
}

// Initialize Lucide icons
lucide.createIcons();

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



// Initialize editor
function initializeEditor() {
    // Simple rich text editor initialization
    const editor = document.getElementById('editor');
    if (editor) {
        // Basic contenteditable functionality
        editor.setAttribute('contenteditable', 'true');
    }
}

// Initialize Lucide icons
lucide.createIcons();

// Handle form submission with loading animation and content sync
document.getElementById('dasarHukumForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');

    // Sync contenteditable content to hidden input
    const editorElement = document.getElementById('editor');
    const kontenInput = document.getElementById('konten');
    if (editorElement && kontenInput) {
        kontenInput.value = editorElement.innerHTML;
    }

    // Show loading state
    if (submitBtn) {
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
    }
    if (submitText) {
        submitText.textContent = 'Menyimpan...';
    }
});

// Modern notification function
function showNotification(message, type = 'success') {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');

    const icon = type === 'success'
        ? '<svg class="notification-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
        : '<svg class="notification-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            ${icon}
            <div class="notification-text">${message}</div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(notification);

    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 400);
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
