{{-- File: resources/views/frontend/layout/app.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian UNMUL</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">

    @include('frontend.components.header')
    <div class="container mx-auto px-8 py-8">
        <h1 class="text-4xl font-bold text-center mb-2">Usulan Kepegawaian</h1>
        <h2 class="text-4xl font-bold text-center mb-8">Universitas Mulawarman</h2>

        <div class="flex justify-center">

        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 sm:grid-cols-4 gap-8 px-20 py-4">
        <a href="{{ url('/nuptk') }}">
            <img src="{{ asset('images/frontend/Usulan-NUPTK.webp') }}" alt="Usulan Nomor Dosen dan Tendik NUPTK" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/lkd') }}">
            <img src="{{ asset('images/frontend/Usulan-Laporan-LKD.webp') }}" alt="Usulan Laporan LKD" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/pak-konversi') }}">
            <img src="{{ asset('images/frontend/Usulan-PAK-Konversi.webp') }}" alt="Usulan PAK Konversi" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/presensi') }}">
            <img src="{{ asset('images/frontend/Usulan-Presensi.webp') }}" alt="Usulan Presensi" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/masa-kerja') }}">
            <img src="{{ asset('images/frontend/Usulan-Penyesuaian-Masa-Kerja.webp') }}" alt="Usulan Penyesuaian Masa Kerja" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/ujian-penyesuaian') }}">
            <img src="{{ asset('images/frontend/Usulan-Ujian-Dinas-dan-Ujian-Penyesuaian-Ijazah.webp') }}" alt="Usulan Ujian Dinas dan Ujian Penyesuaian Ijazah" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/jabatan') }}">
            <img src="{{ asset('images/frontend/Usulan-Jabatan.webp') }}" alt="Usulan Jabatan" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/serdos') }}">
            <img src="{{ asset('images/frontend/Usulan-Pemenuhan-Serdos-Pekerti-dan-AA.webp') }}" alt="Usulan Laporan Serdos" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/kepangkatan') }}">
            <img src="{{ asset('images/frontend/Usulan-Kepangkatan.webp') }}" alt="Usulan Kepangkatan" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/gelar') }}">
            <img src="{{ asset('images/frontend/Usulan-Pencantuman-Gelar.webp') }}" alt="Usulan Pencantuman Gelar" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/sinta-sister') }}">
            <img src="{{ asset('images/frontend/Usulan-ID-SINTA-ke-SISTER.webp') }}" alt="Usulan ID SINTA ke SISTER" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/pensiun') }}">
            <img src="{{ asset('images/frontend/Usulan-Pensiun.webp') }}" alt="Usulan Pensiun" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/satyalancana') }}">
            <img src="{{ asset('images/frontend/Usulan-Satyalancana-Karya-Satya.webp') }}" alt="Usulan Satyalancana Karya Satya" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/tugas-belajar') }}">
            <img src="{{ asset('images/frontend/Usulan-Tugas-Belajar.webp') }}" alt="Usulan Tugas Belajar" class="hover:scale-105 transition duration-300">
        </a>
        <a href="{{ url('/pengaktifan') }}">
            <img src="{{ asset('images/frontend/Pengaktifan-Kembali.webp') }}" alt="Usulan Pengaktifan Kembali" class="hover:scale-105 transition duration-300">
        </a>
    </div>

    @include('frontend.components.footer')

</body>
</html>
