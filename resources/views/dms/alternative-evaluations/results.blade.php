@php
    $smartScores = collect($smartScores ?? []);
    $totalRawWeight = array_sum($groupResult->weights ?? [1]);
@endphp

<div class="space-y-6 md:space-y-8">
    {{-- Header Section --}}
    <div class="px-2 sm:px-4 text-center md:text-left">
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
            Rincian Perhitungan SMART
        </h2>
        <div
            class="mt-2 flex items-center justify-center md:justify-start gap-2 text-slate-500 text-[10px] md:text-xs font-bold uppercase tracking-widest">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Matriks: (Utility &times; Bobot)
        </div>
    </div>

    {{-- Navigasi Edit --}}
    @if ($decisionSession->status === 'scoring')
        <div class="px-2 sm:px-4">
            <a href="{{ route('dms.index', ['decisionSession' => $decisionSession->id, 'tab' => 'evaluasi-alternatif', 'edit' => 1]) }}"
                class="group flex w-full items-center justify-center gap-4 rounded-3xl border-2 border-dashed border-slate-200 bg-white p-4 transition-all hover:border-primary hover:bg-slate-50">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white shadow-sm group-hover:bg-primary group-hover:text-white transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div class="text-left">
                    <span
                        class="mb-0.5 block text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 group-hover:text-primary">Perlu
                        perbaikan?</span>
                    <span class="text-[10px] font-black uppercase text-slate-700">Revisi Nilai Alternatif</span>
                </div>
            </a>
        </div>
    @endif

    @if ($smartScores->isNotEmpty())
        <div class="relative rounded-2xl md:rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-slate-50/80">
                            {{-- FIXED 1: Rank (Lebar Statis) --}}
                            <th
                                class="sticky left-0 z-30 bg-slate-50 px-4 py-5 text-center font-bold uppercase text-slate-400 text-[10px] border-b border-slate-100 w-[60px]">
                                #
                            </th>

                            {{-- FIXED 2: Nama Alternatif (Lebar Dominan) --}}
                            <th
                                class="sticky left-[60px] z-30 bg-slate-50 px-4 py-5 text-left font-bold uppercase text-slate-400 text-[10px] border-b border-slate-100 min-w-[180px] md:min-w-[240px]">
                                Alternatif
                            </th>

                            {{-- FIXED 3: TOTAL (Lebar Sedang & Mencolok) --}}
                            <th
                                class="sticky left-[240px] md:left-[300px] z-30 bg-indigo-50 px-6 py-5 text-right font-bold uppercase text-indigo-700 text-[10px] border-b border-indigo-100 border-r border-slate-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] w-[100px] md:w-[120px]">
                                Skor Akhir
                            </th>

                            {{-- SCROLLABLE: Detail Kriteria (Lebar Terkontrol) --}}
                            @foreach ($criteria as $c)
                                @php
                                    $rawW = $groupResult->weights[$c->id] ?? 0;
                                    $normalizedWj = $totalRawWeight > 0 ? $rawW / $totalRawWeight : 0;
                                @endphp
                                <th
                                    class="px-6 py-5 text-center font-bold uppercase text-slate-400 text-[10px] border-b border-slate-100 border-l border-slate-50 min-w-[100px] max-w-[140px]">
                                    <span class="block truncate title="{{ $c->name }}">{{ $c->name }}</span>
                                    <span
                                        class="inline-block text-[9px] text-emerald-700 font-black bg-emerald-100/80 px-1.5 py-0.5 rounded-full mt-1">
                                        W: {{ number_format($normalizedWj, 4) }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $rankCounter = 1; @endphp
                        @foreach ($smartScores as $altId => $data)
                            @php
                                $alt = $alternatives->firstWhere('id', $altId);
                                $rankStyle = match ($rankCounter) {
                                    1 => 'bg-amber-500 text-white',
                                    2 => 'bg-slate-400 text-white',
                                    3 => 'bg-orange-400 text-white',
                                    default => 'bg-white text-slate-500 ring-1 ring-slate-200',
                                };
                            @endphp

                            @if ($alt)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    {{-- Rank --}}
                                    <td
                                        class="sticky left-0 z-10 bg-white group-hover:bg-slate-50 px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg font-black text-[10px] {{ $rankStyle }}">
                                            {{ $rankCounter++ }}
                                        </span>
                                    </td>

                                    {{-- Alternatif --}}
                                    <td class="sticky left-[60px] z-10 bg-white group-hover:bg-slate-50 px-4 py-4">
                                        <div
                                            class="font-bold text-slate-800 text-[11px] md:text-sm whitespace-normal leading-tight">
                                            {{ $alt->name }}
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td
                                        class="sticky left-[240px] md:left-[300px] z-10 bg-indigo-50 group-hover:bg-indigo-100 transition-colors px-6 py-4 text-right border-r border-slate-200 font-mono font-black text-indigo-700 text-xs md:text-sm shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                        {{ number_format($data['score'], 4) }}
                                    </td>

                                    {{-- Kriteria --}}
                                    @foreach ($criteria as $c)
                                        @php
                                            $eval = $evaluations[$alt->id][$c->id] ?? null;
                                            $utility = $eval ? (float) $eval->utility_value : 0.0;
                                            $rawW = $groupResult->weights[$c->id] ?? 0;
                                            $normWj = $totalRawWeight > 0 ? $rawW / $totalRawWeight : 0;
                                            $weightedResult = $utility * $normWj;
                                        @endphp
                                        <td
                                            class="px-6 py-4 text-center border-l border-slate-50 min-w-[100px] max-w-[140px]">
                                            <div class="font-bold text-slate-900 text-[11px]">
                                                {{ number_format($weightedResult, 4) }}</div>
                                            <div
                                                class="text-[9px] font-medium text-slate-400 uppercase tracking-tighter">
                                                U: {{ number_format($utility, 2) }}</div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
