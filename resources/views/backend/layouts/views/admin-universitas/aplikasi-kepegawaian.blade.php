@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Aplikasi Kepegawaian')

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
    #aplikasiTableBody {
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

    /* Button Loading Animation */
    .btn-loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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

    /* Form Loading Overlay */
    .form-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        z-index: 10;
    }

    .form-loading-overlay.show {
        display: flex;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* Confirmation Modal Styles */
    .confirmation-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(4px);
    }

    .confirmation-modal.show {
        display: flex;
    }

    .confirmation-content {
        background: white;
        border-radius: 16px;
        padding: 24px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: scale(0.9);
        transition: transform 0.2s ease-out;
    }

    .confirmation-modal.show .confirmation-content {
        transform: scale(1);
    }

    .confirmation-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .confirmation-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        text-align: center;
        margin-bottom: 8px;
    }

    .confirmation-message {
        color: #6b7280;
        text-align: center;
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .confirmation-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .confirmation-btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .confirmation-btn-cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .confirmation-btn-cancel:hover {
        background: #e5e7eb;
    }

    .confirmation-btn-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .confirmation-btn-delete:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }
</style>
@endpush

@section('content')
<!-- Notification Container -->
<div id="notificationContainer"></div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-content">
        <div class="confirmation-icon">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="confirmation-title">Konfirmasi Hapus</div>
        <div class="confirmation-message">Apakah Anda yakin ingin menghapus data aplikasi ini? Tindakan ini tidak dapat dibatalkan.</div>
        <div class="confirmation-buttons">
            <button type="button" class="confirmation-btn confirmation-btn-cancel" onclick="closeConfirmationModal()">
                Batal
            </button>
            <button type="button" class="confirmation-btn confirmation-btn-delete" id="confirmDeleteBtn">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

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
                    Aplikasi Kepegawaian
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola daftar aplikasi kepegawaian yang tersedia
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">
            <!-- Filter and Search -->
            <div class="mb-4 bg-white rounded-2xl shadow-xl p-3 sm:p-4 transition-all duration-300 hover:shadow-2xl">
                <form method="GET" action="{{ route('admin-universitas.aplikasi-kepegawaian.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama aplikasi, sumber, atau keterangan..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 text-xs sm:text-sm">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header with Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 gap-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Daftar Aplikasi Kepegawaian</h3>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <!-- Add Button -->
                        <button onclick="openModal()"
                                class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                            <i data-lucide="plus" class="h-4 w-4 mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Tambah Aplikasi</span>
                            <span class="sm:hidden">Tambah</span>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-16">No</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-48">Nama Aplikasi</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Sumber</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-64">Keterangan</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Status</th>
                                <th class="px-2 sm:px-4 py-3 text-xs font-bold text-black text-center tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="aplikasiTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($aplikasis as $index => $aplikasi)
                                <tr class="hover:bg-gray-50 transition-all duration-200 animate-fade-in" style="animation-delay: {{ $index * 50 }}ms">
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                        {{ $index + $aplikasis->firstItem() }}
                                </td>
                                    <td class="px-2 sm:px-4 py-3">
                                        <div class="text-xs sm:text-sm font-medium text-gray-900 break-words max-w-xs" title="{{ $aplikasi->nama_aplikasi }}">
                                            {{ $aplikasi->nama_aplikasi }}
                                        </div>
                                </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                        <div class="text-xs sm:text-sm text-gray-900">{{ $aplikasi->sumber }}</div>
                                </td>
                                    <td class="px-2 sm:px-4 py-3">
                                        <div class="text-xs sm:text-sm text-gray-900 break-words max-w-xs" title="{{ $aplikasi->keterangan }}">
                                            {{ Str::limit($aplikasi->keterangan, 50) }}
                                        </div>
                                </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{
                                            $aplikasi->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                        }}">
                                        {{ $aplikasi->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                    <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                        <div class="flex justify-center items-center gap-1 sm:gap-2">
                                            <a href="{{ $aplikasi->link }}" target="_blank"
                                               class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                               title="Buka Aplikasi">
                                                <i data-lucide="external-link" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                                <span class="hidden sm:inline">Buka</span>
                                            </a>
                                    <button onclick="editAplikasi({{ $aplikasi->id }})"
                                                    class="inline-flex items-center px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                                    title="Edit Data">
                                                <i data-lucide="edit" class="h-3 w-3 sm:h-4 sm:w-4 mr-1"></i>
                                                <span class="hidden sm:inline">Edit</span>
                                    </button>
                                            <form action="{{ route('admin-universitas.aplikasi-kepegawaian.destroy', $aplikasi->id) }}" method="POST" class="inline">
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
                                    <td colspan="6" class="px-2 sm:px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                            <i data-lucide="package" class="h-10 w-10 text-gray-300 mb-3"></i>
                                            <p class="text-base font-medium">Belum ada data</p>
                                        <p class="text-sm">Data aplikasi kepegawaian akan ditampilkan di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mt-6">
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-700">
                        <span>Menampilkan {{ $aplikasis->firstItem() ?? 0 }} - {{ $aplikasis->lastItem() ?? 0 }} dari {{ $aplikasis->total() }} data</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($aplikasis->onFirstPage())
                            <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                                <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            </span>
                        @else
                            <a href="{{ $aplikasis->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            </a>
                        @endif

                        <div class="flex space-x-1">
                            @foreach($aplikasis->getUrlRange(1, $aplikasis->lastPage()) as $page => $url)
                                @if($page == $aplikasis->currentPage())
                                    <span class="px-3 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg shadow-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-200">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>

                        @if($aplikasis->hasMorePages())
                            <a href="{{ $aplikasis->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
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
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="aplikasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] force-scrollbar">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900" id="modalTitle">Tambah Aplikasi Kepegawaian</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5 sm:h-6 sm:w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="aplikasiForm" method="POST" action="{{ route('admin-universitas.aplikasi-kepegawaian.store') }}" class="relative">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <input type="hidden" id="aplikasiId" name="id">

                <!-- Loading Overlay -->
                <div id="formLoadingOverlay" class="form-loading-overlay">
                    <div class="loading-spinner"></div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi</label>
                        <input type="text" id="nama_aplikasi" name="nama_aplikasi" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               placeholder="Masukkan nama aplikasi">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sumber</label>
                        <input type="text" id="sumber" name="sumber" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               placeholder="Masukkan sumber aplikasi">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="Masukkan keterangan aplikasi"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link</label>
                        <input type="url" id="link" name="link" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                               placeholder="https://example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">Pilih Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3 mt-6">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200 text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200 text-sm sm:text-base">
                        <i data-lucide="save" class="h-4 w-4 mr-2 inline"></i>
                        <span id="submitText">Simpan</span>
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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Page initialization
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
    const filterStatus = document.querySelector('select[name="status"]');
    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            // Auto-submit form when status changes
            this.closest('form').submit();
        });
    }
}


// Modal functions
function openModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Aplikasi Kepegawaian';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('aplikasiForm').action = '{{ route("admin-universitas.aplikasi-kepegawaian.store") }}';
    document.getElementById('aplikasiForm').reset();
    document.getElementById('aplikasiId').value = '';
    document.getElementById('aplikasiModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('aplikasiModal').classList.add('hidden');
    document.getElementById('aplikasiForm').reset();
}

function editAplikasi(id) {
    // Get data from table row
    const row = document.querySelector(`button[onclick="editAplikasi(${id})"]`).closest('tr');
    const cells = row.querySelectorAll('td');

    const namaAplikasi = cells[1].textContent.trim();
    const sumber = cells[2].textContent.trim();
    const keterangan = cells[3].getAttribute('title') || cells[3].textContent.trim();
    const status = cells[4].textContent.trim().toLowerCase() === 'aktif' ? 'aktif' : 'tidak_aktif';

    // Get link from the "Buka" button
    const linkButton = row.querySelector('a[target="_blank"]');
    const link = linkButton ? linkButton.href : '';

    // Populate form
    document.getElementById('modalTitle').textContent = 'Edit Aplikasi Kepegawaian';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('aplikasiForm').action = `{{ url('admin-universitas/aplikasi-kepegawaian') }}/${id}`;
    document.getElementById('aplikasiId').value = id;
    document.getElementById('nama_aplikasi').value = namaAplikasi;
    document.getElementById('sumber').value = sumber;
    document.getElementById('keterangan').value = keterangan;
    document.getElementById('link').value = link;
    document.getElementById('status').value = status;

    // Show modal
    document.getElementById('aplikasiModal').classList.remove('hidden');
}

// Handle form submission with loading animation
document.getElementById('aplikasiForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingOverlay = document.getElementById('formLoadingOverlay');

    // Show loading state
    submitBtn.classList.add('btn-loading');
    submitText.textContent = 'Menyimpan...';
    loadingOverlay.classList.add('show');

    // Disable form to prevent double submission
    submitBtn.disabled = true;
});

// Handle delete button clicks with modern confirmation modal
let currentDeleteForm = null;

document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-btn')) {
        e.preventDefault(); // Prevent default form submission

        const deleteBtn = e.target.closest('.delete-btn');
        const form = deleteBtn.closest('form');

        console.log('Delete button clicked, form found:', form);

        // Store reference to form for later use
        currentDeleteForm = form;

        // Show confirmation modal
        showConfirmationModal();
    }
});

// Show confirmation modal
function showConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.add('show');
}

// Close confirmation modal
function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('show');
    currentDeleteForm = null;
}

// Close modal when clicking outside
document.getElementById('confirmationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmationModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirmationModal();
    }
});

// Handle confirm delete button click
document.addEventListener('DOMContentLoaded', function() {
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            console.log('Confirm delete clicked, currentDeleteForm:', currentDeleteForm);

            if (currentDeleteForm) {
                const deleteBtn = currentDeleteForm.querySelector('.delete-btn');
                console.log('Delete button found:', deleteBtn);

                if (deleteBtn) {
            const originalText = deleteBtn.innerHTML;

            // Show loading state
            deleteBtn.classList.add('btn-loading');
            deleteBtn.innerHTML = '<span class="hidden sm:inline">Menghapus...</span>';
            deleteBtn.disabled = true;

            // Store form reference before closing modal
            const formToSubmit = currentDeleteForm;

            // Close modal
            closeConfirmationModal();

            // Submit the form
            if (formToSubmit && typeof formToSubmit.submit === 'function') {
                formToSubmit.submit();
            } else {
                console.error('Form tidak dapat disubmit');
                // Reset button state if submit fails
                deleteBtn.classList.remove('btn-loading');
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
            }
        }
    }
        });
    }
});

// Show notification function
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
