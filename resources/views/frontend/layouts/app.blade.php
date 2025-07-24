{{-- File: resources/views/frontend/layout/app.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kepegawaian UNMUL</title>
    @php
        $manifestPath = public_path('build/manifest.json');
    @endphp
    @if (file_exists($manifestPath))
        @php
            $manifest = json_decode(file_get_contents($manifestPath), true);
        @endphp
        @isset($manifest['resources/css/app.css'])
            @vite('resources/css/app.css')
        @endisset
    @endif
</head>
<body class="bg-gray-50">

    @include('frontend.components.header')
    @include('frontend.components.content')
    @include('frontend.components.announcement')
    @include('frontend.components.news')
    @include('frontend.components.footer')

</body>
</html>
