@extends('backend.layouts.roles.penilai-universitas.app')

@section('title', 'Detail Usulan')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('backend.admin-univ-usulan.pusat-usulan.index') }}" class="hover:text-gray-700 transition-colors">
                Pusat Usulan
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('backend.admin-univ-usulan.periode-usulan.pendaftar', $usulan->periode_usulan_id) }}" class="hover:text-gray-700 transition-colors">
                Daftar Pendaftar
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Detail Usulan</span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900">
            Detail Usulan: {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
        </h1>
        <p class="mt-2 text-gray-600">
            Review detail usulan {{ strtolower(str_replace(['usulan-', '-'], [' ', ' '], $usulan->jenis_usulan)) }} yang diajukan.
        </p>
    </div>

    {{-- Alert Messages --}}
    @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.alert-messages')

    {{-- Main Form --}}
    <form action="{{ route('backend.admin-univ-usulan.pusat-usulan.process', $usulan->id) }}"
          method="POST"
          id="validationForm"
          enctype="multipart/form-data">
        @csrf

        {{-- Header Info Card --}}
        @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.header-info', [
            'usulan' => $usulan
        ])

        {{-- Validation Sections --}}
        @if(isset($validationFields) && count($validationFields) > 0)
            @foreach($validationFields as $category => $fields)
                @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.validation-section', [
                    'category' => $category,
                    'fields' => $fields,
                    'usulan' => $usulan,
                    'existingValidation' => $existingValidation ?? []
                ])
            @endforeach
        @endif

        {{-- Action Buttons --}}
        @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.action-buttons', [
            'usulan' => $usulan,
            'canEdit' => $canEdit ?? false
        ])

        {{-- Hidden Forms --}}
        @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.return-form')
        @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.forward-form')
    </form>

    {{-- Riwayat Usulan --}}
    @include('backend.layouts.admin-univ-usulan.pusat-usulan.partials.riwayat-log', [
        'logs' => $usulan->logs
    ])
</div>
@endsection


