<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Pegawai') - Kepegawaian UNMUL</title>

    {{-- Memuat CSS dari Vite --}}
    @vite('resources/css/app.css')

    {{-- Memuat pustaka ikon Lucide --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Memuat pustaka Alpine.js untuk interaktivitas dropdown --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100">

    {{-- Container utama menggunakan Flexbox --}}
    <div class="flex h-screen">

        {{-- Sidebar dipanggil di sini --}}
        @include('backend.components.sidebar-admin-universitas')

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

    {{-- ====================================================================== --}}
    {{-- =================== BLOK SCRIPT YANG DIPERBAIKI ====================== --}}
    {{-- ====================================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi ikon Lucide setelah halaman dimuat
            lucide.createIcons();

            // Referensi ke elemen-elemen penting
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            // Cek status sidebar dari penyimpanan lokal (localStorage)
            let isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            // Fungsi utama untuk menerapkan status ciut/lebar pada elemen-elemen
            const applySidebarState = (collapsed) => {
                const sidebarTexts = sidebar.querySelectorAll('.sidebar-text');

                if (collapsed) {
                    // --- Logika saat Sidebar DICCIUTKAN ---
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    mainContent.classList.remove('ml-64');
                    mainContent.classList.add('ml-20');

                    // Sembunyikan semua elemen teks
                    sidebarTexts.forEach(text => text.classList.add('hidden'));
                } else {
                    // --- Logika saat Sidebar DILEBARKAN ---
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    mainContent.classList.remove('ml-20');
                    mainContent.classList.add('ml-64');

                    // Tampilkan kembali semua elemen teks
                    sidebarTexts.forEach(text => text.classList.remove('hidden'));
                }
                // Simpan pilihan pengguna ke localStorage
                localStorage.setItem('sidebarCollapsed', collapsed);
            };

            // Terapkan status yang tersimpan saat halaman dimuat
            applySidebarState(isSidebarCollapsed);

            // Membuat fungsi toggleSidebar bisa diakses secara global
            window.toggleSidebar = function() {
                isSidebarCollapsed = !isSidebarCollapsed;
                applySidebarState(isSidebarCollapsed);
            }
        });
    </script>

</body>
</html>
