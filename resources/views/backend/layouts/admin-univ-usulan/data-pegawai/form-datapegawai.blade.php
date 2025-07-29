@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-semibold leading-tight mb-6">
        {{ isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai' }}
    </h2>

    {{-- Tampilkan error validasi umum jika ada --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">Terdapat beberapa kesalahan pada input Anda.</span>
        </div>
    @endif

    <form action="{{ isset($pegawai) ? route('backend.admin-univ-usulan.data-pegawai.update', $pegawai->id) : route('backend.admin-univ-usulan.data-pegawai.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        @if(isset($pegawai))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            {{-- ======================================================================= --}}
            {{-- ============================ KOLOM KIRI =============================== --}}
            {{-- ======================================================================= --}}
            <div class="space-y-6">
                {{-- KATEGORI: INFORMASI DASAR --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Dasar Pegawai</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="jenis_pegawai" class="block text-sm font-medium text-gray-700">Jenis Pegawai <span class="text-red-500">*</span></label>
                            <select id="jenis_pegawai" name="jenis_pegawai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="Dosen" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="Tenaga Kependidikan" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                            @error('jenis_pegawai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="nip" class="block text-sm font-medium text-gray-700">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip', $pegawai->nip ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="18 Karakter Numerik" maxlength="18">
                            @error('nip') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div id="field_nuptk" class="hidden">
                            <label for="nuptk" class="block text-sm font-medium text-gray-700">NUPTK <span class="text-red-500">*</span></label>
                            <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk', $pegawai->nuptk ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="16 Karakter Numerik" maxlength="16">
                            @error('nuptk') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="gelar_depan" class="block text-sm font-medium text-gray-700">Gelar Depan</label>
                            <input type="text" name="gelar_depan" id="gelar_depan" value="{{ old('gelar_depan', $pegawai->gelar_depan ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('gelar_depan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap (Tanpa Gelar) <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="gelar_belakang" class="block text-sm font-medium text-gray-700">Gelar Belakang</label>
                            <input type="text" name="gelar_belakang" id="gelar_belakang" value="{{ old('gelar_belakang', $pegawai->gelar_belakang ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('gelar_belakang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                         <div>
                            <label for="nomor_kartu_pegawai" class="block text-sm font-medium text-gray-700">Nomor Kartu Pegawai</label>
                            <input type="text" name="nomor_kartu_pegawai" id="nomor_kartu_pegawai" value="{{ old('nomor_kartu_pegawai', $pegawai->nomor_kartu_pegawai ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('nomor_kartu_pegawai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('tempat_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', isset($pegawai) ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('tanggal_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="Laki-Laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="nomor_handphone" class="block text-sm font-medium text-gray-700">Nomor Handphone <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_handphone" id="nomor_handphone" value="{{ old('nomor_handphone', $pegawai->nomor_handphone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('nomor_handphone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                         <div class="sm:col-span-2">
                            <label for="unit_kerja_terakhir_id" class="block text-sm font-medium text-gray-700">Unit Kerja Terakhir <span class="text-red-500">*</span></label>
                             <select name="unit_kerja_terakhir_id" id="unit_kerja_terakhir_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                 <option value="">Pilih Unit Kerja</option>
                                 @foreach($unitKerjas as $unit)
                                     <option
                                         value="{{ $unit->id }}"
                                         data-path="{{ $unit->subUnitKerja->unitKerja->nama }} &gt; {{ $unit->subUnitKerja->nama }} &gt; {{ $unit->nama }}"
                                         {{ old('unit_kerja_terakhir_id', $pegawai->unit_kerja_terakhir_id ?? '') == $unit->id ? 'selected' : '' }}>
                                         {{ $unit->nama }}
                                     </option>
                                 @endforeach
                             </select>
                             <div id="unit_kerja_path_display" class="text-sm text-gray-500 mt-1 font-medium"></div>
                             @error('unit_kerja_terakhir_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- KATEGORI: PENDIDIKAN --}}
                <div>
                     <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Pendidikan Pegawai</h3>
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                         <div>
                             <label for="pendidikan_terakhir" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                             @php
                                 $pendidikanOptions = ['SD', 'SLTP/Sederajat', 'SLTA/Sederajat', 'Diploma Satu (D1)', 'Diploma Dua (D2)', 'Diploma Tiga (D3)', 'Diploma Empat (D4)/ Sarjana (S1)', 'Magister (S2)', 'Doktor (S3)'];
                             @endphp
                             <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                 @foreach($pendidikanOptions as $option)
                                     <option value="{{ $option }}" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                 @endforeach
                             </select>
                              @error('pendidikan_terakhir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                         </div>
                         <div>
                             <label for="ijazah_terakhir" class="block text-sm font-medium text-gray-700">Ijazah Terakhir <span class="text-red-500">*</span></label>
                             <input type="file" name="ijazah_terakhir" id="ijazah_terakhir" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                             @error('ijazah_terakhir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                         </div>
                         <div>
                             <label for="transkrip_nilai_terakhir" class="block text-sm font-medium text-gray-700">Transkrip Nilai Terakhir <span class="text-red-500">*</span></label>
                             <input type="file" name="transkrip_nilai_terakhir" id="transkrip_nilai_terakhir" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                             @error('transkrip_nilai_terakhir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                         </div>
                          <div>
                             <label for="sk_penyetaraan_ijazah" class="block text-sm font-medium text-gray-700">SK Penyetaraan Ijazah (Luar Negeri)</label>
                             <input type="file" name="sk_penyetaraan_ijazah" id="sk_penyetaraan_ijazah" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                             @error('sk_penyetaraan_ijazah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                         </div>
                         <div class="sm:col-span-2">
                             <label for="disertasi_thesis_terakhir" class="block text-sm font-medium text-gray-700">Disertasi/Thesis Terakhir (Cover s/d Latar Belakang)</label>
                             <input type="file" name="disertasi_thesis_terakhir" id="disertasi_thesis_terakhir" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                             @error('disertasi_thesis_terakhir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                         </div>
                     </div>
                </div>
            </div>

            {{-- ======================================================================= --}}
            {{-- ============================ KOLOM KANAN ============================== --}}
            {{-- ======================================================================= --}}
            <div class="space-y-6">
                {{-- KATEGORI: INFORMASI PANGKAT & JABATAN --}}
                <div>
                     <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Pangkat & Jabatan</h3>
                     <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="sm:col-span-3">
                            <label for="pangkat_terakhir_id" class="block text-sm font-medium text-gray-700">Pangkat Terakhir <span class="text-red-500">*</span></label>
                            <select name="pangkat_terakhir_id" id="pangkat_terakhir_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Pilih Pangkat</option>
                                @foreach($pangkats as $pangkat)
                                    <option value="{{ $pangkat->id }}" {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
                                        {{ $pangkat->pangkat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pangkat_terakhir_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="tmt_pangkat" class="block text-sm font-medium text-gray-700">TMT Pangkat <span class="text-red-500">*</span></label>
                            <input type="date" name="tmt_pangkat" id="tmt_pangkat" value="{{ old('tmt_pangkat', isset($pegawai) ? $pegawai->tmt_pangkat->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('tmt_pangkat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="sk_pangkat_terakhir" class="block text-sm font-medium text-gray-700">SK Pangkat Terakhir <span class="text-red-500">*</span></label>
                            <input type="file" name="sk_pangkat_terakhir" id="sk_pangkat_terakhir" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('sk_pangkat_terakhir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-3">
                            <label for="jabatan_terakhir_id" class="block text-sm font-medium text-gray-700">
                                Jabatan Terakhir <span id="jenis_jabatan_display" class="text-indigo-600 font-normal"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select name="jabatan_terakhir_id" id="jabatan_terakhir_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Pilih Jabatan</option>
                                {{-- Menambahkan data-attributes untuk diakses oleh JavaScript --}}
                                @foreach($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}"
                                            data-jenis-pegawai="{{ $jabatan->jenis_pegawai }}"
                                            data-jenis-jabatan="{{ $jabatan->jenis_jabatan }}"
                                            {{ old('jabatan_terakhir_id', $pegawai->jabatan_terakhir_id ?? '') == $jabatan->id ? 'selected' : '' }}>
                                        {{ $jabatan->jabatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jabatan_terakhir_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="tmt_jabatan" class="block text-sm font-medium text-gray-700">TMT Jabatan <span class="text-red-500">*</span></label>
                            <input type="date" name="tmt_jabatan" id="tmt_jabatan" value="{{ old('tmt_jabatan', isset($pegawai) ? $pegawai->tmt_jabatan->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('tmt_jabatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="sk_jabatan_terakhir" class="block text-sm font-medium text-gray-700">SK Jabatan Terakhir <span class="text-red-500">*</span></label>
                            <input type="file" name="sk_jabatan_terakhir" id="sk_jabatan_terakhir" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('sk_jabatan_terakhir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                     </div>
                </div>

                {{-- KATEGORI: BERKAS & KINERJA --}}
                <div>
                     <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Berkas & Kinerja</h3>
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @php
                            $kinerjaOptions = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang'];
                        @endphp
                        <div>
                            <label for="predikat_kinerja_tahun_pertama" class="block text-sm font-medium text-gray-700">Predikat Kinerja Tahun Pertama <span class="text-red-500">*</span></label>
                            <select name="predikat_kinerja_tahun_pertama" id="predikat_kinerja_tahun_pertama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @foreach($kinerjaOptions as $option)
                                    <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('predikat_kinerja_tahun_pertama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="skp_tahun_pertama" class="block text-sm font-medium text-gray-700">SKP Tahun Pertama <span class="text-red-500">*</span></label>
                            <input type="file" name="skp_tahun_pertama" id="skp_tahun_pertama" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('skp_tahun_pertama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                         <div>
                            <label for="predikat_kinerja_tahun_kedua" class="block text-sm font-medium text-gray-700">Predikat Kinerja Tahun Kedua <span class="text-red-500">*</span></label>
                            <select name="predikat_kinerja_tahun_kedua" id="predikat_kinerja_tahun_kedua" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @foreach($kinerjaOptions as $option)
                                    <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('predikat_kinerja_tahun_kedua') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="skp_tahun_kedua" class="block text-sm font-medium text-gray-700">SKP Tahun Kedua <span class="text-red-500">*</span></label>
                            <input type="file" name="skp_tahun_kedua" id="skp_tahun_kedua" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('skp_tahun_kedua') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                                <div id="field_nilai_konversi" class="hidden">
                                    <label for="nilai_konversi" class="block text-sm font-medium text-gray-700">Nilai Konversi <span class="text-red-500">*</span></label>
                                    <input type="number" name="nilai_konversi" id="nilai_konversi" step="any" value="{{ old('nilai_konversi', $pegawai->nilai_konversi ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: 112.50">
                                    @error('nilai_konversi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div id="field_pak_konversi" class="hidden">
                                    <label for="pak_konversi" class="block text-sm font-medium text-gray-700">PAK Konversi</label>
                                    <input type="file" name="pak_konversi" id="pak_konversi" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @error('pak_konversi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                     </div>
                </div>

                 {{-- KATEGORI: SPESIFIK DOSEN --}}
                <div id="dosen_fields_wrapper" class="hidden">
                     <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Spesifik Dosen</h3>
                     <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="mata_kuliah_diampu" class="block text-sm font-medium text-gray-700">Mata Kuliah yang diampu <span class="text-red-500">*</span></label>
                            <textarea name="mata_kuliah_diampu" id="mata_kuliah_diampu" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('mata_kuliah_diampu', $pegawai->mata_kuliah_diampu ?? '') }}</textarea>
                            @error('mata_kuliah_diampu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="ranting_ilmu_kepakaran" class="block text-sm font-medium text-gray-700">Ranting Ilmu / Kepakaran <span class="text-red-500">*</span></label>
                            <textarea name="ranting_ilmu_kepakaran" id="ranting_ilmu_kepakaran" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('ranting_ilmu_kepakaran', $pegawai->ranting_ilmu_kepakaran ?? '') }}</textarea>
                            @error('ranting_ilmu_kepakaran') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="url_profil_sinta" class="block text-sm font-medium text-gray-700">URL Profil Akun Sinta <span class="text-red-500">*</span></label>
                            <input type="url" name="url_profil_sinta" id="url_profil_sinta" value="{{ old('url_profil_sinta', $pegawai->url_profil_sinta ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="https://sinta.kemdikbud.go.id/...">
                            @error('url_profil_sinta') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                     </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
            <a href="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}" class="bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded mr-2">
                Batal
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ isset($pegawai) ? 'Simpan Perubahan' : 'Simpan Data' }}
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Selektor Elemen ---
        const jenisPegawaiSelect = document.getElementById('jenis_pegawai');
        const jabatanSelect = document.getElementById('jabatan_terakhir_id');
        const jenisJabatanDisplay = document.getElementById('jenis_jabatan_display');
        const nuptkField = document.getElementById('field_nuptk');
        const dosenFieldsWrapper = document.getElementById('dosen_fields_wrapper');
        const skKonversiField = document.getElementById('field_pak_konversi');
        const nilaiKonversiField = document.getElementById('field_nilai_konversi');
        const unitKerjaSelect = document.getElementById('unit_kerja_terakhir_id');
        const pathDisplay = document.getElementById('unit_kerja_path_display');

        // --- Fungsi Inti ---

        /**
         * Memfilter dropdown jabatan berdasarkan jenis pegawai yang dipilih.
         */
        function filterJabatan() {
            const selectedJenisPegawai = jenisPegawaiSelect.value;
            let hasVisibleOptions = false;

            // Iterasi semua opsi jabatan
            for (const option of jabatanSelect.options) {
                if (option.value === "") { // Selalu tampilkan opsi "Pilih Jabatan"
                    option.style.display = "block";
                    continue;
                }

                const optionJenisPegawai = option.dataset.jenisPegawai;

                // Tampilkan jika jenis pegawai cocok atau jika tidak ada jenis pegawai yang dipilih
                if (!selectedJenisPegawai || optionJenisPegawai === selectedJenisPegawai) {
                    option.style.display = "block";
                    hasVisibleOptions = true;
                } else {
                    option.style.display = "none";
                }
            }

            // Jika jabatan yang terpilih saat ini disembunyikan, reset pilihan
            if (jabatanSelect.options[jabatanSelect.selectedIndex]?.style.display === 'none') {
                jabatanSelect.value = "";
            }
        }

        /**
         * Memperbarui label untuk menampilkan jenis jabatan dari jabatan yang dipilih.
         */
        function updateJenisJabatanLabel() {
            const selectedOption = jabatanSelect.options[jabatanSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const jenisJabatan = selectedOption.dataset.jenisJabatan;
                jenisJabatanDisplay.textContent = `(${jenisJabatan})`;
            } else {
                jenisJabatanDisplay.textContent = ''; // Kosongkan jika tidak ada yang dipilih
            }
        }

        /**
         * Menampilkan/menyembunyikan field spesifik berdasarkan jenis pegawai dan jenis jabatan.
         */
        function toggleConditionalFields() {
            const selectedJenisPegawai = jenisPegawaiSelect.value;
            const selectedJabatanOption = jabatanSelect.options[jabatanSelect.selectedIndex];
            const selectedJenisJabatan = selectedJabatanOption ? selectedJabatanOption.dataset.jenisJabatan : '';

            // --- KONDISI UNTUK NUPTK, NILAI KONVERSI, PAK KONVERSI ---
            // Tampilkan jika:
            // 1. Jenis Pegawai = Dosen AND Jenis Jabatan = Dosen Fungsional OR Dosen Fungsi Tambahan
            // 2. OR Jenis Pegawai = Tenaga Kependidikan AND Jenis Jabatan = Tenaga Kependidikan Fungsional Tertentu
            const showNuptkAndKonversiFields = (
                (selectedJenisPegawai === 'Dosen' && ['Dosen Fungsional', 'Dosen Fungsi Tambahan'].includes(selectedJenisJabatan)) ||
                (selectedJenisPegawai === 'Tenaga Kependidikan' && selectedJenisJabatan === 'Tenaga Kependidikan Fungsional Tertentu')
            );

            // Toggle visibility untuk NUPTK, Nilai Konversi, PAK Konversi
            nuptkField.classList.toggle('hidden', !showNuptkAndKonversiFields);
            nilaiKonversiField.classList.toggle('hidden', !showNuptkAndKonversiFields);
            skKonversiField.classList.toggle('hidden', !showNuptkAndKonversiFields);

            // --- KONDISI UNTUK FIELD SPESIFIK DOSEN LAINNYA ---
            // Field dosen lainnya (mata kuliah, ranting ilmu, sinta) tetap tampil untuk semua jenis dosen
            const isDosen = selectedJenisPegawai === 'Dosen';
            dosenFieldsWrapper.classList.toggle('hidden', !isDosen);
        }

        /**
         * Menampilkan path lengkap dari unit kerja yang dipilih.
         */
        function displayUnitKerjaPath() {
            const selectedOption = unitKerjaSelect.options[unitKerjaSelect.selectedIndex];
            pathDisplay.innerHTML = (selectedOption && selectedOption.value) ? selectedOption.dataset.path : '';
        }

        // --- Panggilan Awal saat Halaman Dimuat ---
        // Panggil semua fungsi saat halaman dimuat untuk memastikan state awal benar (penting untuk form edit)
        toggleConditionalFields();
        displayUnitKerjaPath();
        filterJabatan();
        updateJenisJabatanLabel();


        // --- Event Listeners ---
        jenisPegawaiSelect.addEventListener('change', function() {
            toggleConditionalFields();
            filterJabatan();
            updateJenisJabatanLabel(); // Panggil juga ini untuk mereset label
        });

        jabatanSelect.addEventListener('change', function() {
            updateJenisJabatanLabel();
            toggleConditionalFields(); // Tambahkan ini untuk update conditional fields saat jabatan berubah
        });

        unitKerjaSelect.addEventListener('change', displayUnitKerjaPath);
    });
</script>

@endsection
