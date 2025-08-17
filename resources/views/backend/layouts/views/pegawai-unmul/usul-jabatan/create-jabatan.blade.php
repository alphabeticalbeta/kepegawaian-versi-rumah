<!-- create-jabatan.blade.php - FIXED VERSION -->
@extends('backend.layouts.roles.pegawai-unmul.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @php
        // Define read-only status
        $isReadOnly = isset($usulan) && $usulan->exists && in_array($usulan->status_usulan, [
            'Diajukan', 'Sedang Direview', 'Disetujui', 'Direkomendasikan'
        ]);

        // Define editable status
        $canEdit = !$isReadOnly && (!isset($usulan) || !$usulan->exists || in_array($usulan->status_usulan, [
            'Draft', 'Perlu Perbaikan', 'Dikembalikan'
        ]));

        // FIX: Ensure usulan exists check is proper
        $isEditMode = isset($usulan) && $usulan->exists && $usulan->id;
    @endphp

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <div class="py-1">
                    <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="py-1">
                    <svg class="h-5 w-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 11-2 0 1 1 0 012 0zm-1-3a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="py-1">
                    <svg class="h-5 w-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 11-2 0 1 1 0 012 0zm-1-3a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-red-800">Terdapat kesalahan pada form:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Dynamic Header Section --}}
    <div class="mb-8 border-collapse bg-gray-500 rounded-xl">
        <div class="bg-gradient-to-r {{ $formConfig['gradient_colors'] }} rounded-xl p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 p-3 rounded-xl">
                    <i data-lucide="{{ $formConfig['icon'] }}" class="w-8 h-8"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">
                        @if($isReadOnly)
                            Detail {{ $formConfig['title'] }}
                        @elseif($isEditMode)
                            Edit {{ $formConfig['title'] }}
                        @else
                            {{ $formConfig['title'] }}
                        @endif
                    </h1>
                    <p class="mt-2 text-white">
                        {{ $formConfig['description'] }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Status Badge for existing usulan --}}
        @if($isEditMode)
            <div class="mt-4 px-6 pb-4">
                <span class="px-4 py-2 rounded-lg text-sm font-medium
                    @if($usulan->status_usulan === 'Draft') bg-gray-100 text-gray-700
                    @elseif($usulan->status_usulan === 'Diajukan') bg-blue-100 text-blue-700
                    @elseif($usulan->status_usulan === 'Perlu Perbaikan') bg-yellow-100 text-yellow-700
                    @elseif($usulan->status_usulan === 'Disetujui') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-700
                    @endif">
                    Status: {{ $usulan->status_usulan }}
                </span>
            </div>
        @endif
    </div>

    {{-- Status Alerts --}}
    @if($isReadOnly)
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-start gap-3 mb-6">
            <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-medium">Usulan Sedang Diproses</p>
                <p class="text-sm">Usulan Anda dengan status "{{ $usulan->status_usulan }}" sedang dalam tahap review. Anda akan dapat melakukan edit kembali jika diminta perbaikan.</p>
            </div>
        </div>
    @endif

    @if($isEditMode && $usulan->status_usulan === 'Perlu Perbaikan')
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-start gap-3 mb-6">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-medium">Perbaikan Diperlukan</p>
                <p class="text-sm">Usulan Anda memerlukan perbaikan. Silakan lakukan perubahan yang diperlukan dan kirim ulang.</p>
                @if($usulan->catatan_verifikator)
                    <div class="text-sm mt-2">
                        <strong>Catatan dari Verifikator:</strong>
                        <div class="mt-1 pl-4 border-l-2 border-yellow-400">
                            {!! nl2br(e($usulan->catatan_verifikator)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- FIXED: Form Wrapper Logic --}}
    @if($canEdit && $jenjangType !== 'tenaga-kependidikan')
        @if($isEditMode)
            <form id="usulan-form" action="{{ route('pegawai-unmul.usulan-jabatan.update', $usulan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
        @else
            <form id="usulan-form" action="{{ route('pegawai-unmul.usulan-jabatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
        @endif
    @else
        <div>
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
                    <input type="text" value="{{ $daftarPeriode->nama_periode ?? 'Tidak ada periode aktif' }}" class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    <input type="hidden" name="periode_usulan_id" value="{{ $daftarPeriode->id ?? '' }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</label>
                    <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                    <input type="text" value="{{ $daftarPeriode ? \Carbon\Carbon::parse($daftarPeriode->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($daftarPeriode->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}" class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Pengusul (Always show) --}}
    <div class="bg-gradient-to-r from-indigo-50 via-white to-purple-50 border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r {{ $formConfig['gradient_colors'] }} px-6 py-5">
            <h3 class="text-xl font-bold text-black flex items-center">
                <i data-lucide="user-circle" class="w-6 h-6 mr-3"></i>
                Informasi Pengusul
            </h3>
            <p class="text-white/90 text-sm mt-1">Detail informasi pemohon usulan jabatan fungsional</p>
        </div>

        <div class="px-6 py-6 bg-slate-600">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-400">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Lengkap</span>
                            <p class="text-sm font-semibold text-gray-800 mt-1">{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }}, {{ $pegawai->gelar_belakang }}</p>
                        </div>
                    </div>
                </div>

                <!-- NIP -->
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-green-400">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                            <i data-lucide="credit-card" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">NIP</span>
                            <p class="text-sm font-semibold text-gray-800 mt-1">{{ $pegawai->nip }}</p>
                        </div>
                    </div>
                </div>

                <!-- Jabatan Saat Ini -->
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-orange-400">
                    <div class="flex items-center">
                        <div class="bg-orange-100 p-2 rounded-lg mr-3">
                            <i data-lucide="briefcase" class="w-4 h-4 text-orange-600"></i>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jabatan Saat Ini</span>
                            <p class="text-sm font-semibold text-gray-800 mt-1">{{ $pegawai->jabatan->jabatan }}</p>
                        </div>
                    </div>
                </div>

                <!-- Jabatan yang Dituju -->
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-400">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-2 rounded-lg mr-3">
                            <i data-lucide="target" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jabatan yang Dituju</span>
                            <div class="mt-1">
                                @if($jabatanTujuan)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <i data-lucide="arrow-up-right" class="w-3 h-3 mr-1"></i>
                                        {{ $jabatanTujuan->jabatan }}
                                    </span>
                                @elseif($jenjangType === 'tenaga-kependidikan')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i data-lucide="shuffle" class="w-3 h-3 mr-1"></i>
                                        Perpindahan Jabatan Tendik
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="crown" class="w-3 h-3 mr-1"></i>
                                        Jabatan Fungsional Tertinggi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- INCLUDE: Profile Display Component (Always show) --}}
    @include('backend.layouts.pegawai-unmul.usul-jabatan.components.profile-display', ['pegawai' => $pegawai])

    {{-- CONDITIONAL: Karya Ilmiah Section (Only for dosen, not for tendik) --}}
    @if($jenjangType !== 'tenaga-kependidikan')
        @include('backend.layouts.pegawai-unmul.usul-jabatan.components.karya-ilmiah-section', [
            'formConfig' => $formConfig,
            'jenjangType' => $jenjangType,
            'usulan' => $usulan,
            'isReadOnly' => $isReadOnly
        ])

        @include('backend.layouts.pegawai-unmul.usul-jabatan.components.bkd-upload', [
            'isReadOnly' => $isReadOnly,
            'usulan' => $usulan,
            'bkdSemesters' => $bkdSemesters,
        ])

        {{-- CONDITIONAL: Dokumen Upload Section (Only for dosen, not for tendik) --}}
        @include('backend.layouts.pegawai-unmul.usul-jabatan.components.dokumen-upload', [
            'formConfig' => $formConfig,
            'jenjangType' => $jenjangType,
            'usulan' => $usulan,
            'isReadOnly' => $isReadOnly
        ])
    @endif

    {{-- SPECIAL: Tenaga Kependidikan Information --}}
    @if($jenjangType === 'tenaga-kependidikan')
        <div class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
            <div class="bg-gradient-to-r {{ $formConfig['gradient_colors'] }} -m-8 mb-8 p-6 rounded-t-xl">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i data-lucide="info" class="w-6 h-6 mr-3"></i>
                    Informasi Usulan Tenaga Kependidikan
                </h2>
                <p class="text-white/90 mt-2">Profil Anda sudah lengkap dan siap untuk usulan</p>
            </div>

            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <i data-lucide="check-circle" class="w-12 h-12 text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Profil Anda Sudah Lengkap</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Semua data profil Anda sudah terverifikasi dan siap untuk proses usulan jabatan tenaga kependidikan
                    ketika fitur ini telah tersedia.
                </p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-lg mx-auto">
                    <p class="text-blue-800 text-sm">
                        <strong>Fitur akan segera hadir:</strong> Form usulan jabatan tenaga kependidikan sedang dalam pengembangan
                        dan akan tersedia dalam update mendatang.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Tombol Submit (Only for dosen and editable) --}}
    @if($jenjangType !== 'tenaga-kependidikan')
        @if(!$isReadOnly)
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center gap-4">
                <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Batal
                </a>
                @if($canEdit)
                    <button type="submit" name="action" value="save_draft"
                            class="px-6 py-2 bg-slate-500 text-white rounded-md shadow-sm hover:bg-slate-600 transition-colors">
                        <span class="loading-text">Simpan Draft</span>
                        <span class="loading-spinner hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                    <button type="submit" name="action" value="submit_final"
                            class="px-6 py-2 bg-blue-600 bg-gradient-to-r {{ $formConfig['gradient_colors'] }} text-white rounded-md shadow-sm hover:opacity-90 transition-opacity">
                        <span class="loading-text">
                            @if($isEditMode)
                                Update & Kirim Usulan
                            @else
                                Kirim Usulan
                            @endif
                        </span>
                        <span class="loading-spinner hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            @if($isEditMode)
                                Memperbarui...
                            @else
                                Mengirim...
                            @endif
                        </span>
                    </button>
                @endif
            </div>
        @else
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center gap-4">
                <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-600 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        @endif
    @else
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center gap-4">
            <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-600 transition-colors">
                Kembali ke Dashboard
            </a>
        </div>
    @endif

    {{-- Close form/div --}}
    @if($canEdit && $jenjangType !== 'tenaga-kependidikan')
        </form>
    @else
        </div>
    @endif
</div>
@endsection
