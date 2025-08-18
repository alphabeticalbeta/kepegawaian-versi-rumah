@extends('backend.layouts.roles.pegawai-unmul.app')

@section('title', 'Detail Usulan Jabatan')

@php
    function formatDate($date) {
        return $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : '-';
    }

    $documentFields = [
        'ijazah_terakhir' => ['label' => 'Ijazah Terakhir', 'icon' => 'graduation-cap'],
        'transkrip_nilai_terakhir' => ['label' => 'Transkrip Nilai', 'icon' => 'file-text'],
        'sk_pangkat_terakhir' => ['label' => 'SK Pangkat Terakhir', 'icon' => 'award'],
        'sk_jabatan_terakhir' => ['label' => 'SK Jabatan Terakhir', 'icon' => 'briefcase'],
        'skp_tahun_pertama' => ['label' => 'SKP Tahun Pertama', 'icon' => 'clipboard-check'],
        'skp_tahun_kedua' => ['label' => 'SKP Tahun Kedua', 'icon' => 'clipboard-list'],
        'sk_cpns' => ['label' => 'SK CPNS', 'icon' => 'user-check'],
        'sk_pns' => ['label' => 'SK PNS', 'icon' => 'user-plus'],
        'pak_konversi' => ['label' => 'PAK Konversi', 'icon' => 'file-digit'],
        'sk_penyetaraan_ijazah' => ['label' => 'SK Penyetaraan Ijazah', 'icon' => 'scale'],
        'disertasi_thesis_terakhir' => ['label' => 'Disertasi/Thesis', 'icon' => 'book-open'],
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
                        Detail Usulan Jabatan
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Informasi lengkap usulan jabatan fungsional dosen
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('pegawai-unmul.usulan-jabatan.index') }}"
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                    'Dikembalikan ke Pegawai' => 'bg-orange-100 text-orange-800 border-orange-300',
                    'Perlu Perbaikan' => 'bg-amber-100 text-amber-800 border-amber-300',
                ];
                $statusColor = $statusColors[$usulan->status_usulan] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            @endphp
            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusColor }}">
                <span class="text-sm font-medium">Status: {{ $usulan->status_usulan }}</span>
            </div>
        </div>

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
                        <input type="text" value="{{ $usulan->periodeUsulan->nama_periode ?? 'Tidak ada periode aktif' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</label>
                        <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                        <input type="text" value="{{ $usulan->periodeUsulan ? \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($usulan->periodeUsulan->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Pegawai --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="user" class="w-6 h-6 mr-3"></i>
                    Informasi Pegawai
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nama Lengkap</label>
                        <p class="text-xs text-gray-600 mb-2">Nama lengkap pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_lengkap ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">NIP</label>
                        <p class="text-xs text-gray-600 mb-2">Nomor Induk Pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->nip ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jabatan Saat Ini</label>
                        <p class="text-xs text-gray-600 mb-2">Jabatan fungsional saat ini</p>
                        <input type="text" value="{{ $usulan->jabatanLama->jabatan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jabatan Tujuan</label>
                        <p class="text-xs text-gray-600 mb-2">Jabatan yang diajukan</p>
                        <input type="text" value="{{ $usulan->jabatanTujuan->jabatan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Pribadi Lengkap --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="user-check" class="w-6 h-6 mr-3"></i>
                    Data Pribadi Lengkap
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Email</label>
                        <p class="text-xs text-gray-600 mb-2">Alamat email pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->email ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nomor Handphone</label>
                        <p class="text-xs text-gray-600 mb-2">Nomor telepon aktif</p>
                        <input type="text" value="{{ $usulan->pegawai->nomor_handphone ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Gelar Depan</label>
                        <p class="text-xs text-gray-600 mb-2">Gelar akademik di depan nama</p>
                        <input type="text" value="{{ $usulan->pegawai->gelar_depan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Gelar Belakang</label>
                        <p class="text-xs text-gray-600 mb-2">Gelar akademik di belakang nama</p>
                        <input type="text" value="{{ $usulan->pegawai->gelar_belakang ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Tempat Lahir</label>
                        <p class="text-xs text-gray-600 mb-2">Tempat kelahiran</p>
                        <input type="text" value="{{ $usulan->pegawai->tempat_lahir ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Tanggal Lahir</label>
                        <p class="text-xs text-gray-600 mb-2">Tanggal kelahiran</p>
                        <input type="text" value="{{ formatDate($usulan->pegawai->tanggal_lahir) }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Kelamin</label>
                        <p class="text-xs text-gray-600 mb-2">Jenis kelamin pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->jenis_kelamin ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Pendidikan Terakhir</label>
                        <p class="text-xs text-gray-600 mb-2">Tingkat pendidikan tertinggi</p>
                        <input type="text" value="{{ $usulan->pegawai->ijazah_terakhir ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Universitas/Sekolah</label>
                        <p class="text-xs text-gray-600 mb-2">Nama institusi pendidikan</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_universitas_sekolah ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Program Studi/Jurusan</label>
                        <p class="text-xs text-gray-600 mb-2">Program studi yang diambil</p>
                        <input type="text" value="{{ $usulan->pegawai->nama_prodi_jurusan ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Kepegawaian --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="briefcase" class="w-6 h-6 mr-3"></i>
                    Data Kepegawaian
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Pegawai</label>
                        <p class="text-xs text-gray-600 mb-2">Kategori pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->jenis_pegawai ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Status Kepegawaian</label>
                        <p class="text-xs text-gray-600 mb-2">Status kepegawaian</p>
                        <input type="text" value="{{ $usulan->pegawai->status_kepegawaian ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Unit Kerja</label>
                        <p class="text-xs text-gray-600 mb-2">Unit kerja pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->unit_kerja ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Sub Unit Kerja</label>
                        <p class="text-xs text-gray-600 mb-2">Sub unit kerja pegawai</p>
                        <input type="text" value="{{ $usulan->pegawai->sub_unit_kerja ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dokumen Kepegawaian --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
                    Dokumen Kepegawaian
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($documentFields as $field => $config)
                        @if($usulan->pegawai->$field)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                        <i data-lucide="{{ $config['icon'] }}" class="w-4 h-4"></i>
                                        {{ $config['label'] }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $usulan->pegawai->$field }}</p>
                                </div>
                                <a href="{{ route('pegawai-unmul.profile.show-document', ['field' => $field]) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Lihat
                                </a>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Beban Kinerja Dosen (BKD) --}}
        @if(isset($usulan->data_usulan['bkd']) && !empty($usulan->data_usulan['bkd']))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="clipboard-list" class="w-6 h-6 mr-3"></i>
                    Beban Kinerja Dosen (BKD)
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($usulan->data_usulan['bkd'] as $semester => $bkdData)
                        @if(isset($bkdData['file_path']) && $bkdData['file_path'])
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        BKD Semester {{ $semester }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $bkdData['original_name'] ?? 'Dokumen BKD' }}</p>
                                </div>
                                <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => 'bkd_semester_' . $semester]) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Lihat
                                </a>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Karya Ilmiah --}}
        @if(isset($usulan->data_usulan['karya_ilmiah']) && !empty($usulan->data_usulan['karya_ilmiah']))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="book-open" class="w-6 h-6 mr-3"></i>
                    Karya Ilmiah
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Jenis Karya Ilmiah</label>
                        <p class="text-xs text-gray-600 mb-2">Jenis karya ilmiah yang diajukan</p>
                        <input type="text" value="{{ $usulan->data_usulan['karya_ilmiah']['jenis_karya'] ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Nama Jurnal</label>
                        <p class="text-xs text-gray-600 mb-2">Nama jurnal publikasi</p>
                        <input type="text" value="{{ $usulan->data_usulan['karya_ilmiah']['nama_jurnal'] ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Judul Artikel</label>
                        <p class="text-xs text-gray-600 mb-2">Judul artikel yang dipublikasikan</p>
                        <input type="text" value="{{ $usulan->data_usulan['karya_ilmiah']['judul_artikel'] ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Penerbit</label>
                        <p class="text-xs text-gray-600 mb-2">Penerbit jurnal</p>
                        <input type="text" value="{{ $usulan->data_usulan['karya_ilmiah']['penerbit_artikel'] ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>

                {{-- Links --}}
                @if(isset($usulan->data_usulan['karya_ilmiah']['links']))
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Link Publikasi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($usulan->data_usulan['karya_ilmiah']['links'] as $type => $link)
                            @if($link)
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $type) }}</label>
                                <a href="{{ $link }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm break-all">
                                    {{ $link }}
                                </a>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Syarat Khusus (Guru Besar) --}}
        @if(isset($usulan->data_usulan['syarat_khusus']) && !empty($usulan->data_usulan['syarat_khusus']))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="award" class="w-6 h-6 mr-3"></i>
                    Syarat Khusus Guru Besar
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Syarat Khusus</label>
                        <p class="text-xs text-gray-600 mb-2">Syarat khusus untuk pengajuan Guru Besar</p>
                        <input type="text" value="{{ $usulan->data_usulan['syarat_khusus']['syarat_guru_besar'] ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Deskripsi</label>
                        <p class="text-xs text-gray-600 mb-2">Deskripsi syarat khusus</p>
                        <input type="text" value="{{ $usulan->data_usulan['syarat_khusus']['deskripsi_syarat'] ?? '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Dokumen Usulan --}}
        @if(isset($usulan->data_usulan['dokumen_usulan']) && !empty($usulan->data_usulan['dokumen_usulan']))
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="file-text" class="w-6 h-6 mr-3"></i>
                    Dokumen Usulan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($usulan->data_usulan['dokumen_usulan'] as $docType => $docData)
                        @if(isset($docData['path']))
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $docType) }}</h4>
                                    <p class="text-sm text-gray-600">{{ $docData['original_name'] ?? 'Dokumen' }}</p>
                                </div>
                                                                 <a href="{{ route('pegawai-unmul.usulan-jabatan.show-document', ['usulanJabatan' => $usulan->id, 'field' => $docType]) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Lihat
                                </a>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Catatan Pengusul --}}
        @if(isset($usulan->data_usulan['catatan_pengusul']) && $usulan->data_usulan['catatan_pengusul'])
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="message-square" class="w-6 h-6 mr-3"></i>
                    Catatan Pengusul
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-gray-800">{{ $usulan->data_usulan['catatan_pengusul'] }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Catatan Verifikator --}}
        @if($usulan->catatan_verifikator)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="clipboard-check" class="w-6 h-6 mr-3"></i>
                    Catatan Verifikator
                </h2>
            </div>
            <div class="p-6">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-amber-800">{{ $usulan->catatan_verifikator }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Metadata --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i data-lucide="info" class="w-6 h-6 mr-3"></i>
                    Informasi Sistem
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Tanggal Dibuat</label>
                        <p class="text-xs text-gray-600 mb-2">Waktu usulan pertama kali dibuat</p>
                        <input type="text" value="{{ $usulan->created_at ? \Carbon\Carbon::parse($usulan->created_at)->isoFormat('D MMMM YYYY HH:mm') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Terakhir Diupdate</label>
                        <p class="text-xs text-gray-600 mb-2">Waktu terakhir usulan diperbarui</p>
                        <input type="text" value="{{ $usulan->updated_at ? \Carbon\Carbon::parse($usulan->updated_at)->isoFormat('D MMMM YYYY HH:mm') : '-' }}"
                               class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
