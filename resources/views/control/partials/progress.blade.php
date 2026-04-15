{{-- SECTION 2: PROGRES BAR (Firm & Aligned) --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    @php
        $pairwisePercent = $pairwiseEligible > 0 ? ($dmPairwiseDone / $pairwiseEligible) * 100 : 0;
        $altPercent = $totalExpectedActions > 0 ? ($totalActualActions / $totalExpectedActions) * 100 : 0;

        // Clamp 0–100 to avoid overflow UI
        $pairwisePercent = max(0, min(100, $pairwisePercent));
        $altPercent = max(0, min(100, $altPercent));
    @endphp

    {{-- PROGRES PAIRWISE --}}
    <div
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 transition-all duration-300 hover:border-slate-300">
        <div class="mb-4 flex items-end justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                        Progres Pairwise
                    </p>
                </div>
                <h4 class="text-sm font-bold text-slate-400">Pembobotan Kriteria</h4>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-2xl font-black tracking-tighter text-slate-800 dark:text-white">
                    {{ (int) round($pairwisePercent) }}%
                </span>

                <button type="button" @click="dmMode = 'criteria'; openDmProgress = true"
                    class="text-slate-400 hover:text-slate-600 transition" aria-label="Detail progres DM">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75
                                 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm0 6a.75.75 0 1 0 0-1.5
                                 .75.75 0 0 0 0 1.5Zm.75 7.5a.75.75 0 0 0-1.5 0v-4.5a.75.75 0 0 0 1.5 0v4.5Z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-900">
            <div class="h-full bg-slate-800 transition-all duration-1000 dark:bg-blue-500"
                style="width: {{ $pairwisePercent }}%"></div>
        </div>

        <div class="mt-3 flex justify-between text-[9px] font-bold uppercase tracking-widest text-slate-400">
            <span>Status: {{ $dmPairwiseDone }} / {{ $pairwiseEligible }} Evaluator</span>
        </div>
    </div>

    {{-- PROGRES ALTERNATIF --}}
    <div
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 transition-all duration-300 hover:border-slate-300">
        <div class="mb-4 flex items-end justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                        Penilaian Alternatif
                    </p>
                </div>
                <h4 class="text-sm font-bold text-slate-400">Evaluasi Alternatif</h4>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-2xl font-black tracking-tighter text-slate-800 dark:text-white">
                    {{ (int) round($altPercent) }}%
                </span>

                <button type="button" @click="dmMode = 'alternative'; openDmProgress = true"
                    class="text-slate-400 hover:text-slate-600 transition" aria-label="Detail progres DM">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75
                                 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm0 6a.75.75 0 1 0 0-1.5
                                 .75.75 0 0 0 0 1.5Zm.75 7.5a.75.75 0 0 0-1.5 0v-4.5a.75.75 0 0 0 1.5 0v4.5Z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-900">
            <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: {{ $altPercent }}%"></div>
        </div>

        <div class="mt-3 flex justify-between text-[9px] font-bold uppercase tracking-widest text-slate-400">
            <span>Status: {{ $totalActualActions }} / {{ $totalExpectedActions }} Aksi (berdasarkan assignment)</span>
        </div>

        <div class="mt-2 text-right">
            <button @click="dmMode = 'alternative'; openDmProgress = true"
                class="text-[9px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600">
                Lihat detail DM
            </button>
        </div>
    </div>
</div>
