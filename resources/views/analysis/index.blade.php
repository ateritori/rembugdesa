{{-- =========================================================
 | TAB ANALISIS: Perbandingan Hasil Keputusan (Refined Version)
 | ========================================================= --}}

@php
    $smartResults = $smartResults ?? collect();
    $sawResults = $sawResults ?? collect();
    $comparisonMatrix = $comparisonMatrix ?? collect();
@endphp

<div class="animate-in fade-in slide-in-from-bottom-4 duration-700 space-y-8 p-1">

    {{-- HEADER & SPEARMAN STATS CARD --}}
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="relative pl-6">
            <div class="absolute left-0 top-0 h-full w-1.5 rounded-full bg-gradient-to-b from-indigo-600 to-violet-400">
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-800 lg:text-4xl">
                Analisis <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">Perbandingan</span>
            </h1>
            <p class="mt-2 text-sm font-medium text-slate-500 max-w-xl">
                Validasi akurasi peringkat menggunakan korelasi <span class="text-slate-800 font-semibold">Spearman
                    Rank</span> antara metode SMART dan SAW.
            </p>
        </div>

        @if (!is_null($rhoPercentage ?? null))
            <div
                class="relative min-w-[280px] overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-200/50">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Koefisien
                            Spearman (ρ)</span>
                        <div class="flex items-baseline gap-1">
                            <span
                                class="text-3xl font-black text-slate-800">{{ number_format($rhoPercentage, 1) }}</span>
                            <span class="text-lg font-bold text-slate-400">%</span>
                        </div>
                    </div>
                    <div class="rounded-full bg-indigo-50 p-2 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>

                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all duration-1000 shadow-[0_0_10px_rgba(79,70,229,0.4)]"
                        style="width: {{ $rhoPercentage ?? 0 }}%"></div>
                </div>

                <div class="mt-3 flex items-center gap-1.5">
                    <span
                        class="h-2 w-2 rounded-full {{ $rhoPercentage > 70 ? 'bg-emerald-500' : 'bg-amber-500' }} animate-pulse"></span>
                    <span
                        class="text-[11px] font-bold uppercase text-slate-600 tracking-wide">{{ $rhoInterpretation ?? '-' }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- GRID: TABEL METODE --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

        {{-- SMART CARD --}}
        <div
            class="group flex flex-col rounded-2xl border border-slate-200 bg-white transition-all hover:shadow-2xl hover:shadow-indigo-100">
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                        <span class="text-xs font-bold">A</span>
                    </div>
                    <span class="text-sm font-bold tracking-tight text-slate-700">Metode SMART (Primary)</span>
                </div>
                <span
                    class="inline-flex items-center rounded-md bg-indigo-100 px-2 py-1 text-[10px] font-bold uppercase text-indigo-700">Stabil</span>
            </div>

            <div class="overflow-x-auto overflow-y-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-white text-[10px] uppercase tracking-[0.1em] text-slate-400">
                            <th class="px-6 py-4 text-left font-bold">Peringkat</th>
                            <th class="px-6 py-4 text-left font-bold">Alternatif</th>
                            <th class="px-6 py-4 text-right font-bold">Skor Borda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($smartResults as $row)
                            <tr class="group/row transition-colors hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    @if ($loop->first)
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-400 font-bold text-white shadow-md">
                                            1</div>
                                    @else
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 font-semibold text-slate-500 group-hover/row:bg-white">
                                            {{ $row['rank'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-bold text-slate-700 group-hover/row:text-indigo-600 transition-colors">
                                        {{ $row['name'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-sm font-semibold text-slate-500">
                                    {{ number_format($row['score'], 0) }}</td>
                            </tr>
                        @empty
                            {{-- State Kosong --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SAW CARD --}}
        <div
            class="group flex flex-col rounded-2xl border border-slate-200 bg-white transition-all hover:shadow-2xl hover:shadow-slate-100">
            <div class="flex items-center gap-3 border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-700 text-white">
                    <span class="text-xs font-bold">B</span>
                </div>
                <span class="text-sm font-bold tracking-tight text-slate-700">Metode SAW (Benchmark)</span>
            </div>
            <div class="overflow-x-auto overflow-y-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-white text-[10px] uppercase tracking-[0.1em] text-slate-400">
                            <th class="px-6 py-4 text-left font-bold">Peringkat</th>
                            <th class="px-6 py-4 text-left font-bold">Alternatif</th>
                            <th class="px-6 py-4 text-right font-bold">Skor Borda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($sawResults as $row)
                            <tr class="group/row transition-colors hover:bg-slate-50/80">
                                <td class="px-6 py-4">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-400 group-hover/row:bg-white group-hover/row:text-slate-600 text-sm font-medium">
                                        {{ $row['rank'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-600">{{ $row['name'] }}</td>
                                <td class="px-6 py-4 text-right font-mono text-sm text-slate-400">
                                    {{ number_format($row['score'], 0) }}</td>
                            </tr>
                        @empty
                            {{-- State Kosong --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MATRIKS SINKRONISASI --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/40">
        <div class="border-b border-slate-100 bg-white px-8 py-5">
            <h3 class="flex items-center gap-2 text-lg font-black text-slate-800 uppercase tracking-tight">
                <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                Matriks Sinkronisasi & Selisih
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                        <th class="px-8 py-4 text-center font-bold">ID</th>
                        <th class="px-8 py-4 text-left font-bold">Alternatif Program</th>
                        <th class="px-8 py-4 text-center font-bold">Rank SMART</th>
                        <th class="px-8 py-4 text-center font-bold">Rank SAW</th>
                        <th class="px-8 py-4 text-center font-bold">Δ Delta</th>
                        <th class="px-8 py-4 text-center font-bold">Indikator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($comparisonMatrix as $row)
                        <tr class="group transition-all hover:bg-slate-50/50">
                            <td class="px-8 py-5 text-center">
                                <span
                                    class="rounded bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                    A{{ $row['alternative_id'] }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-sm font-extrabold text-slate-700 uppercase leading-tight">
                                    {{ $row['name'] }}</div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-sm font-black text-indigo-600">{{ $row['rank_smart'] }}</span>
                            </td>
                            <td class="px-8 py-5 text-center text-sm font-medium text-slate-400">
                                {{ $row['rank_saw'] ?? '-' }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                <div class="flex justify-center">
                                    @if ($row['diff'] === 0)
                                        <div
                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-50 text-xs font-bold text-emerald-600 border border-emerald-100">
                                            0</div>
                                    @else
                                        <div
                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-rose-50 text-xs font-bold text-rose-600 border border-rose-100">
                                            {{ abs($row['diff']) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if (is_null($row['diff']))
                                    <span class="text-slate-300">-</span>
                                @elseif ($row['diff'] === 0)
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100/80 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-700 shadow-sm shadow-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Match
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-amber-100/80 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-amber-700 shadow-sm shadow-amber-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Shifted
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="text-slate-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-slate-400 italic">Belum ada data untuk
                                        dianalisis.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
