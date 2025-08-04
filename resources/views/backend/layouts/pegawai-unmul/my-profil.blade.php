@extends('backend.layouts.pegawai-unmul.app')

@section('title', $isEditing ? 'Edit Profil Saya' : 'Profil Saya')

@php
    // Helper dan daftar dokumen
    function formatDate($date) {
        return $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : '-';
    }
    $documentFields = [
        'ijazah_terakhir' => 'Ijazah Terakhir',
        'transkrip_nilai_terakhir' => 'Transkrip Nilai Terakhir', 'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
        'sk_jabatan_terakhir' => 'SK Jabatan Terakhir', 'skp_tahun_pertama' => 'SKP Tahun Pertama',
        'skp_tahun_kedua' => 'SKP Tahun Kedua', 'sk_cpns' => 'SK CPNS', 'sk_pns' => 'SK PNS',
        'pak_konversi' => 'PAK Konversi', 'sk_penyetaraan_ijazah' => 'SK Penyetaraan Ijazah',
        'disertasi_thesis_terakhir' => 'Disertasi/Thesis Terakhir',
    ];
@endphp

@section('content')
<div class="bg-slate-50/50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <form action="{{ route('pegawai-unmul.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($isEditing)
                @method('PUT')
            @endif

            {{-- Header Halaman --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-6">
                <div class="flex flex-wrap gap-4 justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-extrabold leading-tight text-slate-900">{{ $isEditing ? 'Edit Profil Saya' : 'Profil Saya' }}</h2>
                        <p class="text-sm text-slate-500 mt-1"> {{ $isEditing ? 'Perbarui informasi kepegawaian Anda di bawah ini.' : 'Informasi detail mengenai data kepegawaian Anda.' }}</p>
                    </div>
                    @if($isEditing)
                        <div class="flex items-center gap-4">
                            <a href="{{ route('pegawai-unmul.profile.show') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-lg hover:bg-slate-200 transition">Batal</a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-lg shadow-lg">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                            </button>
                        </div>
                    @else
                        <a href="{{ route('pegawai-unmul.profile.edit') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-lg shadow-lg">
                            <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Profil
                        </a>
                    @endif
                </div>
            </div>

            {{-- Notifikasi --}}
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
             @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Konten Profil --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                {{-- KOLOM KIRI (INFO UTAMA) --}}
                <div class="xl:col-span-1 space-y-8">
                    {{-- Kartu Profil Utama --}}
                    <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100 text-center">
                        <img src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai->nama_lengkap) }}" alt="Foto Profil" class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-white shadow-lg mb-4">
                        @if($isEditing)
                             <label for="foto" class="text-sm font-medium text-indigo-600 cursor-pointer hover:underline">Ganti Foto</label>
                             <input type="file" id="foto" name="foto" class="hidden">
                        @else
                            <h3 class="text-xl font-bold text-slate-800">{{ $pegawai->gelar_depan }} {{ $pegawai->nama_lengkap }} {{ $pegawai->gelar_belakang }}</h3>
                            <p class="text-sm text-slate-500">{{ $pegawai->nip }}</p>
                            <p class="text-sm text-indigo-600 font-medium mt-1">{{ $pegawai->email }}</p>
                        @endif
                    </div>

                    {{-- Kartu Pangkat & Jabatan --}}
                    <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="shield-check" class="w-5 h-5 mr-3 text-indigo-500"></i>Pangkat & Jabatan</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center"><span class="text-slate-500">Pangkat:</span>
                                @if($isEditing)
                                    <select name="pangkat_terakhir_id" class="w-2/3 text-right font-semibold text-slate-700 border-gray-300 rounded-md shadow-sm">
                                        @foreach($pangkats as $pangkat)
                                            <option value="{{ $pangkat->id }}" {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id) == $pangkat->id ? 'selected' : '' }}>{{ $pangkat->pangkat }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="font-semibold text-slate-700 text-right">{{ $pegawai->pangkat?->pangkat ?? '-' }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center"><span class="text-slate-500">TMT Pangkat:</span>
                                @if($isEditing)
                                     <input type="date" name="tmt_pangkat" value="{{ old('tmt_pangkat', $pegawai->tmt_pangkat ? $pegawai->tmt_pangkat->format('Y-m-d') : '') }}" class="w-2/3 text-right font-semibold text-slate-700 border-gray-300 rounded-md shadow-sm">
                                @else
                                    <span class="font-semibold text-slate-700">{{ formatDate($pegawai->tmt_pangkat) }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center"><span class="text-slate-500">Jabatan:</span>
                                @if($isEditing)
                                    <select name="jabatan_terakhir_id" class="w-2/3 text-right font-semibold text-slate-700 border-gray-300 rounded-md shadow-sm">
                                        @foreach($jabatans as $jabatan)
                                            <option value="{{ $jabatan->id }}" {{ old('jabatan_terakhir_id', $pegawai->jabatan_terakhir_id) == $jabatan->id ? 'selected' : '' }}>{{ $jabatan->jabatan }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="font-semibold text-slate-700 text-right">{{ $pegawai->jabatan?->jabatan ?? '-' }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center"><span class="text-slate-500">TMT Jabatan:</span>
                                 @if($isEditing)
                                    <input type="date" name="tmt_jabatan" value="{{ old('tmt_jabatan', $pegawai->tmt_jabatan ? $pegawai->tmt_jabatan->format('Y-m-d') : '') }}" class="w-2/3 text-right font-semibold text-slate-700 border-gray-300 rounded-md shadow-sm">
                                @else
                                    <span class="font-semibold text-slate-700">{{ formatDate($pegawai->tmt_jabatan) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kartu Kinerja --}}
                    <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="trending-up" class="w-5 h-5 mr-3 text-indigo-500"></i>Informasi Kinerja</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center"><span class="text-slate-500">Predikat Kinerja Thn. 1:</span>
                                @if($isEditing)
                                    <input type="text" name="predikat_kinerja_tahun_pertama" value="{{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama) }}" class="w-1/2 text-right font-semibold text-slate-700 border-gray-300 rounded-md shadow-sm">
                                @else
                                    <span class="font-semibold text-slate-700">{{ $pegawai->predikat_kinerja_tahun_pertama ?? '-' }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center"><span class="text-slate-500">Predikat Kinerja Thn. 2:</span>
                                @if($isEditing)
                                    <input type="text" name="predikat_kinerja_tahun_kedua" value="{{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua) }}" class="w-1/2 text-right font-semibold text-slate-700 border-gray-300 rounded-md shadow-sm">
                                @else
                                    <span class="font-semibold text-slate-700">{{ $pegawai->predikat_kinerja_tahun_kedua ?? '-' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="xl:col-span-2 space-y-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100"
                         x-data="{
                             jenisPegawai: '{{ old('jenis_pegawai', $pegawai->jenis_pegawai) ?? 'Dosen' }}',
                             statuses: {
                                 'Dosen': ['Dosen PNS', 'Dosen PPPK', 'Dosen Non ASN'],
                                 'Tenaga Kependidikan': ['Tenaga Kependidikan PNS', 'Tenaga Kependidikan PPPK', 'Tenaga Kependidikan Non ASN']
                             },
                             get availableStatuses() { return this.statuses[this.jenisPegawai] || [] }
                         }">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="user-round" class="w-5 h-5 mr-3 text-indigo-500"></i>Detail Informasi</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="sm:col-span-1">
                                <dt class="text-slate-500">Nama Lengkap</dt>
                                @if($isEditing)
                                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nama_lengkap ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">NIP</dt>
                                @if($isEditing)
                                    <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nip ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Email</dt>
                                @if($isEditing)
                                    <input type="email" name="email" value="{{ old('email', $pegawai->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->email ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Gelar Depan</dt>
                                @if($isEditing)
                                    <input type="text" name="gelar_depan" value="{{ old('gelar_depan', $pegawai->gelar_depan) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->gelar_depan ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Gelar Belakang</dt>
                                @if($isEditing)
                                    <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang', $pegawai->gelar_belakang) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->gelar_belakang ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Jenis Pegawai</dt>
                                @if($isEditing)
                                    <select name="jenis_pegawai" x-model="jenisPegawai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="Dosen">Dosen</option>
                                        <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                                    </select>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->jenis_pegawai ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Status Kepegawaian</dt>
                                @if($isEditing)
                                    <select name="status_kepegawaian" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <template x-for="status in availableStatuses" :key="status">
                                            <option :value="status" :selected="status === '{{ old('status_kepegawian', $pegawai->status_kepegawian) }}'" x-text="status"></option>
                                        </template>
                                    </select>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->status_kepegawian ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">No. Kartu Pegawai</dt>
                                @if($isEditing)
                                    <input type="text" name="nomor_kartu_pegawai" value="{{ old('nomor_kartu_pegawai', $pegawai->nomor_kartu_pegawai) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nomor_kartu_pegawai ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Pendidikan Terakhir</dt>
                                @if($isEditing)
                                    <input type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->pendidikan_terakhir ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Tempat, Tgl Lahir</dt>
                                @if($isEditing)
                                    <div class="flex gap-2">
                                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}" class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm">
                                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}" class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm">
                                    </div>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->tempat_lahir ?? '-' }}, {{ formatDate($pegawai->tanggal_lahir) }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Jenis Kelamin</dt>
                                @if($isEditing)
                                    <select name="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="Laki-Laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->jenis_kelamin ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">TMT CPNS</dt>
                                @if($isEditing)
                                    <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ formatDate($pegawai->tmt_cpns) }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">TMT PNS</dt>
                                @if($isEditing)
                                    <input type="date" name="tmt_pns" value="{{ old('tmt_pns', $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ formatDate($pegawai->tmt_pns) }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-2"><dt class="text-slate-500">Nomor HP</dt>
                                @if($isEditing)
                                    <input type="text" name="nomor_handphone" value="{{ old('nomor_handphone', $pegawai->nomor_handphone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nomor_handphone ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-2"><span class="text-slate-500">Unit Kerja:</span>
                                @if($isEditing)
                                    <select name="unit_kerja_terakhir_id" class="w-2/3 text-right font-semibold text-slate-700 border-gray-300 rounded-md shadow-sm">
                                        @foreach($unitKerjas as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_kerja_terakhir_id', $pegawai->unit_kerja_terakhir_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="font-semibold text-slate-700 text-right">{{ $pegawai->unitKerja?->nama ?? '-' }}</span>
                                @endif
                            </div>
                        </dl>
                    </div>

                    @if($pegawai->jenis_pegawai == 'Dosen' || $isEditing)
                    <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100" x-show="jenisPegawai === 'Dosen'">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="book-marked" class="w-5 h-5 mr-3 text-indigo-500"></i>Informasi Dosen</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="sm:col-span-1"><dt class="text-slate-500">NUPTK</dt>
                                @if($isEditing)
                                    <input type="text" name="nuptk" value="{{ old('nuptk', $pegawai->nuptk) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nuptk ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1"><dt class="text-slate-500">Nilai Konversi</dt>
                                @if($isEditing)
                                    <input type="text" name="nilai_konversi" value="{{ old('nilai_konversi', $pegawai->nilai_konversi) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->nilai_konversi ?? '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-2"><dt class="text-slate-500">Kepakaran</dt>
                                @if($isEditing)
                                    <textarea name="ranting_ilmu_kepakaran" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('ranting_ilmu_kepakaran', $pegawai->ranting_ilmu_kepakaran) }}</textarea>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->ranting_ilmu_kepakaran ?: '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-2"><dt class="text-slate-500">Mata Kuliah Diampu</dt>
                                @if($isEditing)
                                    <textarea name="mata_kuliah_diampu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('mata_kuliah_diampu', $pegawai->mata_kuliah_diampu) }}</textarea>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $pegawai->mata_kuliah_diampu ?: '-' }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-2"><dt class="text-slate-500">URL Profil Sinta</dt>
                                @if($isEditing)
                                    <input type="url" name="url_profil_sinta" value="{{ old('url_profil_sinta', $pegawai->url_profil_sinta) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @else
                                    <dd class="mt-1 font-semibold text-indigo-600 hover:underline"><a href="{{ $pegawai->url_profil_sinta }}" target="_blank" rel="noopener noreferrer">{{ $pegawai->url_profil_sinta ?: '-' }}</a></dd>
                                @endif
                            </div>
                        </dl>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                <h3 class="text-lg font-semibold text-slate-800 flex items-center mb-4 border-b pb-3"><i data-lucide="folder-kanban" class="w-5 h-5 mr-3 text-indigo-500"></i>Dokumen Terlampir</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($documentFields as $field => $label)
                        <div class="p-4 rounded-lg border border-slate-200">
                            <label for="{{ $field }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
                            @if($pegawai->$field)
                                <a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai->id, 'field' => $field]) }}" target="_blank" class="text-xs text-green-600 hover:underline mt-1 inline-block">File sudah ada. Lihat file.</a>
                            @else
                                <span class="text-xs text-slate-400 mt-1 inline-block">Belum ada file.</span>
                            @endif
                            @if($isEditing)
                                <input type="file" name="{{ $field }}" id="{{ $field }}" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
