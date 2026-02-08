<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    {{-- SCRIPT PENCEGAH FLASH (ANTI-KELIP) --}}
    <script>
        (function() {
            // Logic Dark Mode
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.removeAttribute('data-theme');
            }

            // Logic Preset Warna (Penting buat matiin kelip biru saat reload)
            const preset = localStorage.getItem('theme-preset') || 'blue';
            document.documentElement.setAttribute('data-theme-preset', preset);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-app text-app min-h-screen">
    {{-- Overlay untuk Mobile --}}
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    {{-- Flash Toast --}}
    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
            class="fixed top-4 right-4 z-50 bg-red-600 text-white text-sm px-4 py-3 rounded shadow">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex min-h-screen overflow-x-hidden">
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            @include('partials.navbar')

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Script Sidebar Mobile --}}
    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }
    </script>
</body>

</html>
