@php
    $criteriaWeights = $criteriaWeights ?? null;
    $criterias = $criterias ?? collect();
@endphp

<div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
    {{-- Header Status: Consistency Ratio --}}
    <div
        class="relative flex items-center justify-between overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="absolute left-0 top-0 h-full w-2 bg-indigo-600"></div>
        <div class="pl-2">
            <h2 class="text-slate-800 text-sm font-black uppercase tracking-widest">Prioritas Kriteria Anda</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                <p class="text-[10px] font-bold uppercase text-slate-400">Hasil Kalkulasi AHP Berhasil Disimpan</p>
            </div>
        </div>

        <div
            class="group flex flex-col items-end rounded-2xl border border-slate-100 bg-slate-50/50 px-5 py-2.5 transition-all hover:bg-white hover:shadow-inner">
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Consistency Ratio</span>
            <div class="flex items-center gap-2">
                <span
                    class="{{ $criteriaWeights->cr <= 0.1 ? 'text-emerald-600' : 'text-rose-500' }} font-mono text-lg font-black">
                    {{ number_format($criteriaWeights->cr, 4) }}
                </span>
                @if ($criteriaWeights->cr <= 0.1)
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    {{-- Visual List Bobot: Responsif 2 Kolom di Desktop --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl font-black text-xs shadow-md transition-transform group-hover:scale-110 {{ $rankStyle }}">
                            {{ $rank++ }}
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate text-[11px] font-black uppercase tracking-wider text-slate-400 mb-0.5">
                                Kriteria</h3>
                            <p class="truncate text-sm font-bold text-slate-800 uppercase leading-none italic">
                                {{ $criteria->name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span
                            class="text-indigo-600 font-mono text-xl font-black">{{ number_format($percentage, 1) }}<span
                                class="text-xs ml-0.5">%</span></span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="relative h-2 w-full overflow-hidden rounded-full bg-slate-100">
                    <div class="absolute left-0 top-0 h-full rounded-full bg-gradient-to-r from-indigo-600 to-indigo-400 transition-all duration-[1.5s] ease-out shadow-[0_0_8px_rgba(79,70,229,0.4)]"
                        style="width: {{ $visualWidth }}%">
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-3">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Eigenvector</span>
                    <span
                        class="rounded-md bg-slate-50 px-2 py-0.5 font-mono text-[10px] font-bold text-slate-600 tracking-tighter">
                        {{ number_format($weight, 4) }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Navigasi & Information: Responsif Stack --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2">
        {{-- Navigasi Edit --}}
        @if ($decisionSession->status === 'configured')
            <a href="{{ route('decision-sessions.pairwise.index', ['decisionSession' => $decisionSession->id, 'tab' => 'penilaian-kriteria', 'edit' => 1]) }}"
                class="group flex items-center gap-4 rounded-[1.5rem] border-2 border-dashed border-slate-200 bg-white p-5 transition-all hover:border-indigo-600 hover:bg-indigo-50/30">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-slate-100 bg-slate-50 shadow-sm transition-all group-hover:bg-indigo-600 group-hover:text-white">
                    <svg class="h-5 w-5 text-slate-400 group-hover:text-white transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <span
                        class="block text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-indigo-600">Revisi
                        Data</span>
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-tight">Buka Perbandingan
                        Berpasangan</span>
                </div>
            </a>
        @endif

        {{-- Info Box --}}
        <div class="flex items-center gap-4 rounded-[1.5rem] border border-emerald-100 bg-emerald-50/40 p-5">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-lg shadow-emerald-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <h4 class="text-[11px] font-black uppercase tracking-widest text-emerald-800 leading-none">Status
                    Terkunci</h4>
                <p class="mt-1 text-[10px] font-bold text-emerald-600/80 leading-relaxed uppercase tracking-tighter">
                    Data tersimpan permanen di cloud database.
                </p>
            </div>
        </div>
    </div>
</div>
