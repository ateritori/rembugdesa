@extends('layouts.dashboard')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-700">

        {{-- HEADER & ACTION --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black tracking-tight">
                    Dashboard Sesi Keputusan
                </h1>
                <p class="adaptive-text-sub text-sm font-bold">
                    Ringkasan aktivitas dan kontrol sistem keputusan.
                </p>
            </div>
            {{-- Tombol Baru: Pakai bg-primary agar ikut Preset --}}
            <a href="{{ route('decision-sessions.create') }}"
                class="bg-primary shadow-primary/20 flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-black text-white shadow-lg transition-all hover:scale-105 active:scale-95">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Sesi Baru</span>
            </a>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $cards = [
                    [
                        'label' => 'Total Sesi',
                        'value' => $totalSessions,
                        'icon' =>
                            'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                        'color' => 'text-blue-500',
                        'bg' => 'bg-blue-500/10',
                    ],
                    [
                        'label' => 'Draft',
                        'value' => $draftSessions,
                        'icon' =>
                            'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                        'color' => 'text-amber-500',
                        'bg' => 'bg-amber-500/10',
                    ],
                    [
                        'label' => 'Aktif',
                        'value' => $activeSessions,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'color' => 'text-emerald-500',
                        'bg' => 'bg-emerald-500/10',
                    ],
                    [
                        'label' => 'Ditutup',
                        'value' => $closedSessions,
                        'icon' =>
                            'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z',
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
                            {{ $card['label'] }}</p>
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

        {{-- SESSION TABLE SECTION --}}
        <div class="adaptive-card overflow-hidden">
            <div class="border-app flex items-center justify-between border-b px-6 py-5">
                <div>
                    <h2 class="adaptive-text-main text-lg font-black">Sesi Terbaru</h2>
                    <p class="adaptive-text-sub text-xs font-bold uppercase tracking-tighter">Riwayat aktivitas pengambilan
                        keputusan</p>
                </div>
                <a href="{{ route('decision-sessions.index') }}"
                    class="text-primary text-xs font-black uppercase tracking-widest hover:underline">
                    Lihat Semua →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="adaptive-text-sub bg-app/50 text-[10px] uppercase tracking-[0.2em]">
                            <th class="px-6 py-4 text-left font-black">Informasi Sesi</th>
                            <th class="px-6 py-4 text-center font-black">Tahun</th>
                            <th class="px-6 py-4 text-center font-black">Status</th>
                            <th class="px-6 py-4 text-right font-black">Navigasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-app divide-y">
                        @forelse ($latestSessions as $session)
                            <tr class="hover:bg-app/50 group transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span
                                            class="adaptive-text-main group-hover:text-primary text-sm font-black transition-colors">{{ $session->name }}</span>
                                        <span class="font-mono text-[10px] uppercase opacity-50">Session-ID:
                                            #{{ $session->id }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="adaptive-text-main text-sm font-bold">{{ $session->year }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-slate-500/10 text-slate-500 border-slate-500/20',
                                            'active' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                            'closed' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                        ];
                                        $class = $statusClasses[$session->status] ?? $statusClasses['draft'];
                                    @endphp
                                    <span
                                        class="{{ $class }} rounded-lg border px-3 py-1 text-[10px] font-black uppercase tracking-tighter">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <a href="{{ $session->status === 'draft'
                                        ? route('criteria.index', $session->id)
                                        : ($session->status === 'closed'
                                            ? route('control.index', [$session->id, 'tab' => 'analisis'])
                                            : route('control.index', $session->id)) }}"
                                        class="bg-primary inline-flex items-center gap-2 rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white transition-all hover:brightness-110">
                                        <span>Kelola</span>
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <p class="text-xs font-black uppercase tracking-[0.3em] opacity-20">Data belum tersedia
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
