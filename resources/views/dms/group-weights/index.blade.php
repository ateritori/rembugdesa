@extends('layouts.dashboard')

@section('title', 'Bobot Kelompok')

@section('content')
    @include('dms.partials.nav')

    <div class="space-y-8 mt-6 animate-in fade-in slide-in-from-bottom-4 duration-700">

        {{-- HEADER: Locked Style (Sama dengan Hasil SMART) --}}
        <div class="px-2 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Bobot Kelompok</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Konsensus Agregasi Geometric
                        Mean (GM)</p>
                </div>
            </div>

            {{-- Info Badge --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 flex items-center gap-3 w-fit">
                <div class="p-1 bg-white rounded shadow-sm text-primary">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Status: Agregasi
                    Final</span>
            </div>
        </div>

        {{-- MAIN CONTENT: Masonry Layout (Urut 1,2,3 kebawah baru pindah kanan) --}}
        @if ($groupResult && !empty($groupResult->weights))
            @php
                $sortedWeights = collect($groupResult->weights)->sortDesc();
                $maxWeight = $sortedWeights->first() ?: 1;
                $rank = 1;
            @endphp

            {{-- CSS Columns: Menjamin flow vertikal 1..2..3 baru 4..5..6 --}}
            <div class="columns-1 lg:columns-2 gap-6 space-y-6">
                @foreach ($sortedWeights as $criteriaId => $weight)
                    @php
                        $criteria = $decisionSession->criteria->firstWhere('id', $criteriaId);
                        $percentage = $weight * 100;
                        $visualWidth = ($weight / $maxWeight) * 100;

                        $rankStyle = match ($rank) {
                            1 => 'bg-amber-500 text-white shadow-amber-200',
                            2 => 'bg-slate-400 text-white shadow-slate-100',
                            3 => 'bg-orange-400 text-white shadow-orange-100',
                            default => 'bg-white text-slate-500 border border-slate-200',
                        };
                    @endphp

                    {{-- Break-inside-avoid: Agar card tidak terpotong saat pindah kolom --}}
                    <div
                        class="break-inside-avoid relative rounded-3xl border border-slate-200 bg-white p-5 transition-all duration-300 hover:shadow-lg mb-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl font-black text-xs shadow-sm {{ $rankStyle }}">
                                    {{ $rank++ }}
                                </span>
                                <div class="min-w-0">
                                    <h3
                                        class="truncate text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                                        Prioritas Kriteria</h3>
                                    <p
                                        class="truncate text-sm font-black text-slate-800 uppercase leading-none tracking-tight">
                                        {{ $criteria?->name ?? 'Kriteria #' . $criteriaId }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-primary font-mono text-xl font-black">
                                    {{ number_format($percentage, 1) }}%
                                </span>
                            </div>
                        </div>

                        {{-- Progress Bar (Minimalist) --}}
                        <div class="relative h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="absolute left-0 top-0 h-full rounded-full bg-primary transition-all duration-[1s] ease-out"
                                style="width: {{ $visualWidth }}%">
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                            <span
                                class="text-[9px] font-bold uppercase tracking-widest text-slate-400 italic">Persentase</span>
                            <span class="text-[10px] font-black text-slate-500 font-mono">
                                {{ number_format($weight, 4) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Footer Alert: Read-Only --}}
            <div class="flex items-center gap-4 rounded-2xl border border-amber-100 bg-amber-50/50 p-4">
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 shadow-sm border border-amber-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="text-[10px] font-bold text-amber-800 uppercase tracking-widest leading-tight">
                    Akses Terbatas (Read-Only) — Nilai telah dikunci melalui konsensus sistem.
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div
                class="rounded-[2.5rem] border-2 border-dashed border-slate-200 p-24 text-center bg-white flex flex-col items-center justify-center">
                <div class="bg-slate-50 p-4 rounded-full mb-4">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-slate-400 text-xs font-black uppercase tracking-[0.3em]">Menunggu Agregasi Data</p>
            </div>
        @endif
    </div>
@endsection
