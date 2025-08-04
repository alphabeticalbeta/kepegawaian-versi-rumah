@extends('backend.layouts.pegawai-unmul.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

     @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-6 shadow-md" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Riwayat Usulan Saya
        </h1>
        <p class="mt-2 text-gray-600">
            Pantau status dan riwayat semua usulan yang telah Anda ajukan.
        </p>
    </div>

    <div class="bg-white shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Daftar Usulan
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Berikut adalah semua usulan yang pernah Anda buat.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Jenis Usulan</th>
                        <th scope="col" class="px-6 py-4">Periode</th>
                        <th scope="col" class="px-6 py-4">Tanggal Pengajuan</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usulans as $usulan)
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap capitalize">{{ $usulan->jenis_usulan }}</td>
                            <td class="px-6 py-4">{{ $usulan->periodeUsulan->nama_periode }}</td>
                            <td class="px-6 py-4">{{ $usulan->created_at->isoFormat('D MMMM YYYY') }}</td>
                            <td class="px-6 py-4 text-center">
                                {{-- Contoh Status, bisa disesuaikan nanti --}}
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $usulan->status_usulan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white border-b">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Anda belum pernah membuat usulan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($usulans->hasPages())
            <div class="p-4 border-t border-gray-200">
               {{ $usulans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


