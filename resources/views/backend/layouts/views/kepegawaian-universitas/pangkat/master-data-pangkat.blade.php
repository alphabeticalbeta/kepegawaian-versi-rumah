@extends('backend.layouts.roles.kepegawaian-universitas.app')

@section('title', 'Master Data Pangkat')

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

    /* Pagination container styling */
    #paginationContainer {
        display: block;
    }

    /* Table transition animations */
    #pangkatTableBody {
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
                    Master Data Pangkat
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola hirarki pangkat PNS, PPPK, dan Non-ASN untuk sistem usulan
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">

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

        <!-- Filter and Search -->
        <div class="mb-4 bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pangkat</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        <option value="">Semua</option>
                        <option value="PNS">PNS</option>
                        <option value="PPPK">PPPK</option>
                        <option value="Non-ASN">Non-ASN</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Level</label>
                    <select id="filterLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        <option value="">Semua</option>
                        <option value="1">Level 1</option>
                        <option value="2">Level 2</option>
                        <option value="3">Level 3</option>
                        <option value="4">Level 4</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Golongan</label>
                    <select id="filterGolongan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                        <option value="">Semua</option>
                        <option value="I">Golongan I</option>
                        <option value="II">Golongan II</option>
                        <option value="III">Golongan III</option>
                        <option value="IV">Golongan IV</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                    <input type="text" id="searchInput" placeholder="Cari pangkat/golongan..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <!-- Table Header with Add Button -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Pangkat</h3>
                <a href="{{ route('backend.kepegawaian-universitas.pangkat.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-700 focus:from-indigo-700 focus:to-purple-700 active:from-indigo-900 active:to-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Pangkat
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-24">Status</th>
                            <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider">Pangkat</th>
                            <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-32">Golongan</th>
                            <th class="px-4 py-3 text-xs font-bold text-black text-center uppercase tracking-wider w-24">Level</th>
                            <th class="px-4 py-3 text-xs font-bold text-black text-center tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pangkatTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse($pangkats as $index => $pangkat)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                {{ ($pangkats->currentPage() - 1) * $pangkats->perPage() + $index + 1 }}
                            </td>

                            <!-- Status Pangkat -->
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($pangkat->status_pangkat == 'PNS')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        PNS
                                    </span>
                                @elseif($pangkat->status_pangkat == 'PPPK')
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

                            <!-- Pangkat -->
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium">{{ $pangkat->pangkat }}</div>
                            </td>

                            <!-- Golongan -->
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                <div class="font-medium">{{ $pangkat->golongan }}</div>
                            </td>

                            <!-- Level -->
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">
                                <div class="font-medium">{{ $pangkat->hierarchy_level }}</div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('backend.kepegawaian-universitas.pangkat.edit', $pangkat) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                       title="Edit Data">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('backend.kepegawaian-universitas.pangkat.destroy', $pangkat) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pangkat ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                                title="Hapus Data">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
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
                                    <h3 class="text-lg font-medium text-gray-900">Tidak ada data pangkat</h3>
                                    <p class="text-gray-500 mt-1">Tambah data pangkat untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="bg-white rounded-2xl shadow-xl overflow-hidden mt-6">
            <div class="px-4 py-4 flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-700">
                    <span id="paginationInfo">Menampilkan {{ $pangkats->firstItem() ?? 0 }} - {{ $pangkats->lastItem() ?? 0 }} dari {{ $pangkats->total() }} data</span>
                </div>
                <div class="flex items-center space-x-2">
                    @if($pangkats->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $pangkats->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif
                    <div id="pageNumbers" class="flex space-x-1">
                        @foreach($pangkats->getUrlRange(1, $pangkats->lastPage()) as $page => $url)
                            @if($page == $pangkats->currentPage())
                                <span class="px-3 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg shadow-md">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-200">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>
                    @if($pangkats->hasMorePages())
                        <a href="{{ $pangkats->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
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
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // XSS Protection Function
        function escapeHtml(text) {
            if (text === null || text === undefined) {
                return '';
            }
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
        }

document.addEventListener('DOMContentLoaded', function() {
    const notificationContainer = document.getElementById('notificationContainer');

    // Show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `p-4 rounded-lg shadow-lg border-l-4 transform transition-all duration-300 translate-x-full ${
            type === 'success'
                ? 'bg-green-50 border-green-400 text-green-800'
                : 'bg-red-50 border-red-400 text-red-800'
        }`;

        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 ${type === 'success' ? 'text-green-600' : 'text-red-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success'
                            ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                            : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z'
                    }"></path>
                </svg>
                <span class="font-medium">${escapeHtml(message)}</span>
            </div>
        `;

        notificationContainer.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    // Delete pangkat function
    window.deletePangkat = function(id, pangkatName) {
        if (confirm(`Apakah Anda yakin ingin menghapus pangkat "${escapeHtml(pangkatName)}"?`)) {
            // Show loading notification
            showNotification('Menghapus pangkat...', 'info');

            fetch(`/kepegawaian-universitas/pangkat/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Pangkat berhasil dihapus!', 'success');

                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Gagal menghapus pangkat.', 'error');
                }
            })
            .catch(error => {
                showNotification('Terjadi kesalahan pada server. Silakan coba lagi.', 'error');
            });
        }
    };

    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.mb-6.p-4');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (message.parentNode) {
                    message.parentNode.removeChild(message);
                }
            }, 300);
        }, 5000);
    });
});
</script>
@endsection
