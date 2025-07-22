<aside id="sidebar" class="w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30">
    <div class="mt-auto border-t border-gray-200 p-4">
        <div class="flex items-center">
            <img
                id="profile-image"
                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                alt="K. Anderson"
                class="h-10 w-10 rounded-full object-cover transition-all duration-300"
            />
            <div class="ml-3 sidebar-text">
                <p class="text-sm font-semibold text-gray-800">K. Anderson</p>
                <p class="text-xs text-gray-500">Web Designer</p>
            </div>
        </div>
    </div>

    <nav class="py-4">
        <div class="space-y-1">
                <div class="px-4 relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg bg-blue-50 text-blue-700">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Dashboard</span>
                    </a>
                </div>
            <div class="px-4 relative">
                <div onclick="toggleSubmenu('components')" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 cursor-pointer">
                    <div class="flex items-center">
                        <i data-lucide="component" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Profil</span>
                    </div>
                    <i id="components-icon" data-lucide="chevron-down" class="w-4 h-4 sidebar-chevron transition-transform"></i>
                </div>
                <div id="components-submenu" class="hidden mt-1 pl-8 space-y-1 sidebar-submenu">
                    <a href="#" class="block text-sm py-2 px-3 rounded-lg hover:bg-gray-100 sidebar-text">Visi dan Misi</a>
                    <a href="#" class="block text-sm py-2 px-3 rounded-lg hover:bg-gray-100 sidebar-text">Struktur Organisasi</a>
                </div>
            </div>
            <div class="px-4 relative">
                <div onclick="toggleSubmenu('components')" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 cursor-pointer">
                    <div class="flex items-center">
                        <i data-lucide="component" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Informasi</span>
                    </div>
                    <i id="components-icon" data-lucide="chevron-down" class="w-4 h-4 sidebar-chevron transition-transform"></i>
                </div>
                <div id="components-submenu" class="hidden mt-1 pl-8 space-y-1 sidebar-submenu">
                    <a href="#" class="block text-sm py-2 px-3 rounded-lg hover:bg-gray-100 sidebar-text">Pengumuman</a>
                    <a href="#" class="block text-sm py-2 px-3 rounded-lg hover:bg-gray-100 sidebar-text">Berita</a>
                </div>
            </div>
            <div class="px-4 relative">
                <div onclick="toggleSubmenu('forms')" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 cursor-pointer">
                    <div class="flex items-center">
                        <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Forms</span>
                    </div>
                    <i id="forms-icon" data-lucide="chevron-down" class="w-4 h-4 sidebar-chevron transition-transform"></i>
                </div>
                <div id="forms-submenu" class="hidden mt-1 pl-8 space-y-1 sidebar-submenu">
                        <a href="#" class="block text-sm py-2 px-3 rounded-lg hover:bg-gray-100 sidebar-text">Form Elements</a>
                </div>
            </div>
            <div class="mt-8">
                <div class="px-7 mb-2">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider sidebar-text">Pages</h3>
                </div>
                <div class="space-y-1 px-4">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 relative">
                        <i data-lucide="user" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Profile</span>
                    </a>
                        <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 relative">
                        <i data-lucide="log-in" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Login</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</aside>
