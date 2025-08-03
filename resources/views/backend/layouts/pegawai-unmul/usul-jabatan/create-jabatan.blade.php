@extends('backend.layouts.pegawai-unmul.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Formulir Pengajuan Usulan Jabatan
        </h1>
        <p class="mt-2 text-gray-600">
            Silakan lengkapi semua informasi yang dibutuhkan untuk mengajukan kenaikan jabatan fungsional.
        </p>
    </div>

    @if($daftarPeriode->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="py-1"><svg class="h-5 w-5 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 11-2 0 1 1 0 012 0zm-1-3a1 1 0 00-1 1v2a1 1 0 102 0V9a1 1 0 00-1-1z" clip-rule="evenodd"/></svg></div>
                <div>
                    <p class="font-bold text-yellow-800">Perhatian</p>
                    <p class="text-sm text-yellow-700">Saat ini tidak ada periode pengajuan usulan jabatan yang sedang dibuka.</p>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('pegawai-unmul.usulan-jabatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Pengusul</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><strong class="text-gray-600">Nama:</strong> {{ $pegawai->nama_lengkap }}</div>
                    <div><strong class="text-gray-600">NIP:</strong> {{ $pegawai->nip }}</div>
                    <div><strong class="text-gray-600">Jabatan Saat Ini:</strong> {{ $pegawai->jabatan->jabatan }}</div>
                    <div><strong class="text-gray-600">Jabatan yang Dituju:</strong>
                        @if($jabatanTujuan)
                            <span class="font-semibold text-indigo-600">{{ $jabatanTujuan->jabatan }}</span>
                        @else
                            <span class="font-semibold text-green-600">Anda sudah berada di jabatan fungsional tertinggi.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-6 py-8 space-y-6">

                <div>
                    <label for="periode_usulan_id" class="block text-sm font-semibold text-gray-800 mb-1">Pilih Periode Pengajuan <span class="text-red-500">*</span></label>
                    <select name="periode_usulan_id" id="periode_usulan_id" class="block w-full ... " required>
                        <option value="">-- Pilih Periode yang Tersedia --</option>
                        @foreach ($daftarPeriode as $periode)
                            <option value="{{ $periode->id }}">{{ $periode->nama_periode }}</option>
                        @endforeach
                    </select>
                </div>

                <hr>

                <h4 class="text-md font-semibold text-gray-800">Detail Karya Ilmiah Utama</h4>
                {{-- Di sini Anda bisa menambahkan field-field untuk karya ilmiah seperti Nama Jurnal, Judul Artikel, dll. --}}
                {{-- Untuk saat ini kita sederhanakan dengan satu input file saja --}}
                <div>
                    <label for="dokumen_pakta_integritas" class="block text-sm font-semibold text-gray-800 mb-1">Upload Pernyataan Pakta Integritas Karya Ilmiah <span class="text-red-500">*</span></label>
                    <input type="file" name="dokumen_pakta_integritas" id="dokumen_pakta_integritas" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                    <p class="mt-1 text-xs text-gray-500">File harus dalam format PDF, maksimal 1MB.</p>
                </div>

            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end items-center gap-4">
                <a href="{{ route('pegawai-unmul.usulan-pegawai.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-700 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        @if($daftarPeriode->isEmpty() || !$jabatanTujuan) disabled @endif>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Ajukan Usulan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
