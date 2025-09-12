@extends('frontend.layouts.app')

@section('title', 'Peraturan - Universitas Mulawarman')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-6 py-4 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain">
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                    Peraturan Terkini
                </h1>
                <p class="text-base text-gray-600 sm:text-lg">
                    Daftar peraturan Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full px-2 sm:px-4 lg:px-6 py-6">
        <!-- Search and Filter Section -->
        <div class="mb-6 bg-white rounded-2xl shadow-xl p-4 transition-all duration-300 hover:shadow-2xl">
            <form method="GET" action="{{ route('dasar-hukum.peraturan.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200 hover:border-green-400">
                        <option value="">Semua Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                    <select name="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200 hover:border-green-400">
                        <option value="">Semua Tahun</option>
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pencarian</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul/nomor dokumen..."
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200 hover:border-green-400"
                               id="searchInput">
                        <!-- Loading spinner -->
                        <div id="searchLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-500"></div>
                        </div>
                        <!-- Clear button -->
                        @if(request('search'))
                            <a href="{{ route('dasar-hukum.peraturan.index') }}" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors" title="Hapus pencarian" id="clearSearch">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Active Filters Display -->
        @if(request('search') || request('status') || request('tahun'))
        <div class="mb-4 flex flex-wrap gap-2">
            <span class="text-sm text-gray-600">Filter aktif:</span>
            @if(request('search'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Pencarian: "{{ request('search') }}"
                    <a href="{{ route('dasar-hukum.peraturan.index', array_merge(request()->except('search'))) }}" class="ml-2 text-green-600 hover:text-green-800">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Status: {{ ucfirst(request('status')) }}
                    <a href="{{ route('dasar-hukum.peraturan.index', array_merge(request()->except('status'))) }}" class="ml-2 text-blue-600 hover:text-blue-800">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            @if(request('tahun'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Tahun: {{ request('tahun') }}
                    <a href="{{ route('dasar-hukum.peraturan.index', array_merge(request()->except('tahun'))) }}" class="ml-2 text-green-600 hover:text-green-800">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
        </div>
        @endif

        <!-- peraturan Grid - Hampir Penuh -->
        <div class="w-full">
            <div id="peraturanGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 transition-all duration-500">
                @forelse($peraturan as $index => $item)
                <article class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer transform hover:scale-105 animate-fade-in"
                         style="animation-delay: {{ $index * 100 }}ms"
                         onclick="showPeraturanDetail({{ $item->id }}, '{{ addslashes($item->judul) }}', '{{ addslashes($item->konten) }}', '{{ $item->tanggal_dokumen }}', '{{ addslashes($item->penulis ?? 'Admin') }}', '{{ addslashes($item->nomor_dokumen ?? '-') }}', '{{ addslashes($item->nama_instansi ?? '-') }}', '{{ addslashes($item->jenis_label ?? 'peraturan') }}', '{{ addslashes($item->status_label ?? '-') }}', '{{ $item->thumbnail }}', {{ json_encode($item->lampiran ?? []) }})">

                    <!-- Thumbnail Section -->
                    <div class="relative h-48 bg-gradient-to-br from-green-400 to-blue-500">
                        @if($item->thumbnail)
                            <img src="{{ $item->getThumbnailUrl() ?? asset('storage/' . $item->thumbnail) }}"
                                 alt="{{ $item->judul }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="absolute inset-0 flex items-center justify-center {{ $item->thumbnail ? 'hidden' : 'flex' }}">
                            <svg class="w-12 h-12 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>

                        <!-- Status Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1">
                            @if($item->is_featured)
                                <span class="bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded-full shadow-lg">
                                    Featured
                                </span>
                            @endif
                            @if($item->is_pinned)
                                <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full shadow-lg">
                                    Pinned
                                </span>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{
                                $item->status === 'published' ? 'bg-green-100 text-green-800' :
                                ($item->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                            }}">
                                {{ $item->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="p-4">
                        <!-- Date and Author -->
                        <div class="flex items-center text-xs text-gray-500 mb-2">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($item->tanggal_dokumen)->format('d F Y') }}
                            <span class="mx-2">•</span>
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $item->penulis ?? 'Admin' }}
                        </div>

                        <!-- Title -->
                        <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-green-600 transition-colors duration-300">
                            {{ $item->judul }}
                        </h3>

                        <!-- Document Number -->
                        @if($item->nomor_dokumen)
                        <div class="text-xs text-gray-600 mb-2">
                            <span class="font-medium">No:</span> {{ $item->nomor_dokumen }}
                        </div>
                        @endif

                        <!-- Content Preview -->
                        <p class="text-xs text-gray-600 mb-3 line-clamp-3">
                            {!! strip_tags($item->konten) !!}
                        </p>

                        <!-- Tags -->
                        @if($item->tags && count($item->tags) > 0)
                            <div class="mb-3 flex flex-wrap gap-1">
                                @foreach(array_slice($item->tags, 0, 3) as $tag)
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                                @if(count($item->tags) > 3)
                                    <span class="text-xs text-gray-500">+{{ count($item->tags) - 3 }}</span>
                                @endif
                            </div>
                        @endif

                        <!-- Action Button -->
                        <div class="flex items-center justify-between">
                            <div class="text-green-600 font-medium text-xs transition-colors duration-300 hover:text-orange-800">
                                Klik untuk melihat →
                            </div>
                            @if($item->lampiran && count($item->lampiran) > 0)
                                <div class="text-xs text-gray-500">
                                    {{ count($item->lampiran) }} file
                                </div>
                            @endif
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 max-w-md mx-auto">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Tidak Ada peraturan</h3>
                        <p class="text-gray-600">
                            @if(request('search') || request('status') || request('tahun'))
                                Tidak ditemukan peraturan dengan filter yang dipilih.
                            @else
                                Belum ada peraturan yang tersedia saat ini.
                            @endif
                        </p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($peraturan->hasPages())
        <div class="mt-8">
            <nav class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-700">
                    Menampilkan {{ $peraturan->firstItem() ?? 0 }} - {{ $peraturan->lastItem() ?? 0 }} dari {{ $peraturan->total() }} peraturan
                </div>
                <div class="flex items-center space-x-2">
                    @if($peraturan->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $peraturan->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-black bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif

                    <div class="flex items-center space-x-1">
                        @foreach($peraturan->getUrlRange(1, $peraturan->lastPage()) as $page => $url)
                            @if($page == $peraturan->currentPage())
                                <span class="px-3 py-2 text-sm font-medium bg-green-600 text-white rounded-lg shadow-md">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-black bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all duration-200">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>

                    @if($peraturan->hasMorePages())
                        <a href="{{ $peraturan->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-black bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    @endif
                </div>
            </nav>
        </div>
        @endif
    </div>
</div>

<!-- peraturan Detail Modal -->
<div id="peraturanModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Detail peraturan</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div id="modalContent">
                    <!-- Modal content will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Show peraturan detail modal
function showPeraturanDetail(id, judul, konten, tanggalDokumen, penulis, nomorDokumen, namaInstansi, jenisLabel, statusLabel, thumbnail, lampiran) {
    // Set modal title
    document.getElementById('modalTitle').textContent = judul;

    // Build modal content
    let modalContent = `
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    ${formatDate(tanggalDokumen)}
                    <span class="mx-2">•</span>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    ${escapeHtml(penulis)}
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="sharePeraturan('${escapeHtml(judul)}', '${window.location.origin}/dasar-hukum/peraturan/${id}')"
                            class="p-2 text-gray-400 hover:text-gray-600 transition-colors" title="Bagikan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                    </button>
                    <button onclick="printPeraturan()"
                            class="p-2 text-gray-400 hover:text-gray-600 transition-colors" title="Cetak">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-700">Nomor Dokumen:</span>
                    <p class="text-gray-600">${escapeHtml(nomorDokumen)}</p>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Instansi:</span>
                    <p class="text-gray-600">${escapeHtml(namaInstansi)}</p>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Jenis:</span>
                    <p class="text-gray-600">${escapeHtml(jenisLabel)}</p>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Status:</span>
                    <p class="text-gray-600">${escapeHtml(statusLabel)}</p>
                </div>
            </div>`;

    // Add thumbnail if exists
    if (thumbnail && thumbnail.trim() !== '') {
        modalContent += `
            <div class="relative">
                <img src="${escapeHtml(thumbnail)}"
                     alt="${escapeHtml(judul)}"
                     class="w-full h-60 object-cover rounded-lg cursor-pointer"
                     onclick="openImageModal('${escapeHtml(thumbnail)}', '${escapeHtml(judul)}')"
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTNhZiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-white opacity-0 hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                </div>
            </div>`;
    }

    // Add content
    modalContent += `
        <div class="prose max-w-none">
            ${konten}
        </div>`;

    // Add attachments if exist
    if (lampiran && Array.isArray(lampiran) && lampiran.length > 0) {
        modalContent += `
            <div class="border-t pt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Lampiran (${lampiran.length} file)</h4>
                <div class="space-y-2">`;

        lampiran.forEach((file, index) => {
            const fileName = typeof file === 'string' ? file.split('/').pop() : (file.name || file.path?.split('/').pop() || 'file');
            const filePath = typeof file === 'string' ? file : (file.path || file.name || file);

            modalContent += `
                <a href="/dasar-hukum/peraturan/${id}/download/${escapeHtml(filePath.split('/').pop())}"
                   target="_blank"
                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 group-hover:text-green-600">${escapeHtml(fileName)}</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>`;
        });

        modalContent += `
                </div>
            </div>`;
    }

    modalContent += `</div>`;

    // Set modal content and show modal
    document.getElementById('modalContent').innerHTML = modalContent;
    document.getElementById('peraturanModal').classList.remove('hidden');

    // Add keyboard navigation
    document.addEventListener('keydown', handleModalKeyboard);
}

// Close modal
function closeModal() {
    document.getElementById('peraturanModal').classList.add('hidden');
    document.removeEventListener('keydown', handleModalKeyboard);
}

// Keyboard navigation for modal
function handleModalKeyboard(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
}

// Share functionality
function sharePeraturan(title, url) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Link berhasil disalin!', 'success');
        });
    }
}

// Print functionality
function printPeraturan() {
    const modalContent = document.getElementById('modalContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>peraturan - ${document.getElementById('modalTitle').textContent}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .prose { line-height: 1.6; }
                    .prose h1, .prose h2, .prose h3 { color: #333; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                <h1>${document.getElementById('modalTitle').textContent}</h1>
                ${modalContent}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Image modal
function openImageModal(src, alt) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full">
            <img src="${src}" alt="${alt}" class="max-w-full max-h-full object-contain rounded-lg">
            <button onclick="this.parentElement.parentElement.remove()"
                    class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(modal);
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// XSS Protection - Escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
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

// Auto-search functionality
function initializeAutoSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const tahunSelect = document.querySelector('select[name="tahun"]');
    const form = document.querySelector('form');
    const searchLoading = document.getElementById('searchLoading');
    const clearSearch = document.getElementById('clearSearch');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(function(e) {
            const query = e.target.value.trim();

            // Show loading indicator
            if (searchLoading) {
                searchLoading.classList.remove('hidden');
            }
            if (clearSearch) {
                clearSearch.style.display = 'none';
            }

            // Auto-submit form when user stops typing
            setTimeout(() => {
                form.submit();
            }, 100);
        }, 500));
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            // Show loading indicator
            if (searchLoading) {
                searchLoading.classList.remove('hidden');
            }

            // Auto-submit form when status changes
            setTimeout(() => {
                form.submit();
            }, 100);
        });
    }

    if (tahunSelect) {
        tahunSelect.addEventListener('change', function() {
            // Show loading indicator
            if (searchLoading) {
                searchLoading.classList.remove('hidden');
            }

            // Auto-submit form when tahun changes
            setTimeout(() => {
                form.submit();
            }, 100);
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize auto-search
    initializeAutoSearch();

    // Add smooth scrolling for pagination
    document.querySelectorAll('a[href*="page="]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;

            // Add loading state
            document.body.style.opacity = '0.7';
            document.body.style.pointerEvents = 'none';

            // Navigate after short delay for smooth transition
            setTimeout(() => {
                window.location.href = url;
            }, 200);
        });
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for search focus
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.querySelector('input[name="search"]')?.focus();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
/* Search animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
    opacity: 0;
}

/* Grid transition */
#peraturanGrid {
    transition: opacity 0.3s ease-in-out;
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Search input focus animation */
input[name="search"]:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    color: #374151;
    line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #111827;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose p {
    margin-bottom: 1.5rem;
}

.prose img {
    border-radius: 0.5rem;
    margin: 1.5rem 0;
}

.prose ul, .prose ol {
    margin: 1.5rem 0;
    padding-left: 1.5rem;
}

.prose li {
    margin: 0.5rem 0;
}

.prose blockquote {
    border-left: 4px solid #f97316;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6b7280;
}

.prose a {
    color: #f97316;
    text-decoration: underline;
}

.prose a:hover {
    color: #ea580c;
}
</style>
@endpush
@endsection
