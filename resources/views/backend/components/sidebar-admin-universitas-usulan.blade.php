<aside id="sidebar" class="w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 group flex flex-col">
    {{-- CSS for active menu state --}}
    <style>
        .menu-item.active {
            background-color: #f3f4f6;
            position: relative;
        }
        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: #3b82f6;
            border-radius: 0 4px 4px 0;
        }
        .menu-item.active i {
            color: #3b82f6;
        }
        .menu-item.active span {
            color: #111827;
            font-weight: 500;
        }
    </style>
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 shadow flex-shrink-0">
        <div class="flex items-center gap-3 justify-center">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg sidebar-text text-center">Admin Usulan Kepegawaian</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-2">
        {{-- Menu Master Data dengan dropdown --}}
        <div class="mb-2">
            <button type="button"
                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg group {{ request()->routeIs('backend.admin-univ-usulan.unitkerja.*') || request()->routeIs('backend.admin-univ-usulan.sub-unitkerja.*') || request()->routeIs('backend.admin-univ-usulan.pangkat.*') || request()->routeIs('backend.admin-univ-usulan.jabatan.*') || request()->routeIs('backend.admin-univ-usulan.jurusan.*') || request()->routeIs('backend.admin-univ-usulan.prodi.*') ? 'active' : '' }}"
                aria-controls="dropdown-master"
                data-collapse-toggle="dropdown-master">
                <div class="flex items-center">
                    <i data-lucide="database" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Master Data</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform group-[aria-expanded=true]:rotate-180"></i>
            </button>
            <div id="dropdown-master" class="{{ request()->routeIs('backend.admin-univ-usulan.unitkerja.*') || request()->routeIs('backend.admin-univ-usulan.sub-unitkerja.*') || request()->routeIs('backend.admin-univ-usulan.pangkat.*') || request()->routeIs('backend.admin-univ-usulan.jabatan.*') || request()->routeIs('backend.admin-univ-usulan.jurusan.*') || request()->routeIs('backend.admin-univ-usulan.prodi.*') ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                <div class="relative menu-item {{ request()->routeIs('backend.admin-univ-usulan.unitkerja.*') ? 'active' : '' }}">
                    <a href="{{ route('backend.admin-univ-usulan.unitkerja.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="building-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Unit Kerja</span>
                    </a>
                </div>
                <div class="relative menu-item {{ request()->routeIs('backend.admin-univ-usulan.sub-unitkerja.*') ? 'active' : '' }}">
                    <a href="{{ route('backend.admin-univ-usulan.sub-unitkerja.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="building" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Sub Unit Kerja</span>
                    </a>
                </div>
                <div class="relative menu-item {{ request()->routeIs('backend.admin-univ-usulan.pangkat.*') ? 'active' : '' }}">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="award" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Pangkat</span>
                    </a>
                </div>
                <div class="relative menu-item {{ request()->routeIs('backend.admin-univ-usulan.jabatan.*') ? 'active' : '' }}">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="briefcase" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Jabatan</span>
                    </a>
                </div>
                <div class="relative menu-item {{ request()->routeIs('backend.admin-univ-usulan.jurusan.*') ? 'active' : '' }}">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="library" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Jurusan</span>
                    </a>
                </div>
                <div class="relative menu-item {{ request()->routeIs('backend.admin-univ-usulan.prodi.*') ? 'active' : '' }}">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="book-open" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Prodi</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Menu Usulan dengan dropdown --}}
        <div class="mb-2">
            <button type="button"
                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg group {{ request()->is('*/usulan/*') ? 'active' : '' }}"
                aria-controls="dropdown-usulan"
                data-collapse-toggle="dropdown-usulan">
                <div class="flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform group-[aria-expanded=true]:rotate-180"></i>
            </button>
            <div id="dropdown-usulan" class="{{ request()->is('*/usulan/*') ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="user-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan NUPTK</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="file-bar-chart-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Laporan LKD</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="clipboard-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Presensi</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="clock" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Penyesuaian Masa Kerja</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="book-marked" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Ujian Dinas & Ijazah</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="file-user" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Jabatan</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="file-check-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Laporan Serdos</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="user-minus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pensiun</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="trending-up" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Kepangkatan</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="graduation-cap" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pencantuman Gelar</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="link" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan ID SINTA ke SISTER</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="medal" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Satyalancana</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="book-open" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Tugas Belajar</span>
                    </a>
                </div>
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="user-plus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text">Usulan Pengaktifan Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</aside>

<!-- Script untuk dropdown functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Fungsi untuk toggle dropdown
        function setupDropdown(buttonSelector, dropdownId) {
            const button = document.querySelector(buttonSelector);
            const dropdown = document.getElementById(dropdownId);

            if (button && dropdown) {
                button.addEventListener('click', function() {
                    const expanded = this.getAttribute('aria-expanded') === 'true' || false;
                    this.setAttribute('aria-expanded', !expanded);
                    dropdown.classList.toggle('hidden');

                    // Rotate chevron icon
                    const icon = this.querySelector('[data-lucide="chevron-down"]');
                    if (icon) {
                        icon.style.transform = expanded ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                });

                // Set initial expanded state based on active routes
                if (!dropdown.classList.contains('hidden')) {
                    button.setAttribute('aria-expanded', 'true');
                    const icon = button.querySelector('[data-lucide="chevron-down"]');
                    if (icon) {
                        icon.style.transform = 'rotate(180deg)';
                    }
                }
            }
        }

        // Setup dropdown untuk Master Data dan Usulan
        setupDropdown('[aria-controls="dropdown-master"]', 'dropdown-master');
        setupDropdown('[aria-controls="dropdown-usulan"]', 'dropdown-usulan');
    });
</script>
