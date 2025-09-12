@extends('frontend.layouts.app')

@section('title', 'Struktur Organisasi - Universitas Mulawarman')

@section('content')
<!-- Hero Section -->
<div class="text-black py-16 mb-12 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-32 w-auto object-contain drop-shadow-lg">
        </div>
        <h1 class="text-5xl md:text-6xl font-extrabold mb-2 text-black drop-shadow-md">Struktur Organisasi</h1>
        <p class="text-5xl md:text-6xl font-extrabold mb-2 text-black drop-shadow-md">Universitas Mulawarman</p>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto px-4 pb-2">
    @if(isset($error))
        <!-- Error State -->
        <div class="min-h-[400px] flex flex-col items-center justify-center text-center">
            <div class="text-red-500 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Gagal Memuat Data</h3>
            <p class="text-gray-600 mb-4">{{ $error }}</p>
            <a href="{{ route('profil.struktur-organisasi') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                Coba Lagi
            </a>
        </div>
    @elseif($strukturData)
        <!-- Content State -->
        <div class="bg-white rounded-2xl p-8 shadow-xl text-center mb-8">
            <div class="flex justify-center">
                <img src="{{ $strukturData['image_url'] }}" alt="Struktur Organisasi Universitas Mulawarman"
                     class="max-h-[200px] w-auto h-auto rounded-lg shadow-2xl transition-transform duration-300 hover:scale-50 object-contain">
            </div>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Terakhir diupdate: {{ \Carbon\Carbon::parse($strukturData['updated_at'])->format('d M Y H:i') }}
                </p>
            </div>
        </div>
    @else
        <!-- No Data State -->
        <div class="min-h-[400px] flex flex-col items-center justify-center text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Data</h3>
            <p class="text-gray-600">Struktur organisasi belum tersedia.</p>
        </div>
    @endif
</div>

<!-- Responsive adjustments -->
<div class="hidden lg:block">
    <style>
        @media (max-width: 1024px) {
            .max-h-\[600px\] {
                max-height: 500px;
            }
        }
        @media (max-width: 768px) {
            .max-h-\[600px\] {
                max-height: 400px;
            }
            .text-5xl {
                font-size: 2.5rem;
            }
            .text-6xl {
                font-size: 3rem;
            }
            .h-32 {
                height: 6rem;
            }
            .p-8 {
                padding: 1.5rem;
            }
        }
    </style>
</div>
@endsection
