<aside id="sidebar" class="w-64 bg-white shadow-sm fixed top-0 left-0 h-full z-30 group">
   {{-- Header Sidebar --}}
    <div class="flex items-center justify-center p-4 h-16 shadow">
        <div class="flex items-center gap-3 justify-center">
            <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo" class="h-10 flex-shrink-0">
            <span class="font-bold text-lg sidebar-text text-center">Admin Usulan Kepegawaian</span>
        </div>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="py-4 overflow-y-auto h-[calc(100vh-128px)]"> {{-- Dibuat bisa di-scroll jika menu terlalu panjang --}}
        <div class="space-y-1">

            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="user-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan NUPTK</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="file-bar-chart-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Laporan LKD</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="clipboard-check" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Presensi</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="clock" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Penyesuaian Masa Kerja</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="book-marked" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Ujian Dinas & Ijazah</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="file-user" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Jabatan</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="file-check-2" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Laporan Serdos</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="user-minus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Pensiun</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="trending-up" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Kepangkatan</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="graduation-cap" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Pencantuman Gelar</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="link" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan ID SINTA ke SISTER</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="medal" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Satyalancana</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="book-open" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Tugas Belajar</span>
                </a>
            </div>
            <div class="px-4 relative">
                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i data-lucide="user-plus" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-medium sidebar-text">Usulan Pengaktifan Kembali</span>
                </a>
            </div>

        </div>
    </nav>

    {{-- Bagian Profil di Bawah --}}
    <div class="mt-auto border-t border-gray-200 py-4 px-2 group-[.w-20]:py-2 group-[.w-20]:px-0 transition-all duration-300">
        <div class="flex items-center justify-center">
            <img id="profile-image"
                 src="{{ asset('images/logo-unmul.png') }}"
                 alt="Admin"
                 class="rounded-full object-cover transition-all duration-300
                        h-20 w-20
                        group-[.w-20]:h-12 group-[.w-20]:w-12"
            />
        </div>
    </div>
</aside>
