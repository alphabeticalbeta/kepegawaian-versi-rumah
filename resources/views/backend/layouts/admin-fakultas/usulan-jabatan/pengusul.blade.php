@extends('backend.layouts.admin-fakultas.app')

@section('title', 'Daftar Pengusul - ' . $periode->nama_periode)

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        {{-- Tombol Kembali: Arahkan ke halaman spesifik usulan jabatan jika jenisnya jabatan, jika tidak ke dashboard utama --}}
        @if($periode->jenis_usulan == 'jabatan')
            <a href="{{ route('admin-fakultas.usulan-jabatan.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-2 inline-flex items-center">
                <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                Kembali ke Daftar Periode Jabatan
            </a>
        @else
             <a href="{{ route('admin-fakultas.dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-2 inline-flex items-center">
                <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                Kembali ke Dashboard Utama
            </a>
        @endif

        <h1 class="text-2xl font-bold text-gray-800">Daftar Pengusul</h1>
        <p class="mt-1 text-sm text-gray-600">
            Menampilkan daftar pegawai yang mengajukan usulan untuk periode
            <span class="font-semibold text-indigo-600">{{ $periode->nama_periode }}</span>.
        </p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan Tujuan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Usulan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($usulans as $usulan)
                        @php
                            // PERBAIKAN LOGIC: Tentukan status aksi berdasarkan status usulan
                            $actionStatus = 'unknown';
                            $actionColor = 'gray';
                            $actionText = 'Status Tidak Dikenal';
                            $actionIcon = 'question-mark-circle';
                            $canReview = false;
                            $canViewDetail = false;

                            switch($usulan->status_usulan) {
                                case 'Draft':
                                    $actionStatus = 'draft';
                                    $actionColor = 'gray';
                                    $actionText = 'Masih Draft';
                                    $actionIcon = 'document-text';
                                    break;

                                case 'Diajukan':
                                    $actionStatus = 'need_review';
                                    $actionColor = 'green';
                                    $actionText = 'Review Usulan';
                                    $actionIcon = 'clipboard-document-check';
                                    $canReview = true;
                                    break;

                                case 'Sedang Direview':
                                    $actionStatus = 'in_review';
                                    $actionColor = 'blue';
                                    $actionText = 'Lanjutkan Review';
                                    $actionIcon = 'eye';
                                    $canReview = true;
                                    break;

                                case 'Perlu Perbaikan':
                                    $actionStatus = 'returned';
                                    $actionColor = 'orange';
                                    $actionText = 'Lihat Detail';
                                    $actionIcon = 'arrow-uturn-left';
                                    $canViewDetail = true;
                                    break;

                                case 'Dikembalikan':
                                    $actionStatus = 'returned_old';
                                    $actionColor = 'red';
                                    $actionText = 'Lihat Detail';
                                    $actionIcon = 'x-circle';
                                    $canViewDetail = true;
                                    break;

                                case 'Diteruskan Ke Universitas':
                                    $actionStatus = 'forwarded';
                                    $actionColor = 'purple';
                                    $actionText = 'Lihat Detail';
                                    $actionIcon = 'paper-airplane';
                                    $canViewDetail = true;
                                    break;

                                case 'Disetujui':
                                    $actionStatus = 'approved';
                                    $actionColor = 'green';
                                    $actionText = 'Lihat Detail';
                                    $actionIcon = 'check-circle';
                                    $canViewDetail = true;
                                    break;

                                case 'Direkomendasikan':
                                    $actionStatus = 'recommended';
                                    $actionColor = 'emerald';
                                    $actionText = 'Lihat Detail';
                                    $actionIcon = 'star';
                                    $canViewDetail = true;
                                    break;

                                case 'Ditolak':
                                    $actionStatus = 'rejected';
                                    $actionColor = 'red';
                                    $actionText = 'Lihat Detail';
                                    $actionIcon = 'x-circle';
                                    $canViewDetail = true;
                                    break;
                            }

                            // Tentukan class badge berdasarkan status
                            $statusBadgeClass = match($usulan->status_usulan) {
                                'Draft' => 'bg-gray-100 text-gray-800',
                                'Diajukan' => 'bg-blue-100 text-blue-800',
                                'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                                'Perlu Perbaikan' => 'bg-orange-100 text-orange-800',
                                'Dikembalikan' => 'bg-red-100 text-red-800',
                                'Diteruskan Ke Universitas' => 'bg-purple-100 text-purple-800',
                                'Disetujui' => 'bg-green-100 text-green-800',
                                'Direkomendasikan' => 'bg-emerald-100 text-emerald-800',
                                'Ditolak' => 'bg-red-200 text-red-900',
                                default => 'bg-gray-100 text-gray-800'
                            };

                            // Tentukan warna tombol
                            $buttonClass = match($actionColor) {
                                'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white',
                                'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white',
                                'purple' => 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500 text-white',
                                'orange' => 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500 text-white',
                                'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
                                'emerald' => 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 text-white',
                                'gray' => 'bg-gray-400 text-gray-100 cursor-not-allowed',
                                default => 'bg-gray-400 text-gray-100 cursor-not-allowed'
                            };
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $usulan->pegawai->nama_lengkap ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $usulan->pegawai->nip ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $usulan->jabatanTujuan->jabatan ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusBadgeClass }}">
                                    {{ $usulan->status_usulan }}
                                </span>

                                {{-- Keterangan tambahan untuk status tertentu --}}
                                @if($actionStatus === 'returned')
                                    <div class="text-xs text-orange-600 mt-1 font-medium">
                                        ⚠️ Dikembalikan untuk perbaikan
                                    </div>
                                @elseif($actionStatus === 'forwarded')
                                    <div class="text-xs text-purple-600 mt-1 font-medium">
                                        ✈️ Sudah diteruskan ke universitas
                                    </div>
                                @elseif($actionStatus === 'recommended')
                                    <div class="text-xs text-emerald-600 mt-1 font-medium">
                                        ⭐ Proses selesai - direkomendasikan
                                    </div>
                                @elseif($actionStatus === 'rejected')
                                    <div class="text-xs text-red-600 mt-1 font-medium">
                                        ❌ Proses selesai - ditolak
                                    </div>
                                @elseif($actionStatus === 'need_review')
                                    <div class="text-xs text-green-600 mt-1 font-medium">
                                        🔍 Menunggu review Anda
                                    </div>
                                @elseif($actionStatus === 'in_review')
                                    <div class="text-xs text-blue-600 mt-1 font-medium">
                                        👀 Sedang dalam review
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $usulan->created_at->isoFormat('D MMMM YYYY') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @if($canReview)
                                    {{-- Tombol Review Aktif untuk status "Diajukan" atau "Sedang Direview" --}}
                                    <a href="{{ route('admin-fakultas.usulan.show', $usulan->id) }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm {{ $buttonClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200">
                                        @if($actionIcon === 'clipboard-document-check')
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.25-4.875c.376.023.75.05 1.124.08.835.094 1.5 1.057 1.5 2.191V8.25m-9.75 0H18a2.25 2.25 0 012.25 2.25V13.5a2.25 2.25 0 01-2.25 2.25h-2.25M9.75 8.25v10.125c0 .621.504 1.125 1.125 1.125h2.25M9.75 8.25H6a2.25 2.25 0 00-2.25 2.25v4.875c0 .621.504 1.125 1.125 1.125h1.5v-8.625C8.25 8.829 8.579 8.5 9 8.5h.75z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        @endif
                                        {{ $actionText }}
                                    </a>

                                @elseif($canViewDetail)
                                    {{-- Tombol Lihat Detail untuk status yang sudah diproses --}}
                                    <a href="{{ route('admin-fakultas.usulan.show', $usulan->id) }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm {{ $buttonClass }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200">
                                        @if($actionIcon === 'arrow-uturn-left')
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                            </svg>
                                        @elseif($actionIcon === 'paper-airplane')
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                            </svg>
                                        @elseif($actionIcon === 'check-circle')
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($actionIcon === 'star')
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        @endif
                                        {{ $actionText }}
                                    </a>

                                @else
                                    {{-- Tombol Disabled untuk status yang tidak bisa diakses --}}
                                    <div class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm {{ $buttonClass }}">
                                        @if($actionIcon === 'document-text')
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                            </svg>
                                        @endif
                                        {{ $actionText }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500 font-medium">Tidak ada pengusul untuk periode ini.</p>
                                    <p class="text-xs text-gray-400 mt-1">Pengusul akan muncul di sini ketika ada usulan yang diajukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($usulans->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $usulans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
