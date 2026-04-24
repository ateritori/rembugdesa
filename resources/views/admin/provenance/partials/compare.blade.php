@if (empty($borda['ranking']) || empty($sawBorda['ranking']))
    <div class="p-4 border border-black bg-yellow-50 text-sm font-bold">
        ⚠️ Data perbandingan belum tersedia
    </div>
@else
    <div class="space-y-4">

        @php
            // 🔥 Hitung Spearman Rank Correlation
            $n = count($borda['ranking']);
            $sum_d2 = 0;

            foreach ($borda['ranking'] as $altId => $smart) {
                $rankSmart = $smart['rank'];
                $rankSaw = $sawBorda['ranking'][$altId]['rank'] ?? null;

                if ($rankSaw !== null) {
                    $d = $rankSmart - $rankSaw;
                    $sum_d2 += pow($d, 2);
                }
            }

            $spearman = $n > 1 ? 1 - (6 * $sum_d2) / ($n * (pow($n, 2) - 1)) : null;
        @endphp

        <h3 class="text-sm font-black uppercase">
            Perbandingan Ranking SMART vs SAW
        </h3>

        <div class="text-xs font-semibold text-blue-700">
            Spearman Correlation:
            {{ $spearman !== null ? number_format($spearman, 4) : '-' }}
        </div>

        <table class="w-full text-xs border">
            <thead>
                <tr class="bg-slate-100">
                    <th class="p-2 border">Alternatif</th>
                    <th class="p-2 border">Rank SMART</th>
                    <th class="p-2 border">Rank SAW</th>
                    <th class="p-2 border">Δ</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($borda['ranking'])->sortBy('rank') as $altId => $smart)
                    @php
                        $saw = $sawBorda['ranking'][$altId] ?? null;

                        $delta = ($saw['rank'] ?? 0) - $smart['rank'];
                    @endphp

                    <tr>
                        <td class="p-2 border font-bold">
                            {{ $altId }}
                        </td>

                        <td class="p-2 border text-center">
                            {{ $smart['rank'] }}
                        </td>

                        <td class="p-2 border text-center">
                            {{ $saw['rank'] ?? '-' }}
                        </td>

                        <td
                            class="p-2 border text-center
                            @if ($delta < 0) text-green-600
                            @elseif($delta > 0) text-red-600 @endif">
                            {{ $delta }}
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Insight cepat --}}
        <div class="text-xs text-slate-600 mt-2">
            <p><b>Δ &lt; 0</b> = naik di SAW</p>
            <p><b>Δ &gt; 0</b> = turun di SAW</p>
            <p><b>Δ = 0</b> = tidak berubah</p>
        </div>

    </div>
@endif
