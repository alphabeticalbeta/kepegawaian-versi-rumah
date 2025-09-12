@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Visi dan Misi - Admin Universitas')

@push('styles')
<style>
    /* Hide scrollbar for table container */
    .overflow-x-auto::-webkit-scrollbar {
        display: none;
    }

    .overflow-x-auto {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Rich Text Editor Styles */
    #editor {
        min-height: 200px;
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        color: #374151;
    }

    #editor:focus {
        outline: none;
    }

    #editor p {
        margin-bottom: 1rem;
    }

    #editor ul, #editor ol {
        margin: 1rem 0;
        padding-left: 2rem;
    }

    #editor ul {
        list-style-type: disc;
    }

    #editor ol {
        list-style-type: decimal;
    }

    #editor li {
        margin-bottom: 0.5rem;
    }

    #editor strong {
        font-weight: 600;
    }

    #editor em {
        font-style: italic;
    }

    #editor u {
        text-decoration: underline;
    }

    /* Modern Notification Styles */
    #notificationContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .notification {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        margin-bottom: 12px;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid;
        overflow: hidden;
    }

    .notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification.success {
        border-left-color: #10b981;
    }

    .notification.error {
        border-left-color: #ef4444;
    }

    .notification-content {
        display: flex;
        align-items: center;
        padding: 16px;
        gap: 12px;
    }

    .notification-icon {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
    }

    .notification.success .notification-icon {
        color: #10b981;
    }

    .notification.error .notification-icon {
        color: #ef4444;
    }

    .notification-text {
        flex: 1;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        line-height: 1.4;
    }

    .notification-close {
        flex-shrink: 0;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .notification-close:hover {
        color: #6b7280;
        background: #f3f4f6;
    }
</style>
@endpush

@section('content')
<!-- Notification Container -->
<div id="notificationContainer"></div>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header Section -->
    <div class="relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative px-4 py-8 sm:px-6 sm:py-10">
            <div class="mx-auto max-w-full text-center">
                <div class="mb-4 flex justify-center">
                    <img src="{{ asset('images/logo-unmul.png') }}" alt="Logo UNMUL" class="h-20 w-auto object-contain">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-black sm:text-3xl mb-2">
                    Visi dan Misi
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola Visi dan Misi Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-full pt-4 mt-4 animate-fade-in">

            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Table Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Visi dan Misi</h3>
                    <div class="text-sm text-gray-500">
                        <i data-lucide="info" class="h-4 w-4 inline mr-1"></i>
                        Klik Edit untuk mengubah data
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-16 text-center">No</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-24 text-center">Jenis</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider text-center">Konten</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-24 text-center">Status</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-28 text-center">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-bold text-black uppercase tracking-wider w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($visiMisi as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center text-sm text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item->jenis === 'visi' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($item->jenis) }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 text-sm text-gray-900 max-w-xs">
                                    <div class="font-medium text-gray-900 truncate" title="{{ strip_tags($item->konten) }}">
                                        {{ Str::limit(strip_tags($item->konten), 100) }}
                                    </div>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-2 sm:px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                    <div class="flex items-center justify-center">
                                        <button onclick="editVisiMisi({{ $item->id }})" class="edit-btn inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg" data-id="{{ $item->id }}" style="position: relative; z-index: 10;"
                                                title="Edit"
                                                data-jenis="{{ htmlspecialchars($item->jenis, ENT_QUOTES, 'UTF-8') }}"
                                                data-konten="{{ htmlspecialchars($item->konten, ENT_QUOTES, 'UTF-8') }}"
                                                data-status="{{ htmlspecialchars($item->status, ENT_QUOTES, 'UTF-8') }}">
                                            <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="file-text" class="h-10 w-10 text-gray-300 mb-3"></i>
                                        <p class="text-base font-medium">Belum ada data</p>
                                        <p class="text-sm">Data visi dan misi akan ditampilkan di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="visiMisiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-2xl bg-white transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Edit Visi/Misi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="visiMisiForm" class="mt-6" action="" method="POST">
                <input type="hidden" id="editId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="PUT">
                @csrf

                <div class="space-y-6">
                    <!-- Jenis -->
                    <div>
                        <label for="jenis" class="block text-sm font-semibold text-gray-700 mb-3">
                            Jenis <span class="text-gray-400">(Tidak dapat diubah)</span>
                        </label>
                        <select id="jenis" name="jenis" required disabled
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed transition-all duration-200">
                            <option value="visi">Visi</option>
                            <option value="misi">Misi</option>
                        </select>
                        <!-- Hidden input to ensure value is sent even when disabled -->
                        <input type="hidden" id="jenisHidden" name="jenis">
                        <p class="text-xs text-gray-500 mt-1">
                            <i data-lucide="lock" class="h-3 w-3 inline mr-1"></i>
                            Jenis tidak dapat diubah setelah data dibuat.
                        </p>
                    </div>

                    <!-- Konten -->
                    <div>
                        <label for="konten" class="block text-sm font-semibold text-gray-700 mb-3">
                            Konten <span class="text-red-500">*</span>
                        </label>
                        <!-- Rich Text Editor Toolbar -->
                        <div class="border border-gray-300 rounded-t-lg bg-gray-50 p-2">
                            <div class="flex flex-wrap gap-1">
                                <button type="button" class="ql-bold p-2 hover:bg-gray-200 rounded" title="Bold">
                                    <i data-lucide="bold" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-italic p-2 hover:bg-gray-200 rounded" title="Italic">
                                    <i data-lucide="italic" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-underline p-2 hover:bg-gray-200 rounded" title="Underline">
                                    <i data-lucide="underline" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-list p-2 hover:bg-gray-200 rounded" value="ordered" title="Numbered List">
                                    <i data-lucide="list-ordered" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-list p-2 hover:bg-gray-200 rounded" value="bullet" title="Bullet List">
                                    <i data-lucide="list" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="" title="Align Left">
                                    <i data-lucide="align-left" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="center" title="Align Center">
                                    <i data-lucide="align-center" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-align p-2 hover:bg-gray-200 rounded" value="right" title="Align Right">
                                    <i data-lucide="align-right" class="h-4 w-4"></i>
                                </button>
                                <div class="w-px h-6 bg-gray-300 mx-1"></div>
                                <button type="button" class="ql-indent p-2 hover:bg-gray-200 rounded" value="-1" title="Decrease Indent">
                                    <i data-lucide="indent-decrease" class="h-4 w-4"></i>
                                </button>
                                <button type="button" class="ql-indent p-2 hover:bg-gray-200 rounded" value="+1" title="Increase Indent">
                                    <i data-lucide="indent-increase" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                        <div id="editor" contenteditable="true" class="w-full px-4 py-3 border border-gray-300 border-t-0 rounded-b-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400"
                             placeholder="Masukkan konten visi/misi..."></div>
                        <input type="hidden" id="konten" name="konten">
                        <p class="text-xs text-gray-500 mt-1">
                            <i data-lucide="info" class="h-3 w-3 inline mr-1"></i>
                            Maksimal 5000 karakter. Gunakan toolbar untuk memformat teks.
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 hover:border-indigo-400">
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                            class="px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <span id="submitText">Simpan</span>
                        <div id="submitLoader" class="hidden ml-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
let currentEditId = null;

// Escape HTML function for security
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    try {
        initializeForm();
        initializeRichTextEditor();

        // Initialize Lucide icons safely
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    } catch (error) {
        console.error('Error initializing page:', error);
    }
});

// Initialize form
function initializeForm() {
    const form = document.getElementById('visiMisiForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}


// Initialize rich text editor
function initializeRichTextEditor() {
    const editorElement = document.getElementById('editor');
    if (editorElement) {
        // Initialize toolbar buttons
        const toolbarButtons = document.querySelectorAll('.ql-bold, .ql-italic, .ql-underline, .ql-list, .ql-align, .ql-indent');

        toolbarButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const command = this.classList[0].replace('ql-', '');
                const value = this.getAttribute('value') || null;

                if (command === 'list') {
                    document.execCommand('insertOrderedList', false, null);
                } else if (command === 'align') {
                    document.execCommand('justify' + (value || 'Left'), false, null);
                } else if (command === 'indent') {
                    if (value === '+1') {
                        document.execCommand('indent', false, null);
                    } else {
                        document.execCommand('outdent', false, null);
                    }
                } else {
                    document.execCommand(command, false, null);
                }

                // Update hidden input
                updateHiddenContent();
            });
        });

        // Update hidden input when content changes
        editorElement.addEventListener('input', updateHiddenContent);
        editorElement.addEventListener('paste', function(e) {
            setTimeout(updateHiddenContent, 100);
        });
    }
}

// Update hidden content input
function updateHiddenContent() {
    const editor = document.getElementById('editor');
    const hiddenInput = document.getElementById('konten');
    if (editor && hiddenInput) {
        hiddenInput.value = editor.innerHTML;
    }
}

// Open modal function (only for edit)
function openModal(isEdit = true) {
    try {
        console.log('openModal called with isEdit:', isEdit);

        const modal = document.getElementById('visiMisiModal');
        const modalContent = document.getElementById('modalContent');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('visiMisiForm');
        const formMethod = document.getElementById('formMethod');

        if (!modal || !modalContent || !modalTitle || !form || !formMethod) {
            console.error('Modal elements not found');
            return;
        }

        modalTitle.textContent = 'Edit Visi/Misi';
        formMethod.value = 'PUT';

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    } catch (error) {
        console.error('Error in openModal:', error);
    }
}

// Close modal function
function closeModal() {
    const modal = document.getElementById('visiMisiModal');
    const modalContent = document.getElementById('modalContent');

    if (modal && modalContent) {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            currentEditId = null;
        }, 300);
    }
}

// Edit visi misi function
function editVisiMisi(id) {
    try {
        console.log('editVisiMisi called with id:', id);

        // Get the button that was clicked
        const button = document.querySelector(`.edit-btn[data-id="${id}"]`);
        console.log('Button found:', button);

        if (!button) {
            console.error('Edit button not found for id:', id);
            return;
        }

        // Get data from data attributes
        const jenis = button.getAttribute('data-jenis') || '';
        const konten = button.getAttribute('data-konten') || '';
        const status = button.getAttribute('data-status') || '';

        // Set form action first
        const form = document.getElementById('visiMisiForm');
        if (form) {
            form.action = `{{ route('admin-universitas.visi-misi.update', ':id') }}`.replace(':id', id);
            form.method = 'POST'; // Laravel method spoofing
        }

        // Fill form fields with null checks
        const jenisElement = document.getElementById('jenis');
        const jenisHiddenElement = document.getElementById('jenisHidden');
        if (jenisElement) {
            jenisElement.value = jenis;
        }
        if (jenisHiddenElement) {
            jenisHiddenElement.value = jenis;
        }

        const kontenElement = document.getElementById('konten');
        if (kontenElement) kontenElement.value = konten;

        // Also update the rich text editor
        const editorElement = document.getElementById('editor');
        if (editorElement && konten) {
            editorElement.innerHTML = konten;
        }

        const statusElement = document.getElementById('status');
        if (statusElement) statusElement.value = status;

        // Set edit ID
        const editIdElement = document.getElementById('editId');
        if (editIdElement) editIdElement.value = id;

        currentEditId = id;

        // Show modal last
        openModal(true);
    } catch (error) {
        console.error('Error in editVisiMisi:', error);
    }
}

// Delete function removed - only update is allowed

// Handle form submission
function handleFormSubmit(event) {
    // Update hidden content before submit
    updateHiddenContent();

    // Show loading state with animation
    const submitButton = event.target.querySelector('button[type="submit"]');
    const submitText = submitButton.querySelector('#submitText');
    const submitLoader = submitButton.querySelector('#submitLoader');

    if (submitButton && submitText && submitLoader) {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-75', 'cursor-not-allowed');
        submitText.textContent = 'Menyimpan...';
        submitLoader.classList.remove('hidden');

        // Add pulse animation
        submitButton.classList.add('animate-pulse');
    }

    // Let the form submit naturally (server-side)
}

// Modern notification function
function showNotification(message, type = 'success') {
    const container = document.getElementById('notificationContainer');
    const notification = document.createElement('div');

    const icon = type === 'success'
        ? '<svg class="notification-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
        : '<svg class="notification-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

    notification.className = `notification ${type} show`;
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                ${icon}
            </div>
            <div class="notification-text">
                ${message}
            </div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Check for flash messages and show notifications
@if(session('success'))
    showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showNotification('{{ session('error') }}', 'error');
@endif

</script>
@endpush
@endsection
