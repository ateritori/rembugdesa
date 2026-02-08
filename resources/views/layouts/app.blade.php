<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script>
        (function() {
            // 1. Ambil data dari storage (default ke blue/light jika kosong)
            const mode = localStorage.getItem('theme-mode') || 'light';
            const preset = localStorage.getItem('theme-preset') || 'blue';
            const color = localStorage.getItem('theme-color') || '#2563eb';

            // 2. Terapkan secara instan ke tag <html>
            const root = document.documentElement;
            root.classList.toggle('dark', mode === 'dark');
            root.setAttribute('data-theme', mode);
            root.setAttribute('data-theme-preset', preset);
            root.style.setProperty('--primary', color);

            // 3. Fungsi Global supaya tombol preset & dark mode tetep jalan
            window.setThemePreset = function(name) {
                const presets = {
                    gray: "#4b5563",
                    green: "#15803d",
                    blue: "#2563eb",
                    brown: "#78350f"
                };
                if (presets[name]) {
                    localStorage.setItem('theme-preset', name);
                    localStorage.setItem('theme-color', presets[name]);
                    // Reload supaya CSS variabel berubah total tanpa bug
                    window.location.reload();
                }
            };

            window.toggleThemeMode = function() {
                const currentMode = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
                const newMode = currentMode === 'dark' ? 'light' : 'dark';
                localStorage.setItem('theme-mode', newMode);
                window.location.reload();
            };
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-app text-app">
    <div class="min-h-screen bg-app"> @include('layouts.navigation')

        @isset($header)
            <header class="bg-card shadow border-app">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>
