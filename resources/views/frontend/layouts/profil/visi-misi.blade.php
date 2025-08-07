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

    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-2">Visi dan Misi</h1>
        <h2 class="text-4xl font-bold text-center mb-8">Universitas Mulawarman</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-100 p-6 rounded-lg shadow">
            <!-- Visi -->
            <div>
                <h3 class="text-xl font-bold mb-2">Visi</h3>
                <p class="text-justify leading-relaxed">
                    Universitas berstandar internasional yang mampu berperan dalam pembangunan bangsa melalui pendidikan,
                    penelitian, dan pengabdian kepada masyarakat yang bertumpu pada sumber daya alam (sda)
                    khususnya hutan tropis lembab (tropical rain forest) dan lingkungannya.
                </p>
            </div>

            <!-- Misi -->
            <div>
                <h3 class="text-xl font-bold mb-2">Misi</h3>
                <ol class="list-decimal list-inside space-y-2 text-justify leading-relaxed">
                    <li>
                        Menghasilkan sumber daya manusia yang berkualitas, berkepribadian dan profesional
                        melalui penyelenggaraan pendidikan tinggi yang bertaraf Internasional;
                    </li>
                    <li>
                        Menghasilkan riset yang berkualitas serta berdayaguna dengan mengedepankan prinsip-prinsip
                        kelestarian lingkungan hidup;
                    </li>
                    <li>
                        Menyelenggarakan kegiatan pengabdian pada kepada masyarakat dan menghasilkan karya ilmu
                        pengetahuan, teknologi, seni, dan olahraga yang bermakna dan bermanfaat demi terwujudnya
                        pengelolaan universitas yang akuntabel dan mandiri sesuai dengan standar nasional dan internasional.
                    </li>
                </ol>
            </div>
        </div>
    </div>

    @include('frontend.components.footer')

</body>
</html>
