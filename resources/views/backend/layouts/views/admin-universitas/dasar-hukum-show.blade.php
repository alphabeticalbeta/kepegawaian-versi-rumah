@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Lihat Dasar Hukum')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Lihat Dasar Hukum</h1>
                    <p class="mt-2 text-gray-600">Detail dokumen dasar hukum</p>
                </div>
                <a href="{{ route('admin-universitas.dasar-hukum.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Document Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $dasarHukum->judul }}</h2>
                        <p class="text-indigo-100 mt-1">{{ $dasarHukum->jenis_label }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20">
                            {{ $dasarHukum->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Document Info -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Dokumen</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $dasarHukum->nomor_dokumen }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Dokumen</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $dasarHukum->formatted_tanggal_dokumen }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Instansi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $dasarHukum->nama_instansi }}</p>
                        </div>
                        @if($dasarHukum->masa_berlaku)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Masa Berlaku</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $dasarHukum->formatted_masa_berlaku }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Additional Info -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penulis</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $dasarHukum->penulis }}</p>
                        </div>
                        @if($dasarHukum->tags && count($dasarHukum->tags) > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tags</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($dasarHukum->tags as $tag)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($dasarHukum->tanggal_publish)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Publish</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $dasarHukum->formatted_tanggal_publish }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Konten</label>
                    <div class="prose max-w-none bg-gray-50 p-6 rounded-lg">
                        {!! $dasarHukum->konten !!}
                    </div>
                </div>

                <!-- Attachments -->
                @if($dasarHukum->lampiran && count($dasarHukum->lampiran) > 0)
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Lampiran</label>
                    <div class="space-y-3">
                        @foreach($dasarHukum->lampiran as $file)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i data-lucide="file-text" class="h-8 w-8 text-red-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $file['name'] ?? $file }}</p>
                                        @if(isset($file['size']))
                                            <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 2) }} KB</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('admin-universitas.dasar-hukum.download', ['id' => $dasarHukum->id, 'filename' => $file['path'] ?? $file]) }}"
                                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Thumbnail -->
                @if($dasarHukum->thumbnail)
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Thumbnail</label>
                    <div class="max-w-xs">
                        <img src="{{ $dasarHukum->thumbnail }}" alt="Thumbnail" class="rounded-lg shadow-md">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush
