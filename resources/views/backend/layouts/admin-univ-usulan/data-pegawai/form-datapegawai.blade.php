@extends('backend.layouts.admin-univ-usulan.app')

@section('title', isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold leading-tight text-gray-800">
            {{ isset($pegawai) ? 'Edit Data Pegawai' : 'Tambah Data Pegawai' }}
        </h2>
        <a href="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}" class="text-sm text-gray-600 hover:text-indigo-600 flex items-center">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
            Kembali ke Daftar Pegawai
        </a>
    </div>


    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                {{-- CARD: INFORMASI DASAR --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <div class="bg-gray-50 -m-6 mb-6 p-4 rounded-t-xl border-b">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="user-round" class="w-5 h-5 mr-2 text-indigo-500"></i>
                            Informasi Dasar Pegawai
                        </h3>
                    </div>

                    {{-- PERUBAHAN: Tambahkan grid untuk foto dan info dasar --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- KOLOM UNTUK FOTO --}}
                        <div class="md:col-span-1">
                             <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Pegawai</label>
                             <div class="mt-1">
                                 {{-- Image preview --}}
                                 <img id="foto-preview"
                                      src="{{ isset($pegawai) && $pegawai->foto ? Storage::url($pegawai->foto) : 'https://via.placeholder.com/150' }}"
                                      alt="Foto Pegawai"
                                      class="w-32 h-32 object-cover rounded-full mx-auto border-4 border-gray-200 shadow-sm">
                             </div>
                             <div class="mt-4">
                                 <input type="file" name="foto" id="foto" class="block w-full text-sm text-gray-500
                                     file:mr-4 file:py-2 file:px-4
                                     file:rounded-full file:border-0
                                     file:text-sm file:font-semibold
                                     file:bg-indigo-50 file:text-indigo-700
                                     hover:file:bg-indigo-100"
                                     onchange="previewImage(event)">
                             </div>
                        </div>

                        {{-- KOLOM UNTUK INFO DASAR --}}
                        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="sm:col-span-2">
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap (Tanpa Gelar) <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="gelar_depan" class="block text-sm font-medium text-gray-700">Gelar Depan</label>
                                <input type="text" name="gelar_depan" id="gelar_depan" value="{{ old('gelar_depan', $pegawai->gelar_depan ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="gelar_belakang" class="block text-sm font-medium text-gray-700">Gelar Belakang</label>
                                <input type="text" name="gelar_belakang" id="gelar_belakang" value="{{ old('gelar_belakang', $pegawai->gelar_belakang ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $pegawai->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="contoh@email.com">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 border-t pt-6">
                        <div>
                            <label for="jenis_pegawai" class="block text-sm font-medium text-gray-700">Jenis Pegawai <span class="text-red-500">*</span></label>
                            <select id="jenis_pegawai" name="jenis_pegawai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Pilih Jenis Pegawai</option>
                                <option value="Dosen" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="Tenaga Kependidikan" {{ old('jenis_pegawai', $pegawai->jenis_pegawai ?? '') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>
                        </div>
                        <div>
                            <label for="nip" class="block text-sm font-medium text-gray-700">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip', $pegawai->nip ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="18 Karakter Numerik" maxlength="18">
                        </div>
                        <div id="field_nuptk" class="hidden">
                            <label for="nuptk" class="block text-sm font-medium text-gray-700">NUPTK <span class="text-red-500">*</span></label>
                            <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk', $pegawai->nuptk ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="16 Karakter Numerik" maxlength="16">
                        </div>
                         <div>
                            <label for="nomor_kartu_pegawai" class="block text-sm font-medium text-gray-700">Nomor Kartu Pegawai</label>
                            <input type="text" name="nomor_kartu_pegawai" id="nomor_kartu_pegawai" value="{{ old('nomor_kartu_pegawai', $pegawai->nomor_kartu_pegawai ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', isset($pegawai) ? $pegawai->tanggal_lahir->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="Laki-Laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="nomor_handphone" class="block text-sm font-medium text-gray-700">Nomor Handphone <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_handphone" id="nomor_handphone" value="{{ old('nomor_handphone', $pegawai->nomor_handphone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
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
                             <div id="unit_kerja_path_display" class="text-xs text-gray-500 mt-1 font-medium bg-gray-100 p-2 rounded-md"></div>
                        </div>
                    </div>
                </div>

                {{-- CARD: PENDIDIKAN & BERKAS --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                     <div class="bg-gray-50 -m-6 mb-6 p-4 rounded-t-xl border-b">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="graduation-cap" class="w-5 h-5 mr-2 text-indigo-500"></i>
                            Pendidikan & Berkas Terkait
                        </h3>
                    </div>
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
                         </div>
                        <div class="sm:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- TAMPILAN INPUT FILE --}}
                            <div>
                                <label for="ijazah_terakhir" class="block text-sm font-medium text-gray-700 mb-1">Ijazah Terakhir <span class="text-red-500">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->ijazah_terakhir ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="ijazah_terakhir" name="ijazah_terakhir" type="file" class="hidden" />
                                </label>
                                @if(isset($pegawai) && $pegawai->ijazah_terakhir)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'ijazah_terakhir']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                            <div>
                                <label for="transkrip_nilai_terakhir" class="block text-sm font-medium text-gray-700 mb-1">Transkrip Nilai <span class="text-red-500">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->transkrip_nilai_terakhir ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="transkrip_nilai_terakhir" name="transkrip_nilai_terakhir" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->transkrip_nilai_terakhir)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'transkrip_nilai_terakhir']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                        </div>

                         <div class="sm:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="sk_penyetaraan_ijazah" class="block text-sm font-medium text-gray-700 mb-1">SK Penyetaraan Ijazah</label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->sk_penyetaraan_ijazah ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="sk_penyetaraan_ijazah" name="sk_penyetaraan_ijazah" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->sk_penyetaraan_ijazah)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_penyetaraan_ijazah']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                            <div>
                                <label for="disertasi_thesis_terakhir" class="block text-sm font-medium text-gray-700 mb-1">Disertasi/Thesis</label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->disertasi_thesis_terakhir ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="disertasi_thesis_terakhir" name="disertasi_thesis_terakhir" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->disertasi_thesis_terakhir)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'disertasi_thesis_terakhir']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                         </div>
                     </div>
                </div>

                {{-- CARD: INFORMASI CPNS & PNS --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                     <div class="bg-gray-50 -m-6 mb-6 p-4 rounded-t-xl border-b">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="file-badge-2" class="w-5 h-5 mr-2 text-indigo-500"></i>
                            Informasi CPNS & PNS
                        </h3>
                    </div>
                     <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="tmt_cpns" class="block text-sm font-medium text-gray-700">TMT CPNS</label>
                                <input type="date" name="tmt_cpns" id="tmt_cpns" value="{{ old('tmt_cpns', isset($pegawai) && $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="sk_cpns" class="block text-sm font-medium text-gray-700 mb-1">SK CPNS</label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->sk_cpns ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->sk_cpns ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_cpns ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_cpns ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->sk_cpns ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="sk_cpns" name="sk_cpns" type="file" class="hidden" />
                                </label>
                                @if(isset($pegawai) && $pegawai->sk_cpns)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_cpns']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="tmt_pns" class="block text-sm font-medium text-gray-700">TMT PNS</label>
                                <input type="date" name="tmt_pns" id="tmt_pns" value="{{ old('tmt_pns', isset($pegawai) && $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="sk_pns" class="block text-sm font-medium text-gray-700 mb-1">SK PNS</label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->sk_pns ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->sk_pns ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_pns ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_pns ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->sk_pns ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="sk_pns" name="sk_pns" type="file" class="hidden" />
                                </label>
                                @if(isset($pegawai) && $pegawai->sk_pns)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_pns']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            {{-- ============================ KOLOM KANAN (INFORMASI KEPEGAWAIAN) ============================== --}}
            <div class="xl:col-span-1 space-y-8">
                {{-- CARD: PANGKAT & JABATAN --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                     <div class="bg-gray-50 -m-6 mb-6 p-4 rounded-t-xl border-b">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="shield-check" class="w-5 h-5 mr-2 text-indigo-500"></i>
                            Pangkat & Jabatan
                        </h3>
                    </div>
                     <div class="space-y-6">
                        <div>
                            <label for="pangkat_terakhir_id" class="block text-sm font-medium text-gray-700">Pangkat Terakhir <span class="text-red-500">*</span></label>
                            <select name="pangkat_terakhir_id" id="pangkat_terakhir_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Pilih Pangkat</option>
                                @foreach($pangkats as $pangkat)
                                    <option value="{{ $pangkat->id }}" {{ old('pangkat_terakhir_id', $pegawai->pangkat_terakhir_id ?? '') == $pangkat->id ? 'selected' : '' }}>
                                        {{ $pangkat->pangkat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tmt_pangkat" class="block text-sm font-medium text-gray-700">TMT Pangkat <span class="text-red-500">*</span></label>
                            <input type="date" name="tmt_pangkat" id="tmt_pangkat" value="{{ old('tmt_pangkat', isset($pegawai) ? $pegawai->tmt_pangkat->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="sk_pangkat_terakhir" class="block text-sm font-medium text-gray-700 mb-1">SK Pangkat Terakhir <span class="text-red-500">*</span></label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="{{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'text-green-600' : 'text-gray-500' }}"></i>
                                    <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'text-green-700' : 'text-gray-500' }}">
                                        <span class="font-semibold">{{ isset($pegawai) && $pegawai->sk_pangkat_terakhir ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                    </p>
                                </div>
                                <input id="sk_pangkat_terakhir" name="sk_pangkat_terakhir" type="file" class="hidden" />
                            </label>
                             @if(isset($pegawai) && $pegawai->sk_pangkat_terakhir)
                            <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_pangkat_terakhir']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                            @endif
                        </div>
                         <div>
                            <label for="jabatan_terakhir_id" class="block text-sm font-medium text-gray-700">
                                Jabatan Terakhir <span id="jenis_jabatan_display" class="text-indigo-600 font-normal"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select name="jabatan_terakhir_id" id="jabatan_terakhir_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
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
                        <div>
                            <label for="tmt_jabatan" class="block text-sm font-medium text-gray-700">TMT Jabatan <span class="text-red-500">*</span></label>
                            <input type="date" name="tmt_jabatan" id="tmt_jabatan" value="{{ old('tmt_jabatan', isset($pegawai) ? $pegawai->tmt_jabatan->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="sk_jabatan_terakhir" class="block text-sm font-medium text-gray-700 mb-1">SK Jabatan Terakhir <span class="text-red-500">*</span></label>
                             <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="{{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'text-green-600' : 'text-gray-500' }}"></i>
                                    <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'text-green-700' : 'text-gray-500' }}">
                                        <span class="font-semibold">{{ isset($pegawai) && $pegawai->sk_jabatan_terakhir ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                    </p>
                                </div>
                                <input id="sk_jabatan_terakhir" name="sk_jabatan_terakhir" type="file" class="hidden" />
                            </label>
                             @if(isset($pegawai) && $pegawai->sk_jabatan_terakhir)
                            <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'sk_jabatan_terakhir']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                            @endif
                        </div>
                     </div>
                </div>

                 {{-- CARD: KINERJA & KONVERSI --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                     <div class="bg-gray-50 -m-6 mb-6 p-4 rounded-t-xl border-b">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2 text-indigo-500"></i>
                            Kinerja & Konversi
                        </h3>
                    </div>
                     <div class="grid grid-cols-1 gap-6">
                        @php $kinerjaOptions = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang']; @endphp
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="predikat_kinerja_tahun_pertama" class="block text-sm font-medium text-gray-700">Predikat Kinerja Thn. 1 <span class="text-red-500">*</span></label>
                                <select name="predikat_kinerja_tahun_pertama" id="predikat_kinerja_tahun_pertama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach($kinerjaOptions as $option)
                                        <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_pertama', $pegawai->predikat_kinerja_tahun_pertama ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div>
                                <label for="predikat_kinerja_tahun_kedua" class="block text-sm font-medium text-gray-700">Predikat Kinerja Thn. 2 <span class="text-red-500">*</span></label>
                                <select name="predikat_kinerja_tahun_kedua" id="predikat_kinerja_tahun_kedua" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach($kinerjaOptions as $option)
                                        <option value="{{ $option }}" {{ old('predikat_kinerja_tahun_kedua', $pegawai->predikat_kinerja_tahun_kedua ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-6">
                             <div>
                                <label for="skp_tahun_pertama" class="block text-sm font-medium text-gray-700 mb-1">SKP Tahun Pertama <span class="text-red-500">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->skp_tahun_pertama ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="skp_tahun_pertama" name="skp_tahun_pertama" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->skp_tahun_pertama)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'skp_tahun_pertama']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                            <div>
                                <label for="skp_tahun_kedua" class="block text-sm font-medium text-gray-700 mb-1">SKP Tahun Kedua <span class="text-red-500">*</span></label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="{{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'text-green-600' : 'text-gray-500' }}"></i>
                                        <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'text-green-700' : 'text-gray-500' }}">
                                            <span class="font-semibold">{{ isset($pegawai) && $pegawai->skp_tahun_kedua ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                        </p>
                                    </div>
                                    <input id="skp_tahun_kedua" name="skp_tahun_kedua" type="file" class="hidden" />
                                </label>
                                 @if(isset($pegawai) && $pegawai->skp_tahun_kedua)
                                <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'skp_tahun_kedua']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                                @endif
                            </div>
                         </div>
                        <div id="field_nilai_konversi" class="hidden">
                            <label for="nilai_konversi" class="block text-sm font-medium text-gray-700">Nilai Konversi <span class="text-red-500">*</span></label>
                            <input type="number" name="nilai_konversi" id="nilai_konversi" step="any" value="{{ old('nilai_konversi', $pegawai->nilai_konversi ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: 112.50">
                        </div>
                        <div id="field_pak_konversi" class="hidden">
                            <label for="pak_konversi" class="block text-sm font-medium text-gray-700 mb-1">PAK Konversi</label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 {{ isset($pegawai) && $pegawai->pak_konversi ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }} border-dashed rounded-lg cursor-pointer hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="{{ isset($pegawai) && $pegawai->pak_konversi ? 'file-check-2' : 'upload-cloud' }}" class="w-8 h-8 mb-2 {{ isset($pegawai) && $pegawai->pak_konversi ? 'text-green-600' : 'text-gray-500' }}"></i>
                                    <p class="mb-1 text-sm {{ isset($pegawai) && $pegawai->pak_konversi ? 'text-green-700' : 'text-gray-500' }}">
                                        <span class="font-semibold">{{ isset($pegawai) && $pegawai->pak_konversi ? 'File sudah diunggah' : 'Klik untuk unggah' }}</span>
                                    </p>
                                </div>
                                <input id="pak_konversi" name="pak_konversi" type="file" class="hidden" />
                            </label>
                             @if(isset($pegawai) && $pegawai->pak_konversi)
                            <div class="mt-2 text-center"><a href="{{ route('backend.admin-univ-usulan.data-pegawai.show-document', ['pegawai' => $pegawai, 'field' => 'pak_konversi']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat File Saat Ini</a></div>
                            @endif
                        </div>
                     </div>
                </div>


                 {{-- KATEGORI: SPESIFIK DOSEN --}}
                <div id="dosen_fields_wrapper" class="hidden bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                     <div class="bg-gray-50 -m-6 mb-6 p-4 rounded-t-xl border-b">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i data-lucide="book-marked" class="w-5 h-5 mr-2 text-indigo-500"></i>
                            Informasi Spesifik Dosen
                        </h3>
                    </div>
                     <div class="space-y-6">
                        <div>
                            <label for="mata_kuliah_diampu" class="block text-sm font-medium text-gray-700">Mata Kuliah yang diampu <span class="text-red-500">*</span></label>
                            <textarea name="mata_kuliah_diampu" id="mata_kuliah_diampu" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('mata_kuliah_diampu', $pegawai->mata_kuliah_diampu ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="ranting_ilmu_kepakaran" class="block text-sm font-medium text-gray-700">Ranting Ilmu / Kepakaran <span class="text-red-500">*</span></label>
                            <textarea name="ranting_ilmu_kepakaran" id="ranting_ilmu_kepakaran" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('ranting_ilmu_kepakaran', $pegawai->ranting_ilmu_kepakaran ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="url_profil_sinta" class="block text-sm font-medium text-gray-700">URL Profil Akun Sinta <span class="text-red-500">*</span></label>
                            <input type="url" name="url_profil_sinta" id="url_profil_sinta" value="{{ old('url_profil_sinta', $pegawai->url_profil_sinta ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="https://sinta.kemdikbud.go.id/...">
                        </div>
                     </div>
                </div>
            </div>
        </div>

        {{-- PERUBAHAN TAMPILAN TOMBOL AKSI --}}
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center gap-4">
            <a href="{{ route('backend.admin-univ-usulan.data-pegawai.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 px-4 py-2">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-transform transform hover:scale-105">
                 <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                {{ isset($pegawai) ? 'Simpan Perubahan' : 'Simpan Data' }}
            </button>
        </div>
    </form>
</div>

{{-- SCRIPT TIDAK BERUBAH --}}
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

        // =====> 1. SALIN FUNGSI BARU (setupFileUploadFeedback) DI SINI <=====
    /**
     * Handles visual feedback for file inputs.
     * When a file is selected, it displays the file name and a success icon.
     */
        function setupFileUploadFeedback() {
            // Select all file inputs that are part of the styled upload component
            const fileInputs = document.querySelectorAll('input[type="file"].hidden');

            fileInputs.forEach(input => {
                input.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    const label = this.parentElement; // The <label> is the parent
                    const displayDiv = label.querySelector('.flex-col'); // The container for the icon and text
                    const icon = displayDiv.querySelector('i');
                    const textElement = displayDiv.querySelector('p');

                    if (file) {
                        // A file has been selected
                        textElement.innerHTML = `<span class="font-semibold text-green-700 truncate px-2">${file.name}</span>`;
                        icon.setAttribute('data-lucide', 'file-check-2');
                        icon.classList.remove('text-gray-500');
                        icon.classList.add('text-green-600');
                    } else {
                        // No file selected (e.g., user canceled), reset to default
                        textElement.innerHTML = '<span class="font-semibold">Klik untuk unggah</span>';
                        icon.setAttribute('data-lucide', 'upload-cloud');
                        icon.classList.remove('text-green-600');
                        icon.classList.add('text-gray-500');
                    }

                    // The 'lucide' library needs to be re-initialized to render the new icon
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                });
            });
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
            const isDosen = jenisPegawaiSelect.value === 'Dosen';
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
        setupFileUploadFeedback(); // Initialize file upload feedback


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

    // Function for photo preview
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

    // Add form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');

        // Disable submit button to prevent double submission
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Menyimpan...';

        // Re-enable button after 3 seconds in case of error
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = '{{ isset($pegawai) ? "Simpan Perubahan" : "Simpan Data" }}';
            if (window.lucide) {
                lucide.createIcons();
            }
        }, 3000);
    });
</script>

@endsection
