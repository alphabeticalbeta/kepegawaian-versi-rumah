<aside id="sidebar" class="sidebar w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 group">
   {{-- Header Sidebar --}}
   <div class="flex items-center justify-center p-4 h-16 shadow">
       <div class="flex items-center gap-3 justify-center">
           <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
           <span class="font-bold text-lg sidebar-text">Admin Fakultas</span>
       </div>
   </div>

    {{-- Navigasi Menu --}}
    <nav class="py-4 overflow-y-auto h-[calc(100vh-128px)]"> {{-- Dibuat bisa di-scroll jika menu terlalu panjang --}}
        <div class="space-y-1">

            <div class="px-4 mb-4">
                {{-- [PERBAIKAN] Menggunakan nama rute yang benar --}}
                <a href="{{ route('admin-fakultas.dashboard') }}"
                class="flex items-center px-3 py-2.5 rounded-lg font-semibold {{ request()->routeIs('admin-fakultas.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </div>

            <!-- Usulan Section -->
            <div class="px-4 mb-2 mt-4">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                    Usulan
                </div>
            </div>

            <!-- Usulan Dropdown -->
            <div class="px-4 relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 focus:outline-none">
                    <div class="flex items-center">
                        <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform"
                       :class="{ 'rotate-180': open }"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="mt-2 space-y-1">

                    <!-- Usulan Jabatan -->
                    <div class="ml-6 border-l border-gray-200 pl-4 space-y-1">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-1">
                            Jabatan
                        </div>
                        <a href="{{ route('admin-fakultas.dashboard-jabatan') }}"
                           class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin-fakultas.dashboard-jabatan') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2"></i>
                            <span class="sidebar-text">Dashboard Jabatan</span>
                        </a>
                    </div>

                    <!-- Usulan Pangkat -->
                    <div class="ml-6 border-l border-gray-200 pl-4 space-y-1">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-1">
                            Pangkat
                        </div>
                        <a href="{{ route('admin-fakultas.dashboard-pangkat') }}"
                           class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin-fakultas.dashboard-pangkat') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i data-lucide="award" class="w-4 h-4 mr-2"></i>
                            <span class="sidebar-text">Dashboard Pangkat</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</aside>
