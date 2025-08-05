@extends('backend.layouts.pegawai-unmul.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
     {{-- Pesan Notifikasi Sukses atau Error --}}
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


    {{-- Judul Halaman --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Formulir Pengajuan Usulan Jabatan</h1>
        <p class="mt-2 text-gray-600">
            Silakan lengkapi semua informasi yang dibutuhkan untuk mengajukan kenaikan jabatan fungsional.
        </p>
    </div>

    @if($daftarPeriode->count() == 0 || !$jabatanTujuan)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="py-1"><svg class="h-5 w-5 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 11-2 0 1 1 0 012 0zm-1-3a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z" clip-rule="evenodd"/></svg></div>
                <div>
                    <p class="font-bold text-yellow-800">Tidak Dapat Mengajukan Usulan</p>
                    @if($daftarPeriode->count() == 0)
                        <p class="text-sm text-yellow-700">Saat ini tidak ada periode pengajuan usulan jabatan yang sedang dibuka.</p>
                    @else
                        <p class="text-sm text-yellow-700">Anda sudah berada di jenjang jabatan fungsional tertinggi.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <form action="{{ isset($usulan) && $usulan->exists ? route('pegawai-unmul.usulan-jabatan.update', $usulan) : route('pegawai-unmul.usulan-jabatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if (isset($usulan) && $usulan->exists)
            @method('PUT')
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

                    {{-- [PERBAIKAN] Input periode_usulan_id sekarang ada di dalam form utama --}}
                    <input type="hidden" name="periode_usulan_id" value="{{ $daftarPeriode->id ?? '' }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800">Masa Berlaku</sabel>
                    <p class="text-xs text-gray-600 mb-2">Rentang waktu periode usulan</p>
                    <input type="text" value="{{ $daftarPeriode ? \Carbon\Carbon::parse($daftarPeriode->tanggal_mulai)->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($daftarPeriode->tanggal_selesai)->isoFormat('D MMM YYYY') : '-' }}" class="block w-full border-gray-200 rounded-lg shadow-sm bg-gray-100 px-4 py-3 text-gray-800 font-medium cursor-not-allowed" disabled>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ isset($usulan) && $usulan->exists ? route('pegawai-unmul.usulan-jabatan.update', $usulan) : route('pegawai-unmul.usulan-jabatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Jika ini adalah form edit, WAJIB sertakan method PUT --}}
        @if (isset($usulan) && $usulan->exists)
            @method('PUT')
        @endif

    <div class="bg-gradient-to-r from-indigo-50 via-white to-purple-50 border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <!-- Header with gradient background -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="user-circle" class="w-6 h-6 mr-3"></i>
                Informasi Pengusul
            </h3>
            <p class="text-indigo-100 text-sm mt-1">Detail informasi pemohon usulan jabatan fungsional</p>
        </div>

        <!-- Content with improved layout -->
        <div class="px-6 py-6">
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

            <!-- Progress indicator -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span class="flex items-center">
                        <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                        Informasi pengusul telah terverifikasi
                    </span>
                    <span class="flex items-center">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></div>
                        Status: Aktif
                    </span>
                </div>
            </div>
        </div>
    </div>

        {{-- Data Profil & Dokumen --}}
        <div class="bg-gradient-to-br from-slate-50 to-blue-50 p-6 rounded-xl shadow-lg mb-8">
            <!-- Header Section -->
            <div class="border-b border-slate-200 pb-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-100 p-3 rounded-full">
                            <i data-lucide="user-circle" class="w-8 h-8 text-indigo-600"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">Data Profil & Dokumen Pegawai</h1>
                            <p class="text-slate-600 mt-1">Informasi lengkap kepegawaian dan dokumen pendukung</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-1 gap-8">

                <!-- Column 1: Personal & Employment Data -->
                <div class="xl:col-span-1 space-y-6 ">
                    <!-- A. Data Pribadi -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200 ">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-t-xl">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                                A. Data Pribadi
                            </h3>
                        </div>
                        <div class="p-6 space-y-4 grid grid-cols-1 xl:grid-cols-2">
                            {{-- Data Pribadi --}}
                            @foreach ([
                                'jenis_pegawai'      => ['Jenis Pegawai', $pegawai->jenis_pegawai, 'briefcase'],
                                'status_kepegawaian' => ['Status Kepegawaian', $pegawai->status_kepegawaian, 'shield-check'],
                                'nip'               => ['NIP', $pegawai->nip, 'hash'],
                                'nuptk'             => ['NUPTK', $pegawai->nuptk, 'credit-card'],
                                'gelar_depan'       => ['Gelar Depan', $pegawai->gelar_depan, 'award'],
                                'nama_lengkap'      => ['Nama Lengkap', $pegawai->nama_lengkap, 'user'],
                                'gelar_belakang'    => ['Gelar Belakang', $pegawai->gelar_belakang, 'award'],
                                'email'             => ['Email', $pegawai->email, 'mail'],
                                'tempat_lahir'      => ['Tempat Lahir', $pegawai->tempat_lahir, 'map-pin'],
                                'tanggal_lahir'     => ['Tanggal Lahir', optional($pegawai->tanggal_lahir)->isoFormat('D MMMM YYYY'), 'calendar'],
                                'jenis_kelamin'     => ['Jenis Kelamin', $pegawai->jenis_kelamin, 'users'],
                                'nomor_handphone'   => ['Nomor HP', $pegawai->nomor_handphone, 'phone'],
                            ] as $key => [$label, $value, $icon])
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors">
                                    <div class="bg-white p-2 rounded-lg shadow-sm">
                                        <i data-lucide="{{ $icon }}" class="w-4 h-4 text-slate-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <dt class="text-sm font-medium text-slate-700">{{ $label }}</dt>
                                        <dd class="text-sm text-slate-900 mt-1 break-words">{{ $value ?? '-' }}</dd>
                                        <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $pegawai[$key] }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- B. Data Kepegawaian -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-4 rounded-t-xl">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                                B. Data Kepegawaian
                            </h3>
                        </div>
                        <div class="p-6 space-y-4 grid grid-cols-1 xl:grid-cols-2">
                            @php
                                $kep = [
                                    'pangkat_saat_usul'    => ['Pangkat',         $pegawai->pangkat?->pangkat, 'star'],
                                    'tmt_pangkat'          => ['TMT Pangkat',     optional($pegawai->tmt_pangkat)->isoFormat('D MMMM YYYY'), 'calendar-days'],
                                    'jabatan_saat_usul'    => ['Jabatan',         $pegawai->jabatan?->jabatan, 'crown'],
                                    'tmt_jabatan'          => ['TMT Jabatan',     optional($pegawai->tmt_jabatan)->isoFormat('D MMMM YYYY'), 'calendar-check'],
                                    'tmt_cpns'             => ['TMT CPNS',        optional($pegawai->tmt_cpns)->isoFormat('D MMMM YYYY'), 'calendar'],
                                    'tmt_pns'              => ['TMT PNS',         optional($pegawai->tmt_pns)->isoFormat('D MMMM YYYY'), 'calendar-plus'],
                                    'unit_kerja_saat_usul' => ['Unit Kerja',     $pegawai->unitKerja?->nama, 'building'],
                                ];
                            @endphp
                            @foreach ($kep as $key => [$label, $value, $icon])
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors">
                                    <div class="bg-white p-2 rounded-lg shadow-sm">
                                        <i data-lucide="{{ $icon }}" class="w-4 h-4 text-slate-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <dt class="text-sm font-medium text-slate-700">{{ $label }}</dt>
                                        <dd class="text-sm text-slate-900 mt-1 break-words">{{ $value ?? '-' }}</dd>
                                        <input type="hidden" name="snapshot[{{ $key }}]"
                                            value="{{ is_string($value) ? $value : $pegawai->{$key} }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Column 2: Education & Performance -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- C. Data Pendidikan & Fungsional -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-4 rounded-t-xl">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i data-lucide="graduation-cap" class="w-5 h-5 mr-3"></i>
                                C. Data Pendidikan & Fungsional
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach ([
                                'pendidikan_terakhir'    => ['Pendidikan Terakhir',    $pegawai->pendidikan_terakhir, 'book-open'],
                                'mata_kuliah_diampu'     => ['Mata Kuliah Diampu',     $pegawai->mata_kuliah_diampu, 'book'],
                                'ranting_ilmu_kepakaran' => ['Bidang Kepakaran',       $pegawai->ranting_ilmu_kepakaran, 'brain'],
                                'url_profil_sinta'       => ['Profil SINTA',           $pegawai->url_profil_sinta, 'external-link'],
                            ] as $key => [$label, $value, $icon])
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors">
                                    <div class="bg-white p-2 rounded-lg shadow-sm">
                                        <i data-lucide="{{ $icon }}" class="w-4 h-4 text-slate-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <dt class="text-sm font-medium text-slate-700">{{ $label }}</dt>
                                        <dd class="text-sm text-slate-900 mt-1 break-words">
                                            @if($key === 'url_profil_sinta' && $value)
                                                <a href="{{ $value }}" target="_blank" class="text-purple-600 hover:text-purple-800 underline">
                                                    {{ $value }}
                                                </a>
                                            @else
                                                {{ $value ?? '-' }}
                                            @endif
                                        </dd>
                                        <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $value }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- D. Data Kinerja -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-4 rounded-t-xl">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i data-lucide="trending-up" class="w-5 h-5 mr-3"></i>
                                D. Data Kinerja
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach ([
                                'predikat_kinerja_tahun_pertama' => ['Predikat Kinerja Thn. 1', $pegawai->predikat_kinerja_tahun_pertama, 'target'],
                                'predikat_kinerja_tahun_kedua'   => ['Predikat Kinerja Thn. 2', $pegawai->predikat_kinerja_tahun_kedua, 'target'],
                                'nilai_konversi'                 => ['Nilai Konversi',          $pegawai->nilai_konversi, 'calculator'],
                            ] as $key => [$label, $value, $icon])
                                <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors">
                                    <div class="bg-white p-2 rounded-lg shadow-sm">
                                        <i data-lucide="{{ $icon }}" class="w-4 h-4 text-slate-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <dt class="text-sm font-medium text-slate-700">{{ $label }}</dt>
                                        <dd class="text-sm text-slate-900 mt-1">
                                            @if($key === 'nilai_konversi' && $value)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $value }}
                                                </span>
                                            @else
                                                {{ $value ?? '-' }}
                                            @endif
                                        </dd>
                                        <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $value }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Column 3: Documents -->
                <div class="xl:col-span-1">
                    <!-- E. Dokumen Profil -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="bg-gradient-to-r from-rose-500 to-rose-600 text-white p-4 rounded-t-xl">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i data-lucide="folder" class="w-5 h-5 mr-3"></i>
                                E. Dokumen Profil
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 grid grid-cols-1 xl:grid-cols-2">
                                @foreach ([
                                    'ijazah_terakhir'           => ['Ijazah Terakhir', 'graduation-cap'],
                                    'transkrip_nilai_terakhir'  => ['Transkrip Nilai Terakhir', 'file-text'],
                                    'sk_pangkat_terakhir'       => ['SK Pangkat Terakhir', 'award'],
                                    'sk_jabatan_terakhir'       => ['SK Jabatan Terakhir', 'briefcase'],
                                    'skp_tahun_pertama'         => ['SKP Tahun Pertama', 'target'],
                                    'skp_tahun_kedua'           => ['SKP Tahun Kedua', 'target'],
                                    'pak_konversi'              => ['PAK Konversi', 'refresh-cw'],
                                    'sk_cpns'                   => ['SK CPNS', 'user-plus'],
                                    'sk_pns'                    => ['SK PNS', 'user-check'],
                                    'sk_penyetaraan_ijazah'     => ['SK Penyetaraan Ijazah', 'scale'],
                                    'disertasi_thesis_terakhir' => ['Disertasi/Thesis Terakhir', 'book-open'],
                                ] as $key => [$label, $icon])
                                    <div class="group flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition-all duration-200">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="bg-white p-2 rounded-lg shadow-sm group-hover:shadow-md transition-shadow">
                                                <i data-lucide="{{ $icon }}" class="w-4 h-4 text-slate-600"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-700 truncate">{{ $label }}</p>
                                                @if ($pegawai->{$key})
                                                    <p class="text-xs text-slate-500 mt-1">File tersedia</p>
                                                @else
                                                    <p class="text-xs text-slate-400 italic mt-1">Belum diunggah</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if ($pegawai->{$key})
                                                <div class="flex items-center gap-1">
                                                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                                    <a href="{{ asset('storage/' . $pegawai->{$key}) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors duration-200">
                                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                                        Lihat
                                                    </a>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-1">
                                                    <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                                                    <span class="px-3 py-1.5 text-xs font-medium text-slate-500 bg-slate-200 rounded-lg">
                                                        Tidak ada
                                                    </span>
                                                </div>
                                            @endif
                                            <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $pegawai->{$key} }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Karya Ilmiah & Artikel --}}
        <div class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 -m-8 mb-8 p-6 rounded-t-xl">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i data-lucide="book-open" class="w-6 h-6 mr-3"></i>
                    Karya Ilmiah &amp; Artikel
                </h2>
                <p class="text-indigo-100 mt-2">Lengkapi informasi karya ilmiah dan artikel yang akan disubmit</p>
            </div>

            <!-- Jenis Karya Ilmiah -->
            <div class="mb-8">
                <div class="bg-gray-50 rounded-lg p-6 border-l-4 border-indigo-500">
                    <label for="karya_ilmiah" class="block text-sm font-semibold text-gray-800 mb-3">
                        <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-2"></i>
                        Karya Ilmiah <span class="text-red-500">*</span>
                    </label>
                    <select id="karya_ilmiah" name="karya_ilmiah" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 bg-white">
                        <option value="">-- Pilih Jenis Karya Ilmiah --</option>
                        <option value="Jurnal Nasional Bereputasi" {{ old('karya_ilmiah', $usulan->data_usulan['karya_ilmiah'] ?? '') == 'Jurnal Nasional Bereputasi' ? 'selected' : '' }}>Jurnal Nasional Bereputasi</option>
                        <option value="Jurnal Internasional Bereputasi" {{ old('karya_ilmiah', $usulan->data_usulan['karya_ilmiah'] ?? '') == 'Jurnal Internasional Bereputasi' ? 'selected' : '' }}>Jurnal Internasional Bereputasi</option>
                    </select>
                    @error('karya_ilmiah')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Form Fields Grid -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-2 text-indigo-600"></i>
                    Informasi Artikel
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        // Definisikan array pertama untuk input teks
                        $textFields = [
                            'nama_jurnal'       => ['Nama Jurnal', 'Nama jurnal tempat terbit', 'bookmark', 'md:col-span-2'],
                            'judul_artikel'     => ['Judul Artikel', 'Judul artikel ilmiah', 'type', 'md:col-span-2'],
                            'penerbit_artikel'  => ['Penerbit Artikel', 'Nama penerbit', 'building', ''],
                            'volume_artikel'    => ['Volume Artikel', 'Volume (edisi)', 'layers', ''],
                            'nomor_artikel'     => ['Nomor Artikel', 'Nomor artikel / issue', 'hash', ''],
                            'edisi_artikel'     => ['Edisi Artikel (Tahun)', 'Edisi atau tahun artikel', 'calendar', ''],
                            'halaman_artikel'   => ['Halaman Artikel', 'Halaman artikel', 'file-minus', ''],
                        ];

                        // Definisikan array kedua untuk input link
                        $linkFields = [
                            'link_artikel' => ['Link Artikel', 'Tautan ke artikel online (wajib diisi)', 'link', 'md:col-span-2', true],
                            'link_sinta'   => ['Link SINTA', 'Tautan ke profil SINTA (opsional)', 'link-2', 'md:col-span-2', false],
                            'link_scopus'  => ['Link SCOPUS', 'Tautan ke profil SCOPUS (opsional)', 'link-2', 'md:col-span-2', false],
                            'link_scimago' => ['Link SCIMAGO', 'Tautan ke profil SCIMAGO (opsional)', 'link-2', 'md:col-span-2', false],
                            'link_wos'     => ['Link WoS', 'Tautan ke profil WoS (opsional)', 'link-2', 'md:col-span-2', false],
                        ];
                    @endphp

                    {{-- PERULANGAN UNTUK INPUT TEKS --}}
                    @foreach ($textFields as $name => [$label, $placeholder, $icon, $colSpan])
                        <div class="{{ $colSpan }}">
                            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="{{ $icon }}" class="w-4 h-4 inline mr-1"></i>
                                {{ $label }}
                            </label>
                            <input id="{{ $name }}" name="{{ $name }}" type="text"
                                value="{{ old($name, $usulan->data_usulan[$name] ?? '') }}"
                                class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4"
                                placeholder="{{ $placeholder }}">
                            @error($name)<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    @endforeach

                    {{-- PERULANGAN UNTUK INPUT LINK --}}
                    @foreach ($linkFields as $name => [$label, $placeholder, $icon, $colSpan, $isRequired])
                        <div class="{{ $colSpan }}">
                            <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="{{ $icon }}" class="w-4 h-4 inline mr-1"></i>
                                {{ $label }} @if($isRequired) <span class="text-red-500">*</span> @endif
                            </label>
                            <input id="{{ $name }}" name="{{ $name }}" type="url"
                                value="{{ old($name, $usulan->data_usulan[$name] ?? '') }}"
                                class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4"
                                placeholder="{{ $placeholder }}" {{ $isRequired ? 'required' : '' }}>
                            @error($name)<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Upload Documents -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                    <i data-lucide="upload" class="w-5 h-5 mr-2 text-indigo-600"></i>
                    Upload Dokumen Pendukung
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i data-lucide="award" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <label for="pakta_integritas" class="block text-sm font-semibold text-gray-800">
                                    Pakta Integritas <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-600">Surat Pakta Integritas</p>
                            </div>
                        </div>
                        @if(isset($usulan) && !empty($usulan->data_usulan['pakta_integritas']))
                            <a href="{{ asset('storage/' . $usulan->data_usulan['pakta_integritas']) }}" target="_blank" class="text-xs text-green-600 hover:underline mt-1 inline-block mb-2">
                                <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                            </a>
                        @endif
                        <input type="file" name="pakta_integritas" id="pakta_integritas" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 file:cursor-pointer cursor-pointer"
                            {{-- [FIX] 'required' dibuat kondisional --}}
                            @if(!$usulan || empty($usulan->data_usulan['pakta_integritas'])) required @endif>
                        <p class="mt-2 text-xs text-gray-500">File harus dalam format PDF, maksimal 1MB.</p>
                        @error('pakta_integritas')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i data-lucide="mail" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <label for="bukti_korespondensi" class="block text-sm font-semibold text-gray-800">
                                    Bukti Korespondensi <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-600">Surat korespondensi dengan jurnal</p>
                            </div>
                        </div>
                        @if(isset($usulan) && !empty($usulan->data_usulan['bukti_korespondensi']))
                            <a href="{{ asset('storage/' . $usulan->data_usulan['bukti_korespondensi']) }}" target="_blank" class="text-xs text-green-600 hover:underline mt-1 inline-block mb-2">
                                <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                            </a>
                        @endif
                        <input type="file" name="bukti_korespondensi" id="bukti_korespondensi" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 file:cursor-pointer cursor-pointer"
                            {{-- [FIX] 'required' dibuat kondisional --}}
                            @if(!$usulan || empty($usulan->data_usulan['bukti_korespondensi'])) required @endif>
                        <p class="mt-2 text-xs text-gray-500">File harus dalam format PDF, maksimal 1MB.</p>
                        @error('bukti_korespondensi')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i data-lucide="shield-check" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <label for="turnitin" class="block text-sm font-semibold text-gray-800">
                                    Turnitin <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-600">Laporan similarity check</p>
                            </div>
                        </div>
                        @if(isset($usulan) && !empty($usulan->data_usulan['turnitin']))
                            <a href="{{ asset('storage/' . $usulan->data_usulan['turnitin']) }}" target="_blank" class="text-xs text-green-600 hover:underline mt-1 inline-block mb-2">
                                <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                            </a>
                        @endif
                        <input type="file" name="turnitin" id="turnitin" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-100 file:text-green-700 hover:file:bg-green-200 file:cursor-pointer cursor-pointer"
                            {{-- [FIX] 'required' dibuat kondisional --}}
                            @if(!$usulan || empty($usulan->data_usulan['turnitin'])) required @endif>
                        <p class="mt-2 text-xs text-gray-500">File harus dalam format PDF, maksimal 1MB.</p>
                        @error('turnitin')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-200 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i data-lucide="file-text" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div>
                                <label for="upload_artikel" class="block text-sm font-semibold text-gray-800">
                                    Upload Artikel <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-600">File artikel lengkap</p>
                            </div>
                        </div>
                        @if(isset($usulan) && !empty($usulan->data_usulan['upload_artikel']))
                            <a href="{{ asset('storage/' . $usulan->data_usulan['upload_artikel']) }}" target="_blank" class="text-xs text-green-600 hover:underline mt-1 inline-block mb-2">
                                <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                            </a>
                        @endif
                        <input type="file" name="upload_artikel" id="upload_artikel" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-100 file:text-purple-700 hover:file:bg-purple-200 file:cursor-pointer cursor-pointer"
                            {{-- [FIX] 'required' dibuat kondisional --}}
                            @if(!$usulan || empty($usulan->data_usulan['upload_artikel'])) required @endif>
                        <p class="mt-2 text-xs text-gray-500">File harus dalam format PDF, maksimal 1MB.</p>
                        @error('upload_artikel')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                @if($jabatanTujuan && $jabatanTujuan->jabatan == 'Guru Besar')
                    <div class="space-y-4 pt-6 border-t"
                        x-data="{
                            syaratGb: '{{ old('syarat_guru_besar', $usulan->data_usulan['syarat_guru_besar'] ?? '') }}',
                            syaratDeskripsi: {
                                'hibah': 'Dokumen yang di-upload: MoU, SK Hibah, Laporan Hibah.',
                                'bimbingan': 'Dokumen yang di-upload: SK Pembimbing, Halaman Pengesahan, Cover Tesis yang dibimbing.',
                                'pengujian': 'Dokumen yang di-upload: SK Penguji, Berita Acara Hasil Ujian, Cover Tesis yang diuji.',
                                'reviewer': 'Dokumen yang di-upload: Surat Permohonan Reviewer, Dokumen yang di review.'
                            }
                        }">
                        <h4 class="text-md font-semibold text-gray-800">Syarat Khusus Pengajuan Guru Besar</h4>
                        <div>
                            <label for="syarat_guru_besar" class="block text-sm font-semibold text-gray-800 mb-1">Pilih Salah Satu Syarat <span class="text-red-500">*</span></label>
                            <select name="syarat_guru_besar" id="syarat_guru_besar" x-model="syaratGb" class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 bg-white rounded-xl shadow-sm">
                                <option value="">-- Silakan Pilih --</option>
                                <option value="hibah">Pernah mendapatkan hibah penelitian</option>
                                <option value="bimbingan">Pernah membimbing program doktor</option>
                                <option value="pengujian">Pernah menguji mahasiswa doktor</option>
                                <option value="reviewer">Sebagai reviewer jurnal internasional</option>
                            </select>
                        </div>

                        <div x-show="syaratGb" class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                            <p class="font-semibold">Keterangan Dokumen:</p>
                            <p x-text="syaratDeskripsi[syaratGb]"></p>
                        </div>

                        <div x-show="syaratGb">
                            <label for="bukti_syarat_guru_besar" class="block text-sm font-semibold text-gray-800 mb-1">Upload Bukti Pendukung <span class="text-red-500">*</span></label>

                            {{-- [FIX] Menampilkan file yang sudah ada --}}
                            @if(isset($usulan) && !empty($usulan->data_usulan['bukti_syarat_guru_besar']))
                                <a href="{{ asset('storage/' . $usulan->data_usulan['bukti_syarat_guru_besar']) }}" target="_blank" class="text-xs text-green-600 hover:underline mt-1 inline-block mb-2">
                                    <i data-lucide="check-circle" class="inline w-3 h-3 mr-1"></i> File sudah ada. Lihat file.
                                </a>
                            @endif

                            <input type="file" name="bukti_syarat_guru_besar" id="bukti_syarat_guru_besar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">File harus dalam format PDF, maksimal 1MB.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center gap-4">
            <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                Batal
            </a>
            <button type="submit" name="action" value="save_draft" class="px-6 py-2 bg-slate-500 text-white rounded-md shadow-sm hover:bg-slate-600">
                Simpan Draft
            </button>
            <button type="submit" name="action" value="submit_final" class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">
                Kirim Usulan
            </button>
        </div>
    </form>
</div>
@endsection
