@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-10">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-app">Dashboard Decision Maker</h1>
                <p class="text-sm text-app opacity-60">Ringkasan sesi keputusan yang ditugaskan kepada Anda.</p>
            </div>

            <div class="px-4 py-2 bg-primary/10 border border-primary/20 rounded-2xl">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Role: Decision Maker</span>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $cards = [
                    [
                        'label' => 'Total Sesi',
                        'value' => $assignedCount,
                        'icon' =>
                            'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                        'bg' => 'bg-blue-500',
                    ],
                    [
                        'label' => 'Sesi Aktif',
                        'value' => $activeCount,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'bg' => 'bg-emerald-500',
                    ],
                    [
                        'label' => 'Tugas Pending',
                        'value' => $pendingTaskCount,
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'bg' => $pendingTaskCount > 0 ? 'bg-amber-500' : 'bg-primary',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="adaptive-card p-6 group hover:border-primary/50 transition-all duration-500 relative overflow-hidden">
                    <div class="relative z-10 flex flex-col">
                        <span
                            class="text-[11px] font-black uppercase tracking-widest text-app opacity-40 mb-1">{{ $card['label'] }}</span>
                        <span
                            class="text-4xl font-black text-app group-hover:text-primary transition-colors">{{ $card['value'] }}</span>
                    </div>
                    {{-- Icon Background --}}
                    <div
                        class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-10 group-hover:scale-110 transition-all duration-700">
                        <svg class="w-24 h-24 text-app" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}">
                            </path>
                        </svg>
                    </div>
                    {{-- Accent Line --}}
                    <div class="absolute top-0 left-0 w-1 h-full {{ $card['bg'] }} opacity-50"></div>
                </div>
            @endforeach
        </div>

        {{-- DAFTAR TUGAS HEADER --}}
        <div class="flex items-center gap-4">
            <h2 class="text-base font-black uppercase tracking-widest text-app">Daftar Penilaian</h2>
            <div class="h-px flex-1 bg-app opacity-10"></div>
        </div>

        {{-- SESSION CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($assignedSessions as $session)
                @php
                    $weight = $session->criteriaWeights->where('dm_id', auth()->id())->first();
                @endphp

                <div
                    class="adaptive-card p-6 flex flex-col justify-between group hover:shadow-2xl hover:shadow-primary/5 transition-all">
                    <div class="space-y-5">
                        <div class="flex justify-between items-start">
                            <div class="min-w-0">
                                <h3 class="font-black text-lg text-app truncate group-hover:text-primary transition-colors">
                                    {{ $session->name }}
                                </h3>
                                <p class="text-xs font-bold text-app opacity-40 italic">Periode {{ $session->year }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 py-4 border-y border-app/50">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-40">Status Sesi</span>
                                <div class="mt-1">
                                    @if ($session->status === 'active')
                                        <span
                                            class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase">Active</span>
                                    @elseif($session->status === 'draft')
                                        <span
                                            class="px-2 py-0.5 rounded-md bg-gray-500/10 text-gray-500 text-[10px] font-black uppercase">Draft</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-500 text-[10px] font-black uppercase">Closed</span>
                                    @endif
                                </div>
                            </div>
                            @if ($weight)
                                <div class="flex flex-col border-l border-app/50 pl-4">
                                    <span class="text-[9px] font-black uppercase tracking-widest opacity-40">Konsistensi
                                        (CR)
                                    </span>
                                    <span
                                        class="mt-1 text-xs font-black text-primary">{{ number_format($weight->cr, 4) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($weight)
                                <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                </div>
                                <p class="text-[11px] text-emerald-500 font-black uppercase tracking-tighter">Penilaian
                                    Selesai</p>
                            @else
                                <div
                                    class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shadow-[0_0_8px_rgba(245,158,11,0.5)]">
                                </div>
                                <p class="text-[11px] text-amber-500 font-black uppercase tracking-tighter">Menunggu Aksi
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        @if ($session->status === 'active')
                            <a href="{{ route('dms.index', $session->id) }}"
                                class="inline-flex items-center justify-center w-full px-4 py-3.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all
                                {{ $weight
                                    ? 'border-2 border-primary/20 text-primary hover:bg-primary hover:text-white'
                                    : 'bg-primary text-white shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95' }}">
                                {{ $weight ? 'Lihat / Perbarui Bobot' : 'Mulai Perbandingan' }}
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @else
                            <div
                                class="w-full px-4 py-3.5 text-[10px] font-black uppercase tracking-widest rounded-xl bg-app/40 text-center opacity-50 border border-app">
                                Akses Terkunci
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-24 adaptive-card rounded-3xl border-dashed">
                    <div class="inline-flex p-4 rounded-full bg-app/50 mb-4 text-app opacity-20">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <p class="text-sm font-black uppercase tracking-[0.2em] text-app opacity-30">Belum ada sesi ditugaskan
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
