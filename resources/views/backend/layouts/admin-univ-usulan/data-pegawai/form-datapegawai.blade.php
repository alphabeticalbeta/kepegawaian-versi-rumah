@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai')

@section('content')
{{-- Desain Latar Belakang --}}
<div class="bg-slate-50/50 min-h-screen">
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-5">

        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-extrabold leading-tight text-slate-900">
                    {{ isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai' }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Harap isi semua kolom yang ditandai dengan <span class="text-red-500 font-semibold">*</span>.
                </p>
            </div>

            {{-- Tombol Kembali --}}
            <a href="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-white bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm hover:bg-gray-500 transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar
            </a>
        </div>

    </div>


    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-md shadow-red-500/10" role="alert">
            <div class="flex">
                <div class="py-1"><i data-lucide="alert-triangle" class="w-6 h-6 text-red-500 mr-4"></i></div>
                <div>
                    <strong class="font-bold">Oops! Terjadi kesalahan validasi.</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ isset($pegawai) ? route('backend.admin-univ-usulan.data-pegawai.update', $pegawai->id) : route('backend.admin-univ-usulan.data-pegawai.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @if(isset($pegawai))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            {{-- ============================ KOLOM KIRI (INFORMASI UTAMA) =============================== --}}
            <div class="xl:col-span-2 space-y-8">
                {{-- Kartu Informasi Dasar --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                    <div class="bg-slate-50 -m-6 mb-6 px-6 py-4 rounded-t-2xl border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <i data-lucide="user-round" class="w-5 h-5 mr-3 text-indigo-500"></i>
                            Informasi Dasar Pegawai
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- KOLOM UNTUK FOTO --}}
                        <div class="md:col-span-1 flex flex-col items-center">
                             <label for="foto" class="block text-sm font-semibold text-slate-600 mb-2">Foto Pegawai</label>
                             <div class="mt-1">
                                 <img id="foto-preview"
                                      src="{{ isset($pegawai) && $pegawai->foto ? Storage::url($pegawai->foto) : 'https://ui-avatars.com/api/?name=?&background=random&size=128' }}"
                                      alt="Foto Pegawai"
                                      class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-white shadow-lg">
                             </div>
                             <div class="mt-4 w-full">
                                 <label for="foto" class="cursor-pointer text-center block w-full text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 py-2 px-4 rounded-lg transition-colors">
                                     Ganti Foto
                                 </label>
                                 <input type="file" name="foto" id="foto" class="hidden" onchange="previewImage(event)">
                             </div>
                        </div>

                        {{-- KOLOM UNTUK INFO DASAR --}}
                        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- PERBAIKAN: Menambahkan padding px-4 py-2 pada input --}}
                            <div class="sm:col-span-2">
                                <label for="nama_lengkap" class="block text-sm font-semibold text-slate-600">Nama Lengkap (Tanpa Gelar) <span class="text-red-500 ml-0.5">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">
                            </div>
                            <div>
                                <label for="gelar_depan" class="block text-sm font-semibold text-slate-600">Gelar Depan</label>
                                <input type="text" name="gelar_depan" id="gelar_depan" value="{{ old('gelar_depan', $pegawai->gelar_depan ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">
                            </div>
                            <div>
                                <label for="gelar_belakang" class="block text-sm font-semibold text-slate-600">Gelar Belakang</label>
                                <input type="text" name="gelar_belakang" id="gelar_belakang" value="{{ old('gelar_belakang', $pegawai->gelar_belakang ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-slate-600">Alamat Email <span class="text-red-500 ml-0.5">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $pegawai->email ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors" placeholder="contoh@email.com">
                            </div>
                            @if(isset($pegawai))
                            <div class="sm:col-span-2">
                                <label for="password" class="block text-sm font-semibold text-slate-600">Ubah Password</label>
                                <input type="password" name="password" id="password" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                                <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 border-t border-slate-200/80 pt-6">
                        <div>
                            <label for="jenis_pegawai" class="block text-sm font-semibold text-slate-600">Jenis Pegawai <span class="text-red-500 ml-0.5">*</span></label>
                            <select id="jenis_pegawai" name="jenis_pegawai" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors sm:text-sm">
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="Dosen" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="Tenaga Kependidikan" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                        </div>
                        <div>
                            <label for="nip" class="block text-sm font-semibold text-slate-600">NIP <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip', $pegawai->nip ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400" placeholder="18 Karakter Numerik" maxlength="18">
                        </div>
                         <div>
                            <label for="nomor_kartu_pegawai" class="block text-sm font-semibold text-slate-600">Nomor Kartu Pegawai</label>
                            <input type="text" name="nomor_kartu_pegawai" id="nomor_kartu_pegawai" value="{{ old('nomor_kartu_pegawai', $pegawai->nomor_kartu_pegawai ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">
                        </div>
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-semibold text-slate-600">Tempat Lahir <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-semibold text-slate-600">Tanggal Lahir <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', isset($pegawai) ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                        </div>
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-semibold text-slate-600">Jenis Kelamin <span class="text-red-500 ml-0.5">*</span></label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                                <option value="Laki-Laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="nomor_handphone" class="block text-sm font-semibold text-slate-600">Nomor Handphone <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="text" name="nomor_handphone" id="nomor_handphone" value="{{ old('nomor_handphone', $pegawai->nomor_handphone ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">
                        </div>
                         <div class="sm:col-span-2">
                            <label for="unit_kerja_terakhir_id" class="block text-sm font-semibold text-slate-600">Unit Kerja Terakhir <span class="text-red-500 ml-0.5">*</span></label>
                             <select name="unit_kerja_terakhir_id" id="unit_kerja_terakhir_id" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
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
                             <div id="unit_kerja_path_display" class="text-xs text-slate-500 mt-2 font-medium bg-slate-100 p-2 rounded-md"></div>
                        </div>
                    </div>
                </div>

                {{-- CARD: PENDIDIKAN & BERKAS --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                     <div class="bg-slate-50 -m-6 mb-6 px-6 py-4 rounded-t-2xl border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <i data-lucide="graduation-cap" class="w-5 h-5 mr-3 text-indigo-500"></i>
                            Pendidikan & Berkas Terkait
                        </h3>
                    </div>
                     <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                             <label for="pendidikan_terakhir" class="block text-sm font-semibold text-slate-600">Pendidikan Terakhir <span class="text-red-500 ml-0.5">*</span></label>
                             @php
                                 $pendidikanOptions = ['SD', 'SLTP/Sederajat', 'SLTA/Sederajat', 'Diploma Satu (D1)', 'Diploma Dua (D2)', 'Diploma Tiga (D3)', 'Diploma Empat (D4)/ Sarjana (S1)', 'Magister (S2)', 'Doktor (S3)'];
                             @endphp
                             <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                                 @foreach($pendidikanOptions as $option)
                                     <option value="{{ $option }}" {{ old('pendidikan_terakhir', $pegawai->pendidikan_terakhir ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                 @endforeach
                             </select>
                         </div>
                        <div class="sm:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- PERBAIKAN: Struktur HTML untuk input file --}}
                            <div>
                                <label for="ijazah_terakhir" class="block text-sm font-semibold text-slate-600 mb-1">Ijazah Terakhir <span class="text-red-500 ml-0.5">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="ijazah_terakhir" name="ijazah_terakhir" type="file" class="hidden" />
                                </label>
                                @if(isset($pegawai) && $pegawai->ijazah_terakhir)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'ijazah_terakhir']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                            <div>
                                <label for="transkrip_nilai_terakhir" class="block text-sm font-semibold text-slate-600 mb-1">Transkrip Nilai <span class="text-red-500 ml-0.5">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="transkrip_nilai_terakhir" name="transkrip_nilai_terakhir" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->transkrip_nilai_terakhir)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'transkrip_nilai_terakhir']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                        </div>

                         <div class="sm:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="sk_penyetaraan_ijazah" class="block text-sm font-semibold text-slate-600 mb-1">SK Penyetaraan Ijazah</label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="sk_penyetaraan_ijazah" name="sk_penyetaraan_ijazah" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->sk_penyetaraan_ijazah)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_penyetaraan_ijazah']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                            <div>
                                <label for="disertasi_thesis_terakhir" class="block text-sm font-semibold text-slate-600 mb-1">Disertasi/Thesis</label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="disertasi_thesis_terakhir" name="disertasi_thesis_terakhir" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->disertasi_thesis_terakhir)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'disertasi_thesis_terakhir']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                         </div>
                     </div>
                </div>

                {{-- CARD: INFORMASI CPNS & PNS --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                     <div class="bg-slate-50 -m-6 mb-6 px-6 py-4 rounded-t-2xl border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <i data-lucide="file-badge-2" class="w-5 h-5 mr-3 text-indigo-500"></i>
                            Informasi CPNS & PNS
                        </h3>
                    </div>
                     <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="tmt_cpns" class="block text-sm font-semibold text-slate-600">TMT CPNS</label>
                                <input type="date" name="tmt_cpns" id="tmt_cpns" value="{{ old('tmt_cpns', isset($pegawai) && $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('Y-m-d') : '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                            </div>
                            <div>
                                <label for="sk_cpns" class="block text-sm font-semibold text-slate-600 mb-1">SK CPNS</label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->sk_cpns ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->sk_cpns ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_cpns ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_cpns ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_cpns ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="sk_cpns" name="sk_cpns" type="file" class="hidden" />
                                </label>
                                @if(isset($pegawai) && $pegawai->sk_cpns)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_cpns']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="tmt_pns" class="block text-sm font-semibold text-slate-600">TMT PNS</label>
                                <input type="date" name="tmt_pns" id="tmt_pns" value="{{ old('tmt_pns', isset($pegawai) && $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                            </div>
                            <div>
                                <label for="sk_pns" class="block text-sm font-semibold text-slate-600 mb-1">SK PNS</label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->sk_pns ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->sk_pns ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_pns ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_pns ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_pns ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="sk_pns" name="sk_pns" type="file" class="hidden" />
                                </label>
                                @if(isset($pegawai) && $pegawai->sk_pns)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_pns']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            {{-- ============================ KOLOM KANAN (INFORMASI KEPEGAWAIAN) ============================== --}}
            <div class="xl:col-span-1 space-y-8">
                {{-- CARD: PANGKAT & JABATAN --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                     <div class="bg-slate-50 -m-6 mb-6 px-6 py-4 rounded-t-2xl border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <i data-lucide="shield-check" class="w-5 h-5 mr-3 text-indigo-500"></i>
                            Pangkat & Jabatan
                        </h3>
                    </div>
                     <div class="space-y-6">
                        <div>
                            <label for="pangkat_terakhir_id" class="block text-sm font-semibold text-slate-600">Pangkat Terakhir <span class="text-red-500 ml-0.5">*</span></label>
                            <select name="pangkat_terakhir_id" id="pangkat_terakhir_id" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                                <option value="">Pilih Pangkat</option>
                                @foreach($pangkats as $pangkat)
                                    <option value="{{ $pangkat->id }}" {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
                                        {{ $pangkat->pangkat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tmt_pangkat" class="block text-sm font-semibold text-slate-600">TMT Pangkat <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="date" name="tmt_pangkat" id="tmt_pangkat" value="{{ old('tmt_pangkat', isset($pegawai) ? $pegawai->tmt_pangkat->format('Y-m-d') : '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                        </div>
                        <div>
                            <label for="sk_pangkat_terakhir" class="block text-sm font-semibold text-slate-600 mb-1">SK Pangkat Terakhir <span class="text-red-500 ml-0.5">*</span></label>
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                    <i data-lucide="{{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'text-green-600' : 'text-slate-500' }}"></i>
                                    <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                        <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                    </p>
                                </div>
                                <input id="sk_pangkat_terakhir" name="sk_pangkat_terakhir" type="file" class="hidden" />
                            </label>
                             @if(isset($pegawai) && $pegawai->sk_pangkat_terakhir)
                            <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_pangkat_terakhir']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                            @endif
                        </div>
                         <div>
                            <label for="jabatan_terakhir_id" class="block text-sm font-semibold text-slate-600">
                                Jabatan Terakhir <span id="jenis_jabatan_display" class="text-indigo-600 font-normal"></span>
                                <span class="text-red-500 ml-0.5">*</span>
                            </label>
                            <select name="jabatan_terakhir_id" id="jabatan_terakhir_id" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                                <option value="">Pilih Jabatan</option>
                                @foreach($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}"
                                            data-jenis-pegawai="{{ $jabatan->jenis_pegawai }}"
                                            data-jenis-jabatan="{{ $jabatan->jenis_jabatan }}"
                                            {{ old('jabatan_terakhir_id', $pegawai->jabatan_terakhir_id ?? '') == $jabatan->id ? 'selected' : '' }}>
                                        {{ $jabatan->jabatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="field_nuptk" class="hidden">
                            <label for="nuptk" class="block text-sm font-semibold text-slate-600">NUPTK <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk', $pegawai->nuptk ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400" placeholder="16 Karakter Numerik" maxlength="16">
                        </div>
                        <div>
                            <label for="tmt_jabatan" class="block text-sm font-semibold text-slate-600">TMT Jabatan <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="date" name="tmt_jabatan" id="tmt_jabatan" value="{{ old('tmt_jabatan', isset($pegawai) ? $pegawai->tmt_jabatan->format('Y-m-d') : '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                        </div>
                        <div>
                            <label for="sk_jabatan_terakhir" class="block text-sm font-semibold text-slate-600 mb-1">SK Jabatan Terakhir <span class="text-red-500 ml-0.5">*</span></label>
                             <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                    <i data-lucide="{{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'text-green-600' : 'text-slate-500' }}"></i>
                                    <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'text-green-800' : 'text-slate-500' }} w-full">
                                        <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                    </p>
                                </div>
                                <input id="sk_jabatan_terakhir" name="sk_jabatan_terakhir" type="file" class="hidden" />
                            </label>
                             @if(isset($pegawai) && $pegawai->sk_jabatan_terakhir)
                            <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_jabatan_terakhir']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                            @endif
                        </div>
                     </div>
                </div>

                 {{-- CARD: KINERJA & KONVERSI --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                     <div class="bg-slate-50 -m-6 mb-6 px-6 py-4 rounded-t-2xl border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3 text-indigo-500"></i>
                            Kinerja & Konversi
                        </h3>
                    </div>
                     <div class="grid grid-cols-1 gap-6">
                        @php $kinerjaOptions = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang']; @endphp
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="predikat_kinerja_tahun_pertama" class="block text-sm font-semibold text-slate-600">Predikat Kinerja Thn. 1 <span class="text-red-500 ml-0.5">*</span></label>
                                <select name="predikat_kinerja_tahun_pertama" id="predikat_kinerja_tahun_pertama" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                                    @foreach($kinerjaOptions as $option)
                                        <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div>
                                <label for="predikat_kinerja_tahun_kedua" class="block text-sm font-semibold text-slate-600">Predikat Kinerja Thn. 2 <span class="text-red-500 ml-0.5">*</span></label>
                                <select name="predikat_kinerja_tahun_kedua" id="predikat_kinerja_tahun_kedua" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors">
                                    @foreach($kinerjaOptions as $option)
                                        <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-6">
                             <div>
                                <label for="skp_tahun_pertama" class="block text-sm font-semibold text-slate-600 mb-1">SKP Tahun Pertama <span class="text-red-500 ml-0.5">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="skp_tahun_pertama" name="skp_tahun_pertama" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->skp_tahun_pertama)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'skp_tahun_pertama']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                            <div>
                                <label for="skp_tahun_kedua" class="block text-sm font-semibold text-slate-600 mb-1">SKP Tahun Kedua <span class="text-red-500 ml-0.5">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'text-green-600' : 'text-slate-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'text-green-800' : 'text-slate-500' }} w-full">
                                            <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="skp_tahun_kedua" name="skp_tahun_kedua" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->skp_tahun_kedua)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'skp_tahun_kedua']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                         </div>
                        <div id="field_nilai_konversi" class="hidden">
                            <label for="nilai_konversi" class="block text-sm font-semibold text-slate-600">Nilai Konversi <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="number" name="nilai_konversi" id="nilai_konversi" step="any" value="{{ old('nilai_konversi', $pegawai->nilai_konversi ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400" placeholder="Contoh: 112.50">
                        </div>
                        <div id="field_pak_konversi" class="hidden">
                            <label for="pak_konversi" class="block text-sm font-semibold text-slate-600 mb-1">PAK Konversi</label>
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 {{ isset($pegawai) && $pegawai->pak_konversi ? 'border-green-400 bg-green-50' : 'border-slate-300 bg-slate-50' }} border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center w-full px-2 overflow-hidden">
                                    <i data-lucide="{{ isset($pegawai) && $pegawai->pak_konversi ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->pak_konversi ? 'text-green-600' : 'text-slate-500' }}"></i>
                                    <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->pak_konversi ? 'text-green-800' : 'text-slate-500' }} w-full">
                                        <span class="font-semibold file-name-display block truncate">{{ isset($pegawai) && $pegawai->pak_konversi ? 'File sudah ada' : 'Klik untuk unggah' }}</span>
                                    </p>
                                </div>
                                <input id="pak_konversi" name="pak_konversi" type="file" class="hidden" />
                            </label>
                             @if(isset($pegawai) && $pegawai->pak_konversi)
                            <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'pak_konversi']) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat File Saat Ini</a></div>
                            @endif
                        </div>
                     </div>
                </div>


                 {{-- KATEGORI: SPESIFIK DOSEN --}}
                <div id="dosen_fields_wrapper" class="hidden bg-white p-6 rounded-2xl shadow-lg shadow-slate-600/5 border border-slate-100">
                     <div class="bg-slate-50 -m-6 mb-6 px-6 py-4 rounded-t-2xl border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                            <i data-lucide="book-marked" class="w-5 h-5 mr-3 text-indigo-500"></i>
                            Informasi Spesifik Dosen
                        </h3>
                    </div>
                     <div class="space-y-6">
                        <div>
                            <label for="mata_kuliah_diampu" class="block text-sm font-semibold text-slate-600">Mata Kuliah yang diampu <span class="text-red-500 ml-0.5">*</span></label>
                            <textarea name="mata_kuliah_diampu" id="mata_kuliah_diampu" rows="3" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">{{ old('mata_kuliah_diampu', $pegawai->mata_kuliah_diampu ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="ranting_ilmu_kepakaran" class="block text-sm font-semibold text-slate-600">Ranting Ilmu / Kepakaran <span class="text-red-500 ml-0.5">*</span></label>
                            <textarea name="ranting_ilmu_kepakaran" id="ranting_ilmu_kepakaran" rows="3" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400">{{ old('ranting_ilmu_kepakaran', $pegawai->ranting_ilmu_kepakaran ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="url_profil_sinta" class="block text-sm font-semibold text-slate-600">URL Profil Akun Sinta <span class="text-red-500 ml-0.5">*</span></label>
                            <input type="url" name="url_profil_sinta" id="url_profil_sinta" value="{{ old('url_profil_sinta', $pegawai->url_profil_sinta ?? '') }}" class="mt-1 block w-full px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors placeholder-slate-400" placeholder="https://sinta.kemdikbud.go.id/...">
                        </div>
                     </div>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-8 pt-6 border-t border-slate-200 flex justify-end items-center gap-4">
            <a href="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white px-6 py-2.5 rounded-lg border border-slate-300 shadow-sm hover:bg-slate-50 transition-all">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-105">
                 <i data-lucide="save" class="w-4 h-4"></i>
                <span>{{ isset($pegawai) ? 'Simpan Perubahan' : 'Simpan Data Baru' }}</span>
            </button>
        </div>
    </form>
</div>
</div>

{{-- ====================================================================================================== --}}
{{-- =================================== SCRIPT (DENGAN PERBAIKAN) ======================================= --}}
{{-- ====================================================================================================== --}}
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

        function filterJabatan() {
            const selectedJenisPegawai = jenisPegawaiSelect.value;
            let hasVisibleOptions = false;

            for (const option of jabatanSelect.options) {
                if (option.value === "") {
                    option.style.display = "block";
                    continue;
                }
                const optionJenisPegawai = option.dataset.jenisPegawai;
                if (!selectedJenisPegawai || optionJenisPegawai === selectedJenisPegawai) {
                    option.style.display = "block";
                    hasVisibleOptions = true;
                } else {
                    option.style.display = "none";
                }
            }

            if (jabatanSelect.options[jabatanSelect.selectedIndex]?.style.display === 'none') {
                jabatanSelect.value = "";
            }
        }

        function updateJenisJabatanLabel() {
            const selectedOption = jabatanSelect.options[jabatanSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const jenisJabatan = selectedOption.dataset.jenisJabatan;
                jenisJabatanDisplay.textContent = `(${jenisJabatan})`;
            } else {
                jenisJabatanDisplay.textContent = '';
            }
        }

        function setupFileUploadFeedback() {
            const fileInputs = document.querySelectorAll('input[type="file"].hidden');

            fileInputs.forEach(input => {
                input.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    const label = this.parentElement;
                    const icon = label.querySelector('i');
                    const fileNameDisplay = label.querySelector('.file-name-display');
                    const linkElement = label.nextElementSibling?.querySelector('a');

                    if (file && fileNameDisplay) {
                        fileNameDisplay.textContent = file.name;
                        // Kelas truncate sudah ada di HTML, tidak perlu ditambah via JS
                        // fileNameDisplay.classList.add('truncate');

                        icon.setAttribute('data-lucide', 'file-check-2');
                        icon.classList.remove('text-slate-500');
                        icon.classList.add('text-green-600');
                        label.classList.remove('border-slate-300', 'bg-slate-50');
                        label.classList.add('border-green-400', 'bg-green-50');
                        if (linkElement) linkElement.style.display = 'none';
                    }

                    if (window.lucide) {
                        lucide.createIcons();
                    }
                });
            });
        }

        function toggleConditionalFields() {
            const selectedJenisPegawai = jenisPegawaiSelect.value;
            const selectedJabatanOption = jabatanSelect.options[jabatanSelect.selectedIndex];
            const selectedJenisJabatan = selectedJabatanOption ? selectedJabatanOption.dataset.jenisJabatan : '';

            const showNuptkAndKonversiFields = (
                (selectedJenisPegawai === 'Dosen' && ['Dosen Fungsional', 'Dosen Fungsi Tambahan'].includes(selectedJenisJabatan)) ||
                (selectedJenisPegawai === 'Tenaga Kependidikan' && selectedJenisJabatan === 'Tenaga Kependidikan Fungsional Tertentu')
            );

            nuptkField.classList.toggle('hidden', !showNuptkAndKonversiFields);
            nilaiKonversiField.classList.toggle('hidden', !showNuptkAndKonversiFields);
            skKonversiField.classList.toggle('hidden', !showNuptkAndKonversiFields);

            const isDosen = jenisPegawaiSelect.value === 'Dosen';
            dosenFieldsWrapper.classList.toggle('hidden', !isDosen);
        }

        function displayUnitKerjaPath() {
            const selectedOption = unitKerjaSelect.options[unitKerjaSelect.selectedIndex];
            pathDisplay.innerHTML = (selectedOption && selectedOption.value) ? `Path: ${selectedOption.dataset.path}` : '';
            pathDisplay.classList.toggle('hidden', !(selectedOption && selectedOption.value));
        }

        // --- Panggilan Awal saat Halaman Dimuat ---
        toggleConditionalFields();
        displayUnitKerjaPath();
        filterJabatan();
        updateJenisJabatanLabel();
        setupFileUploadFeedback();

        // --- Event Listeners ---
        jenisPegawaiSelect.addEventListener('change', function() {
            toggleConditionalFields();
            filterJabatan();
            jabatanSelect.value = ""; // Reset jabatan saat jenis pegawai berubah
            updateJenisJabatanLabel();
        });

        jabatanSelect.addEventListener('change', function() {
            updateJenisJabatanLabel();
            toggleConditionalFields();
        });

        unitKerjaSelect.addEventListener('change', displayUnitKerjaPath);
    });

    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('foto-preview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        const buttonSpan = submitButton.querySelector('span');
        const originalText = buttonSpan.innerText;

        submitButton.disabled = true;
        submitButton.classList.add('cursor-not-allowed');
        buttonSpan.innerText = 'Menyimpan...';
        // Hapus ikon save dan tambahkan ikon loading
        submitButton.querySelector('i').setAttribute('data-lucide', 'loader-2');
        submitButton.querySelector('i').classList.add('animate-spin');
        lucide.createIcons();

        // Antisipasi jika ada error validasi dari server
        setTimeout(() => {
            if (submitButton.disabled) { // Hanya reset jika masih disabled
                submitButton.disabled = false;
                submitButton.classList.remove('cursor-not-allowed');
                buttonSpan.innerText = originalText;
                submitButton.querySelector('i').setAttribute('data-lucide', 'save');
                submitButton.querySelector('i').classList.remove('animate-spin');
                lucide.createIcons();
            }
        }, 5000); // Waktu tunggu 5 detik
    });
</script>

@endsection
