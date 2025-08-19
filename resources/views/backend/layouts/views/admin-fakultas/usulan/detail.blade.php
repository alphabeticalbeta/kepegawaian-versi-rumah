@extends('backend.layouts.roles.admin-fakultas.app')

@section('title', 'Detail Usulan - ' . $usulan->jenis_usulan)

@php
    // Determine current role and permissions
    $currentRole = auth()->user()->roles->first()->name ?? 'admin-fakultas';
    $canEdit = in_array($usulan->status_usulan, ['Diajukan', 'Sedang Direview', 'Diusulkan ke Universitas']);

    // Role-specific configurations
    $roleConfigs = [
        'Admin Fakultas' => [
            'title' => 'Validasi Usulan Fakultas',
            'description' => 'Validasi data usulan sebelum diteruskan ke universitas',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'dokumen_usulan', 'karya_ilmiah'],
            'nextStatus' => 'Diusulkan ke Universitas'
        ],
        'Admin Universitas' => [
            'title' => 'Validasi Usulan Universitas',
            'description' => 'Validasi final usulan sebelum diteruskan ke tim penilai',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'dokumen_usulan', 'karya_ilmiah', 'bkd'],
            'nextStatus' => 'Sedang Direview'
        ],
        'Tim Penilai' => [
            'title' => 'Penilaian Usulan',
            'description' => 'Penilaian mendalam terhadap usulan',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'dokumen_usulan', 'karya_ilmiah', 'bkd'],
            'nextStatus' => 'Direkomendasikan'
        ],
        'Tim Senat' => [
            'title' => 'Keputusan Senat',
            'description' => 'Keputusan akhir senat terhadap usulan',
            'validationFields' => ['data_pribadi', 'data_kepegawaian', 'data_pendidikan', 'data_kinerja', 'dokumen_profil', 'dokumen_usulan', 'karya_ilmiah', 'bkd'],
            'nextStatus' => 'Disetujui'
        ]
    ];

    $config = $roleConfigs[$currentRole] ?? $roleConfigs['Admin Fakultas'];

    // Get existing validation data from controller (already cached)
    $existingValidation = $existingValidation ?? $usulan->getValidasiByRole(strtolower(str_replace(' ', '_', $currentRole))) ?? [];

    // Define field groups and their labels
    $fieldGroups = [
        'data_pribadi' => [
            'label' => 'Data Pribadi',
            'icon' => 'user',
            'fields' => [
                'jenis_pegawai' => 'Jenis Pegawai',
                'status_kepegawaian' => 'Status Kepegawaian',
                'nip' => 'NIP',
                'nuptk' => 'NUPTK',
                'gelar_depan' => 'Gelar Depan',
                'nama_lengkap' => 'Nama Lengkap',
                'gelar_belakang' => 'Gelar Belakang',
                'email' => 'Email',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
                'jenis_kelamin' => 'Jenis Kelamin',
                'nomor_handphone' => 'Nomor Handphone'
            ]
        ],
        'data_kepegawaian' => [
            'label' => 'Data Kepegawaian',
            'icon' => 'briefcase',
            'fields' => [
                'pangkat_saat_usul' => 'Pangkat',
                'tmt_pangkat' => 'TMT Pangkat',
                'jabatan_saat_usul' => 'Jabatan',
                'tmt_jabatan' => 'TMT Jabatan',
                'tmt_cpns' => 'TMT CPNS',
                'tmt_pns' => 'TMT PNS',
                'unit_kerja_saat_usul' => 'Unit Kerja'
            ]
        ],
        'data_pendidikan' => [
            'label' => 'Data Pendidikan & Fungsional',
            'icon' => 'graduation-cap',
            'fields' => [
                'pendidikan_terakhir' => 'Pendidikan Terakhir',
                'nama_universitas_sekolah' => 'Nama Universitas/Sekolah',
                'nama_prodi_jurusan' => 'Nama Program Studi/Jurusan',
                'mata_kuliah_diampu' => 'Mata Kuliah Diampu',
                'ranting_ilmu_kepakaran' => 'Bidang Kepakaran',
                'url_profil_sinta' => 'Profil SINTA'
            ]
        ],
        'data_kinerja' => [
            'label' => 'Data Kinerja',
            'icon' => 'trending-up',
            'fields' => [
                'predikat_kinerja_tahun_pertama' => 'Predikat SKP Tahun ' . (date('Y') - 1),
                'predikat_kinerja_tahun_kedua' => 'Predikat SKP Tahun ' . (date('Y') - 2),
                'nilai_konversi' => 'Nilai Konversi ' . (date('Y') - 1)
            ]
        ],
        'dokumen_profil' => [
            'label' => 'Dokumen Profil',
            'icon' => 'folder',
            'fields' => [
                'ijazah_terakhir' => 'Ijazah Terakhir',
                'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
                'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                'skp_tahun_pertama' => 'SKP Tahun ' . (date('Y') - 1),
                'skp_tahun_kedua' => 'SKP Tahun ' . (date('Y') - 2),
                'pak_konversi' => 'PAK Konversi ' . (date('Y') - 1),
                'sk_cpns' => 'SK CPNS',
                'sk_pns' => 'SK PNS',
                'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
                'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir'
            ]
        ],
        'dokumen_usulan' => [
            'label' => 'Dokumen Usulan',
            'icon' => 'file-plus',
            'fields' => [
                'pakta_integritas' => 'Pakta Integritas',
                'bukti_korespondensi' => 'Bukti Korespondensi',
                'turnitin' => 'Hasil Turnitin',
                'upload_artikel' => 'Upload Artikel',
                'bukti_syarat_guru_besar' => 'Bukti Syarat Guru Besar'
            ]
        ],
        'karya_ilmiah' => [
            'label' => 'Karya Ilmiah',
            'icon' => 'book-open',
            'fields' => [
                'jenis_karya' => 'Jenis Karya',
                'nama_jurnal' => 'Nama Jurnal',
                'judul_artikel' => 'Judul Artikel',
                'penerbit_artikel' => 'Penerbit Artikel',
                'volume_artikel' => 'Volume Artikel',
                'nomor_artikel' => 'Nomor Artikel',
                'edisi_artikel' => 'Edisi Artikel (Tahun)',
                'halaman_artikel' => 'Halaman Artikel',
                'link_artikel' => 'Link Artikel',
                'link_sinta' => 'Link SINTA',
                'link_scopus' => 'Link SCOPUS',
                'link_scimago' => 'Link SCIMAGO',
                'link_wos' => 'Link WoS'
            ]
        ],
        'bkd' => [
            'label' => 'Beban Kinerja Dosen (BKD)',
            'icon' => 'clipboard-list',
            'fields' => [
                'bkd_semester_1' => 'BKD Semester 1',
                'bkd_semester_2' => 'BKD Semester 2'
            ]
        ]
    ];
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    {{-- Header Section --}}
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $config['title'] }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $config['description'] }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Notification area moved to top --}}
        <div id="action-feedback" class="mb-4 text-sm hidden"></div>

        {{-- Status Badge --}}
        <div class="mb-6">
            @php
                $statusColors = [
                    'Draft' => 'bg-gray-100 text-gray-800 border-gray-300',
                    'Diajukan' => 'bg-blue-100 text-blue-800 border-blue-300',
                    'Sedang Direview' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                    'Disetujui' => 'bg-green-100 text-green-800 border-green-300',
                    'Direkomendasikan' => 'bg-purple-100 text-purple-800 border-purple-300',
                    'Ditolak' => 'bg-red-100 text-red-800 border-red-300',
                    'Diusulkan ke Universitas' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                ];
                $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $usulan->status_usulan }}</span>
            </div>
        </div>

        {{-- Informasi Usulan --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="info" class="w-6 h-6 mr-3"></i>
                    Informasi Usulan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Pegawai</label>
                        <p class="text-xs text-gray-600 mb-2">Nama pegawai pengusul</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_lengkap ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Periode</label>
                        <p class="text-xs text-gray-600 mb-2">Periode usulan</p>
                        <input type="text" value="{{ $usulan->periodeUsulan->nama_periode ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Usulan</label>
                        <p class="text-xs text-gray-600 mb-2">Jenis usulan yang diajukan</p>
                        <input type="text" value="{{ $usulan->jenis_usulan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- CSRF token for autosave --}}
        @if($canEdit)

        @endif



        {{-- Validation Table --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="check-square" class="w-6 h-6 mr-3"></i>
                    Tabel Validasi
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Usulan
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Validasi
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($config['validationFields'] as $groupKey)
                            @if(isset($fieldGroups[$groupKey]))
                                @php $group = $fieldGroups[$groupKey]; @endphp
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-6 py-3">
                                        <div class="flex items-center">
                                            <i data-lucide="{{ $group['icon'] }}" class="w-4 h-4 mr-2 text-gray-600"></i>
                                            <span class="font-semibold text-gray-800">{{ $group['label'] }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($group['fields'] as $fieldKey => $fieldLabel)
                                    @php
                                        $fieldValidation = $existingValidation['validation'][$groupKey][$fieldKey] ?? ['status' => 'sesuai', 'keterangan' => ''];
                                        $isInvalid = $fieldValidation['status'] === 'tidak_sesuai';
                                    @endphp
                                    <tr class="hover:bg-gray-50 {{ $isInvalid ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $fieldLabel }}</div>
                                            <div class="text-sm text-gray-500">
                                                @php
                                                    $value = '';
                                                    if ($groupKey === 'data_pribadi') {
                                                        if ($fieldKey === 'tanggal_lahir') {
                                                            $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'data_kepegawaian') {
                                                        if ($fieldKey === 'pangkat_saat_usul') {
                                                            $value = $usulan->pegawai->pangkat?->pangkat ?? '-';
                                                        } elseif ($fieldKey === 'jabatan_saat_usul') {
                                                            $value = $usulan->pegawai->jabatan?->jabatan ?? '-';
                                                        } elseif ($fieldKey === 'unit_kerja_saat_usul') {
                                                            $value = $usulan->pegawai->unitKerja?->nama ?? '-';
                                                        } elseif (str_starts_with($fieldKey, 'tmt_')) {
                                                            $value = $usulan->pegawai->$fieldKey ? \Carbon\Carbon::parse($usulan->pegawai->$fieldKey)->isoFormat('D MMMM YYYY') : '-';
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'data_pendidikan') {
                                                        if ($fieldKey === 'url_profil_sinta' && $usulan->pegawai->$fieldKey) {
                                                            $value = '<a href="' . e($usulan->pegawai->$fieldKey) . '" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline">Buka Profil SINTA</a>';
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'data_kinerja') {
                                                        if ($fieldKey === 'nilai_konversi' && $usulan->pegawai->$fieldKey) {
                                                            $value = $usulan->pegawai->$fieldKey;
                                                        } else {
                                                            $value = $usulan->pegawai->$fieldKey ?? '-';
                                                        }
                                                    } elseif ($groupKey === 'dokumen_profil') {
                                                        if ($usulan->pegawai->$fieldKey) {
                                                            $route = route('admin-fakultas.usulan.show-pegawai-document', [$usulan->id, $fieldKey]);
                                                            $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                        } else {
                                                            $value = 'Dokumen tidak tersedia';
                                                        }
                                                    } elseif ($groupKey === 'dokumen_usulan') {
                                                        // Check multiple possible locations for document path
                                                        $docPath = null;

                                                        // Check new structure first
                                                        if (isset($usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'])) {
                                                            $docPath = $usulan->data_usulan['dokumen_usulan'][$fieldKey]['path'];
                                                        }
                                                        // Check old structure
                                                        elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                            $docPath = $usulan->data_usulan[$fieldKey];
                                                        }
                                                        // Check using getDocumentPath method
                                                        else {
                                                            $docPath = $usulan->getDocumentPath($fieldKey);
                                                        }

                                                        if ($docPath) {
                                                            $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $fieldKey]);
                                                            $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                        } else {
                                                            $value = 'Dokumen tidak tersedia';
                                                        }
                                                                                                        } elseif ($groupKey === 'karya_ilmiah') {
                                                        // Handle link fields with proper data structure
                                                        if (str_contains($fieldKey, 'link_')) {
                                                            // Map field names for links
                                                            $fieldMapping = [
                                                                'link_artikel' => 'artikel',
                                                                'link_sinta' => 'sinta',
                                                                'link_scopus' => 'scopus',
                                                                'link_scimago' => 'scimago',
                                                                'link_wos' => 'wos'
                                                            ];
                                                            $mappedField = $fieldMapping[$fieldKey] ?? $fieldKey;

                                                            // Check new structure first (links object)
                                                            if (isset($usulan->data_usulan['karya_ilmiah']['links'][$mappedField])) {
                                                                $karyaValue = $usulan->data_usulan['karya_ilmiah']['links'][$mappedField];
                                                            }
                                                            // Check old structure
                                                            elseif (isset($usulan->data_usulan['karya_ilmiah'][$fieldKey])) {
                                                                $karyaValue = $usulan->data_usulan['karya_ilmiah'][$fieldKey];
                                                            }
                                                            // Check direct structure
                                                            elseif (isset($usulan->data_usulan[$fieldKey])) {
                                                                $karyaValue = $usulan->data_usulan[$fieldKey];
                                                            }
                                                            else {
                                                                $karyaValue = '-';
                                                            }

                                                            if ($karyaValue && $karyaValue !== '-') {
                                                                $value = '<a href="' . e($karyaValue) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Buka Link</a>';
                                                            } else {
                                                                $value = 'Link tidak tersedia';
                                                            }
                                                        } else {
                                                            // Handle non-link fields
                                                            $karyaValue = $usulan->data_usulan['karya_ilmiah'][$fieldKey] ?? '-';
                                                            $value = $karyaValue;
                                                        }
                                                    } elseif ($groupKey === 'bkd') {
                                                        if ($usulan->getDocumentPath($fieldKey)) {
                                                            $route = route('admin-fakultas.usulan.show-document', [$usulan->id, $fieldKey]);
                                                            $value = '<a href="' . e($route) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Lihat</a>';
                                                        } else {
                                                            $value = 'BKD tidak tersedia';
                                                        }
                                                    }
                                                @endphp
                                                {!! $value !!}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($canEdit)
                                                <select name="validation[{{ $groupKey }}][{{ $fieldKey }}][status]"
                                                        class="validation-status block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm"
                                                        data-group="{{ $groupKey }}" data-field="{{ $fieldKey }}">
                                                    <option value="sesuai" {{ $fieldValidation['status'] === 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                                    <option value="tidak_sesuai" {{ $fieldValidation['status'] === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                                </select>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $fieldValidation['status'] === 'sesuai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $fieldValidation['status'])) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($canEdit)
                                                <textarea name="validation[{{ $groupKey }}][{{ $fieldKey }}][keterangan]"
                                                          class="validation-keterangan block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 text-sm {{ $fieldValidation['status'] === 'tidak_sesuai' ? '' : 'bg-gray-100' }}"
                                                          rows="2"
                                                          placeholder="Keterangan Wajib Diisi Jika Tidak Sesuai"
                                                          {{ $fieldValidation['status'] === 'tidak_sesuai' ? '' : 'disabled' }}>{{ $fieldValidation['keterangan'] ?? '' }}</textarea>
                                            @else
                                                <div class="text-sm text-gray-900">
                                                    {{ $fieldValidation['keterangan'] ?? '-' }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Action Bar: only two actions as requested --}}
        @if($canEdit)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mt-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-1"></i>
                    Perubahan validasi tersimpan otomatis. Gunakan tombol berikut untuk melanjutkan proses.
                </div>
                <form id="action-form" action="{{ route('admin-fakultas.usulan.save-validation', $usulan->id) }}" method="POST" class="flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="action_type" id="action_type" value="save_only">
                    <input type="hidden" name="catatan_umum" id="catatan_umum" value="">
                    <button type="button" id="btn-perbaikan" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                        <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                        Perbaikan Usulan
                    </button>
                    <button type="button" id="btn-forward" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Usulkan ke Universitas
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@if($canEdit)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save configuration
    const AUTOSAVE_ENDPOINT = @json(route('admin-fakultas.usulan.autosave', $usulan->id));
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let autosaveTimer = null;
    let isSaving = false;
    let pending = false;



    // Status indicator
    const statusBar = document.createElement('div');
    statusBar.className = 'fixed bottom-4 right-4 z-50 px-3 py-2 rounded-lg shadow text-xs hidden';
    document.body.appendChild(statusBar);

    function showStatus(text, type) {
        statusBar.textContent = text;
        statusBar.classList.remove('hidden');
        statusBar.className = 'fixed bottom-4 right-4 z-50 px-3 py-2 rounded-lg shadow text-xs ' +
            (type === 'saving' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' :
             type === 'success' ? 'bg-green-100 text-green-800 border border-green-300' :
             'bg-red-100 text-red-800 border border-red-300');
        if (type !== 'saving') {
            setTimeout(() => statusBar.classList.add('hidden'), 2000);
        }
    }

    function collectValidationPayload() {
        const payload = {};
        document.querySelectorAll('.validation-status').forEach(select => {
            const group = select.dataset.group;
            const field = select.dataset.field;
            const keteranganField = document.querySelector(`textarea[name="validation[${group}][${field}][keterangan]"]`);
            if (!payload[group]) payload[group] = {};
            payload[group][field] = {
                status: select.value,
                keterangan: (keteranganField && !keteranganField.disabled) ? (keteranganField.value || '') : ''
            };
        });
        return payload;
    }

    async function autosaveNow() {
        if (isSaving) {
            pending = true;
            return;
        }
        isSaving = true;
        showStatus('Menyimpan...', 'saving');

        try {
            const formData = new FormData();
            formData.append('_token', CSRF_TOKEN);
            formData.append('action_type', 'save_only');

            const payload = collectValidationPayload();
            Object.keys(payload).forEach(group => {
                Object.keys(payload[group]).forEach(field => {
                    formData.append(`validation[${group}][${field}][status]`, payload[group][field].status);
                    formData.append(`validation[${group}][${field}][keterangan]`, payload[group][field].keterangan);
                });
            });

            const res = await fetch(AUTOSAVE_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

                        if (!res.ok) throw new Error('Autosave gagal');
            const data = await res.json();

            if (data.success) {
                showStatus('Tersimpan otomatis', 'success');
            } else {
                throw new Error(data.message || 'Autosave gagal');
            }
        } catch (e) {
            showStatus('Gagal menyimpan otomatis', 'error');
        } finally {
            isSaving = false;
            if (pending) {
                pending = false;
                scheduleAutosave();
            }
        }
    }

    function scheduleAutosave() {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(autosaveNow, 600);
    }

    // Handle validation status changes and enable/disable keterangan fields
        const statusSelects = document.querySelectorAll('.validation-status');
    const keteranganTextareas = document.querySelectorAll('.validation-keterangan');

    statusSelects.forEach(select => {
        ['change','blur'].forEach(evt => select.addEventListener(evt, function() {
            const group = this.dataset.group;
            const field = this.dataset.field;
            const keteranganField = document.querySelector(`textarea[name="validation[${group}][${field}][keterangan]"]`);
            const row = this.closest('tr');

            if (this.value === 'tidak_sesuai') {
                keteranganField.disabled = false;
                keteranganField.classList.remove('bg-gray-100');
                row.classList.add('bg-red-50');
            } else {
                keteranganField.disabled = true;
                keteranganField.classList.add('bg-gray-100');
                row.classList.remove('bg-red-50');
            }

            // Trigger autosave
            scheduleAutosave();
        }));
    });

    // Handle keterangan field changes
    keteranganTextareas.forEach(textarea => {
        ['input','change','blur'].forEach(evt => textarea.addEventListener(evt, function() {
            scheduleAutosave();
        }));
    });

    // Action buttons
    const actionForm = document.getElementById('action-form');
    const actionTypeInput = document.getElementById('action_type');
    const catatanUmumInput = document.getElementById('catatan_umum');
    const btnPerbaikan = document.getElementById('btn-perbaikan');
    const btnForward = document.getElementById('btn-forward');

    function syncValidationToForm() {
        const payload = collectValidationPayload();
        // Clear previous validation inputs
        Array.from(actionForm.querySelectorAll('input[name^="validation["]')).forEach(n => n.remove());

        // Add current validation data
        Object.keys(payload).forEach(group => {
            Object.keys(payload[group]).forEach(field => {
                const st = document.createElement('input');
                st.type = 'hidden';
                st.name = `validation[${group}][${field}][status]`;
                st.value = payload[group][field].status;
                actionForm.appendChild(st);

                const ket = document.createElement('input');
                ket.type = 'hidden';
                ket.name = `validation[${group}][${field}][keterangan]`;
                ket.value = payload[group][field].keterangan || '';
                actionForm.appendChild(ket);
            });
        });
    }

    if (btnPerbaikan) {
        btnPerbaikan.addEventListener('click', function() {
            // Create modal for perbaikan usulan
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Perbaikan Usulan</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Perbaikan</label>
                            <textarea id="catatan-perbaikan"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      rows="4"
                                      placeholder="Masukkan catatan perbaikan untuk pegawai (minimal 10 karakter)"></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
                                    onclick="this.closest('.fixed').remove()">
                                Batal
                            </button>
                            <button type="button"
                                    id="submit-perbaikan"
                                    class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors">
                                Kirim Perbaikan
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Handle submit
            document.getElementById('submit-perbaikan').addEventListener('click', async function() {
                const note = document.getElementById('catatan-perbaikan').value.trim();
                if (!note || note.length < 10) {
                    alert('Catatan minimal 10 karakter.');
                    return;
                }
                actionTypeInput.value = 'return_to_pegawai';
                catatanUmumInput.value = note;
                syncValidationToForm();

                // Submit via AJAX to get inline feedback
                const formData = new FormData(actionForm);
                const feedback = document.getElementById('action-feedback');
                feedback.className = 'text-sm text-gray-600';
                feedback.textContent = 'Mengirim perbaikan...';

                try {
                    const res = await fetch(actionForm.action, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    let data = {};
                    try { data = await res.json(); } catch (_) {}
                    if (!res.ok || data.success === false) throw (data || { message: 'Gagal memproses.' });
                    feedback.className = 'text-sm text-amber-700';
                    feedback.textContent = 'Usulan dikembalikan ke Pegawai untuk perbaikan.';
                    modal.remove();
                } catch (err) {
                    feedback.className = 'text-sm text-red-600';
                    const msg = (err && err.message) ? err.message : (err && err.errors ? Object.values(err.errors).flat().join(', ') : 'Gagal mengembalikan usulan. Coba lagi.');
                    feedback.textContent = msg;
                }
            });
        });
    }

    if (btnForward) {
        btnForward.addEventListener('click', function() {
            // Create confirmation modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Konfirmasi Usulan</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="mb-4">
                            <div class="flex items-center mb-3">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-500 mr-2"></i>
                                <span class="text-sm text-gray-700">Pastikan validasi sudah lengkap sebelum melanjutkan.</span>
                            </div>
                            <p class="text-sm text-gray-600">Apakah Anda yakin ingin mengirim usulan ke Universitas?</p>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
                                    onclick="this.closest('.fixed').remove()">
                                Batal
                            </button>
                            <button type="button"
                                    id="submit-forward"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                                Kirim ke Universitas
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Handle submit
            document.getElementById('submit-forward').addEventListener('click', async function() {
                actionTypeInput.value = 'forward_to_university';
                syncValidationToForm();

                const formData = new FormData(actionForm);
                const feedback = document.getElementById('action-feedback');
                feedback.className = 'text-sm text-gray-600';
                feedback.textContent = 'Mengirim ke Universitas...';

                try {
                    const res = await fetch(actionForm.action, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    let data = {};
                    try { data = await res.json(); } catch (_) {}
                    if (!res.ok || data.success === false) throw (data || { message: 'Gagal memproses.' });
                    feedback.className = 'text-sm text-indigo-700';
                    feedback.textContent = 'Usulan berhasil dikirim ke Universitas.';
                    modal.remove();
                } catch (err) {
                    feedback.className = 'text-sm text-red-600';
                    const msg = (err && err.message) ? err.message : (err && err.errors ? Object.values(err.errors).flat().join(', ') : 'Gagal mengirim usulan. Coba lagi.');
                    feedback.textContent = msg;
                }
            });
        });
    }

    // Initial setup for keterangan fields
    document.querySelectorAll('.validation-status').forEach(select => {
        const group = select.dataset.group;
        const field = select.dataset.field;
        const keteranganField = document.querySelector(`textarea[name="validation[${group}][${field}][keterangan]"]`);

        if (select.value === 'tidak_sesuai') {
            keteranganField.disabled = false;
            keteranganField.classList.remove('bg-gray-100');
        } else {
            keteranganField.disabled = true;
            keteranganField.classList.add('bg-gray-100');
        }
    });
});
</script>
@endif
@endsection
