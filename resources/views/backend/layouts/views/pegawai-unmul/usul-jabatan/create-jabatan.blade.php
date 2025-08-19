<!-- create-jabatan.blade.php - FIXED VERSION -->
@extends('backend.layouts.roles.pegawai-unmul.app')

@php
    // Set default values for variables that might not be defined
    $isEditMode = $isEditMode ?? false;
    $isReadOnly = $isReadOnly ?? false;
    $isShowMode = $isShowMode ?? false;
    $existingUsulan = $existingUsulan ?? null;
    $daftarPeriode = $daftarPeriode ?? null;
    $pegawai = $pegawai ?? null;
    $usulan = $usulan ?? null;
    $jabatanTujuan = $jabatanTujuan ?? null;
    $jenjangType = $jenjangType ?? null;
    $formConfig = $formConfig ?? [];
    $jenisUsulanPeriode = $jenisUsulanPeriode ?? null;
    $bkdSemesters = $bkdSemesters ?? [];
    $documentFields = $documentFields ?? [];
    $catatanPerbaikan = $catatanPerbaikan ?? [];

    // Get validation data from all roles for edit mode
    $validationData = [];
    if ($isEditMode && $usulan) {
        $roles = ['admin_fakultas', 'admin_universitas', 'tim_penilai'];

        foreach ($roles as $role) {
            $roleData = $usulan->getValidasiByRole($role);
            if (!empty($roleData['validation'])) {
                $validationData[$role] = $roleData['validation'];
            }
        }
    }

    // Function to check if field has validation issues
    function hasValidationIssue($fieldGroup, $fieldName, $validationData) {
        foreach ($validationData as $role => $data) {
            if (isset($data[$fieldGroup][$fieldName]['status']) &&
                $data[$fieldGroup][$fieldName]['status'] === 'tidak_sesuai') {
                return true;
            }
        }
        return false;
    }

    // Function to get validation notes for a field
    function getValidationNotes($fieldGroup, $fieldName, $validationData) {
        $notes = [];
        foreach ($validationData as $role => $data) {
            if (isset($data[$fieldGroup][$fieldName]['keterangan']) &&
                !empty($data[$fieldGroup][$fieldName]['keterangan'])) {
                $roleName = str_replace('_', ' ', ucfirst($role));
                $notes[] = "<strong>{$roleName}:</strong> " . $data[$fieldGroup][$fieldName]['keterangan'];
            }
        }
        return $notes;
    }

    // Function to get all validation notes for a field (for display)
    function getAllValidationNotes($fieldGroup, $fieldName, $validationData) {
        $notes = [];
        foreach ($validationData as $role => $data) {
            if (isset($data[$fieldGroup][$fieldName]['keterangan']) &&
                !empty($data[$fieldGroup][$fieldName]['keterangan'])) {
                $roleName = str_replace('_', ' ', ucfirst($role));
                $notes[] = [
                    'role' => $roleName,
                    'note' => $data[$fieldGroup][$fieldName]['keterangan'],
                    'status' => $data[$fieldGroup][$fieldName]['status'] ?? 'tidak_sesuai'
                ];
            }
        }
        return $notes;
    }
@endphp

@section('title', $isShowMode ? 'Detail Usulan Jabatan' : ($isEditMode ? 'Edit Usulan Jabatan' : 'Buat Usulan Jabatan'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isShowMode ? 'Detail Usulan Jabatan' : ($isEditMode ? 'Edit Usulan Jabatan' : 'Buat Usulan Jabatan') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $isShowMode ? 'Detail lengkap usulan jabatan fungsional dosen' : ($isEditMode ? 'Perbarui usulan jabatan fungsional dosen' : 'Formulir pengajuan jabatan fungsional dosen') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($isShowMode)
                        <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}"
                           class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                            Kembali ke Usulan Saya
                        </a>
                    @else
                        <a href="{{ route('pegawai-unmul.usulan-jabatan.index') }}"
                           class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                            Kembali
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Notification for revision status in edit mode --}}
        @if($isEditMode && $usulan && $usulan->status_usulan === 'Perlu Perbaikan' && $usulan->catatan_verifikator)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">
                        Usulan Dikembalikan untuk Perbaikan
                    </h3>
                    <div class="mt-2 text-sm text-amber-700">
                        <p class="mb-2"><strong>Catatan dari Admin Fakultas:</strong></p>
                        <p class="bg-white p-3 rounded border border-amber-200">{{ $usulan->catatan_verifikator }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @php
            // Cek kelengkapan data profil
            $requiredFields = [
                'nama_lengkap', 'nip', 'email', 'tempat_lahir', 'tanggal_lahir',
                'jenis_kelamin', 'nomor_handphone', 'gelar_depan', 'gelar_belakang',
                'ijazah_terakhir', 'transkrip_nilai_terakhir', 'sk_pangkat_terakhir',
                'sk_jabatan_terakhir', 'skp_tahun_pertama', 'skp_tahun_kedua'
            ];

            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($pegawai->$field)) {
                    $missingFields[] = $field;
                }
            }

            $isProfileComplete = empty($missingFields);
            $canProceed = $isProfileComplete;

            // Jika mode edit atau show, pastikan form tetap ditampilkan
            if ($isEditMode || $isShowMode) {
                $canProceed = true;
            }
        @endphp

        {{-- Profile Completeness Check --}}
        @if(!$isProfileComplete && !$isShowMode)
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Profil Belum Lengkap</h3>
                        <p class="text-sm mt-1">Anda harus melengkapi data profil terlebih dahulu sebelum dapat membuat usulan jabatan.</p>
                        <div class="mt-3">
                            <h4 class="font-medium text-sm">Field yang perlu dilengkapi:</h4>
                            <ul class="text-sm mt-1 list-disc list-inside space-y-1">
                                @foreach($missingFields as $field)
                                    <li class="capitalize">{{ str_replace('_', ' ', $field) }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}"
                               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                                Lengkapi Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(!$isShowMode)
            {{-- Success Notification --}}
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Profil Lengkap</h3>
                        <p class="text-sm mt-1">Data profil Anda sudah lengkap. Anda dapat melanjutkan untuk membuat usulan jabatan.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Period Availability Check --}}
        @if((!isset($daftarPeriode) || empty($daftarPeriode)) && !$isShowMode)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Tidak Ada Periode Aktif</h3>
                        <p class="text-sm mt-1">Saat ini tidak ada periode usulan yang sedang berlangsung. Silakan cek kembali nanti.</p>
                    </div>
                </div>
            </div>
            @php $canProceed = false; @endphp
        @endif

        {{-- Existing Usulan Check --}}
        @if(isset($existingUsulan) && $existingUsulan && !$isEditMode && !$isShowMode)
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="w-6 h-6 text-blue-600 mt-0.5"></i>
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg">Usulan Sudah Ada</h3>
                        <p class="text-sm mt-1">Anda sudah memiliki usulan jabatan untuk periode ini dengan status: <strong>{{ $existingUsulan->status_usulan }}</strong></p>
                        <div class="mt-3">
                            <a href="{{ route('pegawai-unmul.usulan-jabatan.edit', $existingUsulan->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                                Edit Usulan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @php $canProceed = false; @endphp
        @endif

        {{-- Form Content --}}
        @if($canProceed)
            @if($isEditMode)
                <form id="usulan-form" action="{{ route('pegawai-unmul.usulan-jabatan.update', $usulan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
            @elseif(!$isShowMode)
                <form id="usulan-form" action="{{ route('pegawai-unmul.usulan-jabatan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
            @endif

            {{-- Informasi Periode Usulan --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i data-lucide="calendar-clock" class="w-6 h-6 mr-3"></i>
                        Informasi Periode Usulan
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Periode</label>
                            <p class="text-xs text-gray-600 mb-2">Periode usulan yang sedang berlangsung</p>
                            <input type="text" value="{{ $daftarPeriode->nama_periode ?? 'Tidak ada periode aktif' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                            <input type="hidden" name="periode_usulan_id" value="{{ $daftarPeriode->id ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</label>
                            <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                            <input type="text" value="{{ $daftarPeriode ? \Carbon\Carbon::parse($daftarPeriode->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($daftarPeriode->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Pegawai --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                    <h2 class="text-xl font-bold text-black flex items-center">
                        <i data-lucide="user" class="w-6 h-6 mr-3"></i>
                        Informasi Usulan Pegawai
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Nama Lengkap</label>
                            <p class="text-xs text-gray-600 mb-2">Nama lengkap pegawai</p>
                            <input type="text" value="{{ $pegawai->nama_lengkap ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">NIP</label>
                            <p class="text-xs text-gray-600 mb-2">Nomor Induk Pegawai</p>
                            <input type="text" value="{{ $pegawai->nip ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Jabatan Sekarang</label>
                            <p class="text-xs text-gray-600 mb-2">Jabatan fungsional saat ini</p>
                            <input type="text" value="{{ $pegawai->jabatan->jabatan ?? '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Jabatan yang Dituju</label>
                            <p class="text-xs text-gray-600 mb-2">Jabatan fungsional yang diajukan</p>
                            <input type="text" value="{{ $pegawai->jabatan && $pegawai->jabatan->getNextLevel() ? $pegawai->jabatan->getNextLevel()->jabatan : '-' }}"
                                   class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Display Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.profile-display')

            {{-- Karya Ilmiah Section Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.karya-ilmiah-section')

            {{-- Dokumen Upload Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.dokumen-upload')

            {{-- BKD Upload Component --}}
            @include('backend.layouts.views.pegawai-unmul.usul-jabatan.components.bkd-upload')

            {{-- Form Actions --}}
            @if(!$isShowMode)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                        Pastikan semua data yang diperlukan telah diisi dengan benar
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="history.back()"
                                class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" name="action" value="save_draft"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Usulan
                        </button>
                        <button type="submit" name="action" value="submit"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Kirim Usulan
                        </button>
                    </div>
                </div>
            </div>
            @endif

            </form>
        @else
            {{-- Cannot Proceed Message --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                <i data-lucide="alert-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak Dapat Melanjutkan</h3>
                <p class="text-gray-600">Silakan perbaiki masalah di atas terlebih dahulu sebelum dapat membuat usulan jabatan.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation - SIMPLIFIED FOR TESTING
    const form = document.getElementById('usulan-form');
    if (form) {
        console.log('Form found, simplified validation for testing');

        // Add debug logging
        form.addEventListener('submit', function(e) {
            console.log('Form submission attempted');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);

            // Log all form data
            const formData = new FormData(form);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            // SIMPLIFIED VALIDATION - Only check basic required fields
            const basicRequiredFields = form.querySelectorAll('input[name="periode_usulan_id"], input[name="action"]');
            let isValid = true;

            basicRequiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    console.log('Basic required field empty:', field.name);
                }
            });

            if (!isValid) {
                e.preventDefault();
                console.log('Basic validation failed - preventing submission');
                alert('Mohon pilih periode dan action.');
            } else {
                console.log('Basic validation passed - allowing submission');
                // Don't prevent default - let form submit
            }
        });
    } else {
        console.log('Form not found');
    }
});

// Test form submission function
function testFormSubmission() {
    console.log('=== TEST FORM SUBMISSION ===');

    const form = document.getElementById('usulan-form');
    if (!form) {
        console.log('Form not found');
        return;
    }

    // Collect form data
    const formData = new FormData(form);
    const data = {};

    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }

    console.log('Form data to submit:', data);

    // Submit via AJAX to test route
    fetch('/test-usulan-submission', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('✅ Test submission successful! Usulan ID: ' + data.usulan_id);
        } else {
            alert('❌ Test submission failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Test submission error: ' + error.message);
    });
}
</script>
@endsection
