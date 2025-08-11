{{-- File: resources/views/frontend/layout/app.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian UNMUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- External Libraries --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Alpine.js for reactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-gray-50">

    @include('frontend.components.header')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-2">Aplikasi</h1>
        <h2 class="text-4xl font-bold text-center mb-2">Universitas Mulawarman</h2>

        <div class="container mx-auto px-4 py-12">
            <div class="overflow-x-auto animate-fade-in-up delay-100">
                <table class="min-w-full bg-white border border-gray-300 shadow rounded-lg">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="py-3 px-4 border-b">Aplikasi</th>
                            <th class="py-3 px-4 border-b">Sumber</th>
                            <th class="py-3 px-4 border-b">Keterangan</th>
                            <th class="py-3 px-4 border-b">Link</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @php
                            $data = [
                                ['SIDAK', 'Universitas Mulawarman', 'Sistem Informasi Database Administrasi Kepegawaian'],
                                ['Simkinerja', 'Universitas Mulawarman', 'Sistem Informasi Kinerja (Remunerasi)'],
                                ['Repository', 'Universitas Mulawarman', 'Repositori Universitas Mulawarman'],
                                ['BKD', 'Universitas Mulawarman', 'Beban Kerja Dosen'],
                                ['Sister', 'Universitas Mulawarman', 'Sistem Informasi Sumberdaya Terintegrasi'],
                                ['Sister', 'Kemendikbudristek', 'Sistem Informasi Sumberdaya Terintegrasi'],
                                ['UD dan PI Online', 'Kemendikbudristek', 'Ujian Dinas Berbasis Komputer Secara Daring (Online)'],
                                ['HCDP', 'Kemendikbudristek', 'Sistem Rencana Pengembangan Kompetensi Biro SDM'],
                                ['Asesmen', 'Kemendikbudristek', 'Sistem Pusat Asesmen Pegawai'],
                                ['Simdiklat', 'Kemendikbudristek', 'Sistem Manajemen Pelatihan'],
                                ['SDM PDDIKTI', 'Kemendikbudristek', 'Portal Registrasi Sumber Daya'],
                                ['SIPD', 'Kemendikbudristek', 'Sistem Layanan SDM'],
                                ['Arsip SDM', 'Kemendikbudristek', 'Sistem Arsip Nasional Pegawai'],
                                ['PAK', 'Kemendikbudristek', 'Sistem Penilaian Angka Kredit Dosen'],
                                ['Akses Sister', 'Kemendikbudristek', 'Manajemen Akses SISTER'],
                                ['Ban PT', 'Kemendikbudristek', 'Data Akreditasi Universitas dan Program Studi'],
                                ['Sijapti', 'Komisi Aparatur Sipil Negara', 'Sistem Informasi Jabatan Pimpinan Tinggi'],
                                ['EJafung', 'Kemenkeu', 'Jafung Perbendaharaan'],
                                ['Siarka', 'Menpan RB', 'Sistem Informasi Pelaporan Harta Kekayaan ASN'],
                                ['E-LHKPN', 'KPK', 'Sistem Laporan Hasil Kekayaan Penyelenggara Negara'],
                                ['Docu Digital', 'BKN', 'Docudigital Instansi'],
                                ['Sikejab', 'BKN', 'Sistem Informasi Kamus Kelas Jabatan(SIKEJAB)'],
                                ['e-Dupak', 'BKN', 'Sistem Informasi Penilaian DUPAK'],
                                ['e-Dupak Training', 'BKN', 'Sistem Informasi Percobaan Penilaian DUPAK'],
                                ['E-Dabu', 'BPJS', 'Elektronik Data Badan Usaha'],
                                ['SIPP', 'BPJS', 'Pelaporan Data Perusahaan BPJAMSOSTEK'],
                                ['PLTI', 'PLTI', 'Pusat Layanan Tes Indonesia'],
                            ];
                        @endphp

                        @foreach($data as $row)
                            <tr class="hover:bg-gray-100 transition duration-200 ease-in-out">
                                <td class="py-2 px-4 border-b">{{ $row[0] }}</td>
                                <td class="py-2 px-4 border-b">{{ $row[1] }}</td>
                                <td class="py-2 px-4 border-b">{{ $row[2] }}</td>
                                <td class="py-2 px-4 border-b">
                                    <a href="#" target="_blank"
                                    class="inline-block bg-black text-white text-sm px-4 py-2 rounded hover:bg-gray-400 transition">
                                        Buka Aplikasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @include('frontend.components.footer')

</body>
</html>
