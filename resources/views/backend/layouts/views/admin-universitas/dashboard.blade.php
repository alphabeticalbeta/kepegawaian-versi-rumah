@extends('backend.layouts.roles.admin-universitas.app')

@section('title', 'Dashboard Admin Universitas')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Dashboard Admin Universitas
            </h1>
            <p class="mt-2 text-gray-600">
                Selamat datang, <span class="font-medium">{{ Auth::user()->nama_lengkap ?? 'Admin' }}</span>.
                Pantau statistik dan aktivitas sistem kepegawaian UNMUL.
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pegawai</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_pegawai'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Usulan</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['total_usulan'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Usulan Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['usulan_pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i data-lucide="calendar" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Periode Aktif</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['periode_aktif'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                <p class="text-sm text-gray-600">Usulan yang baru diajukan</p>
            </div>
            <div class="p-6">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $activity->pegawai->nama_lengkap ?? 'Pegawai' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Mengajukan usulan {{ $activity->jenis_usulan ?? 'jabatan' }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 text-sm text-gray-500">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500">Belum ada aktivitas terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


