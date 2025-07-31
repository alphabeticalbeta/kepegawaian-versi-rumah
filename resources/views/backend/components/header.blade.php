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
        {{-- DIUBAH: Tampilkan blok ini HANYA JIKA user sudah login --}}
        @auth
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
                        src="{{ Auth::user()->foto ? Storage::url(Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->nama_lengkap) . '&background=random' }}"
                        alt="{{ Auth::user()->nama_lengkap }}"
                        class="w-8 h-8 rounded-full object-cover"
                    />
                    <div class="hidden md:flex flex-col items-start">
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->nama_lengkap }}</span>
                        <span class="text-xs text-gray-500">{{ Auth::user()->roles->first()->name ?? 'Pegawai' }}</span>
                    </div>
                </button>
                <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <div class="border-t border-gray-100"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        @endauth

        {{-- DIUBAH: Tampilkan blok ini HANYA JIKA user adalah tamu (belum login) --}}
        @guest
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                Login
            </a>
        @endguest
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Fungsi untuk dropdown profil
        window.toggleProfileDropdown = function() {
            const menu = document.getElementById('profile-dropdown-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        };

        // Menutup dropdown jika klik di luar
        window.addEventListener('click', function(e) {
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            const profileButton = document.querySelector('button[onclick="toggleProfileDropdown()"]');

            if (profileDropdown && profileButton) {
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            }
        });
    });
</script>
