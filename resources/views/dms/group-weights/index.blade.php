@extends('layouts.dashboard')

@section('title', 'Bobot Kelompok')

@section('content')
    @include('dms.partials.nav')

    <div class="space-y-6 mt-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
        {{-- Header Status: Group Aggregation --}}
        <div
            class="relative flex flex-col md:flex-row md:items-center justify-between overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm gap-4">
            <div class="absolute left-0 top-0 h-full w-2 bg-indigo-600"></div>
            <div class="pl-2">
                <h2 class="text-slate-800 text-sm font-black uppercase tracking-widest">Bobot Kelompok</h2>
                <div class="flex items-center gap-2 mt-1 text-indigo-600">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    <p class="text-[10px] font-black uppercase tracking-tight">Konsensus Agregasi Berjalan</p>
                </div>
            </div>

            {{-- Info Metode --}}
            <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100">
                <div class="p-2 bg-white rounded-lg shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="text-left">
                    <span
                        class="block text-[8px] font-black uppercase tracking-widest text-slate-400 leading-none">Agregasi</span>
                    <span class="text-[10px] font-bold text-slate-600 uppercase">Arithmetic Mean</span>
                </div>
            </div>
        </div>

        {{-- Main Grid Content --}}
        @if ($groupResult && !empty($groupResult->weights))
            @php
                $sortedWeights = collect($groupResult->weights)->sortDesc();
                $maxWeight = $sortedWeights->first() ?: 1;
                $rank = 1;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($sortedWeights as $criteriaId => $weight)
                    @php
                        $criteria = $decisionSession->criteria->firstWhere('id', $criteriaId);
                        $percentage = $weight * 100;
                        $visualWidth = ($weight / $maxWeight) * 100;

                        $rankStyle = match ($rank) {
                            1 => 'bg-amber-500 text-white shadow-amber-200',
                            2 => 'bg-slate-400 text-white shadow-slate-100',
                            3 => 'bg-orange-400 text-white shadow-orange-100',
                            default => 'bg-white text-slate-400 border border-slate-200',
                        };
                    @endphp

                    <div
                        class="group relative rounded-[1.5rem] border border-slate-200 bg-white p-5 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl font-black text-xs shadow-md transition-transform group-hover:scale-110 {{ $rankStyle }}">
                                    {{ $rank++ }}
                                </span>
                                <div class="min-w-0">
                                    <h3
                                        class="truncate text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                                        Kriteria Kelompok</h3>
                                    <p
                                        class="truncate text-sm font-bold text-slate-800 uppercase leading-none tracking-tight">
                                        {{ $criteria?->name ?? 'Kriteria #' . $criteriaId }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-indigo-600 font-mono text-xl font-black">
                                    {{ number_format($weight, 4) }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress Bar Visual --}}
                        <div class="relative h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="absolute left-0 top-0 h-full rounded-full bg-gradient-to-r from-indigo-600 to-indigo-400 transition-all duration-[1.5s] ease-out shadow-[0_0_8px_rgba(79,70,229,0.3)]"
                                style="width: {{ $visualWidth }}%">
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Distribusi
                                Prioritas</span>
                            <span class="text-[10px] font-black text-slate-500 font-mono">
                                {{ number_format($percentage, 1) }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Info Read-Only --}}
            <div class="flex items-center gap-4 rounded-[1.5rem] border border-amber-100 bg-amber-50/40 p-5">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 shadow-sm border border-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-[11px] font-black uppercase tracking-widest text-amber-900 leading-none">Akses Terbatas
                        (Read-Only)</h4>
                    <p
                        class="mt-1 text-[10px] font-bold text-amber-700/70 leading-relaxed uppercase tracking-tighter italic">
                        Perubahan hanya dapat dilakukan melalui konsensus atau kebijakan administrator sistem.
                    </p>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="rounded-[2.5rem] border-2 border-dashed border-slate-200 p-16 text-center bg-white shadow-sm">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-slate-50 mb-6 text-slate-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.944a1 1 0 10-1.414-1.414l.707-.707a1 1 0 101.414 1.414l-.707.707zm8.232-1.414a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM8.867 14.035a1 1 0 01.595-.939l3-1.5a1 1 0 011.214 1.428l-3.5 3.5a1 1 0 01-1.414 0l-1.5-1.5a1 1 0 011.414-1.414l.791.791z" />
                    </svg>
                </div>
                <h3 class="text-slate-900 font-black text-xl uppercase tracking-tight">Menunggu Agregasi</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm leading-relaxed italic">
                    Bobot kelompok akan muncul secara otomatis setelah sistem selesai menghitung preferensi dari seluruh
                    responden.
                </p>
            </div>
        @endif
    </div>
@endsection
