{{-- Script untuk Pegawai UNMUL --}}
@if(Request::is('pegawai-unmul/*'))
@push('scripts')
<script>
// ========================================
// GLOBAL FUNCTIONS FOR PEGAWAI UNMUL
// ========================================

// Enhanced file preview with validation (Profile & Usulan)
function previewUploadedFile(input, previewId) {
    const file = input.files[0];
    const previewElement = document.getElementById(previewId);
    const progressElement = document.getElementById('progress-' + input.name);

    if (file) {
        // Validate file type
        if (file.type !== 'application/pdf') {
            alert('Hanya file PDF yang diperbolehkan!');
            input.value = '';
            return;
        }

        // Validate file size (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            alert('Ukuran file maksimal 2MB!');
            input.value = '';
            return;
        }

        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileName = file.name;

        // Show progress (for profile upload)
        if (progressElement) {
            progressElement.classList.remove('hidden');
            let progress = 0;
            const progressBar = progressElement.querySelector('div div');
            const interval = setInterval(() => {
                progress += Math.random() * 30;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    setTimeout(() => {
                        progressElement.classList.add('hidden');
                    }, 1000);
                }
                progressBar.style.width = progress + '%';
            }, 200);
        }

        // Enhanced preview for profile pages
        if (previewElement) {
            previewElement.innerHTML = `
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                            <i data-lucide="file-check" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-green-800 truncate">${fileName}</p>
                            <div class="flex items-center gap-4 mt-1">
                                <span class="text-xs text-green-600">Ukuran: ${fileSize} MB</span>
                                <span class="text-xs text-green-600 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                                    Siap diupload
                                </span>
                            </div>
                        </div>
                        <button type="button" onclick="clearFilePreview('${input.id}', '${previewId}')"
                                class="flex-shrink-0 text-green-600 hover:text-green-800">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;
            previewElement.classList.remove('hidden');
        }

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    } else if (previewElement) {
        previewElement.classList.add('hidden');
    }
}

// Image preview for foto pegawai
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

// Profile photo preview (specific for header photo)
function previewProfilePhoto(input) {
    const file = input.files[0];
    const previewElement = document.getElementById('preview-foto');
    const profilePhoto = document.getElementById('profile-photo');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (profilePhoto) {
                profilePhoto.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);

        // Show file info
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileName = file.name;

        if (previewElement) {
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
        }

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    } else if (previewElement) {
        previewElement.classList.add('hidden');
    }
}

// Clear file preview
function clearFilePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (input) input.value = '';
    if (preview) preview.classList.add('hidden');
}

// Handle document access with loading state
function handleDocumentAccess(event, element) {
    const originalText = element.innerHTML;
    element.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Memuat...';
    element.classList.add('pointer-events-none');

    // Reset after 3 seconds if still loading
    setTimeout(() => {
        element.innerHTML = originalText;
        element.classList.remove('pointer-events-none');
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 3000);
}

// ========================================
// USULAN SPECIFIC FUNCTIONS
// ========================================

// Alpine.js component for usulan form
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

// Show usulan log modal
function showLogModal(usulanId) {
    const modal = document.getElementById('logModal');
    const content = document.getElementById('logContent');

    if (!modal || !content) return;

    modal.classList.remove('hidden');
    content.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';

    fetch(`/pegawai-unmul/usulan/${usulanId}/logs`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
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
    const modal = document.getElementById('logModal');
    if (modal) modal.classList.add('hidden');
}

function showAllLogs(usulanId) {
    showLogModal(usulanId);
}

// Render logs HTML
function renderLogs(logs) {
    if (!logs || logs.length === 0) {
        return '<p class="text-gray-500 text-center py-8">Belum ada log aktivitas.</p>';
    }

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

    let html = '<div class="space-y-4">';
    logs.forEach((log, index) => {
        const isLast = index === logs.length - 1;
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

// ========================================
// PROFILE SPECIFIC FUNCTIONS
// ========================================

// Update upload summary for profile documents
function updateUploadSummary() {
    const inputs = document.querySelectorAll('input[type="file"]');
    let newUploads = 0;

    inputs.forEach(input => {
        if (input.files && input.files.length > 0) {
            newUploads++;
        }
    });
    // Additional logic for summary updates can be added here
}

// ========================================
// SIDEBAR & GENERAL UI
// ========================================

// Sidebar toggle functionality
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    if (!sidebar || !mainContent) return;

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
}

// ========================================
// DOM READY INITIALIZATION
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Initialize sidebar
    initializeSidebar();

    // Event listeners for file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', updateUploadSummary);
    });

    // Modal event listeners
    const logModal = document.getElementById('logModal');
    if (logModal) {
        logModal.addEventListener('click', function(e) {
            if (e.target === this) closeLogModal();
        });
    }

    // Keyboard event listeners
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLogModal();
    });

    // Re-initialize icons on various events
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
});
</script>
@endpush
@endif
