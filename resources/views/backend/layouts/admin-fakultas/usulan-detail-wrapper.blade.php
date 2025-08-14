@extends('backend.layouts.admin-fakultas.app')

@section('title', 'Validasi Detail Usulan Jabatan')

@section('content')
<div class="container mx-auto p-5">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Sukses!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Form Container --}}
    <form action="{{ $formAction }}" method="POST" id="validationForm" enctype="multipart/form-data" class="mt-8 space-y-8">
        @csrf

        {{-- Header Navigation --}}
        <div class="mb-8 border border-red-200">
            <a href="{{ route('admin-fakultas.periode.pendaftar', $usulan->periode_usulan_id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 inline-flex items-center">
                <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Kembali ke Daftar Pengusul
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Validasi Usulan: {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}</h1>
            <p class="text-sm text-gray-500">Lakukan validasi terhadap setiap item data usulan pegawai.</p>
        </div>

        {{-- Header Info Card - Reuse existing --}}
        @include('backend.components.usulan._header', [
            'usulan' => $usulan
        ])

        {{-- Validation Sections - Reuse existing --}}
        @php
            $validationFields = \App\Models\BackendUnivUsulan\Usulan::getValidationFieldsWithDynamicBkd($usulan);
        @endphp

        @if(isset($validationFields) && count($validationFields) > 0)
            @foreach($validationFields as $category => $fields)
                @include('backend.components.usulan._validation-section', [
                    'category' => $category,
                    'fields'   => $fields,
                    'usulan'   => $usulan,
                    'canEdit'  => in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview']),
                    'existingValidation' => $existingValidation ?? []
                ])
            @endforeach
        @endif

        {{-- Admin Fakultas Specific Action Buttons --}}
        @include('backend.layouts.admin-fakultas.partials._action-buttons', [
            'usulan' => $usulan
        ])

        {{-- ADDED: Form Input Dokumen Fakultas --}}
        @include('backend.layouts.admin-fakultas.partials._dokumen-fakultas-form', [
            'usulan' => $usulan
        ])

        {{-- Return Form (existing) --}}
        <div id="returnForm" class="hidden mt-6 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">Kembalikan Usulan untuk Perbaikan</h3>
                <p class="text-yellow-100 text-sm mt-1">Berikan catatan yang jelas untuk pegawai</p>
            </div>
            <div class="p-6">
                {{-- Validation Issue Summary --}}
                <div id="validationIssueSummary" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="font-medium text-red-900 mb-2">Item yang tidak sesuai:</h4>
                    <ul id="issueList" class="text-sm text-red-800 space-y-1"></ul>
                </div>

                <div class="mb-4">
                    <label for="catatan_umum_return" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan untuk Pegawai <span class="text-red-500">*</span>
                    </label>
                    <textarea id="catatan_umum_return" name="catatan_umum" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Jelaskan secara detail item mana yang perlu diperbaiki dan bagaimana cara memperbaikinya..."
                              required></textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimum 10 karakter</p>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideReturnForm()" 
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="button" onclick="submitReturnForm()" 
                            class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        Kembalikan ke Pegawai
                    </button>
                </div>
            </div>
        </div>

        {{-- Riwayat Log - Reuse existing --}}
        @include('backend.components.usulan._riwayat_log', ['usulan' => $usulan])
    </form>
</div>
@endsection

{{-- FIXED: Include validation scripts dari component yang benar --}}
@include('backend.components.usulan._validation-scripts')