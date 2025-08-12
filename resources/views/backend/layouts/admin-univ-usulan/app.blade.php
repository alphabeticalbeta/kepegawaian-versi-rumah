<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Kepegawaian UNMUL</title>

    {{-- Vite Integration - FIXED: Removed hardcoded development URLs --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- CSS Kustom --}}
    <style>
        #sidebar, #main-content {
            transition: all 0.3s ease-in-out;
        }
        /* Styling tambahan untuk tabel, tombol, dll. tetap dipertahankan */
        .data-table { border-collapse: separate; border-spacing: 0; width: 100%; }
        .data-table th { background-color: #f9fafb; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #6b7280; padding: 0.75rem 1rem; text-align: left; }
        .data-table td { padding: 0.75rem 1rem; vertical-align: middle; border-top: 1px solid #e5e7eb; }
        .data-table tbody tr:hover { background-color: #f9fafb; }
        .btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 0.375rem; font-weight: 500; padding: 0.5rem 1rem; transition: all 0.2s; }
        .btn-primary { background-color: #3b82f6; color: white; }
        .btn-primary:hover { background-color: #2563eb; }
        .card { background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; }
        .card-header { padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; }
        .card-body { padding: 1.5rem; }

        /* Loading overlay styles */
        #loadingOverlay {
            display: none;
        }
        #loadingOverlay.show {
            display: flex;
        }
    </style>

    {{-- Additional styles from views --}}
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">

    {{-- Loading Overlay (Hidden by default) --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] items-center justify-center">
        <div class="relative mx-auto p-5 w-96">
            <div class="bg-white rounded-lg shadow-xl p-6">
                <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                </div>
                <p class="text-center mt-4 text-gray-600">Memproses...</p>
            </div>
        </div>
    </div>

    <div class="flex h-screen">
        {{-- Memuat Sidebar --}}
        @include('backend.components.sidebar-admin-universitas-usulan')

        <div id="main-content" class="flex-1 flex flex-col overflow-hidden ml-64">
            {{-- Memuat Header --}}
            @include('backend.components.header')

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @include('backend.components.flash')
                @yield('content')
            </main>
        </div>
    </div>
    @include('backend.components.scripts.script-admin-univ-usulan')
    @stack('scripts')
</body>
</html>
