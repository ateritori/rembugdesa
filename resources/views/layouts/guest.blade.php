<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rembug Desa - Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-900 antialiased bg-[#fdfdfe] selection:bg-primary selection:text-white">

    {{-- Atmosphere: Background Glows --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-primary/5 rounded-full blur-[120px] animate-pulse">
        </div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[50%] h-[50%] bg-blue-400/5 rounded-full blur-[120px] animate-pulse"
            style="animation-delay: 2s;"></div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        {{--
            Container ini sengaja dibuat lebar agar form kita
            bisa "bernapas" dan tidak kaku.
        --}}
        <div class="w-full max-w-[500px]">
            {{ $slot }}
        </div>
    </div>

    <style>
        /* Mencegah tumpang tindih desain card kita dengan sisa style Breeze */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px white inset !important;
        }
    </style>
</body>

</html>
