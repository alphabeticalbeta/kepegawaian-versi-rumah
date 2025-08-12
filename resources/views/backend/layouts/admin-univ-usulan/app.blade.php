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

    {{-- ========================================================= --}}
    {{-- JAVASCRIPT SECTION - PROPERLY ORGANIZED --}}
    {{-- ========================================================= --}}

    {{-- Global JavaScript Functions --}}
    <script>
        // CSRF Token Setup for AJAX requests
        window.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token for all AJAX requests
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.csrfToken = token.getAttribute('content');

                // Setup for fetch API
                window.fetchWithCsrf = function(url, options = {}) {
                    options.headers = options.headers || {};
                    options.headers['X-CSRF-TOKEN'] = window.csrfToken;
                    options.headers['X-Requested-With'] = 'XMLHttpRequest';
                    return fetch(url, options);
                };
            }
        });
    </script>

    {{-- Main Application JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

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
                            const chevron = button.querySelector('[data-lucide="chevron-down"]');
                            if (chevron) {
                                chevron.classList.remove('rotate-180');
                            }
                        }
                    });
                }
            };

            // --- FUNGSI UNTUK DROPDOWN SIDEBAR (Master Data, Usulan) ---
            document.querySelectorAll('button[data-collapse-toggle]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const targetId = this.getAttribute('data-collapse-toggle');
                    const dropdown = document.getElementById(targetId);

                    if (!dropdown) return;

                    const isOpening = dropdown.classList.contains('hidden');

                    // Saat membuka dropdown, tutup dulu semua dropdown lain
                    if (isOpening) {
                        document.querySelectorAll('.dropdown-menu').forEach(otherDropdown => {
                            if (otherDropdown.id !== targetId) {
                                otherDropdown.classList.add('hidden');
                                const otherButton = document.querySelector(`[data-collapse-toggle="${otherDropdown.id}"]`);
                                if (otherButton) {
                                    otherButton.setAttribute('aria-expanded', 'false');
                                    const otherChevron = otherButton.querySelector('[data-lucide="chevron-down"]');
                                    if (otherChevron) {
                                        otherChevron.classList.remove('rotate-180');
                                    }
                                }
                            }
                        });
                    }

                    // Toggle dropdown yang diklik
                    dropdown.classList.toggle('hidden');
                    this.setAttribute('aria-expanded', isOpening);

                    const chevron = this.querySelector('[data-lucide="chevron-down"]');
                    if (chevron) {
                        chevron.classList.toggle('rotate-180', isOpening);
                    }

                    // Re-initialize Lucide icons after DOM changes
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            });

            // --- FUNGSI UNTUK DROPDOWN HEADER (PROFIL & PINDAH HALAMAN) ---
            const profileDropdownMenu = document.getElementById('profile-dropdown-menu');
            const roleDropdownMenu = document.getElementById('role-dropdown-menu');
            const profileButton = document.querySelector('button[onclick="toggleProfileDropdown()"]');
            const roleButton = document.querySelector('button[onclick="toggleRoleDropdown()"]');

            window.toggleProfileDropdown = function() {
                if (roleDropdownMenu) {
                    roleDropdownMenu.classList.add('hidden');
                }
                if (profileDropdownMenu) {
                    profileDropdownMenu.classList.toggle('hidden');
                }
            };

            window.toggleRoleDropdown = function() {
                if (profileDropdownMenu) {
                    profileDropdownMenu.classList.add('hidden');
                }
                if (roleDropdownMenu) {
                    roleDropdownMenu.classList.toggle('hidden');
                }
            };

            // Close dropdowns when clicking outside
            window.addEventListener('click', function(e) {
                if (profileButton && !profileButton.contains(e.target) && profileDropdownMenu && !profileDropdownMenu.contains(e.target)) {
                    profileDropdownMenu.classList.add('hidden');
                }
                if (roleButton && !roleButton.contains(e.target) && roleDropdownMenu && !roleDropdownMenu.contains(e.target)) {
                    roleDropdownMenu.classList.add('hidden');
                }
            });

            // --- HELPER FUNCTIONS ---

            // Show loading overlay
            window.showLoadingOverlay = function(message = 'Memproses...') {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    const messageEl = overlay.querySelector('p');
                    if (messageEl) {
                        messageEl.textContent = message;
                    }
                    overlay.classList.add('show');
                }
            };

            // Hide loading overlay
            window.hideLoadingOverlay = function() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.classList.remove('show');
                }
            };

            // Show SweetAlert success
            window.showSuccessAlert = function(title, text) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: title,
                        text: text,
                        confirmButtonColor: '#3b82f6'
                    });
                }
            };

            // Show SweetAlert error
            window.showErrorAlert = function(title, text) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: title,
                        text: text,
                        confirmButtonColor: '#ef4444'
                    });
                }
            };

            // Show SweetAlert confirmation
            window.showConfirmation = function(title, text, callback) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed && typeof callback === 'function') {
                            callback();
                        }
                    });
                }
            };
        });

        // Re-initialize icons after any dynamic content load
        document.addEventListener('htmx:afterSwap', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Re-initialize icons after Alpine renders
        document.addEventListener('alpine:initialized', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    {{-- Additional scripts from views (THIS IS CRITICAL FOR VALIDATION SCRIPTS) --}}
    @stack('scripts')

</body>
</html>
