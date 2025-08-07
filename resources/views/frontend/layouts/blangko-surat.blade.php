{{-- File: resources/views/frontend/layout/app.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian UNMUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">

    @include('frontend.components.header')

        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 py-5">
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum1/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            Internalisasi BerAKHLAK, Taktik Membangun Budaya Kerja ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>12 May 2022</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum2/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            BKN Dorong Seluruh Instansi Implementasikan Manajemen ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>18 April 2022</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum2/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            BKN Dorong Seluruh Instansi Implementasikan Manajemen ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>18 April 2022</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum2/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            BKN Dorong Seluruh Instansi Implementasikan Manajemen ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>18 April 2022</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum2/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            BKN Dorong Seluruh Instansi Implementasikan Manajemen ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>18 April 2022</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum2/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            BKN Dorong Seluruh Instansi Implementasikan Manajemen ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>18 April 2022</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum2/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            BKN Dorong Seluruh Instansi Implementasikan Manajemen ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>18 April 2022</span>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-xl">
                    <a href="#">
                        <img src="https://picsum.photos/seed/picsum2/400/250" alt="Gambar Berita" class="h-40 w-full object-cover">
                    </a>
                    <div class="p-4">
                        <a href="#" class="block font-bold text-gray-900 hover:text-blue-700">
                            BKN Dorong Seluruh Instansi Implementasikan Manajemen ASN
                        </a>
                        <div class="mt-3 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                            <span>kepegawaian unmul</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                             <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-4.5 12h27" /></svg>
                            <span>18 April 2022</span>
                        </div>
                    </div>
                </div>
        </div>

    @include('frontend.components.footer')

</body>
</html>
