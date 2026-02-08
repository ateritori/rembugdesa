@extends('layouts.dashboard')

@section('content')
    <style>
        /* CSS Pintar: Menjamin keselarasan total dengan Dashboard Admin */
        .adaptive-card {
            background-color: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(156, 163, 175, 0.15);
            transition: all 0.3s ease;
        }

        .adaptive-text-main {
            color: inherit;
        }

        .adaptive-text-sub {
            color: inherit;
            opacity: 0.6;
        }

        /* Tombol Perbarui (Amber) agar tetap kontras tapi tidak menabrak Primary */
        .btn-update-penilaian {
            border: 2px solid #f59e0b;
            color: #f59e0b !important;
            background: rgba(245, 158, 11, 0.1);
        }

        .btn-update-penilaian:hover {
            background: #f59e0b !important;
            color: white !important;
        }
    </style>

    <div class="space-y-8 animate-in fade-in duration-700">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 adaptive-text-main">
            <div>
                <h1 class="text-2xl font-black tracking-tight">Dashboard Decision Maker</h1>
                <p class="text-sm adaptive-text-sub">Ringkasan sesi keputusan yang ditugaskan kepada Anda.</p>
            </div>
        </div>

        {{-- SUMMARY CARDS (SAMA DENGAN ADMIN) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $cards = [
                    [
                        'label' => 'Total Sesi Ditugaskan',
                        'value' => $assignedCount,
                        'icon' =>
                            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        'color' => 'bg-blue-500',
                    ],
                    [
                        'label' => 'Sesi Aktif',
                        'value' => $activeCount,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'color' => 'bg-emerald-500',
                    ],
                    [
                        'label' => 'Tugas Pending',
                        'value' => $pendingTaskCount,
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => $pendingTaskCount > 0 ? 'bg-amber-500' : 'bg-emerald-500',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="relative overflow-hidden adaptive-card p-6 rounded-2xl group hover:shadow-xl transition-all duration-300">
                    <div class="relative z-10 flex flex-col adaptive-text-main">
                        <span
                            class="text-[11px] font-black uppercase tracking-widest opacity-50 mb-1">{{ $card['label'] }}</span>
                        <span class="text-3xl font-black">{{ $card['value'] }}</span>
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

        {{-- DAFTAR TUGAS HEADER --}}
        <div class="adaptive-text-main">
            <h2 class="text-base font-bold">Daftar Tugas</h2>
            <p class="text-[11px] uppercase tracking-tighter adaptive-text-sub">Sesi keputusan yang perlu Anda nilai</p>
        </div>

        {{-- SESSION CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($assignedSessions as $session)
                @php
                    $weight = $session->criteriaWeights->where('dm_id', auth()->id())->first();
                @endphp

                <div
                    class="adaptive-card rounded-2xl p-6 flex flex-col justify-between group hover:shadow-lg transition-all border border-white/5">
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <h3 class="font-black text-base adaptive-text-main group-hover:text-primary transition-colors">
                                {{ $session->name }} - {{ $session->year }}
                            </h3>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase tracking-widest adaptive-text-sub">Status
                                    Sesi</span>
                                <span
                                    class="mt-1 inline-block px-2 py-1 rounded text-[10px] font-black uppercase tracking-tighter
                                @if ($session->status === 'active') bg-emerald-500/20 text-emerald-500
                                @elseif($session->status === 'draft') bg-gray-500/20 text-gray-400
                                @else bg-rose-500/20 text-rose-500 @endif">
                                    {{ $session->status }}
                                </span>
                            </div>
                            @if ($weight)
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest adaptive-text-sub">Konsistensi
                                        (CR)
                                    </span>
                                    <span
                                        class="mt-1 text-xs font-bold adaptive-text-main">{{ number_format($weight->cr, 4) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-2">
                            @if ($weight)
                                <p class="text-[11px] text-emerald-500 font-bold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                    </svg>
                                    Penilaian Selesai
                                </p>
                            @else
                                <p class="text-[11px] text-amber-500 font-bold flex items-center gap-1 animate-pulse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3"
                                            stroke-linecap="round" />
                                    </svg>
                                    Menunggu Penilaian
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        @if ($session->status === 'active')
                            <a href="{{ route('decision-sessions.show', $session->id) }}?tab={{ $weight ? 'weights' : 'pairwise' }}"
                                class="inline-flex items-center justify-center w-full px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg
                                {{ $weight
                                    ? 'border-2 border-amber-500 text-amber-500 bg-amber-500/10 hover:bg-amber-500 hover:text-white shadow-amber-500/20'
                                    : 'bg-emerald-500 text-white hover:bg-emerald-600 shadow-emerald-500/20' }}">
                                Kelola Penilaian
                            </a>
                        @else
                            <button disabled
                                class="w-full px-4 py-3 text-[10px] font-black uppercase tracking-widest rounded-xl bg-gray-500/10 adaptive-text-sub cursor-not-allowed border border-white/5 opacity-50">
                                Sesi Tidak Aktif
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 adaptive-card rounded-2xl opacity-50">
                    <p class="text-sm font-bold uppercase tracking-widest adaptive-text-sub">Belum ada sesi ditugaskan</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
