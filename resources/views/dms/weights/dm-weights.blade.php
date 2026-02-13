@php
    $criteriaWeights = $criteriaWeights ?? null;
    $criterias = $criterias ?? collect();
@endphp

<div class="space-y-4">
    {{-- Header Status: Lebih Rapi --}}
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">
                Prioritas Kriteria
            </h2>
            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-tighter">
                Hasil kalkulasi Eigenvector
            </p>
        </div>
        <div class="px-4 py-2 bg-slate-50 rounded-xl border border-slate-100 text-right min-w-[100px]">
            <span class="text-[8px] font-black uppercase opacity-40 block tracking-wider mb-0.5">CR Ratio</span>
            <span class="text-sm font-black {{ $criteriaWeights->cr <= 0.1 ? 'text-emerald-500' : 'text-rose-500' }}">
                {{ number_format($criteriaWeights->cr, 4) }}
            </span>
        </div>
    </div>

    {{-- Visual List --}}
    <div class="grid gap-3">
        @php
            $sortedWeights = collect($criteriaWeights->weights)->sortDesc();
            $maxWeight = $sortedWeights->first() ?: 1;
        @endphp

        @foreach ($sortedWeights as $criteriaId => $weight)
            @php
                $criteria = $criterias->firstWhere('id', $criteriaId);
                $percentage = $weight * 100;
                $visualWidth = ($weight / $maxWeight) * 100;
            @endphp

            <div
                class="relative group p-4 rounded-2xl border border-slate-200 bg-white hover:border-primary/30 transition-all duration-300 shadow-sm hover:shadow-md">
                <div class="relative z-10 flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Ranking Badge --}}
                        <div
                            class="shrink-0 w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all duration-500">
                            <span class="text-[10px] font-black italic">#{{ $loop->iteration }}</span>
                        </div>
                        <span class="text-xs md:text-sm font-black text-slate-700 uppercase tracking-tight truncate">
                            {{ $criteria->name ?? 'Unknown' }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm md:text-base font-black text-primary">
                            {{ number_format($percentage, 1) }}%
                        </span>
                    </div>
                </div>

                {{-- Progress Track --}}
                <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                    {{-- Progress Fill --}}
                    <div class="h-full bg-gradient-to-r from-primary to-blue-400 rounded-full transition-all duration-1000"
                        style="width: {{ $visualWidth }}%">
                    </div>
                </div>

                {{-- Absolute Value Footer --}}
                <div class="flex justify-between items-center mt-2 px-0.5">
                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Global
                        Priority</span>
                    <span class="text-[9px] font-bold text-slate-400 tracking-tighter tabular-nums">
                        {{ number_format($weight, 4) }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
