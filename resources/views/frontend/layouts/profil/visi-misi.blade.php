@extends('frontend.layouts.app')

@section('title', 'Visi dan Misi - Universitas Mulawarman')

@push('styles')
<style>
/* Prose styling for content */
.prose {
    color: #374151;
    max-width: none;
}

.prose p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}

.prose strong {
    color: #111827;
    font-weight: 600;
}

.prose em {
    color: #4B5563;
    font-style: italic;
}

.prose ol {
    list-style-type: decimal;
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.prose ul {
    list-style-type: disc;
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.prose li {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}

.prose li::marker {
    color: #6B7280;
}

/* Card hover effects */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-6 py-16 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain">
                </div>
                <h1 class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                    Visi dan Misi
                </h1>
                <p class="text-4xl font-bold tracking-tight text-black sm:text-5xl mb-4">
                    Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 -mt-8 px-6 py-6 sm:px-8 pb-2">
        <div class="mx-auto max-w-full pt-5 mt-5">
            @if(isset($error))
                <!-- Error State -->
                <div class="text-center py-12">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                            <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-red-800 mb-2">Gagal Memuat Data</h3>
                        <p class="text-red-600 mb-4">{{ $error }}</p>
                        <a href="{{ route('profil.visi-misi') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <i data-lucide="refresh-cw" class="h-4 w-4 mr-2"></i>
                            Coba Lagi
                        </a>
                    </div>
                </div>
            @else
                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 -mx-8 pb-2">
                    <!-- Visi Card -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform hover:scale-105 transition-all duration-300 mx-8">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                                        <i data-lucide="eye" class="h-7 w-7 text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-white">Visi Universitas Mulawarman</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="prose prose-lg max-w-none">
                                @if($visi)
                                    {!! $visi->konten !!}
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <i data-lucide="eye-off" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
                                        <p>Data visi belum tersedia</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Misi Card -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden transform hover:scale-105 transition-all duration-300 mx-8">
                        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                                        <i data-lucide="list-checks" class="h-7 w-7 text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-2xl font-bold text-white">Misi Universitas Mulawarman</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="prose prose-lg max-w-none">
                                @if($misi)
                                    {!! $misi->konten !!}
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <i data-lucide="list-x" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
                                        <p>Data misi belum tersedia</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush
@endsection
