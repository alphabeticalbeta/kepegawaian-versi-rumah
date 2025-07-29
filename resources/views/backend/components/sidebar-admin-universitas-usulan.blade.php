<aside id="sidebar" class="w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 flex flex-col">
    {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 shadow flex-shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg text-center">Admin Usulan Kepegawaian</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto py-2">

        {{-- Master Data Dropdown --}}
        @php
            $masterMenus = [
                [
                    'route' => 'backend.admin-univ-usulan.data-pegawai.index',
                    'icon' => 'users',
                    'label' => 'Data Pegawai',
                    'pattern' => 'backend.admin-univ-usulan.data-pegawai.*',
                ],
                // --- PENAMBAHAN MENU ROLE PEGAWAI DI SINI ---
                [
                    'route' => 'backend.admin-univ-usulan.role-pegawai.index',
                    'icon' => 'user-cog',
                    'label' => 'Role Pegawai',
                    'pattern' => 'backend.admin-univ-usulan.role-pegawai.*',
                ],
                // ---------------------------------------------
                [
                    'route' => 'backend.admin-univ-usulan.unitkerja.index',
                    'icon' => 'building-2',
                    'label' => 'Unit Kerja',
                    'pattern' => 'backend.admin-univ-usulan.unitkerja.*',
                ],
                [
                    'route' => 'backend.admin-univ-usulan.sub-unitkerja.index',
                    'icon' => 'building',
                    'label' => 'Sub Unit Kerja',
                    'pattern' => 'backend.admin-univ-usulan.sub-unitkerja.*',
                ],
                [
                    'route' => 'backend.admin-univ-usulan.sub-sub-unitkerja.index',
                    'icon' => 'book-open',
                    'label' => 'Sub-Sub Unit Kerja',
                    'pattern' => 'backend.admin-univ-usulan.sub-sub-unitkerja.*',
                ],
                [
                    'route' => 'backend.admin-univ-usulan.pangkat.index',
                    'icon' => 'award',
                    'label' => 'Pangkat',
                    'pattern' => 'backend.admin-univ-usulan.pangkat.*',
                ],
                [
                    'route' => 'backend.admin-univ-usulan.jabatan.index',
                    'icon' => 'briefcase',
                    'label' => 'Jabatan',
                    'pattern' => 'backend.admin-univ-usulan.jabatan.*',
                ],
            ];
            $isMasterActive = collect($masterMenus)->contains(function($menu){
                return request()->routeIs($menu['pattern']);
            });
        @endphp

        <div class="mb-2">
            <button type="button"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isMasterActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                    aria-controls="dropdown-master"
                    data-collapse-toggle="dropdown-master"
                    aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="database" class="w-5 h-5 mr-3"></i>
                    <span class="font-medium">Master Data</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isMasterActive ? 'rotate-180' : '' }}"></i>
            </button>
            <div id="dropdown-master" class="{{ $isMasterActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                @foreach($masterMenus as $menu)
                <div class="relative">
                    <a href="{{ route($menu['route']) }}"
                       class="flex items-center px-3 py-2.5 rounded-lg transition
                              {{ request()->routeIs($menu['pattern']) ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="{{ $menu['icon'] }}" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">{{ $menu['label'] }}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Usulan Dropdown --}}
        @php $isUsulanActive = request()->is('*/usulan/*'); @endphp
        <div class="mb-2">
            <button type="button"
                    class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isUsulanActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                    aria-controls="dropdown-usulan"
                    data-collapse-toggle="dropdown-usulan"
                    aria-expanded="{{ $isUsulanActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                    <span class="font-medium">Usulan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isUsulanActive ? 'rotate-180' : '' }}"></i>
            </button>
            <div id="dropdown-usulan" class="{{ $isUsulanActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                {{-- Usulan menu contoh --}}
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="user-check" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">Usulan NUPTK</span>
                    </a>
                </div>
                {{-- Tambah menu lain di sini sesuai kebutuhan --}}
            </div>
        </div>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide Icons
        lucide.createIcons();

        // Dropdown Toggle Logic
        document.querySelectorAll('button[data-collapse-toggle]').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-collapse-toggle');
                const dropdown = document.getElementById(targetId);
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                this.setAttribute('aria-expanded', !isExpanded);
                dropdown.classList.toggle('hidden');

                const chevron = this.querySelector('[data-lucide="chevron-down"]');
                if (chevron) {
                    chevron.classList.toggle('rotate-180', !isExpanded);
                }
            });
        });
    });
</script>
