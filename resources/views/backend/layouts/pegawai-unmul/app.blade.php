<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Pegawai') - Kepegawaian UNMUL</title>

    {{-- Memuat CSS dari Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-slate-100">

    {{-- Container utama menggunakan Flexbox --}}
    <div class="flex h-screen">

        {{-- Sidebar dipanggil di sini --}}
        @include('backend.components.sidebar-pegawai-unmul')

        {{-- Kontainer untuk Header dan Konten Utama --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300 ml-64">

            {{-- Header dipanggil di sini --}}
            @include('backend.components.header')

            {{-- Konten Dinamis dengan scroll internal --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
    @include('backend.components.scripts.script-pegawai-profil')
    @include('backend.components.scripts.script-pegawai-usulan')
    @stack('scripts')
</body>
</html>
