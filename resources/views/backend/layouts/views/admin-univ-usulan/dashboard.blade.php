@extends('backend.layouts.roles.admin-univ-usulan.app')

@section('title', 'Dashboard Admin Universitas Usulan')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error Message -->
        @if(isset($error))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg relative shadow-md" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ $error }}</span>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Dashboard Admin Universitas Usulan
            </h1>
            <p class="mt-2 text-gray-600">
                Selamat datang, <span class="font-medium">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>.
                Kelola data master dan usulan kepegawaian UNMUL.
            </p>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                <p class="text-sm text-gray-600">Akses cepat ke fitur utama</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}"
                       class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i data-lucide="users" class="w-5 h-5 text-blue-600 mr-3"></i>
                        <span class="text-sm font-medium text-blue-900">Data Pegawai</span>
                    </a>

                    <a href="{{ route('backend.admin-univ-usulan.pusat-usulan.index') }}"
                       class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <i data-lucide="file-text" class="w-5 h-5 text-green-600 mr-3"></i>
                        <span class="text-sm font-medium text-green-900">Pusat Usulan</span>
                    </a>

                    <a href="{{ route('backend.admin-univ-usulan.jabatan.index') }}"
                       class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <i data-lucide="briefcase" class="w-5 h-5 text-purple-600 mr-3"></i>
                        <span class="text-sm font-medium text-purple-900">Master Jabatan</span>
                    </a>

                    <a href="{{ route('backend.admin-univ-usulan.pangkat.index') }}"
                       class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <i data-lucide="award" class="w-5 h-5 text-orange-600 mr-3"></i>
                        <span class="text-sm font-medium text-orange-900">Master Pangkat</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Usulans -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Usulan Terbaru</h3>
                <p class="text-sm text-gray-600">Usulan yang baru diajukan</p>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                    <p class="text-gray-500">Belum ada usulan terbaru</p>
                    <p class="text-sm text-gray-400 mt-2">Dashboard dalam mode minimal untuk stabilitas</p>
                </div>
            </div>
        </div>
    </div>
@endsection


