@php
    $smartScores = collect($smartScores ?? []);

    if ($smartScores->isEmpty()) {
        echo '<div class="text-center py-16 text-slate-400 text-sm font-bold uppercase tracking-widest">Belum ada hasil penilaian</div>';
        return;
    }

    $smartScores = $smartScores->sortByDesc(fn($item) => $item['score'] ?? 0);
    $total = $smartScores->count();

    // Bagi data jadi 2 bagian (kiri & kanan) secara dinamis
    $half = ceil($total / 2);
    $chunks = $smartScores->chunk($half);
@endphp

<div class="space-y-8">
    {{-- Header & Edit Button --}}
    <div class="px-2 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Peringkat Hasil Evaluasi Individu (SMART)</h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Rekapitulasi Nilai dan
                    Peringkat Alternatif per Penilai</p>
            </div>
        </div>

        @if ($decisionSession->status === 'scoring')
            <a href="{{ route('dms.index', ['decisionSession' => $decisionSession->id, 'tab' => 'evaluasi-alternatif', 'edit' => 1]) }}"
                class="bg-primary shadow-primary/20 group flex items-center gap-2 rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:scale-105 active:scale-95">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Revisi Nilai</span>
            </a>
        @endif
    </div>

    {{-- Grid Layout: 1 Kolom di Mobile, 2 Kolom di Desktop --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        @foreach ($chunks as $chunk)
            <div class="adaptive-card border border-slate-200 bg-white shadow-sm overflow-hidden rounded-2xl">
                <table class="w-full text-left border-collapse table-fixed">
                    <colgroup>
                        <col style="width: 55px;"> {{-- Rank --}}
                        <col> {{-- Info (Nama + Kode) --}}
                        <col style="width: 100px;"> {{-- Skor --}}
                    </colgroup>
                    <thead class="bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-4 text-center text-[10px] font-black uppercase text-slate-400">#</th>
                            <th class="px-4 py-4 text-left text-[10px] font-black uppercase text-slate-400">Alternatif
                            </th>
                            <th class="px-4 py-4 text-right text-[10px] font-black uppercase text-primary">Skor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($chunk as $altId => $data)
                            @php
                                $rankIndex = $smartScores->keys()->search($altId);
                                $originalRank = $rankIndex !== false ? $rankIndex + 1 : '-';
                                $alt = $alternatives->firstWhere('id', $altId);
                                $isTop3 = is_int($originalRank) && $originalRank <= 3;
                            @endphp

                            @if ($alt)
                                <tr class="group hover:bg-slate-50 transition-all">
                                    {{-- Rank --}}
                                    <td class="px-4 py-4 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg font-black text-xs
                                            {{ $isTop3 ? 'bg-primary text-white shadow-md' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $originalRank }}
                                        </span>
                                    </td>

                                    {{-- Alternatif (Nama + Kode Inline) --}}
                                    <td class="px-4 py-4">
                                        <div class="min-w-0">
                                            <span
                                                class="font-bold text-slate-800 text-sm md:text-base leading-tight break-words">
                                                {{ $alt->name }}
                                                <span class="text-[9px] font-black text-slate-400 uppercase">
                                                    ({{ $alt->code ?? 'ALT-' . $originalRank }})
                                                </span>
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Skor --}}
                                    <td class="px-4 py-4 text-right">
                                        <span
                                            class="font-mono font-black text-sm {{ $isTop3 ? 'text-primary' : 'text-slate-600' }} tabular-nums">
                                            {{ number_format($data['score'] ?? 0, 4) }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    {{-- Legend --}}
    <div
        class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
            <span>{{ $total }} Alternatif Terhitung</span>
        </div>
    </div>
</div>
