{{-- =========================================================
 | TAB ANALISIS: Academic High-Contrast (Thesis Ready)
 | ========================================================= --}}

<div class="p-6 bg-white min-h-screen text-black space-y-8">

    {{-- HEADER: Bersih & Tajam --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 border-b-2 border-black pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-tight text-black">
                Analisis Perbandingan Hasil
            </h1>
            <p class="text-sm font-medium text-black italic">
                Validasi peringkat: SMART (Primary) vs SAW (Benchmark)
            </p>
        </div>

        @if (!is_null($spearman ?? null))
            <div class="border-2 border-black p-3 bg-white flex flex-col items-center min-w-[180px]">
                <span class="text-[10px] font-bold uppercase tracking-widest mb-1">Korelasi Spearman (ρ)</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-black">{{ number_format($spearman * 100, 1) }}%</span>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 bg-black text-white uppercase mt-1">
                    @php
                        $interp =
                            $rhoInterpretation ??
                            ($spearman >= 0.8
                                ? 'Sangat kuat'
                                : ($spearman >= 0.6
                                    ? 'Kuat'
                                    : ($spearman >= 0.4
                                        ? 'Sedang'
                                        : 'Lemah')));
                    @endphp
                    {{ $interp }}
                </span>
            </div>
        @endif
    </div>

    {{-- DUAL TABLE: SMART VS SAW --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- SMART CARD --}}
        <div class="border border-black bg-white">
            <div class="bg-slate-100 p-3 border-b border-black">
                <h2 class="text-black text-center font-bold uppercase text-xs tracking-widest">Metode SMART (Hasil
                    Utama)</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white border-b border-black text-[10px] font-bold uppercase">
                        <th class="px-4 py-2 text-left border-r border-black">Rank</th>
                        <th class="px-4 py-2 text-left border-r border-black">Alternatif</th>
                        <th class="px-4 py-2 text-right">Skor Borda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20">
                    @foreach (collect($smartBorda['ranking'])->sortBy('rank')->values() as $altId => $row)
                        <tr class="text-black">
                            <td
                                class="px-4 py-2 font-bold bg-slate-50 border-r border-black/20 w-16 text-center text-lg">
                                {{ $row['rank'] }}
                            </td>
                            <td class="px-4 py-2 font-bold uppercase border-r border-black/20 text-xs">
                                A{{ $row['alternative_id'] }}
                            </td>
                            <td class="px-4 py-2 text-right font-mono font-bold text-black">
                                {{ number_format($row['score'], 6) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- SAW CARD --}}
        <div class="border border-black bg-white">
            <div class="bg-slate-100 p-3 border-b border-black">
                <h2 class="text-black text-center font-bold uppercase text-xs tracking-widest">Metode SAW (Pembanding)
                </h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white border-b border-black text-[10px] font-bold uppercase">
                        <th class="px-4 py-2 text-left border-r border-black">Rank</th>
                        <th class="px-4 py-2 text-left border-r border-black">Alternatif</th>
                        <th class="px-4 py-2 text-right">Skor Borda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20">
                    @foreach (collect($sawBorda['ranking'])->sortBy('rank')->values() as $altId => $row)
                        <tr class="text-black">
                            <td
                                class="px-4 py-2 font-bold bg-slate-50 border-r border-black/20 w-16 text-center text-lg italic">
                                {{ $row['rank'] }}
                            </td>
                            <td class="px-4 py-2 font-medium uppercase border-r border-black/20 text-xs">
                                A{{ $row['alternative_id'] }}
                            </td>
                            <td class="px-4 py-2 text-right font-mono text-black">
                                {{ number_format($row['score'], 6) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- SYNC MATRIX: THE VALIDATION TABLE --}}
    <div class="border border-black bg-white shadow-sm">
        <div class="bg-black text-white p-3 border-b border-black">
            <h3 class="text-sm font-bold uppercase tracking-tight text-center">Matriks Sinkronisasi & Selisih Peringkat
                (Delta)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-white border-b border-black text-black font-bold uppercase text-[10px]">
                        <th class="px-4 py-3 border-r border-black text-center">ID</th>
                        <th class="px-4 py-3 border-r border-black">Nama Alternatif Program</th>
                        <th class="px-4 py-3 border-r border-black text-center bg-slate-50">Rank SMART</th>
                        <th class="px-4 py-3 border-r border-black text-center bg-slate-50">Rank SAW</th>
                        <th class="px-4 py-3 border-r border-black text-center">Delta (Δ)</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20">
                    @foreach (collect($comparisonMatrix)->sortBy('rank_smart')->values() as $row)
                        <tr class="text-black">
                            <td class="px-4 py-3 text-center border-r border-black/20 font-bold bg-slate-50">
                                A{{ $row['alternative_id'] }}
                            </td>
                            <td class="px-4 py-3 border-r border-black/20 font-bold uppercase text-xs">
                                {{ $row['name'] }}
                            </td>
                            <td
                                class="px-4 py-3 text-center border-r border-black/20 text-xl font-bold text-indigo-700">
                                {{ $row['rank_smart'] }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-black/20 text-xl font-bold">
                                {{ $row['rank_saw'] }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-black/20 bg-slate-50">
                                <span
                                    class="text-xl font-bold {{ $row['diff'] === 0 ? 'text-black' : 'text-rose-700' }}">
                                    {{ abs($row['diff']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($row['diff'] === 0)
                                    <span
                                        class="text-[10px] font-bold border border-black px-2 py-0.5 uppercase bg-white">Match</span>
                                @else
                                    <span
                                        class="text-[10px] font-bold border border-black px-2 py-0.5 uppercase bg-black text-white">Bergeser</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- FOOTER CATATAN --}}
    <div class="text-[10px] text-black italic font-medium">
        * Koefisien Spearman mendekati 100% menunjukkan konsistensi yang sangat tinggi antara kedua metode.
    </div>
</div>

<style>
    /* Global Lock Contrast */
    .text-black {
        color: #000000 !important;
    }

    .border-black {
        border-color: #000000 !important;
    }

    .bg-black {
        background-color: #000000 !important;
    }

    .text-indigo-700 {
        color: #1d4ed8 !important;
    }
</style>
