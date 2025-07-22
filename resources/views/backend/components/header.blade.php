<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    {{-- Menggunakan CDN untuk kemudahan. Untuk proyek produksi, gunakan Vite. --}}
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* Transisi untuk animasi yang lebih mulus */
        #sidebar, #main-content {
            transition: all 0.3s ease-in-out;
        }
        /* Tooltip sederhana saat sidebar di-collapse */
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 10px;
            padding: 4px 8px;
            background-color: #1f2937;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 50;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex">


        <div id="main-content" class="flex-1 flex flex-col h-screen">
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-3 h-16 flex items-center justify-between flex-shrink-0 z-20">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="p-1.5 rounded-lg hover:bg-gray-100">
                        <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
                    </button>
                    <div class="relative hidden sm:block">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5"></i>
                        <input
                            type="text"
                            placeholder="Search"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="p-2 hover:bg-gray-100 rounded-full relative">
                        <i data-lucide="bell" class="w-5 h-5 text-gray-600"></i>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-blue-600 text-white text-xs rounded-full flex items-center justify-center">4</span>
                    </button>
                    <button class="p-2 hover:bg-gray-100 rounded-full relative">
                        <i data-lucide="message-square" class="w-5 h-5 text-gray-600"></i>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-green-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                    </button>
                    <div class="w-px h-6 bg-gray-200"></div>
                    <div class="relative">
                        <button onclick="toggleProfileDropdown()" class="flex items-center gap-2">
                            <img
                                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                alt="K. Anderson"
                                class="w-8 h-8 rounded-full object-cover"
                            />
                            <div class="hidden md:flex flex-col items-start">
                                <span class="text-sm font-medium text-gray-700">K. Anderson</span>
                                <span class="text-xs text-gray-500">Web Designer</span>
                            </div>
                        </button>
                        <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <div class="border-t border-gray-100"></div>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign Out</a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                 <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold">Selamat Datang!</h2>
                    <p class="mt-2 text-gray-600">
                        Ini adalah konten utama halaman dashboard.
                    </p>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();

            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const menuItems = sidebar.querySelectorAll('a, div[onclick]');

            // Fungsi untuk toggle sidebar
            window.toggleSidebar = function() {
                const isCollapsed = sidebar.classList.toggle('w-20');
                sidebar.classList.toggle('w-64', !isCollapsed);
                mainContent.style.marginLeft = isCollapsed ? '80px' : '256px';

                document.querySelectorAll('.sidebar-text').forEach(text => text.classList.toggle('hidden', isCollapsed));
                document.querySelectorAll('.sidebar-chevron').forEach(icon => icon.classList.toggle('hidden', isCollapsed));

                menuItems.forEach(item => {
                    const textSpan = item.querySelector('.sidebar-text');
                    if (isCollapsed && textSpan) {
                        item.setAttribute('data-tooltip', textSpan.textContent.trim());
                    } else {
                        item.removeAttribute('data-tooltip');
                    }
                });

                if (isCollapsed) {
                    document.querySelectorAll('.sidebar-submenu').forEach(submenu => submenu.classList.add('hidden'));
                    document.querySelectorAll('.sidebar-chevron').forEach(icon => icon.classList.remove('rotate-180'));
                }
            };

            // Fungsi untuk toggle submenu
            window.toggleSubmenu = function(menuId) {
                if (sidebar.classList.contains('w-20')) {
                    toggleSidebar();
                }
                const submenu = document.getElementById(menuId + '-submenu');
                const icon = document.getElementById(menuId + '-icon');
                submenu.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            };

            // Fungsi untuk dropdown profil
            window.toggleProfileDropdown = function() {
                const menu = document.getElementById('profile-dropdown-menu');
                menu.classList.toggle('hidden');
            };

            // Menutup dropdown jika klik di luar
            window.addEventListener('click', function(e) {
                const profileDropdown = document.getElementById('profile-dropdown-menu');
                const profileButton = profileDropdown.previousElementSibling;
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });

            // Atur margin awal untuk konten utama
            mainContent.style.marginLeft = '256px';
        });
    </script>
</body>
</html>
