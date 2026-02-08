@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-700">

        {{-- HEADER & ACTION --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black tracking-tight adaptive-header-title">
                    Dashboard Sesi Keputusan
                </h1>
                <p class="text-sm font-medium adaptive-text-sub">
                    Ringkasan aktivitas dan kontrol sistem keputusan.
                </p>
            </div>
            <a href="{{ route('decision-sessions.create') }}" class="btn-manage">
                {{-- Icon Plus (+) --}}
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
                        'color' => 'bg-blue-500',
                    ],
                    [
                        'label' => 'Draft',
                        'value' => $draftSessions,
                        'icon' =>
                            'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                        'color' => 'bg-amber-500',
                    ],
                    [
                        'label' => 'Aktif',
                        'value' => $activeSessions,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'color' => 'bg-emerald-500',
                    ],
                    [
                        'label' => 'Ditutup',
                        'value' => $closedSessions,
                        'icon' =>
                            'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z',
                        'color' => 'bg-rose-500',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="relative overflow-hidden adaptive-card p-6 rounded-2xl group hover:shadow-xl transition-all duration-300">
                    <div class="relative z-10 flex flex-col adaptive-text-main">
                        <span class="card-label mb-1">{{ $card['label'] }}</span>
                        <span class="card-value text-3xl">{{ $card['value'] }}</span>
                    </div>
                    <div class="absolute -right-2 -bottom-2 opacity-10 group-hover:opacity-20 transition-all duration-500">
                        <svg class="w-20 h-20 {{ $card['color'] }} text-white rounded-full p-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}">
                            </path>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SESSION TABLE SECTION --}}
        <div class="adaptive-card rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-white/5 flex justify-between items-center adaptive-table-header">
                <div class="adaptive-text-main">
                    <h2 class="text-base font-bold opacity-100">Sesi Terbaru</h2>
                    <p class="text-[11px] uppercase tracking-tighter opacity-70">Riwayat aktivitas pengambilan keputusan</p>
                </div>
                <a href="{{ route('decision-sessions.index') }}" class="text-xs font-bold uppercase adaptive-header-link">
                    Lihat Semua →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-widest adaptive-text-sub">
                            <th class="px-6 py-4 text-left font-black">Informasi Sesi</th>
                            <th class="px-6 py-4 text-center font-black">Tahun</th>
                            <th class="px-6 py-4 text-center font-black">Status</th>
                            <th class="px-6 py-4 text-right font-black">Navigasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 adaptive-text-main">
                        @forelse ($latestSessions as $session)
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold group-hover:text-primary transition-colors">{{ $session->name }}</span>
                                        <span class="text-[10px] opacity-50">ID:
                                            #{{ str_pad($session->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-3 py-1 badge-status rounded-lg text-xs font-bold">
                                        {{ $session->year }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
                                            'active' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                            'closed' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                        ];
                                        $class = $statusClasses[$session->status] ?? $statusClasses['draft'];
                                    @endphp
                                    <span
                                        class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter border {{ $class }}">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('decision-sessions.show', $session->id) }}" class="btn-manage">
                                        Kelola
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <p class="text-sm font-bold opacity-30 uppercase tracking-widest">Belum ada sesi
                                        keputusan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
