{{-- =========================================================
 | HALAMAN HASIL AKHIR: AHP + SMART + BORDA (FINAL RANKING)
 | TANPA KOMPARASI / TANPA SAW
 | ========================================================= --}}

<div class="p-6 bg-white min-h-screen text-black space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 border-b-2 border-black pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold uppercase tracking-tight text-black">
                Hasil Akhir Peringkat Alternatif
            </h1>
            <p class="text-sm font-medium text-black italic">
                Integrasi Metode AHP + SMART dengan Agregasi Borda
            </p>
        </div>
    </div>

    {{-- INSIGHT BOX: KONTRIBUSI DM --}}
    @if (isset($insight))
        <div class="border-2 border-black p-4 bg-white space-y-3">
            <div class="text-sm font-bold uppercase">🧠 Dampak Penilaian Anda</div>

            {{-- TINGKAT KESELARASAN (SPEARMAN) --}}
            @if (!is_null($insight['spearman'] ?? null))
                <div class="text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>📊 Tingkat keselarasan Anda</span>
                        <span class="font-bold">
                            {{ number_format($insight['spearman'] * 100, 1) }}%
                        </span>
                    </div>

                    <div class="w-full border border-black h-2">
                        <div class="h-2 bg-black" style="width: {{ max(0, min(1, $insight['spearman'])) * 100 }}%">
                        </div>
                    </div>

                    <div class="text-[10px] italic">
                        {{ $insight['label'] }}
                    </div>
                </div>
            @endif

            {{-- PERBEDAAN PREFERENSI DM VS FINAL --}}
            @if (!empty($insight['preference']))
                <div class="text-xs">
                    🔍 Perbedaan preferensi Anda:
                    <ul class="mt-1 ml-4 list-disc">
                        @foreach ($insight['preference'] as $p)
                            <li>
                                A{{ $p['alternative_id'] }}:
                                Anda {{ number_format($p['rank_dm'], 2) }} →
                                Final {{ number_format($p['rank_final'], 2) }}
                                ({{ $p['diff'] > 0 ? '⬇️' : '⬆️' }}
                                {{ number_format(abs($p['diff']), 2) }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Jika tidak ada perubahan --}}
            @if (empty($insight['changes']))
                <div class="text-xs">
                    ✔️ Penilaian Anda sejalan dengan keputusan kelompok.
                </div>

                @if (!empty($insight['note']))
                    <div class="text-[10px] italic mt-1">
                        ℹ️ {{ $insight['note'] }}
                    </div>
                @endif
            @else
                {{-- Daftar perubahan --}}
                <div class="text-xs">
                    ⚠️ Penilaian Anda memengaruhi {{ count($insight['changes']) }} alternatif:
                    <ul class="mt-1 ml-4 list-disc">
                        @foreach ($insight['changes'] as $c)
                            <li>
                                {{ $c['diff'] > 0 ? '⬆️' : '⬇️' }}
                                A{{ $c['alternative_id'] }}
                                {{ number_format(abs($c['diff']), 2) }} peringkat
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Dampak terbesar --}}
                @if (!empty($insight['top']))
                    <div class="text-xs font-medium">
                        🎯 Dampak terbesar Anda terdapat pada alternatif:
                        <span class="font-bold">
                            A{{ $insight['top']['alternative_id'] }}
                        </span>
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- TABLE FINAL RANKING --}}
    <div class="border border-black bg-white">
        <div class="bg-black text-white p-3 border-b border-black">
            <h2 class="text-center font-bold uppercase text-xs tracking-widest">
                Peringkat Final Alternatif
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white border-b border-black text-[10px] font-bold uppercase">
                        <th class="px-4 py-3 text-center border-r border-black">Rank</th>
                        <th class="px-4 py-3 text-left border-r border-black">Alternatif</th>
                        <th class="px-4 py-3 text-left border-r border-black">Nama</th>
                        <th class="px-4 py-3 text-right">Skor Borda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/20">
                    @foreach (collect($smartBorda['ranking'])->sortBy('rank')->values() as $row)
                        <tr class="text-black">
                            <td class="px-4 py-3 text-center font-bold text-lg bg-slate-50 border-r border-black/20">
                                {{ $row['rank'] }}
                            </td>
                            <td class="px-4 py-3 font-bold uppercase border-r border-black/20 text-xs">
                                A{{ $row['alternative_id'] }}
                            </td>
                            <td class="px-4 py-3 border-r border-black/20 text-xs font-medium uppercase">
                                {{ $row['name'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-black">
                                {{ number_format($row['score'], 1) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- TOP 1 HIGHLIGHT --}}
    @php
        $top = collect($smartBorda['ranking'])->sortBy('rank')->first();
    @endphp

    @if ($top)
        <div class="border-2 border-black p-4 bg-slate-50">
            <h3 class="text-xs font-bold uppercase mb-2">Alternatif Terbaik</h3>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-lg font-bold uppercase">
                        A{{ $top['alternative_id'] }} - {{ $top['name'] }}
                    </div>
                    <div class="text-xs italic">Skor tertinggi berdasarkan agregasi Borda</div>
                </div>
                <div class="text-2xl font-bold">
                    {{ number_format($top['score'], 1) }}
                </div>
            </div>
        </div>
    @endif

    {{-- FOOTNOTE --}}
    <div class="text-[10px] text-black italic font-medium">
        * Peringkat dihasilkan dari integrasi bobot AHP, perhitungan utilitas SMART, dan agregasi preferensi menggunakan
        metode Borda.
    </div>
</div>

<style>
    .text-black {
        color: #000000 !important;
    }

    .border-black {
        border-color: #000000 !important;
    }

    .bg-black {
        background-color: #000000 !important;
    }
</style>
