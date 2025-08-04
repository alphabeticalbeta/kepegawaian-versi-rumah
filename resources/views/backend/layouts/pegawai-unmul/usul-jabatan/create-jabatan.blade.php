@extends('backend.layouts.pegawai-unmul.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Judul Halaman --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Formulir Pengajuan Usulan Jabatan</h1>
        <p class="mt-2 text-gray-600">
            Silakan lengkapi semua informasi yang dibutuhkan untuk mengajukan kenaikan jabatan fungsional.
        </p>
    </div>

    {{-- Informasi Periode Usulan --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-bold text-indigo-700 mb-4 flex items-center">
            <i data-lucide="calendar" class="w-5 h-5 mr-2"></i>
            Informasi Periode Usulan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="periode" class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                <input type="text" id="periode" value="{{ $daftarPeriode->nama_periode }}"
                       class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" disabled>
                <input type="hidden" name="periode_usulan_id" value="{{ $daftarPeriode->id }}">
            </div>
            <div>
                <label for="masa_berlaku" class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku</label>
                <input type="text" id="masa_berlaku"
                       value="{{ \Carbon\Carbon::parse($daftarPeriode->tanggal_mulai)->isoFormat('D MMM YYYY') }} - {{ \Carbon\Carbon::parse($daftarPeriode->tanggal_selesai)->isoFormat('D MMM YYYY') }}"
                       class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" disabled>
            </div>
        </div>
    </div>

    <form action="{{ route('pegawai-unmul.usulan-jabatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Data Profil & Dokumen --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold text-indigo-700 mb-4 flex items-center">
                <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                Data Profil &amp; Dokumen
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Kolom kiri: Data Pribadi & Kepegawaian --}}
                <div class="space-y-6">
                    {{-- A. Data Pribadi --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h3 class="text-lg font-medium text-indigo-600 flex items-center mb-3">
                            <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                            A.&nbsp;Data Pribadi
                        </h3>
                        <div class="space-y-2 text-sm">
                            @foreach ([
                                'jenis_pegawai'      => ['Jenis Pegawai', $pegawai->jenis_pegawai],
                                'status_kepegawaian' => ['Status Kepegawaian', $pegawai->status_kepegawaian],
                                'nip'               => ['NIP', $pegawai->nip],
                                'nuptk'             => ['NUPTK', $pegawai->nuptk],
                                'gelar_depan'       => ['Gelar Depan', $pegawai->gelar_depan],
                                'nama_lengkap'      => ['Nama Lengkap', $pegawai->nama_lengkap],
                                'gelar_belakang'    => ['Gelar Belakang', $pegawai->gelar_belakang],
                                'email'             => ['Email', $pegawai->email],
                                'tempat_lahir'      => ['Tempat Lahir', $pegawai->tempat_lahir],
                                'tanggal_lahir'     => ['Tanggal Lahir', optional($pegawai->tanggal_lahir)->isoFormat('D MMMM YYYY')],
                                'jenis_kelamin'     => ['Jenis Kelamin', $pegawai->jenis_kelamin],
                                'agama'             => ['Agama', $pegawai->agama],
                                'status_perkawinan' => ['Status Perkawinan', $pegawai->status_perkawinan],
                                'nomor_handphone'   => ['Nomor HP', $pegawai->nomor_handphone],
                                'alamat'            => ['Alamat', $pegawai->alamat],
                            ] as $key => [$label, $value])
                                <p>
                                    <span class="text-gray-500">{{ $label }}:</span>
                                    {{ $value ?? '-' }}
                                    <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $pegawai[$key] }}">
                                </p>
                            @endforeach
                        </div>
                    </div>

                    {{-- B. Data Kepegawaian --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h3 class="text-lg font-medium text-indigo-600 flex items-center mb-3">
                            <i data-lucide="briefcase" class="w-5 h-5 mr-2"></i>
                            B.&nbsp;Data Kepegawaian
                        </h3>
                        <div class="space-y-2 text-sm">
                            @php
                                $kep = [
                                    'pangkat_saat_usul'    => ['Pangkat',         $pegawai->pangkat?->pangkat],
                                    'golongan_saat_usul'   => ['Golongan',        $pegawai->pangkat?->golongan],
                                    'tmt_pangkat'          => ['TMT Pangkat',     optional($pegawai->tmt_pangkat)->isoFormat('D MMMM YYYY')],
                                    'jabatan_saat_usul'    => ['Jabatan',         $pegawai->jabatan?->jabatan],
                                    'tmt_jabatan'          => ['TMT Jabatan',     optional($pegawai->tmt_jabatan)->isoFormat('D MMMM YYYY')],
                                    'tmt_cpns'             => ['TMT CPNS',        optional($pegawai->tmt_cpns)->isoFormat('D MMMM YYYY')],
                                    'tmt_pns'              => ['TMT PNS',         optional($pegawai->tmt_pns)->isoFormat('D MMMM YYYY')],
                                    'unit_kerja_saat_usul' => ['Unit Kerja',     $pegawai->unitKerja?->nama],
                                ];
                            @endphp
                            @foreach ($kep as $key => [$label, $value])
                                <p>
                                    <span class="text-gray-500">{{ $label }}:</span>
                                    {{ $value ?? '-' }}
                                    <input type="hidden" name="snapshot[{{ $key }}]"
                                           value="{{ is_string($value) ? $value : $pegawai->{$key} }}">
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kolom kanan: Pendidikan, Kinerja & Dokumen --}}
                <div class="space-y-6">
                    {{-- C. Data Pendidikan & Fungsional --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h3 class="text-lg font-medium text-indigo-600 flex items-center mb-3">
                            <i data-lucide="book-open" class="w-5 h-5 mr-2"></i>
                            C.&nbsp;Data Pendidikan &amp; Fungsional
                        </h3>
                        <div class="space-y-2 text-sm">
                            @foreach ([
                                'pendidikan_terakhir'    => ['Pendidikan Terakhir',    $pegawai->pendidikan_terakhir],
                                'mata_kuliah_diampu'     => ['Mata Kuliah Diampu',     $pegawai->mata_kuliah_diampu],
                                'ranting_ilmu_kepakaran' => ['Bidang Kepakaran',       $pegawai->ranting_ilmu_kepakaran],
                                'url_profil_sinta'       => ['Profil SINTA',           $pegawai->url_profil_sinta],
                            ] as $key => [$label, $value])
                                <p>
                                    <span class="text-gray-500">{{ $label }}:</span>
                                    {{ $value ?? '-' }}
                                    <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $value }}">
                                </p>
                            @endforeach
                        </div>
                    </div>

                    {{-- D. Data Kinerja --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h3 class="text-lg font-medium text-indigo-600 flex items-center mb-3">
                            <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2"></i>
                            D.&nbsp;Data Kinerja
                        </h3>
                        <div class="space-y-2 text-sm">
                            @foreach ([
                                'predikat_kinerja_tahun_pertama' => ['Predikat Kinerja Thn. 1', $pegawai->predikat_kinerja_tahun_pertama],
                                'predikat_kinerja_tahun_kedua'   => ['Predikat Kinerja Thn. 2', $pegawai->predikat_kinerja_tahun_kedua],
                                'nilai_konversi'                 => ['Nilai Konversi',          $pegawai->nilai_konversi],
                            ] as $key => [$label, $value])
                                <p>
                                    <span class="text-gray-500">{{ $label }}:</span>
                                    {{ $value ?? '-' }}
                                    <input type="hidden" name="snapshot[{{ $key }}]" value="{{ $value }}">
                                </p>
                            @endforeach
                        </div>
                    </div>

                    {{-- E. Dokumen Profil --}}
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <h3 class="text-lg font-medium text-indigo-600 flex items-center mb-3">
                            <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                            E.&nbsp;Dokumen Profil
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ([
                                'ijazah_terakhir'           => 'Ijazah Terakhir',
                                'transkrip_nilai_terakhir'  => 'Transkrip Nilai Terakhir',
                                'sk_pangkat_terakhir'       => 'SK Pangkat Terakhir',
                                'sk_jabatan_terakhir'       => 'SK Jabatan Terakhir',
                                'skp_tahun_pertama'         => 'SKP Tahun Pertama',
                                'skp_tahun_kedua'           => 'SKP Tahun Kedua',
                                'pak_konversi'              => 'PAK Konversi',
                                'sk_cpns'                   => 'SK CPNS',
                                'sk_pns'                    => 'SK PNS',
                                'sk_penyetaraan_ijazah'     => 'SK Penyetaraan Ijazah',
                                'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
                            ] as $key => $label)
                                <div class="flex items-start justify-between p-2 bg-white border border-gray-200 rounded-md">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i data-lucide="file" class="w-4 h-4 text-gray-500"></i>
                                        <span>{{ $label }}</span>
                                    </div>
                                    <div class="flex flex-col items-end text-xs">
                                        @if ($pegawai->{$key})
                                            {{-- <span class="max-w-[140px] truncate text-gray-700">{{ basename($pegawai->{$key}) }}</span> --}}
                                            <a href="{{ asset('storage/' . $pegawai->{$key}) }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 mt-1">
                                                <i data-lucide="eye" class="w-4 h-4"></i>Lihat
                                            </a>
                                        @else
                                            <span class="italic text-gray-400">Belum ada</span>
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

        {{-- Karya Ilmiah & Artikel --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-bold text-indigo-700 mb-4 flex items-center">
                <i data-lucide="book" class="w-5 h-5 mr-2"></i>
                Karya Ilmiah &amp; Artikel
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ([
                    'karya_ilmiah'      => ['Karya Ilmiah',     'Jenis karya ilmiah'],
                    'nama_jurnal'       => ['Nama Jurnal',      'Nama jurnal tempat terbit'],
                    'judul_artikel'     => ['Judul Artikel',    'Judul artikel ilmiah'],
                    'penerbit_artikel'  => ['Penerbit Artikel', 'Nama penerbit'],
                    'volume_artikel'    => ['Volume Artikel',   'Volume (edisi)'],
                    'nomor_artikel'     => ['Nomor Artikel',    'Nomor artikel / issue'],
                    'edisi_artikel'     => ['Edisi Artikel (Tahun)', 'Edisi atau tahun artikel'],
                    'halaman_artikel'   => ['Halaman Artikel',  'Halaman artikel'],
                    'issn_artikel'      => ['ISSN Artikel',     'ISSN jurnal'],
                    'link_artikel'      => ['Link Artikel',     'Tautan ke artikel online'],
                ] as $name => [$label, $placeholder])
                    <div>
                        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                        <input id="{{ $name }}" name="{{ $name }}" type="text" value="{{ old($name) }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm" placeholder="{{ $placeholder }}">
                        @error($name)<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end items-center gap-4">
            <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}"
               class="text-sm font-medium text-gray-600 hover:text-gray-900">Batal</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Kirim Usulan
            </button>
        </div>
    </form>
</div>
@endsection
