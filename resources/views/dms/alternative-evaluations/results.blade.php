@php
    $smartScores = collect($smartScores ?? []);
    $smartContext = $smartContext ?? null;

    // State berasal dari controller (single source of truth)
    $hasEvaluations = $hasEvaluations ?? false;
    $hasSmartResult = $hasSmartResult ?? false;
@endphp

<div class="space-y-8">
    {{-- Header Section --}}
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between px-2">
        <div class="space-y-1">
            @if (!$hasEvaluations)
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Input Penilaian Alternatif
                </h2>
                <p class="text-slate-500 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    Silakan beri nilai alternatif berdasarkan kriteria yang tersedia.
                </p>
            @elseif ($hasEvaluations && !$hasSmartResult)
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Edit Penilaian Alternatif
                </h2>
                <p class="text-slate-500 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                    Anda masih dapat menyesuaikan nilai sebelum hasil ditampilkan.
                </p>
            @else
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Hasil Penilaian Alternatif
                </h2>
                <p class="text-slate-500 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Berikut adalah hasil perankingan SMART untuk Anda.
                </p>
            @endif
        </div>
    </div>

    @if ($smartScores->isNotEmpty())
        @php
            $rank = 1;
        @endphp

        <div
            class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
            {{-- Card Header --}}
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-[0.15em] text-slate-400">
                    Ranking SMART – {{ $smartContext['dm_name'] ?? 'Decision Maker Aktif' }}
                </h3>
                <span
                    class="text-[10px] font-bold bg-white px-2 py-1 rounded-full border border-slate-200 text-slate-400">
                    {{ $smartScores->count() }} Alternatif
                </span>
            </div>

            {{-- Table Wrapper --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white">
                            <th
                                class="w-20 px-6 py-4 text-left font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                Rank</th>
                            <th
                                class="px-6 py-4 text-left font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                Alternatif</th>
                            <th
                                class="px-6 py-4 text-right font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                Skor SMART</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($smartScores->sortDesc() as $altId => $score)
                            @php
                                $alt = $alternatives->firstWhere('id', $altId);
                                $rankColor = match ($rank) {
                                    1 => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
                                    2 => 'bg-slate-100 text-slate-600 ring-slate-200',
                                    3 => 'bg-orange-100 text-orange-700 ring-orange-200',
                                    default => 'bg-white text-slate-500 ring-slate-100',
                                };
                            @endphp

                            @if ($alt)
                                <tr class="group transition-colors hover:bg-slate-50/80">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-black text-xs ring-1 shadow-sm {{ $rankColor }}">
                                            {{ $rank++ }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            class="font-bold text-slate-800 group-hover:text-primary transition-colors">
                                            {{ $alt->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-mono font-black text-primary text-base">
                                            {{ number_format($score, 4) }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Navigasi Edit (Baseline Match) --}}
        @if ($decisionSession->status === 'scoring')
            <div class="pt-2">
                <a href="{{ route('dms.index', ['decisionSession' => $decisionSession->id, 'tab' => 'evaluasi-alternatif', 'edit' => 1]) }}"
                    class="group flex w-full items-center justify-center gap-4 rounded-3xl border-2 border-dashed border-slate-200 bg-white p-5 transition-all duration-300 hover:border-primary hover:bg-slate-50">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white shadow-sm transition-all duration-300 group-hover:bg-primary group-hover:text-white">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-white transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>

                    <div class="text-left">
                        <span
                            class="mb-0.5 block text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 group-hover:text-primary transition-colors">Sudah
                            benar?</span>
                        <span class="text-[11px] font-black uppercase text-slate-700">Revisi Nilai Alternatif</span>
                    </div>
                </a>
            </div>
        @endif
    @endif
</div>
