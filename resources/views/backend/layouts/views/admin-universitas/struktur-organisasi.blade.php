@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Struktur Organisasi - Admin Universitas')

@push('styles')
<style>
    .image-upload-container {
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .image-upload-container:hover {
        border-color: #6366f1;
        background-color: #f8fafc;
    }

    .image-upload-container.dragover {
        border-color: #6366f1;
        background-color: #eef2ff;
    }

    .upload-icon {
        color: #9ca3af;
        transition: color 0.3s ease;
    }

    .image-upload-container:hover .upload-icon {
        color: #6366f1;
    }

    .image-preview {
        max-width: 100%;
        max-height: 400px;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .image-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
    }

    .current-image {
        max-width: 100%;
        max-height: 400px;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .notification {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1000;
        max-width: 400px;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: slideIn 0.3s ease-out;
    }

    .notification.success {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .notification.error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
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
                    Struktur Organisasi
                </h1>
                <p class="text-base text-black sm:text-lg">
                    Kelola Struktur Organisasi Universitas Mulawarman
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="relative z-10 -mt-8 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl pt-4 mt-4 animate-fade-in">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i data-lucide="alert-circle" class="h-5 w-5 mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i data-lucide="alert-circle" class="h-5 w-5 mr-2"></i>
                        <strong>Terjadi kesalahan:</strong>
                    </div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Current Image Display -->
            @if($strukturData)
                <div class="mb-6 bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i data-lucide="image" class="h-6 w-6 text-green-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-xl font-bold text-gray-900">Gambar Saat Ini</h3>
                                    <p class="text-gray-600 text-sm">Struktur organisasi yang sedang aktif</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin-universitas.struktur-organisasi.destroy') }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 flex items-center">
                                    <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <img src="{{ $strukturData['image_url'] }}" alt="Struktur Organisasi" class="current-image mx-auto">
                            @if($strukturData['description'])
                                <p class="mt-4 text-gray-600">{{ $strukturData['description'] }}</p>
                            @endif
                            <p class="mt-2 text-sm text-gray-500">
                                <i data-lucide="calendar" class="h-4 w-4 inline mr-1"></i>
                                Terakhir diupdate: {{ \Carbon\Carbon::parse($strukturData['updated_at'])->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Image Upload Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i data-lucide="upload" class="h-6 w-6 text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ $strukturData ? 'Update Struktur Organisasi' : 'Upload Struktur Organisasi' }}
                            </h3>
                            <p class="text-gray-600 text-sm">
                                {{ $strukturData ? 'Upload gambar baru untuk mengganti yang lama' : 'Upload gambar struktur organisasi universitas' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Image Upload Form -->
                    <form method="POST" action="{{ route('admin-universitas.struktur-organisasi.store') }}" enctype="multipart/form-data" id="imageUploadForm">
                        @csrf
                        <div class="space-y-6">
                            <!-- File Upload -->
                            <div>
                                <label for="struktur_image" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Gambar <span class="text-red-500">*</span>
                                </label>
                                <div class="image-upload-container p-8 text-center cursor-pointer" onclick="document.getElementById('struktur_image').click()">
                                    <input type="file" id="struktur_image" name="struktur_image" accept="image/*" class="hidden" required>
                                    <div class="space-y-4">
                                        <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i data-lucide="image" class="h-8 w-8 upload-icon"></i>
                                        </div>
                                        <div>
                                            <p class="text-lg font-medium text-gray-900">Klik untuk upload gambar</p>
                                            <p class="text-sm text-gray-500">atau drag & drop gambar di sini</p>
                                        </div>
                                        <p class="text-xs text-gray-400">
                                            Format yang didukung: JPG, PNG, GIF (Maksimal 5MB)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div id="imagePreviewSection" class="hidden">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Preview Gambar</h4>
                                <div class="relative inline-block">
                                    <img id="imagePreview" class="image-preview" alt="Preview">
                                    <div class="image-actions">
                                        <button type="button" onclick="removePreview()" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                            <i data-lucide="x" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea id="description" name="description" rows="3"
                                          placeholder="Masukkan deskripsi struktur organisasi (opsional)..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                            <button type="button" onclick="resetForm()"
                                    class="px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg">
                                Reset
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <span id="submitBtnText">{{ $strukturData ? 'Update Struktur Organisasi' : 'Upload Struktur Organisasi' }}</span>
                                <i data-lucide="loader-2" class="h-4 w-4 animate-spin hidden ml-2" id="submitBtnLoader"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    try {
        initializeImageUpload();
        initializeLucideIcons();
    } catch (error) {
        console.error('Error initializing page:', error);
    }
});

// Initialize image upload functionality
function initializeImageUpload() {
    const fileInput = document.getElementById('struktur_image');
    const previewSection = document.getElementById('imagePreviewSection');
    const previewImage = document.getElementById('imagePreview');
    const uploadContainer = document.querySelector('.image-upload-container');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewSection.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Drag and drop functionality
    if (uploadContainer) {
        uploadContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadContainer.classList.add('dragover');
        });

        uploadContainer.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadContainer.classList.remove('dragover');
        });

        uploadContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadContainer.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }
}

// Remove preview function
function removePreview() {
    const fileInput = document.getElementById('struktur_image');
    const previewSection = document.getElementById('imagePreviewSection');

    if (fileInput) {
        fileInput.value = '';
    }

    if (previewSection) {
        previewSection.classList.add('hidden');
    }
}

// Reset form function
function resetForm() {
    const form = document.getElementById('imageUploadForm');
    const previewSection = document.getElementById('imagePreviewSection');

    if (form) {
        form.reset();
    }

    if (previewSection) {
        previewSection.classList.add('hidden');
    }
}

// Initialize Lucide icons
function initializeLucideIcons() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Form submission handling
document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnLoader = document.getElementById('submitBtnLoader');

    if (submitBtn && submitBtnText && submitBtnLoader) {
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Mengupload...';
        submitBtnLoader.classList.remove('hidden');
    }
});
</script>
@endsection
