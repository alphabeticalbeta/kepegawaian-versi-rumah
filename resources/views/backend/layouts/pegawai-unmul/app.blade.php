<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Pegawai') - Kepegawaian UNMUL</title>

    {{-- Memuat CSS dari Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Memuat pustaka ikon Lucide --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-slate-100">

    {{-- Container utama menggunakan Flexbox --}}
    <div class="flex h-screen">

        {{-- Sidebar dipanggil di sini --}}
        @include('backend.components.sidebar-pegawai-unmul')

        {{-- Kontainer untuk Header dan Konten Utama --}}
        <div id="main-content" class="flex-1 flex flex-col transition-all duration-300 ml-64">

            {{-- Header dipanggil di sini --}}
            @include('backend.components.header')

            {{-- Konten Dinamis dengan scroll internal --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ====================================================================== --}}
    {{-- =================== BLOK SCRIPT YANG DIPERBAIKI ====================== --}}
    {{-- ====================================================================== --}}
    <script>
        function previewUploadedFile(input, previewId) {
            console.log('Function called with:', input, previewId);

            const file = input.files[0];
            const previewElement = document.getElementById(previewId);

            console.log('File:', file);
            console.log('Preview element:', previewElement);

            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileName = file.name;

                console.log('File name:', fileName, 'Size:', fileSize);

                previewElement.innerHTML = `
                    <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <i data-lucide="file-check" class="w-4 h-4 text-blue-600"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-800">${fileName}</p>
                                <p class="text-xs text-blue-600">Ukuran: ${fileSize} MB</p>
                            </div>
                            <div class="text-green-600">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                `;
                previewElement.classList.remove('hidden');

                // Re-initialize Lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                previewElement.classList.add('hidden');
            }
        }

        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = input.closest('.relative').querySelector('img');
                    if (imgElement) {
                        imgElement.src = e.target.result;
                    }
                    previewUploadedFile(input, 'preview-foto');
                }
                reader.readAsDataURL(file);
            }
        }

        function usulanForm() {
            return {
                action: 'save_draft',
                isSubmitting: false,
                showConfirmModal: false,

                submitForm() {
                    if (this.action === 'submit_final') {
                        this.showConfirmModal = true;
                    } else {
                        this.doSubmit();
                    }
                },

                confirmSubmit() {
                    this.showConfirmModal = false;
                    this.doSubmit();
                },

                doSubmit() {
                    this.isSubmitting = true;
                    this.$el.submit();
                }
            }
        }

        function showLogModal(usulanId) {
            const modal = document.getElementById('logModal');
            const content = document.getElementById('logContent');

            // Show modal
            modal.classList.remove('hidden');

            // Show loading
            content.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';

            // Load log data - FIX: URL yang benar sesuai route
            fetch(`/pegawai-unmul/usulan/${usulanId}/logs`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        content.innerHTML = renderLogs(data.logs);
                    } else {
                        content.innerHTML = '<p class="text-red-500 text-center">Gagal memuat log aktivitas.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = '<p class="text-red-500 text-center">Gagal memuat log aktivitas. Silakan coba lagi.</p>';
                });
        }

        function closeLogModal() {
            document.getElementById('logModal').classList.add('hidden');
        }

        function renderLogs(logs) {
            if (!logs || logs.length === 0) {
                return '<p class="text-gray-500 text-center py-8">Belum ada log aktivitas.</p>';
            }

            let html = '<div class="space-y-4">';

            logs.forEach((log, index) => {
                const isLast = index === logs.length - 1;
                const statusColors = {
                    'Draft': 'bg-gray-100 text-gray-800 border-gray-300',
                    'Diajukan': 'bg-blue-100 text-blue-800 border-blue-300',
                    'Sedang Direview': 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Perlu Perbaikan': 'bg-orange-100 text-orange-800 border-orange-300',
                    'Dikembalikan': 'bg-red-100 text-red-800 border-red-300',
                    'Disetujui': 'bg-green-100 text-green-800 border-green-300',
                    'Direkomendasikan': 'bg-purple-100 text-purple-800 border-purple-300',
                    'Ditolak': 'bg-red-100 text-red-800 border-red-300'
                };

                const statusClass = statusColors[log.status] || 'bg-gray-100 text-gray-800 border-gray-300';

                html += `
                    <div class="flex items-start space-x-4 relative">
                        ${!isLast ? '<div class="absolute left-4 top-8 w-0.5 h-full bg-gray-300"></div>' : ''}
                        <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 ${statusClass} flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-current"></div>
                        </div>
                        <div class="flex-1 min-w-0 pb-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">${log.status}</p>
                                <p class="text-xs text-gray-500">${log.formatted_date}</p>
                            </div>
                            ${log.keterangan ? `<p class="text-sm text-gray-600 mt-1">${log.keterangan}</p>` : ''}
                            ${log.user_name ? `<p class="text-xs text-gray-500 mt-1">oleh: ${log.user_name}</p>` : ''}
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            return html;
        }

        function showAllLogs(usulanId) {
            showLogModal(usulanId);
        }


        // ========================================
        // DOM READY EVENTS
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi ikon Lucide
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Event listeners untuk re-create icons
        document.addEventListener('click', () => {
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        });

        document.addEventListener('alpine:initialized', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        document.getElementById('logModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogModal();
            }
        });

            // Sidebar functionality
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            let isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            const applySidebarState = (collapsed) => {
                const sidebarTexts = sidebar.querySelectorAll('.sidebar-text');

                if (collapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    mainContent.classList.remove('ml-64');
                    mainContent.classList.add('ml-20');
                    sidebarTexts.forEach(text => text.classList.add('hidden'));
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    mainContent.classList.remove('ml-20');
                    mainContent.classList.add('ml-64');
                    sidebarTexts.forEach(text => text.classList.remove('hidden'));
                }
                localStorage.setItem('sidebarCollapsed', collapsed);
            };

            applySidebarState(isSidebarCollapsed);

            window.toggleSidebar = function() {
                isSidebarCollapsed = !isSidebarCollapsed;
                applySidebarState(isSidebarCollapsed);
            }
        });


    </script>
    @stack('scripts')
</body>
</html>
