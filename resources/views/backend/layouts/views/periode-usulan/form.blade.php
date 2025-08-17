@extends('backend.layouts.roles.periode-usulan.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-6">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        {{ isset($periode) ? 'Edit Periode ' . ($nama_usulan ?? 'Usulan Jabatan') : 'Tambah Periode ' . ($nama_usulan ?? 'Usulan Jabatan') }}
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">
                        Kelola periode untuk {{ $nama_usulan ?? 'usulan jabatan dosen dan tenaga kependidikan' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 py-8">
            <form action="{{ isset($periode) ? route('backend.admin-univ-usulan.periode-usulan.update', $periode->id) : route('backend.admin-univ-usulan.periode-usulan.store') }}" method="POST" class="space-y-8">
                @csrf
                @if(isset($periode))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="nama_periode" class="block text-sm font-semibold text-gray-800">
                            Nama Periode <span class="text-red-500">*</span>
                        </label>
                            <input type="text" name="nama_periode" id="nama_periode"
                                class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:border-gray-300"
                                placeholder="Contoh: Periode Usulan Jabatan Semester Ganjil 2024"
                                value="{{ old('nama_periode', isset($periode) ? $periode->nama_periode : '') }}" required>
                        @error('nama_periode')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="jenis_usulan" class="block text-sm font-semibold text-gray-800">
                            Jenis Usulan <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_usulan" id="jenis_usulan"
                                class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 bg-white rounded-xl shadow-sm appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required onchange="updateJenisUsulanInfo()">
                            <option value="">Pilih Jenis Usulan</option>
                            <option value="Usulan Jabatan" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : (isset($jenis_usulan_otomatis) ? $jenis_usulan_otomatis : '')) == 'Usulan Jabatan' ? 'selected' : '' }}>
                                Usulan Jabatan
                            </option>
                            <option value="usulan-jabatan-dosen" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'usulan-jabatan-dosen' ? 'selected' : '' }}>
                                Usulan Jabatan Dosen
                            </option>
                            <option value="usulan-jabatan-tendik" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'usulan-jabatan-tendik' ? 'selected' : '' }}>
                                Usulan Jabatan Tenaga Kependidikan
                            </option>
                            <option value="Usulan NUPTK" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan NUPTK' ? 'selected' : '' }}>
                                Usulan NUPTK
                            </option>
                            <option value="Usulan Laporan LKD" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Laporan LKD' ? 'selected' : '' }}>
                                Usulan Laporan LKD
                            </option>
                            <option value="Usulan Presensi" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Presensi' ? 'selected' : '' }}>
                                Usulan Presensi
                            </option>
                            <option value="Usulan Penyesuaian Masa Kerja" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Penyesuaian Masa Kerja' ? 'selected' : '' }}>
                                Usulan Penyesuaian Masa Kerja
                            </option>
                            <option value="Usulan Ujian Dinas & Ijazah" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Ujian Dinas & Ijazah' ? 'selected' : '' }}>
                                Usulan Ujian Dinas & Ijazah
                            </option>
                            <option value="Usulan Laporan Serdos" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Laporan Serdos' ? 'selected' : '' }}>
                                Usulan Laporan Serdos
                            </option>
                            <option value="Usulan Pensiun" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Pensiun' ? 'selected' : '' }}>
                                Usulan Pensiun
                            </option>
                            <option value="Usulan Kepangkatan" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Kepangkatan' ? 'selected' : '' }}>
                                Usulan Kepangkatan
                            </option>
                            <option value="Usulan Pencantuman Gelar" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Pencantuman Gelar' ? 'selected' : '' }}>
                                Usulan Pencantuman Gelar
                            </option>
                            <option value="Usulan ID SINTA ke SISTER" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan ID SINTA ke SISTER' ? 'selected' : '' }}>
                                Usulan ID SINTA ke SISTER
                            </option>
                            <option value="Usulan Satyalancana" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Satyalancana' ? 'selected' : '' }}>
                                Usulan Satyalancana
                            </option>
                            <option value="Usulan Tugas Belajar" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Tugas Belajar' ? 'selected' : '' }}>
                                Usulan Tugas Belajar
                            </option>
                            <option value="Usulan Pengaktifan Kembali" {{ old('jenis_usulan', isset($periode) ? $periode->jenis_usulan : '') == 'Usulan Pengaktifan Kembali' ? 'selected' : '' }}>
                                Usulan Pengaktifan Kembali
                            </option>
                        </select>
                        @error('jenis_usulan')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Info Box untuk Dosen -->
                <div id="info-dosen" class="info-box hidden p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Kriteria Eligibilitas:</strong> Hanya pegawai dengan status kepegawaian "Dosen PNS" yang dapat mengajukan usulan jabatan dosen.
                            </p>
                            <p class="text-sm text-blue-600 mt-1">
                                Jenjang usulan: Tenaga Pengajar → Asisten Ahli → Lektor → Lektor Kepala → Guru Besar
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Box untuk Tenaga Kependidikan -->
                <div id="info-tendik" class="info-box hidden p-4 bg-purple-50 border-l-4 border-purple-400 rounded-r-lg">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-purple-700">
                                <strong>Kriteria Eligibilitas:</strong> Hanya pegawai dengan status kepegawaian "Tenaga Kependidikan PNS" yang dapat mengajukan usulan jabatan tenaga kependidikan.
                            </p>
                            <p class="text-sm text-purple-600 mt-1">
                                Jenis Jabatan: Fungsional Umum ↔ Struktural ↔ Fungsional Tertentu
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Warning Box untuk Tenaga Kependidikan -->
                <div id="warning-tendik" class="info-box hidden p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Dalam Pengembangan:</strong> Form ini masih dalam tahap pengembangan. Saat ini hanya dapat membuat periode, namun fungsi usulan belum tersedia.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Jenjang Jabatan Dosen -->
                <div id="jenjang-dosen" class="info-box hidden p-6 border-2 border-blue-200 rounded-xl bg-blue-50 space-y-4">
                    <h4 class="text-md font-semibold text-blue-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Jenjang Jabatan Dosen yang Tersedia
                    </h4>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Tenaga Pengajar</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Asisten Ahli</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Asisten Ahli</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Lektor</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Lektor</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Lektor Kepala</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded-lg border border-blue-200">
                            <div class="text-sm font-semibold text-blue-700">Dari</div>
                            <div class="text-xs text-blue-600 mt-1">Lektor Kepala</div>
                            <div class="text-blue-500 my-1">↓</div>
                            <div class="text-sm font-semibold text-green-700">Ke</div>
                            <div class="text-xs text-green-600 mt-1">Guru Besar</div>
                        </div>
                    </div>
                </div>

                <!-- Jenjang Jabatan Tenaga Kependidikan -->
                <div id="jenjang-tendik" class="info-box hidden p-6 border-2 border-purple-200 rounded-xl bg-purple-50 space-y-4">
                    <h4 class="text-md font-semibold text-purple-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Jenis Jabatan Tenaga Kependidikan yang Tersedia
                    </h4>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-white rounded-lg border border-purple-200">
                            <div class="text-sm font-semibold text-purple-700 mb-2">Fungsional Umum</div>
                            <div class="flex justify-center items-center space-x-2 text-purple-500">
                                <span>↕</span>
                            </div>
                            <div class="text-sm font-semibold text-purple-700 mt-2">Struktural</div>
                            <div class="text-xs text-purple-600 mt-1">Perpindahan dua arah</div>
                        </div>
                        <div class="text-center p-4 bg-white rounded-lg border border-purple-200">
                            <div class="text-sm font-semibold text-purple-700 mb-2">Fungsional Umum</div>
                            <div class="flex justify-center items-center space-x-2 text-purple-500">
                                <span>↕</span>
                            </div>
                            <div class="text-sm font-semibold text-purple-700 mt-2">Fungsional Tertentu</div>
                            <div class="text-xs text-purple-600 mt-1">Perpindahan dua arah</div>
                        </div>
                        <div class="text-center p-4 bg-white rounded-lg border border-purple-200">
                            <div class="text-sm font-semibold text-purple-700 mb-2">Struktural</div>
                            <div class="flex justify-center items-center space-x-2 text-purple-500">
                                <span>↕</span>
                            </div>
                            <div class="text-sm font-semibold text-purple-700 mt-2">Fungsional Tertentu</div>
                            <div class="text-xs text-purple-600 mt-1">Perpindahan dua arah</div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-2 border-gray-200 rounded-xl space-y-4">
                    <h4 class="text-md font-semibold text-gray-800">Jadwal Pengajuan Usulan</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="tanggal_mulai" class="block text-sm font-semibold text-gray-800">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            {{-- PERBAIKAN: Tambahkan value --}}
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   value="{{ old('tanggal_mulai', isset($periode) ? $periode->tanggal_mulai->format('Y-m-d') : '') }}" required>
                            @error('tanggal_mulai')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="tanggal_selesai" class="block text-sm font-semibold text-gray-800">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                             {{-- PERBAIKAN: Tambahkan value --}}
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                   class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   value="{{ old('tanggal_selesai', isset($periode) ? $periode->tanggal_selesai->format('Y-m-d') : '') }}" required>
                            @error('tanggal_selesai')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="p-6 border-2 border-gray-200 rounded-xl space-y-4">
                     <h4 class="text-md font-semibold text-gray-800">Jadwal Perbaikan (Opsional)</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="tanggal_mulai_perbaikan" class="block text-sm font-semibold text-gray-800">
                                Tanggal Mulai Perbaikan
                            </label>
                                <input type="date" name="tanggal_mulai_perbaikan" id="tanggal_mulai_perbaikan"
                                    class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    value="{{ old('tanggal_mulai_perbaikan', isset($periode) && $periode->tanggal_mulai_perbaikan ? $periode->tanggal_mulai_perbaikan->format('Y-m-d') : '') }}">
                            @error('tanggal_mulai_perbaikan')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="tanggal_selesai_perbaikan" class="block text-sm font-semibold text-gray-800">
                                Tanggal Selesai Perbaikan
                            </label>
                            <input type="date" name="tanggal_selesai_perbaikan" id="tanggal_selesai_perbaikan"
                                class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                value="{{ old('tanggal_selesai_perbaikan', isset($periode) && $periode->tanggal_selesai_perbaikan ? $periode->tanggal_selesai_perbaikan->format('Y-m-d') : '') }}">
                            @error('tanggal_selesai_perbaikan')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="senat_min_setuju" class="block text-sm font-semibold text-gray-800">
                            Minimal Anggota Senat “Direkomendasikan”
                        </label>
                        <input
                            type="number"
                            min="1"
                            step="1"
                            id="senat_min_setuju"
                            name="senat_min_setuju"
                            value="{{ old('senat_min_setuju', isset($periode) ? $periode->senat_min_setuju : 1) }}"
                            class="block w-40 px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            required
                        >
                        <p class="text-xs text-gray-500">
                            Jumlah minimal anggota Senat yang harus memilih <b>Direkomendasikan</b>
                            agar usulan bisa direkomendasikan oleh Admin Universitas.
                        </p>
                        @error('senat_min_setuju')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div class="space-y-2">
                    <label for="status" class="block text-sm font-semibold text-gray-800">
                        Status Periode <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-200 bg-white rounded-xl shadow-sm appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="Tutup" {{ old('status', isset($periode) ? $periode->status : 'Tutup') == 'Tutup' ? 'selected' : '' }}>Tutup</option>
                        <option value="Buka" {{ old('status', isset($periode) ? $periode->status : 'Tutup') == 'Buka' ? 'selected' : '' }}>Buka</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('backend.admin-univ-usulan.pusat-usulan.index') }}" class="px-6 py-3 border-2 border-gray-300 text-sm font-semibold text-gray-700 bg-white rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg transition-all duration-200">
                        {{ isset($periode) ? 'Update Periode' : 'Simpan Periode' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

