@php
    $criteriaWeights = $criteriaWeights ?? null;
    $criterias = $criterias ?? collect();
@endphp

<div class="space-y-6">
    {{-- Header Status --}}
    <div
        class="relative flex items-center justify-between overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="bg-primary absolute left-0 top-0 h-full w-1.5"></div>
        <div>
            <h2 class="text-slate-900 text-sm font-black uppercase tracking-[0.15em]">
                Prioritas Kriteria Anda
            </h2>
            <p class="mt-0.5 text-[11px] font-bold uppercase tracking-normal text-slate-400">
                Hasil kalkulasi perbandingan berpasangan (AHP)
            </p>
        </div>
        <div
            class="min-w-[100px] rounded-2xl border border-slate-100 bg-slate-50 px-4 py-2 text-right transition-all hover:bg-slate-100">
            <span class="mb-0.5 block text-[8px] font-black uppercase tracking-wider text-slate-400">Consistency
                Ratio</span>
            <span
                class="{{ $criteriaWeights->cr <= 0.1 ? 'text-emerald-500' : 'text-rose-500' }} font-mono text-sm font-black">
                {{ number_format($criteriaWeights->cr, 4) }}
            </span>
        </div>
    </div>

    {{-- Visual List Bobot --}}
    <div class="grid gap-4">
        @php
            $sortedWeights = collect($criteriaWeights->weights)->sortDesc();
            $maxWeight = $sortedWeights->first() ?: 1;
            $rank = 1;
        @endphp

        @foreach ($sortedWeights as $criteriaId => $weight)
            @php
                $criteria = $criterias->firstWhere('id', $criteriaId);
                $percentage = $weight * 100;
                $visualWidth = ($weight / $maxWeight) * 100;

                // Baseline Rank Styles
                $rankColor = match ($rank) {
                    1 => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
                    2 => 'bg-slate-100 text-slate-600 ring-slate-200',
                    3 => 'bg-orange-100 text-orange-700 ring-orange-200',
                    default => 'bg-white text-slate-500 ring-slate-100',
                };
            @endphp

            <div
                class="group relative rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300">
                <div class="relative z-10 mb-4 flex items-center justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        {{-- Rank Badge (Senada dengan Baseline) --}}
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-black text-xs ring-1 shadow-sm {{ $rankColor }}">
                            {{ $rank++ }}
                        </span>
                        <span class="truncate text-sm font-bold uppercase tracking-tight text-slate-800">
                            {{ $criteria->name ?? 'Unknown' }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-primary font-mono text-base font-black">
                            {{ number_format($percentage, 1) }}%
                        </span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="from-primary h-full rounded-full bg-gradient-to-r to-indigo-400 transition-all duration-1000"
                        style="width: {{ $visualWidth }}%">
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between px-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.1em] text-slate-400">Eigenvector
                        Value</span>
                    <span class="text-[10px] font-bold tabular-nums text-slate-500 font-mono">
                        {{ number_format($weight, 4) }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Navigasi Edit --}}
    @if ($decisionSession->status === 'configured')
        <div class="pt-2">
            <a href="{{ route('decision-sessions.pairwise.index', ['decisionSession' => $decisionSession->id, 'tab' => 'penilaian-kriteria', 'edit' => 1]) }}"
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
                        class="mb-0.5 block text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 group-hover:text-primary transition-colors">Ingin
                        merevisi?</span>
                    <span class="text-[11px] font-black uppercase text-slate-700">Buka Kembali Perbandingan
                        Berpasangan</span>
                </div>
            </a>
        </div>
    @endif

    {{-- Waiting State Info --}}
    <div class="flex items-start gap-4 rounded-3xl border border-emerald-100 bg-emerald-50/50 p-5">
        <div
            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white shadow-sm">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <h4 class="text-xs font-black uppercase tracking-tight text-emerald-800">Penilaian Terkunci & Aman</h4>
            <p class="mt-1 text-[11px] font-medium leading-relaxed text-emerald-600/80">
                Data Anda telah disimpan secara permanen. Anda dapat memantau status sesi pada dashboard utama sembari
                menunggu responden lainnya.
            </p>
        </div>
    </div>
</div>
