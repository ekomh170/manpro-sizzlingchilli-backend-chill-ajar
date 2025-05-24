<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chill Ajar Backend API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
        }

        .gradient {
            background: linear-gradient(90deg, #fde047 0%, #facc15 100%);
        }

        .glass {
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 8px 32px 0 rgba(251, 191, 36, 0.15);
            backdrop-filter: blur(6px);
            border-radius: 1.5rem;
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-yellow-50 via-amber-50 to-yellow-100 min-h-screen flex flex-col items-center justify-center p-6">
    <div class="max-w-2xl mx-auto glass p-8 mt-8 text-center border border-amber-200 rounded-3xl">
        <img src="/developer/Foto_Eko%20Muchamad%20Haryono.jpg" alt="Foto Developer Chill Ajar"
            class="mx-auto mb-3 rounded-full shadow-lg border-4 border-amber-200 w-32 h-32 object-cover">
        <div class="mb-4">
            <span class="block text-lg font-bold text-amber-700">Eko Muchamad Haryono</span>
            <span class="block text-xs text-gray-500">Developer</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 gradient bg-clip-text text-transparent drop-shadow-lg">Chill
            Ajar Backend API</h1>
        <p class="text-lg md:text-xl text-gray-700 mb-6 leading-relaxed">
            Selamat datang di backend <b>Chill Ajar</b>.<br>
            Sistem backend Laravel 12 dengan API modern, dokumentasi otomatis, dan UI pengujian interaktif.<br><br>
            <span class="block text-amber-700 font-semibold mt-4">Akses API ini bersifat tersembunyi dan hanya untuk
                kebutuhan internal aplikasi Chill Ajar.<br>
                Endpoint, dokumentasi, dan UI pengujian hanya dapat diakses oleh tim pengembang dan admin melalui jalur
                khusus.</span>
        </p>
        <div class="mt-8 text-sm text-gray-500">
            <span class="inline-flex items-center gap-1">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                </svg>
                Backend Laravel 12, UI modern, dokumentasi otomatis, dan pengujian API interaktif.
            </span>
        </div>
    </div>
</body>

</html>
