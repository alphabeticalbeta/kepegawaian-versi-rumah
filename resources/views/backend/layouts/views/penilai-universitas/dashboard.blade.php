@extends('backend.layouts.roles.penilai-universitas.app')

@section('title', 'Dashboard Penilai Universitas')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Dashboard Penilai Universitas
            </h1>
            <p class="mt-2 text-gray-600">
                Selamat datang, <span class="font-medium">{{ Auth::user()->nama_lengkap ?? 'Penilai' }}</span>.
                Pantau dan kelola penilaian usulan kepegawaian UNMUL.
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Penilaian</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $assessmentStats['total_assessments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Selesai Dinilai</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $assessmentStats['completed_assessments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Menunggu Penilaian</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $assessmentStats['pending_assessments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i data-lucide="star" class="w-6 h-6"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rata-rata Nilai</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($assessmentStats['average_score'] ?? 0, 1) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Assessments -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Penilaian Prioritas Tinggi</h3>
                <p class="text-sm text-gray-600">Usulan yang memerlukan penilaian segera</p>
            </div>
            <div class="p-6">
                @if(isset($pendingAssessments) && $pendingAssessments->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingAssessments as $assessment)
                            <div class="flex items-center space-x-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $assessment->pegawai->nama_lengkap ?? 'Pegawai' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $assessment->jenis_usulan ?? 'jabatan' }} - {{ $assessment->jabatan->jabatan ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('penilai-universitas.pusat-usulan.detail', $assessment) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 transition-colors">
                                        <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                        Nilai Sekarang
                                    </a>
                                </div>
                                <div class="flex-shrink-0 text-sm text-gray-500">
                                    {{ $assessment->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="check-circle" class="w-12 h-12 text-green-300 mx-auto mb-4"></i>
                        <p class="text-gray-500">Tidak ada penilaian yang menunggu</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Assessments -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Penilaian Terbaru</h3>
                <p class="text-sm text-gray-600">Penilaian yang baru diselesaikan</p>
            </div>
            <div class="p-6">
                @if(isset($recentAssessments) && $recentAssessments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAssessments as $assessment)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="user-check" class="w-5 h-5 text-green-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $assessment->pegawai->nama_lengkap ?? 'Pegawai' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $assessment->jenis_usulan ?? 'jabatan' }} - {{ $assessment->jabatan->jabatan ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($assessment->assessment_score)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Nilai: {{ $assessment->assessment_score }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Belum dinilai
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 text-sm text-gray-500">
                                    {{ $assessment->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500">Belum ada penilaian terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


