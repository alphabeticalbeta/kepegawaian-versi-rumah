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
            $isMasterActive = request()->routeIs('backend.admin-univ-usulan.unitkerja.*')
                || request()->routeIs('backend.admin-univ-usulan.sub-unitkerja.*')
                || request()->routeIs('backend.admin-univ-usulan.sub-sub-unitkerja.*')
                || request()->routeIs('backend.admin-univ-usulan.pangkat.*')
                || request()->routeIs('backend.admin-univ-usulan.jabatan.*');
        @endphp

        <div class="mb-2">
            <button type="button"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isMasterActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                aria-controls="dropdown-master"
                aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="database" class="w-5 h-5 mr-3"></i>
                    <span class="font-medium">Master Data</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isMasterActive ? 'rotate-180' : '' }}"></i>
            </button>
            <div id="dropdown-master" class="{{ $isMasterActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                {{-- Unit Kerja --}}
                <div class="relative">
                    <a href="{{ route('backend.admin-univ-usulan.unitkerja.index') }}"
                       class="flex items-center px-3 py-2.5 rounded-lg transition
                       {{ request()->routeIs('backend.admin-univ-usulan.unitkerja.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="building-2" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">Unit Kerja</span>
                    </a>
                </div>
                {{-- Sub Unit Kerja --}}
                <div class="relative">
                    <a href="{{ route('backend.admin-univ-usulan.sub-unitkerja.index') }}"
                       class="flex items-center px-3 py-2.5 rounded-lg transition
                       {{ request()->routeIs('backend.admin-univ-usulan.sub-unitkerja.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="building" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">Sub Unit Kerja</span>
                    </a>
                </div>
                {{-- Sub-Sub Unit Kerja --}}
                <div class="relative">
                    <a href="{{ route('backend.admin-univ-usulan.sub-sub-unitkerja.index') }}"
                       class="flex items-center px-3 py-2.5 rounded-lg transition
                       {{ request()->routeIs('backend.admin-univ-usulan.sub-sub-unitkerja.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="book-open" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">Sub-Sub Unit Kerja</span>
                    </a>
                </div>
                {{-- Pangkat --}}
                <div class="relative">
                    <a href="{{ route('backend.admin-univ-usulan.pangkat.index') }}"
                       class="flex items-center px-3 py-2.5 rounded-lg transition
                       {{ request()->routeIs('backend.admin-univ-usulan.pangkat.*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i data-lucide="award" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">Pangkat</span>
                    </a>
                </div>
                {{-- Jabatan --}}
                <div class="relative">
                    <a href="{{ route('backend.admin-univ-usulan.jabatan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">Jabatan</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Usulan Dropdown --}}
        @php $isUsulanActive = request()->is('*/usulan/*'); @endphp
        <div class="mb-2">
            <button type="button"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg group transition {{ $isUsulanActive ? 'bg-gray-100 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                aria-controls="dropdown-usulan"
                aria-expanded="{{ $isUsulanActive ? 'true' : 'false' }}">
                <div class="flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                    <span class="font-medium">Usulan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform {{ $isUsulanActive ? 'rotate-180' : '' }}"></i>
            </button>
            <div id="dropdown-usulan" class="{{ $isUsulanActive ? '' : 'hidden' }} space-y-1 pl-4 mt-1">
                {{-- Contoh usulan --}}
                <div class="relative">
                    <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i data-lucide="user-check" class="w-5 h-5 mr-3"></i>
                        <span class="font-medium">Usulan NUPTK</span>
                    </a>
                </div>
                {{-- ... dst --}}
            </div>
        </div>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        document.querySelectorAll('button[aria-controls]').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = btn.getAttribute('aria-controls');
                const dropdown = document.getElementById(targetId);
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', String(!expanded));
                dropdown.classList.toggle('hidden');
            });
        });
    });
</script>
