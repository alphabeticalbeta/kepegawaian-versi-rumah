{{-- Script untuk Admin Univ Usulan --}}
@if(Request::is('admin-universitas-usulan/*'))
@push('scripts')
<script>
    {{-- Global JavaScript Functions --}}
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

    {{-- Main Application JavaScript --}}
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
@endpush
@endif
