@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-700 pb-10">

        {{-- HEADER & ACTION --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight adaptive-text-main">
                    Dashboard Sesi Keputusan
                </h1>
                <p class="text-sm font-bold adaptive-text-sub">
                    Ringkasan aktivitas dan kontrol sistem keputusan.
                </p>
            </div>
            {{-- Tombol Baru: Pakai bg-primary agar ikut Preset --}}
            <a href="{{ route('decision-sessions.create') }}"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-black text-sm shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Sesi Baru</span>
            </a>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
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
                    class="adaptive-card p-6 flex items-center justify-between group hover:border-primary/50 transition-all">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest adaptive-text-sub mb-1">
                            {{ $card['label'] }}</p>
                        <p class="text-3xl font-black adaptive-text-main">{{ $card['value'] }}</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-2xl {{ $card['bg'] }} {{ $card['color'] }} flex items-center justify-center transition-transform group-hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}">
                            </path>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SESSION TABLE SECTION --}}
        <div class="adaptive-card overflow-hidden">
            <div class="px-6 py-5 border-b border-app flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-black adaptive-text-main">Sesi Terbaru</h2>
                    <p class="text-xs font-bold adaptive-text-sub uppercase tracking-tighter">Riwayat aktivitas pengambilan
                        keputusan</p>
                </div>
                <a href="{{ route('decision-sessions.index') }}"
                    class="text-xs font-black uppercase tracking-widest text-primary hover:underline">
                    Lihat Semua →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.2em] adaptive-text-sub bg-app/50">
                            <th class="px-6 py-4 text-left font-black">Informasi Sesi</th>
                            <th class="px-6 py-4 text-center font-black">Tahun</th>
                            <th class="px-6 py-4 text-center font-black">Status</th>
                            <th class="px-6 py-4 text-right font-black">Navigasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-app">
                        @forelse ($latestSessions as $session)
                            <tr class="hover:bg-app/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black adaptive-text-main group-hover:text-primary transition-colors">{{ $session->name }}</span>
                                        <span class="text-[10px] font-mono opacity-50 uppercase">Session-ID:
                                            #{{ $session->id }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-sm font-bold adaptive-text-main">{{ $session->year }}</span>
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
                                        class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter border {{ $class }}">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <a href="{{ $session->status === 'draft' ? route('criteria.index', $session->id) : route('control.index', $session->id) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:brightness-110 transition-all">
                                        <span>Kelola</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <p class="text-xs font-black opacity-20 uppercase tracking-[0.3em]">Data belum tersedia
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
