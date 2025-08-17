<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Sistem Kepegawaian UNMUL')">
    <meta name="author" content="UNMUL">

    <title>@yield('title', 'Dashboard') - Kepegawaian UNMUL</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Vite Integration - CSS dan JS --}}
    @vite(['resources/css/app.css', 'resources/js/' . ($jsModule ?? 'app.js')])

    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Additional styles from views --}}
    @stack('styles')

    {{-- Custom CSS for specific role --}}
    @if(isset($role))
        <style>
            /* Role-specific styles */
            .role-{{ $role }} {
                /* Custom styles for {{ $role }} */
            }
        </style>
    @endif

    {{-- Global CSS for sidebar and header --}}
    <style>
        /* Sidebar collapsed state */
        .sidebar.collapsed {
            width: 4rem;
        }

        .sidebar.collapsed .sidebar-text {
            display: none;
        }

                /* Main content adjustment when sidebar is collapsed */
        #main-content {
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
        }

        #main-content.ml-16 {
            margin-left: 4rem;
        }

        /* Ensure sidebar is properly positioned */
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 30 !important;
        }

        /* Ensure header stays on top */
        header {
            position: sticky !important;
            top: 0 !important;
            z-index: 40 !important;
            background: white !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
        }

        /* Ensure header is always visible */
        header.bg-white {
            background-color: white !important;
        }

        /* Fix header positioning issues */
        #main-content > header {
            position: sticky !important;
            top: 0 !important;
            z-index: 40 !important;
        }

        /* Ensure dropdowns appear above other elements */
        #role-dropdown-menu,
        #profile-dropdown-menu {
            z-index: 50;
        }

        /* Loading overlay z-index */
        #loadingOverlay {
            z-index: 9999;
        }

        /* Password modal z-index */
        #passwordModal {
            z-index: 60;
        }

        /* Hide accessibility link properly */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        /* Ensure accessibility link is hidden by default */
        a[href="#main-content"] {
            position: absolute !important;
            left: -9999px !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
        }

        /* Only show on focus for keyboard navigation */
        a[href="#main-content"]:focus {
            position: absolute !important;
            left: 4px !important;
            top: 4px !important;
            width: auto !important;
            height: auto !important;
            overflow: visible !important;
            background: #4f46e5 !important;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            z-index: 50 !important;
            text-decoration: none !important;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased">

    {{-- Loading Overlay (Hidden by default) --}}
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] items-center justify-center hidden">
        <div class="relative mx-auto p-5 w-96">
            <div class="bg-white rounded-lg shadow-xl p-6">
                <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                </div>
                <p class="text-center mt-4 text-gray-600" id="loadingText">Memproses...</p>
            </div>
        </div>
    </div>

    {{-- Skip to main content (Accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-indigo-600 text-white px-4 py-2 rounded-md z-50" style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;">
        Skip to main content
    </a>

    <div class="flex h-screen">
        {{-- Sidebar --}}
        @include($sidebarComponent ?? 'backend.components.sidebar-default')

        {{-- Main Content Container --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300 ml-64">
            {{-- Header --}}
            @include('backend.components.header')

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100" style="padding-top: 1rem;">
                {{-- Breadcrumb (if provided) --}}
                @hasSection('breadcrumb')
                    <nav class="mb-6" aria-label="Breadcrumb">
                        @yield('breadcrumb')
                    </nav>
                @endif

                {{-- Page Header (if provided) --}}
                @hasSection('page-header')
                    <div class="mb-6">
                        @yield('page-header')
                    </div>
                @endif

                {{-- Flash Messages --}}
                @include('backend.components.flash')

                {{-- Main Content --}}
                @yield('content')

                {{-- Page Footer (if provided) --}}
                @hasSection('page-footer')
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        @yield('page-footer')
                    </div>
                @endif
            </main>
        </div>
    </div>

    {{-- Additional scripts from child templates --}}
    @stack('scripts')

    {{-- Header Functions Script --}}
    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
                console.log('Lucide icons initialized');

                // Re-initialize icons when new content is added dynamically
                const observer = new MutationObserver(function(mutations) {
                    let shouldReInit = false;
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            // Check if any added nodes contain lucide icons
                            mutation.addedNodes.forEach(function(node) {
                                if (node.nodeType === 1) { // Element node
                                    if (node.querySelector && (node.querySelector('[data-lucide]') || node.hasAttribute('data-lucide'))) {
                                        shouldReInit = true;
                                    }
                                }
                            });
                        }
                    });

                    if (shouldReInit) {
                        lucide.createIcons();
                        console.log('Lucide icons re-initialized after DOM changes');
                    }
                });

                // Start observing
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });

            } else {
                console.error('Lucide library not loaded');
            }
        });

        // Ensure header functions are available globally
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Base layout loaded');

            // Initialize header functions if not already done
            if (typeof window.toggleSidebar === 'undefined') {
                console.log('Initializing header functions from base layout');

                // Toggle sidebar function
                window.toggleSidebar = function() {
                    const sidebar = document.querySelector('.sidebar');
                    const mainContent = document.getElementById('main-content');

                    if (sidebar && mainContent) {
                        sidebar.classList.toggle('collapsed');

                        if (sidebar.classList.contains('collapsed')) {
                            mainContent.classList.remove('ml-64');
                            mainContent.classList.add('ml-16');
                        } else {
                            mainContent.classList.remove('ml-16');
                            mainContent.classList.add('ml-64');
                        }

                        console.log('Sidebar toggled:', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
                    } else {
                        console.error('Sidebar or main content not found');
                    }
                };

                // Toggle role dropdown function
                window.toggleRoleDropdown = function() {
                    const dropdown = document.getElementById('role-dropdown-menu');
                    if (dropdown) {
                        dropdown.classList.toggle('hidden');

                        // Close other dropdowns
                        const profileDropdown = document.getElementById('profile-dropdown-menu');
                        if (profileDropdown) {
                            profileDropdown.classList.add('hidden');
                        }
                    }
                };

                // Toggle profile dropdown function
                window.toggleProfileDropdown = function() {
                    const dropdown = document.getElementById('profile-dropdown-menu');
                    if (dropdown) {
                        dropdown.classList.toggle('hidden');

                        // Close other dropdowns
                        const roleDropdown = document.getElementById('role-dropdown-menu');
                        if (roleDropdown) {
                            roleDropdown.classList.add('hidden');
                        }
                    }
                };

                // Open password modal function
                window.openPasswordModal = function() {
                    const modal = document.getElementById('passwordModal');
                    if (modal) {
                        modal.classList.remove('hidden');

                        // Close dropdowns
                        const profileDropdown = document.getElementById('profile-dropdown-menu');
                        if (profileDropdown) {
                            profileDropdown.classList.add('hidden');
                        }
                    }
                };

                // Close password modal function
                window.closePasswordModal = function() {
                    const modal = document.getElementById('passwordModal');
                    if (modal) {
                        modal.classList.add('hidden');
                    }
                };

                console.log('Header functions initialized from base layout');
            }
        });
    </script>

    {{-- Global JavaScript Functions --}}
    <script>
        // Global loading functions
        window.showLoading = function(message = 'Memproses...') {
            const overlay = document.getElementById('loadingOverlay');
            const text = document.getElementById('loadingText');
            if (overlay && text) {
                text.textContent = message;
                overlay.classList.remove('hidden');
            }
        };

        window.hideLoading = function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.add('hidden');
            }
        };

        // Global error handling
        window.handleError = function(error, title = 'Error') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: error.message || error,
                    confirmButtonText: 'OK'
                });
            } else {
                alert(`${title}: ${error.message || error}`);
            }
        };

        // Global success handling
        window.handleSuccess = function(message, title = 'Success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    confirmButtonText: 'OK'
                });
            } else {
                alert(`${title}: ${message}`);
            }
        };

        // Global confirmation
        window.confirmAction = function(message, callback) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed && callback) {
                        callback();
                    }
                });
            } else {
                if (confirm(message) && callback) {
                    callback();
                }
            }
        };
    </script>
</body>
</html>
