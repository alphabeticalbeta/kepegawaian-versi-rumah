<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kepegawaian UNMUL</title>
    @vite('resources/css/app.css')
</head>
<body style="background-image: url('{{ asset('images/bg-unmul.jpg') }}');" class="bg-contain bg-center bg-gray-100">

    {{-- Latar Belakang Utama --}}
    {{-- Ganti class 'bg-gray-100' di atas dengan style untuk background gambar jika diinginkan --}}
    {{-- Contoh: style="background-image: url('{{ asset('images/background.jpg') }}');" class="bg-cover bg-center" --}}

    <main class="flex min-h-screen items-center justify-center p-4">

        {{-- Kartu Login Utama --}}
        <div class="relative flex w-full max-w-4xl overflow-hidden rounded-2xl shadow-2xl">

            <div class="w-full bg-gray-400/90 p-8 sm:p-12 lg:w-3/5 ">
                <div class="text-left">
                    <h1 class="text-3xl font-bold py-7">Halaman Login</h1>

                    <form action="#" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="NIP" class="w-full rounded-md border border-gray-300 bg-gray-100 p-3">
                        <input type="password" name="password" placeholder="Password" class="mt-4 w-full rounded-md border border-gray-300 bg-gray-100 p-3">

                        {{-- <div class="mt-4 flex items-center justify-between">
                            <a href="#" class="text-sm hover:text-yellow-500">Lupa Password?</a>
                        </div> --}}

                        <button type="submit" class="mt-6 w-full rounded-md bg-black py-3 text-white font-bold transition-colors hover:bg-yellow-500">
                            SIGN IN
                        </button>
                    </form>
                </div>
            </div>

            <div
                style="background-image: url('{{ asset('images/logo-unmul.png') }}');"
                class="relative hidden w-2/5 bg-contain bg-center text-center lg:flex lg:rounded-bl-[100px] lg:rounded-br-2xl lg:rounded-tr-2xl">

                <div class="absolute inset-0 rounded-br-2xl rounded-tr-2xl bg-yellow-400/90 lg:rounded-bl-[100px]"></div>

                <div class="relative z-10 flex w-full items-center justify-center p-8">
                    <h2 class="text-4xl font-bold text-black">
                        Selamat Datang di Website Kepegawaian Universitas Mulawarman
                    </h2>
                </div>

            </div>

        </div>
    </main>

</body>
</html>
