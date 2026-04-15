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
            @foreach ($dmProgress as $row)
                @php
                    $dm = $row['dm'] ?? null;
                    $name = $row['name'] ?? ($dm->name ?? '');
                    $pairwiseDone = $row['pairwise'] ?? false;
                    $altDone = $row['alternative'] ?? false;
                    $expected = $row['expected'] ?? 0;
                    $actual = $row['actual'] ?? 0;
                @endphp

                <div x-show="(dmMode === 'criteria' && {{ $row['has_pairwise'] ? 'true' : 'false' }})
                         || (dmMode === 'alternative' && {{ $row['has_evaluate'] ? 'true' : 'false' }})"
                    class="flex items-center justify-between rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                        {{ $name }}
                    </span>

                    <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                        {{-- Kriteria --}}
                        <span x-show="dmMode === 'criteria'" class="flex items-center gap-2">
                            @if ($pairwiseDone)
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-emerald-600">Selesai</span>
                                <span class="text-slate-500">1 / 1</span>
                            @else
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                <span class="text-red-500">Belum</span>
                                <span class="text-slate-500">0 / 1</span>
                            @endif
                        </span>

                        {{-- Alternatif --}}
                        <span x-show="dmMode === 'alternative'" class="flex items-center gap-2">
                            @if ($expected === 0)
                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                <span class="text-slate-400">Tidak ditugaskan</span>
                            @else
                                @if ($altDone)
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-emerald-600">Selesai</span>
                                @else
                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                    <span class="text-amber-600">Proses</span>
                                @endif
                                <span class="text-slate-500">{{ $actual }} / {{ $expected }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
