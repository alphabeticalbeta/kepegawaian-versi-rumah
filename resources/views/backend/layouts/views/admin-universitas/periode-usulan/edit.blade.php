@extends('backend.layouts.roles.admin-universitas.app')
@section('title', 'Edit Periode Usulan - Admin Universitas')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-blue-50">
    <!-- Header Section -->
    <div class="admin-universitas-bg text-white p-6 rounded-2xl mb-6 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Edit Periode Usulan</h1>
                <p class="text-indigo-100">Ubah pengaturan periode usulan: {{ $periode->nama_periode }}</p>
            </div>
            <div class="hidden md:block">
                <a href="{{ route('admin-universitas.periode-usulan.index') }}"
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/30 p-8">
        <form action="{{ route('admin-universitas.periode-usulan.update', $periode) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama_periode" class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Periode <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama_periode"
                           id="nama_periode"
                           value="{{ old('nama_periode', $periode->nama_periode) }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nama_periode') border-red-300 @enderror"
                           placeholder="Contoh: Periode Usulan Semester Ganjil"
                           required>
                    @error('nama_periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jenis_usulan" class="block text-sm font-medium text-slate-700 mb-2">
                        Jenis Usulan <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_usulan"
                            id="jenis_usulan"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('jenis_usulan') border-red-300 @enderror"
                            required>
                        <option value="">Pilih Jenis Usulan</option>
                        <option value="Jabatan Fungsional" {{ old('jenis_usulan', $periode->jenis_usulan) == 'Jabatan Fungsional' ? 'selected' : '' }}>Jabatan Fungsional</option>
                        <option value="Jabatan Struktural" {{ old('jenis_usulan', $periode->jenis_usulan) == 'Jabatan Struktural' ? 'selected' : '' }}>Jabatan Struktural</option>
                        <option value="Kenaikan Pangkat" {{ old('jenis_usulan', $periode->jenis_usulan) == 'Kenaikan Pangkat' ? 'selected' : '' }}>Kenaikan Pangkat</option>
                        <option value="Tugas Belajar" {{ old('jenis_usulan', $periode->jenis_usulan) == 'Tugas Belajar' ? 'selected' : '' }}>Tugas Belajar</option>
                        <option value="Umum" {{ old('jenis_usulan', $periode->jenis_usulan) == 'Umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                    @error('jenis_usulan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tahun_periode" class="block text-sm font-medium text-slate-700 mb-2">
                        Tahun Periode <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="tahun_periode"
                           id="tahun_periode"
                           value="{{ old('tahun_periode', $periode->tahun_periode) }}"
                           min="2020"
                           max="2050"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('tahun_periode') border-red-300 @enderror"
                           required>
                    @error('tahun_periode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('status') border-red-300 @enderror"
                            required>
                        <option value="Tutup" {{ old('status', $periode->status) == 'Tutup' ? 'selected' : '' }}>Tutup</option>
                        <option value="Buka" {{ old('status', $periode->status) == 'Buka' ? 'selected' : '' }}>Buka</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Date Range -->
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-lg font-medium text-slate-900 mb-4">Periode Waktu</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Pembukaan <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_mulai"
                               id="tanggal_mulai"
                               value="{{ old('tanggal_mulai', $periode->tanggal_mulai ? $periode->tanggal_mulai->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('tanggal_mulai') border-red-300 @enderror"
                               required>
                        @error('tanggal_mulai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Penutup <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="tanggal_selesai"
                               id="tanggal_selesai"
                               value="{{ old('tanggal_selesai', $periode->tanggal_selesai ? $periode->tanggal_selesai->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('tanggal_selesai') border-red-300 @enderror"
                               required>
                        @error('tanggal_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Perbaikan Date Range -->
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-lg font-medium text-slate-900 mb-4">Periode Perbaikan (Opsional)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal_mulai_perbaikan" class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Awal Perbaikan
                        </label>
                        <input type="date"
                               name="tanggal_mulai_perbaikan"
                               id="tanggal_mulai_perbaikan"
                               value="{{ old('tanggal_mulai_perbaikan', $periode->tanggal_mulai_perbaikan ? $periode->tanggal_mulai_perbaikan->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('tanggal_mulai_perbaikan') border-red-300 @enderror">
                        @error('tanggal_mulai_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_selesai_perbaikan" class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Akhir Perbaikan
                        </label>
                        <input type="date"
                               name="tanggal_selesai_perbaikan"
                               id="tanggal_selesai_perbaikan"
                               value="{{ old('tanggal_selesai_perbaikan', $periode->tanggal_selesai_perbaikan ? $periode->tanggal_selesai_perbaikan->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('tanggal_selesai_perbaikan') border-red-300 @enderror">
                        @error('tanggal_selesai_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Settings -->
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-lg font-medium text-slate-900 mb-4">Pengaturan Tambahan</h3>

                <div>
                    <label for="senat_min_setuju" class="block text-sm font-medium text-slate-700 mb-2">
                        Minimum Persetujuan Senat (%)
                    </label>
                    <input type="number"
                           name="senat_min_setuju"
                           id="senat_min_setuju"
                           value="{{ old('senat_min_setuju', $periode->senat_min_setuju) }}"
                           min="1"
                           max="100"
                           class="w-full md:w-1/3 px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('senat_min_setuju') border-red-300 @enderror"
                           placeholder="70">
                    <p class="mt-1 text-sm text-slate-500">Persentase minimum persetujuan yang diperlukan dari senat</p>
                    @error('senat_min_setuju')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-6">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition-colors duration-200 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Perbarui Periode
                </button>

                <a href="{{ route('admin-universitas.periode-usulan.index') }}"
                   class="bg-slate-200 text-slate-700 px-6 py-3 rounded-xl hover:bg-slate-300 transition-colors duration-200 font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
