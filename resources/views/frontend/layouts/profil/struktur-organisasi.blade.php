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
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-2">Struktur Organisasi</h1>
        <h2 class="text-4xl font-bold text-center mb-8">Universitas Mulawarman</h2>

        <div class="flex justify-center">
            <img
                src="https://kepegawaian.unmul.ac.id/wp-content/uploads/2022/04/Susunan-Struktur-Organisasi-991x1024.png"
                alt="Struktur Organisasi Universitas Mulawarman"
                class="w-full max-w-4xl rounded shadow-lg"
            >
        </div>
    </div>
    @include('frontend.components.footer')

</body>
</html>
