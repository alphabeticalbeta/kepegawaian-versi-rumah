<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Kepegawaian UNMUL</title>
    @vite('resources/css/app.css')
    {{-- Jika Anda menggunakan font atau library JS tambahan, panggil di sini --}}
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">
        @include('backend.components.sidebar-admin-universitas')

        <div class="flex-1 flex flex-col overflow-hidden">
            @include('backend.components.header')

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
