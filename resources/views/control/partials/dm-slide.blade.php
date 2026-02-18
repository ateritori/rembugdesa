{{-- SLIDE-OVER: DETAIL PROGRES DM --}}
<div x-show="openDmProgress" x-cloak>
    {{-- Overlay --}}
    <div class="fixed inset-0 z-40 bg-black/40" @click="openDmProgress = false"></div>

    {{-- Panel --}}
    <div class="fixed right-0 top-0 z-50 h-full w-80 bg-white dark:bg-slate-900 shadow-xl
               transform transition-transform duration-300"
        x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="translate-x-0"
        x-transition:leave-end="translate-x-full">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-200 p-4 dark:border-slate-700">
            <h3 class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">
                Progres DM –
                <span x-text="dmMode === 'criteria' ? 'Kriteria' : 'Alternatif'"></span>
            </h3>
            <button @click="openDmProgress = false" class="text-slate-400 hover:text-slate-600">
                ✕
            </button>
        </div>

        {{-- Content --}}
        <div class="p-4 space-y-2 overflow-y-auto">
            @foreach ($decisionSession->dms as $dm)
                @php
                    $pairwiseDone = \Illuminate\Support\Facades\DB::table('criteria_weights')
                        ->where('decision_session_id', $decisionSession->id)
                        ->where('dm_id', $dm->id)
                        ->exists();

                    $altDone = \Illuminate\Support\Facades\DB::table('alternative_evaluations')
                        ->where('decision_session_id', $decisionSession->id)
                        ->where('dm_id', $dm->id)
                        ->exists();
                @endphp

                <div
                    class="flex items-center justify-between rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                        {{ $dm->name }}
                    </span>

                    <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                        {{-- Kriteria --}}
                        <span x-show="dmMode === 'criteria'" class="flex items-center gap-1">
                            @if ($pairwiseDone)
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-emerald-600">Selesai</span>
                            @else
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                <span class="text-red-500">Belum</span>
                            @endif
                        </span>

                        {{-- Alternatif --}}
                        <span x-show="dmMode === 'alternative'" class="flex items-center gap-1">
                            @if ($altDone)
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-emerald-600">Selesai</span>
                            @else
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                <span class="text-red-500">Belum</span>
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
