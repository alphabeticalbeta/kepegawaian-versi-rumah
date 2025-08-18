@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Dashboard Pegawai')

@section('content')

    @php
    // Cek apakah ada usulan yang perlu diperbaiki
        $usulanPerluPerbaikan = $usulans->firstWhere('status_usulan', 'Perlu Perbaikan');
    @endphp

    @if($usulanPerluPerbaikan)
        <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded-lg mb-6 shadow-md" role="alert">
            <div class="flex">
                <div class="py-1"><svg class="fill-current h-6 w-6 text-orange-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5v6h2V5H9zm0 8h2v2H9v-2z"/></svg></div>
                <div>
                    <p class="font-bold">Aksi Diperlukan</p>
                    <p class="text-sm">Satu atau lebih usulan Anda telah dikembalikan dan memerlukan perbaikan. Silakan periksa detailnya di bawah.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Notifikasi sudah ditangani oleh component flash di layout base --}}

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Riwayat Usulan Saya
            </h1>
            <p class="mt-2 text-gray-600">
                Pantau status dan riwayat semua usulan yang telah Anda ajukan.
            </p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Usulan
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Berikut adalah semua usulan yang pernah Anda buat.
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4">Jenis Usulan</th>
                            <th scope="col" class="px-6 py-4">Periode</th>
                            <th scope="col" class="px-6 py-4">Tanggal Pengajuan</th>
                            <th scope="col" class="px-6 py-4 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usulans as $usulan)
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap capitalize">{{ $usulan->jenis_usulan }}</td>
                                <td class="px-6 py-4">{{ $usulan->periodeUsulan->nama_periode }}</td>
                                <td class="px-6 py-4">{{ $usulan->created_at->isoFormat('D MMMM YYYY') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($usulan->status_usulan) {
                                            'Draft' => 'bg-gray-100 text-gray-800',
                                            'Diajukan' => 'bg-blue-100 text-blue-800',
                                            'Sedang Direview' => 'bg-yellow-100 text-yellow-800',
                                            'Perlu Perbaikan' => 'bg-orange-100 text-orange-800',
                                            'Dikembalikan' => 'bg-red-100 text-red-800',
                                            'Disetujui' => 'bg-green-100 text-green-800',
                                            'Direkomendasikan' => 'bg-purple-100 text-purple-800',
                                            'Ditolak' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $usulan->status_usulan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        @php
                                            // Tentukan route berdasarkan jenis usulan
                                            $routeName = match($usulan->jenis_usulan) {
                                                'Usulan Jabatan' => 'pegawai-unmul.usulan-jabatan',
                                                'Usulan Kepangkatan' => 'pegawai-unmul.usulan-kepangkatan',
                                                'Usulan ID SINTA ke SISTER' => 'pegawai-unmul.usulan-id-sinta-sister',
                                                'Usulan Laporan LKD' => 'pegawai-unmul.usulan-laporan-lkd',
                                                'Usulan Laporan SERDOS' => 'pegawai-unmul.usulan-laporan-serdos',
                                                'Usulan NUPTK' => 'pegawai-unmul.usulan-nuptk',
                                                'Usulan Pencantuman Gelar' => 'pegawai-unmul.usulan-pencantuman-gelar',
                                                'Usulan Pengaktifan Kembali' => 'pegawai-unmul.usulan-pengaktifan-kembali',
                                                'Usulan Pensiun' => 'pegawai-unmul.usulan-pensiun',
                                                'Usulan Penyesuaian Masa Kerja' => 'pegawai-unmul.usulan-penyesuaian-masa-kerja',
                                                'Usulan Presensi' => 'pegawai-unmul.usulan-presensi',
                                                'Usulan Satyalancana' => 'pegawai-unmul.usulan-satyalancana',
                                                'Usulan Tugas Belajar' => 'pegawai-unmul.usulan-tugas-belajar',
                                                'Usulan Ujian Dinas & Ijazah' => 'pegawai-unmul.usulan-ujian-dinas-ijazah',
                                                default => 'pegawai-unmul.usulan-jabatan'
                                            };

                                            // Tentukan apakah bisa edit atau hanya lihat detail
                                            $canEdit = in_array($usulan->status_usulan, ['Draft', 'Perlu Perbaikan', 'Dikembalikan ke Pegawai']);
                                            $actionRoute = $canEdit ? $routeName . '.edit' : $routeName . '.show';
                                        @endphp

                                        @if($usulan->status_usulan == 'Perlu Perbaikan')
                                            <a href="{{ route($actionRoute, $usulan) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 hover:text-orange-700 transition-colors duration-200">
                                                <i data-lucide="edit" class="w-3 h-3 mr-1"></i>
                                                Perbaiki Usulan
                                            </a>
                                        @else
                                            <a href="{{ route($actionRoute, $usulan) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 transition-colors duration-200">
                                                <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                                Detail
                                            </a>
                                        @endif
                                        <button type="button"
                                                onclick="showLogModal({{ $usulan->id }}, '{{ $routeName }}')"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200">
                                            <i data-lucide="history" class="w-3 h-3 mr-1"></i>
                                            Log
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="file-x" class="w-12 h-12 text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-400">Belum ada usulan</p>
                                        <p class="text-sm text-gray-400">Anda belum pernah membuat usulan apapun.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($usulans->hasPages())
                <div class="p-4 border-t border-gray-200">
                {{ $usulans->links() }}
                </div>
            @endif
        </div>

        {{-- Modal untuk menampilkan semua log --}}
        <div id="logModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-xl bg-white">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i data-lucide="history" class="w-5 h-5 inline mr-2"></i>
                        Riwayat Log Usulan
                    </h3>
                    <button type="button" onclick="closeLogModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <div id="logContent" class="mt-4 max-h-96 overflow-y-auto">
                    <!-- Log content will be loaded here -->
                </div>
                <div class="flex justify-end pt-4 border-t border-gray-200 mt-4">
                    <button type="button" onclick="closeLogModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function showLogModal(usulanId, routeName) {
    // Show modal
    document.getElementById('logModal').classList.remove('hidden');

    // Show loading
    document.getElementById('logContent').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Memuat log...</span>
        </div>
    `;

    // Fetch log data
    fetch(`/pegawai-unmul/${routeName.replace('pegawai-unmul.', '')}/${usulanId}/logs`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.logs) {
                displayLogs(data.logs);
            } else {
                document.getElementById('logContent').innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                        <p>Tidak ada log yang tersedia</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching logs:', error);
            document.getElementById('logContent').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i data-lucide="alert-triangle" class="w-12 h-12 mx-auto mb-4 text-red-300"></i>
                    <p>Gagal memuat log. Silakan coba lagi.</p>
                </div>
            `;
        });
}

function displayLogs(logs) {
    const logContent = document.getElementById('logContent');

    if (logs.length === 0) {
        logContent.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                <p>Belum ada log untuk usulan ini</p>
            </div>
        `;
        return;
    }

    let logHTML = '<div class="space-y-4">';

    logs.forEach(log => {
        const statusChange = log.is_status_change ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200';
        const statusIcon = log.is_status_change ? 'refresh-cw' : 'file-text';

        logHTML += `
            <div class="border rounded-lg p-4 ${statusChange}">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i data-lucide="${statusIcon}" class="w-5 h-5 text-gray-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${log.action}</p>
                            ${log.is_status_change ? `
                                <div class="mt-1 flex items-center space-x-2">
                                    <span class="text-xs text-gray-500">Status:</span>
                                    <span class="text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-700">${log.status_sebelumnya || 'N/A'}</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3 text-gray-400"></i>
                                    <span class="text-xs px-2 py-1 rounded-full ${log.status_badge_class}">${log.status_baru}</span>
                                </div>
                            ` : ''}
                            ${log.catatan ? `<p class="mt-1 text-sm text-gray-600">${log.catatan}</p>` : ''}
                            <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                <span>Oleh: ${log.user_name}</span>
                                <span>${log.created_at}</span>
                                <span class="text-gray-400">${log.relative_time}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    logHTML += '</div>';
    logContent.innerHTML = logHTML;

    // Reinitialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function closeLogModal() {
    document.getElementById('logModal').classList.add('hidden');
    document.getElementById('logContent').innerHTML = '';
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    const logModal = document.getElementById('logModal');
    if (logModal && !logModal.hasAttribute('data-initialized')) {
        logModal.setAttribute('data-initialized', 'true');
        logModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogModal();
            }
        });
    }

    // Close modal with Escape key (only add once)
    if (!document.hasAttribute('data-escape-listener')) {
        document.setAttribute('data-escape-listener', 'true');
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogModal();
            }
        });
    }
});
</script>
@endpush


