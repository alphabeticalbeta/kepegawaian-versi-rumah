@extends('backend.layouts.pegawai-unmul.app')

@section('title', 'Profil Saya')

@section('content')
@php
    // Helper untuk menampilkan tanggal dengan format Indonesia
    function formatDate($date) {
        return $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : '-';
    }

    // Daftar dokumen untuk ditampilkan
    $documentFields = [
        'foto' => 'Foto',
        'ijazah_terakhir' => 'Ijazah Terakhir',
        'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir',
        'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
        'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
        'skp_tahun_pertama' => 'SKP Tahun Pertama',
        'skp_tahun_kedua' => 'SKP Tahun Kedua',
        'sk_cpns' => 'SK CPNS',
        'sk_pns' => 'SK PNS',
        'pak_konversi' => 'PAK Konversi',
        'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
        'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
    ];
@endphp

<div class="bg-slate-50/50 min-h-screen">
    <div class="container mx-auto px-4 py-8">

        {{-- Header Halaman --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-6">
            <div class="flex flex-wrap gap-4 justify-between items-center">
                <div>
                    <h2 class="text-3xl font-extrabold leading-tight text-slate-900">Profil Saya</h2>
                    <p class="text-sm text-slate-500 mt-1">Informasi detail mengenai data kepegawaian Anda.</p>
                </div>
                <a href="{{ route('pegawai-unmul.profile.edit') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit Profil</span>
                </a>
            </div>
        </div>

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-md shadow-green-500/10" role="alert">
                <div class="flex">
                    <div class="py-1"><i data-lucide="check-circle" class="w-6 h-6 text-green-500 mr-4"></i></div>
                    <div>
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Konten Profil --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            {{-- KOLOM KIRI (INFO UTAMA) --}}
            <div class="xl:col-span-1 space-y-8">
                {{-- Kartu Profil Utama --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100 text-center">
                    <img src="{{ $pegawai->foto ? Storage::url($pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) }}" alt="Foto Profil" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-white shadow-lg mb-4">
                    <h3 class="text-xl font-bold text-slate-800">{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }} {{ $pegawai->gelar_belakang }}</h3>
                    <p class="text-sm text-slate-500">{{ $pegawai->nip }}</p>
                    <p class="text-sm text-indigo-600 font-medium mt-1">{{ $pegawai->email }}</p>
                </div>

                {{-- Kartu Pangkat & Jabatan --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="shield-check" class="w-5 h-5 mr-3 text-indigo-500"></i>Pangkat & Jabatan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Pangkat:</span><span class="font-semibold text-slate-700 text-right">{{ $pegawai->pangkat?->pangkat ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">TMT Pangkat:</span><span class="font-semibold text-slate-700">{{ formatDate($pegawai->tmt_pangkat) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Jabatan:</span><span class="font-semibold text-slate-700 text-right">{{ $pegawai->jabatan?->jabatan ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">TMT Jabatan:</span><span class="font-semibold text-slate-700">{{ formatDate($pegawai->tmt_jabatan) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Unit Kerja:</span><span class="font-semibold text-slate-700 text-right">{{ $pegawai->unitKerja?->nama ?? '-' }}</span></div>
                    </div>
                </div>

                 {{-- Kartu Kinerja --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="trending-up" class="w-5 h-5 mr-3 text-indigo-500"></i>Informasi Kinerja</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">Predikat Kinerja Thn. 1:</span><span class="font-semibold text-slate-700">{{ $pegawai->predikat_kinerja_tahun_pertama ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Predikat Kinerja Thn. 2:</span><span class="font-semibold text-slate-700">{{ $pegawai->predikat_kinerja_tahun_kedua ?? '-' }}</span></div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (DETAIL DATA & DOKUMEN) --}}
            <div class="xl:col-span-2 space-y-8">
                {{-- Kartu Detail Informasi --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="user-round" class="w-5 h-5 mr-3 text-indigo-500"></i>Detail Informasi</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="sm:col-span-1"><dt class="text-slate-500">Jenis Pegawai</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->jenis_pegawai ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">Status Kepegawaian</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->status_kepegawaian ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">No. Kartu Pegawai</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nomor_kartu_pegawai ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">Pendidikan Terakhir</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->pendidikan_terakhir ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">Tempat, Tgl Lahir</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->tempat_lahir ?? '-' }}, {{ formatDate($pegawai->tanggal_lahir) }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">Jenis Kelamin</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->jenis_kelamin ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">TMT CPNS</dt><dd class="mt-1 font-semibold text-slate-800">{{ formatDate($pegawai->tmt_cpns) }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">TMT PNS</dt><dd class="mt-1 font-semibold text-slate-800">{{ formatDate($pegawai->tmt_pns) }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Nomor HP</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nomor_handphone ?? '-' }}</dd></div>
                    </dl>
                </div>

                {{-- Kartu Informasi Dosen (jika dosen) --}}
                @if($pegawai->jenis_pegawai == 'Dosen')
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                    <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="book-marked" class="w-5 h-5 mr-3 text-indigo-500"></i>Informasi Dosen</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="sm:col-span-1"><dt class="text-slate-500">NUPTK</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nuptk ?? '-' }}</dd></div>
                        <div class="sm:col-span-1"><dt class="text-slate-500">Nilai Konversi</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nilai_konversi ?? '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Kepakaran</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->ranting_ilmu_kepakaran ?: '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Mata Kuliah Diampu</dt><dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->mata_kuliah_diampu ?: '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">URL Profil Sinta</dt><dd class="mt-1 font-semibold text-indigo-600 hover:underline"><a href="{{ $pegawai->url_profil_sinta }}" target="_blank" rel="noopener noreferrer">{{ $pegawai->url_profil_sinta ?: '-' }}</a></dd></div>
                    </dl>
                </div>
                @endif


            </div>
        </div>
        {{-- Kartu Dokumen Terlampir --}}
        <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="folder-kanban" class="w-5 h-5 mr-3 text-indigo-500"></i>Dokumen Terlampir</h3>
            {{-- PERUBAHAN: Mengubah grid menjadi 1 kolom --}}
            <div class="grid grid-cols-1 gap-y-2">
                @foreach($documentFields as $field => $label)
                    @if($pegawai->$field)
                        <a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => $field]) }}" target="_blank" class="text-sm group">
                            <div class="flex items-center justify-between p-2.5 rounded-md hover:bg-indigo-50 transition-colors border border-transparent hover:border-indigo-200">
                                <div class="flex items-center">
                                    <i data-lucide="file-check-2" class="w-4 h-4 text-green-500 mr-3"></i>
                                    <span class="text-slate-600 group-hover:text-indigo-700 font-medium">{{ $label }}</span>
                                </div>
                                <i data-lucide="external-link" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600"></i>
                            </div>
                        </a>
                    @else
                        <div class="text-sm group">
                            <div class="flex items-center justify-between p-2.5 rounded-md bg-slate-50/80 border border-slate-200/60">
                                <div class="flex items-center">
                                    <i data-lucide="file-x-2" class="w-4 h-4 text-slate-400 mr-3"></i>
                                    <span class="text-slate-500">{{ $label }}</span>
                                </div>
                                <span class="text-xs font-medium text-slate-400">Kosong</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
