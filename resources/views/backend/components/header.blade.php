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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Fungsi untuk dropdown profil
        window.toggleProfileDropdown = function() {
            const menu = document.getElementById('profile-dropdown-menu');
            menu.classList.toggle('hidden');
        };

        // Menutup dropdown jika klik di luar
        window.addEventListener('click', function(e) {
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            if (profileDropdown) {
                const profileButton = profileDropdown.previousElementSibling;
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            }
        });
    });
</script>
