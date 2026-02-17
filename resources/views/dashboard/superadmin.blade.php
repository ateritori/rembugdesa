@extends('layouts.dashboard')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-700">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black tracking-tight">
                    Dashboard Superadmin
                </h1>
                <p class="adaptive-text-sub text-sm font-bold">
                    Kontrol global sistem dan manajemen pengguna.
                </p>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $cards = [
                    [
                        'label' => 'Total Pengguna',
                        'value' => $totalUsers,
                        'icon' => 'M5 12h14M12 5v14',
                        'color' => 'text-blue-500',
                        'bg' => 'bg-blue-500/10',
                    ],
                    [
                        'label' => 'Admin',
                        'value' => $totalAdmins,
                        'icon' => 'M9 12h6M12 9v6',
                        'color' => 'text-amber-500',
                        'bg' => 'bg-amber-500/10',
                    ],
                    [
                        'label' => 'Decision Maker',
                        'value' => $totalDms,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'color' => 'text-emerald-500',
                        'bg' => 'bg-emerald-500/10',
                    ],
                    [
                        'label' => 'Total Sesi',
                        'value' => $totalSessions,
                        'icon' => 'M19 11H5m14 0a2 2 0 012 2v6',
                        'color' => 'text-rose-500',
                        'bg' => 'bg-rose-500/10',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="adaptive-card hover:border-primary/50 group flex items-center justify-between p-6 transition-all">
                    <div>
                        <p class="adaptive-text-sub mb-1 text-[10px] font-black uppercase tracking-widest">
                            {{ $card['label'] }}
                        </p>
                        <p class="adaptive-text-main text-3xl font-black">{{ $card['value'] }}</p>
                    </div>
                    <div
                        class="{{ $card['bg'] }} {{ $card['color'] }} flex h-12 w-12 items-center justify-center rounded-2xl transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}">
                            </path>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- MENU KONTROL SUPERADMIN --}}
        <div class="adaptive-card p-6">
            <h2 class="adaptive-text-main mb-4 text-lg font-black">
                Menu Administrasi
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('superadmin.users.index') }}"
                    class="adaptive-card hover:border-primary/50 group flex items-center gap-4 p-5 transition-all">
                    <div class="bg-blue-500/10 text-blue-500 flex h-10 w-10 items-center justify-center rounded-xl">
                        <svg class="h-5 w-5 transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1" />
                        </svg>
                    </div>
                    <div>
                        <p class="adaptive-text-main text-sm font-black">Manajemen Pengguna</p>
                        <p class="adaptive-text-sub text-xs font-bold">User, admin, dan DM</p>
                    </div>
                </a>

                <a href="{{ route('superadmin.roles.index') }}"
                    class="adaptive-card hover:border-primary/50 group flex items-center gap-4 p-5 transition-all">
                    <div class="bg-amber-500/10 text-amber-500 flex h-10 w-10 items-center justify-center rounded-xl">
                        <svg class="h-5 w-5 transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 2.21-1.79 4-4 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="adaptive-text-main text-sm font-black">Role & Permission</p>
                        <p class="adaptive-text-sub text-xs font-bold">Hak akses sistem</p>
                    </div>
                </a>

                <a href="{{ route('superadmin.decision-sessions.index') }}"
                    class="adaptive-card hover:border-primary/50 group flex items-center gap-4 p-5 transition-all">
                    <div class="bg-emerald-500/10 text-emerald-500 flex h-10 w-10 items-center justify-center rounded-xl">
                        <svg class="h-5 w-5 transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="adaptive-text-main text-sm font-black">Sesi Keputusan</p>
                        <p class="adaptive-text-sub text-xs font-bold">Monitoring seluruh sesi</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
@endsection
