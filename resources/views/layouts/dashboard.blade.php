<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-app text-app">
    {{-- Flash Toast --}}
    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
            class="fixed top-4 right-4 z-50 bg-red-600 text-white text-sm px-4 py-3 rounded shadow">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
            class="fixed top-4 right-4 z-50 bg-green-600 text-white text-sm px-4 py-3 rounded shadow">
            {{ session('success') }}
        </div>
    @endif
    {{-- Mobile sidebar backdrop --}}
    <div onclick="toggleSidebar()" class="sidebar-backdrop fixed inset-0 bg-black/40 z-30 md:hidden hidden">
    </div>
    <div class="flex min-h-screen">

        @include('partials.sidebar')

        <div class="flex-1 flex flex-col">
            @include('partials.navbar')

            <main class="p-6">
                @yield('content')
            </main>
        </div>

    </div>
</body>

</html>
