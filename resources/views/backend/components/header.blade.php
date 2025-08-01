<header class="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-3 h-16 flex items-center justify-between flex-shrink-0 z-20">
    {{-- Bagian kiri header tidak berubah --}}
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="p-1.5 rounded-lg hover:bg-gray-100">
            <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
        </button>
    </div>

    <div class="flex items-center gap-4">
        @auth('pegawai')
            @php
                $user = Auth::guard('pegawai')->user();
                $roles = $user->roles->pluck('name');
                $availableDashboards = [];

                // ==========================================================
                // ===== PERBAIKAN LOGIKA DIMULAI DI SINI ===================
                // ==========================================================
                // Periksa setiap role yang mungkin dimiliki pengguna
                if ($roles->contains('Admin Universitas')) {
                    $availableDashboards['Admin Universitas'] = route('admin-universitas.dashboard-universitas');
                }
                if ($roles->contains('Admin Universitas Usulan')) {
                    $availableDashboards['Admin Usulan'] = route('backend.admin-univ-usulan.dashboard');
                }
                if ($roles->contains('Admin Fakultas')) {
                    $availableDashboards['Admin Fakultas'] = route('admin-fakultas.dashboard-fakultas');
                }
                if ($roles->contains('Penilai')) {
                    $availableDashboards['Penilai'] = route('penilai-universitas.dashboard-penilai-universitas');
                }

                // Selalu tambahkan dashboard default Pegawai
                $availableDashboards['Pegawai'] = route('pegawai-unmul.dashboard-pegawai-unmul');
                // ==========================================================
                // ======================= AKHIR PERBAIKAN ====================
                // ==========================================================
            @endphp

            {{-- Tombol notifikasi (contoh) --}}
            <button class="p-2 hover:bg-gray-100 rounded-full relative">
                <i data-lucide="bell" class="w-5 h-5 text-gray-600"></i>
            </button>

            <div class="w-px h-6 bg-gray-200"></div>

            {{-- MENU PINDAH HALAMAN (TERPISAH) --}}
            @if(count($availableDashboards) > 1)
            <div class="relative">
                <button onclick="toggleRoleDropdown()" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="arrow-right-left" class="w-5 h-5 text-gray-600"></i>
                    <span class="hidden sm:block text-sm font-medium text-gray-700">Pindah Halaman</span>
                </button>
                <div id="role-dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg hidden z-50 border">
                    <div class="py-1">
                        <div class="px-4 pt-2 pb-1 text-xs font-semibold text-gray-400">Pindah Ke Dashboard</div>
                        @foreach($availableDashboards as $name => $url)
                            <a href="{{ $url }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Dropdown Profil --}}
            <div class="relative">
                <button onclick="toggleProfileDropdown()" class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100">
                    <img
                        src="{{ $user->foto ? Storage::url($user->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap) }}"
                        alt="{{ $user->nama_lengkap }}"
                        class="w-8 h-8 rounded-full object-cover"
                    />
                    <span class="hidden md:inline text-sm font-medium text-gray-700">{{ $user->nama_lengkap }}</span>
                </button>
                <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50 border">
                    <div class="py-1">
                        <a href="{{ route('pegawai-unmul.profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                        </a>
                    </div>
                    <div class="border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth

        @guest('pegawai')
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Login</a>
        @endguest
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        window.toggleProfileDropdown = function() {
            document.getElementById('role-dropdown-menu')?.classList.add('hidden');
            document.getElementById('profile-dropdown-menu')?.classList.toggle('hidden');
        };

        window.toggleRoleDropdown = function() {
            document.getElementById('profile-dropdown-menu')?.classList.add('hidden');
            document.getElementById('role-dropdown-menu')?.classList.toggle('hidden');
        };

        window.addEventListener('click', function(e) {
            const profileDropdown = document.getElementById('profile-dropdown-menu');
            const profileButton = document.querySelector('button[onclick="toggleProfileDropdown()"]');
            const roleDropdown = document.getElementById('role-dropdown-menu');
            const roleButton = document.querySelector('button[onclick="toggleRoleDropdown()"]');

            if (profileDropdown && profileButton && !profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
            if (roleDropdown && roleButton && !roleButton.contains(e.target) && !roleDropdown.contains(e.target)) {
                roleDropdown.classList.add('hidden');
            }
        });
    });
</script>
