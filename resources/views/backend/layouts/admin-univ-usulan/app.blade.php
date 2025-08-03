<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Kepegawaian UNMUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script type="module" src="http://127.0.0.1:5173/resources/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="http://127.0.0.1:5173/resources/css/app.css">

    {{-- CSS Kustom (jika ada) bisa ditambahkan di sini atau di file app.css --}}
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
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        {{-- Memuat Sidebar --}}
        @include('backend.components.sidebar-admin-universitas-usulan')

        <div id="main-content" class="flex-1 flex flex-col overflow-hidden ml-64">
            {{-- Memuat Header --}}
            @include('backend.components.header')

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- SEMUA JAVASCRIPT DIKONSOLIDASIKAN DI SINI UNTUK EFISIENSI --}}
    {{-- ========================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Alpine.start();
            // Inisialisasi semua ikon Lucide
            lucide.createIcons();

            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            // --- FUNGSI UNTUK TOGGLE SIDEBAR (Collapse/Expand) ---
            window.toggleSidebar = function() {
                // Cek apakah sidebar akan diciutkan
                const isCollapsing = !sidebar.classList.contains('w-20');

                sidebar.classList.toggle('w-64', !isCollapsing);
                sidebar.classList.toggle('w-20', isCollapsing);
                mainContent.style.marginLeft = isCollapsing ? '5rem' : '16rem';

                // Toggle semua teks di dalam sidebar
                document.querySelectorAll('.sidebar-text').forEach(text => {
                    text.classList.toggle('hidden', isCollapsing);
                });

                // Jika sidebar diciutkan, pastikan semua submenu juga tertutup
                if (isCollapsing) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.add('hidden');
                        const button = document.querySelector(`[data-collapse-toggle="${menu.id}"]`);
                        if (button) {
                            button.setAttribute('aria-expanded', 'false');
                            button.querySelector('[data-lucide="chevron-down"]')?.classList.remove('rotate-180');
                        }
                    });
                }
            };

            // --- FUNGSI UNTUK DROPDOWN SIDEBAR (Master Data, Usulan) ---
            // PERBAIKAN UTAMA ADA DI SINI: Logika disederhanakan
            document.querySelectorAll('button[data-collapse-toggle]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-collapse-toggle');
                    const dropdown = document.getElementById(targetId);

                    // Langsung buka/tutup dropdown tanpa mengubah ukuran sidebar.
                    const isOpening = dropdown.classList.contains('hidden');

                    // PENTING: Saat membuka dropdown, tutup dulu semua dropdown lain
                    // agar tidak tumpang tindih.
                    if (isOpening) {
                        document.querySelectorAll('.dropdown-menu').forEach(otherDropdown => {
                            if (otherDropdown.id !== targetId) {
                                otherDropdown.classList.add('hidden');
                                const otherButton = document.querySelector(`[data-collapse-toggle="${otherDropdown.id}"]`);
                                if (otherButton) {
                                    otherButton.setAttribute('aria-expanded', 'false');
                                    otherButton.querySelector('[data-lucide="chevron-down"]')?.classList.remove('rotate-180');
                                }
                            }
                        });
                    }

                    // Toggle dropdown yang diklik
                    dropdown.classList.toggle('hidden');
                    btn.setAttribute('aria-expanded', isOpening);
                    btn.querySelector('[data-lucide="chevron-down"]')?.classList.toggle('rotate-180', isOpening);
                });
            });

            // --- FUNGSI UNTUK DROPDOWN HEADER (PROFIL & PINDAH HALAMAN) ---
            // (Tidak ada perubahan di bagian ini)
            const profileDropdownMenu = document.getElementById('profile-dropdown-menu');
            const roleDropdownMenu = document.getElementById('role-dropdown-menu');
            const profileButton = document.querySelector('button[onclick="toggleProfileDropdown()"]');
            const roleButton = document.querySelector('button[onclick="toggleRoleDropdown()"]');

            window.toggleProfileDropdown = function() {
                roleDropdownMenu?.classList.add('hidden');
                profileDropdownMenu?.classList.toggle('hidden');
            };

            window.toggleRoleDropdown = function() {
                profileDropdownMenu?.classList.add('hidden');
                roleDropdownMenu?.classList.toggle('hidden');
            };

            window.addEventListener('click', function(e) {
                if (profileButton && !profileButton.contains(e.target) && !profileDropdownMenu?.contains(e.target)) {
                    profileDropdownMenu?.classList.add('hidden');
                }
                if (roleButton && !roleButton.contains(e.target) && !roleDropdownMenu?.contains(e.target)) {
                    roleDropdownMenu?.classList.add('hidden');
                }
            });
        });
    </script>

</body>
</html>
