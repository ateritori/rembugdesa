@if (empty($borda['ranking']) || empty($sawBorda['ranking']))
    <div class="p-4 border border-black bg-yellow-50 text-sm font-bold">
        ⚠️ Data perbandingan belum tersedia
    </div>
@else
    <div class="space-y-8 antialiased text-slate-800">

        @php
            // 🔥 Hitung Spearman Rank Correlation (RANK.AVG - tie safe)

            // helper rank avg
            $rankAvg = function ($ranking) {
                // ambil skor borda
                $scores = collect($ranking)->mapWithKeys(fn($v, $k) => [$k => $v['score']])->toArray();

                arsort($scores);

                $ranks = [];
                $i = 1;

                while (!empty($scores)) {
                    $value = current($scores);

                    $ties = array_keys($scores, $value, true);
                    $count = count($ties);

                    $avgRank = ($i + ($i + $count - 1)) / 2;

                    foreach ($ties as $key) {
                        $ranks[$key] = $avgRank;
                        unset($scores[$key]);
                    }

                    $i += $count;
                }

                return $ranks;
            };

            $rankSmartAvg = $rankAvg($borda['ranking']);
            $rankSawAvg = $rankAvg($sawBorda['ranking']);

            $n = count($rankSmartAvg);
            $sum_d2 = 0;

            foreach ($rankSmartAvg as $altId => $r1) {
                $r2 = $rankSawAvg[$altId] ?? null;

                if ($r2 !== null) {
                    $d = $r1 - $r2;
                    $sum_d2 += pow($d, 2);
                }
            }

            $spearman = $n > 1 ? 1 - (6 * $sum_d2) / ($n * (pow($n, 2) - 1)) : null;
        @endphp

        <div class="flex items-baseline gap-4 mb-2 border-b border-slate-200 pb-2">
            <h3 class="text-lg font-black text-slate-900">
                PERBANDINGAN SMART vs SAW
            </h3>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                Ranking Analysis
            </span>
        </div>

        <div class="text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1 inline-block rounded border border-blue-200">
            Spearman: {{ $spearman !== null ? number_format($spearman, 4) : '-' }}
        </div>

        <div class="bg-white border border-slate-300 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-[12px] border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase tracking-wider text-[11px]">
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

                                $r1 = $rankSmartAvg[$altId] ?? null;
                                $r2 = $rankSawAvg[$altId] ?? null;

                                $delta = $r2 !== null && $r1 !== null ? $r2 - $r1 : null;
                            @endphp

                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-3 font-black text-slate-900 border-r border-slate-200 bg-slate-50/50">
                                    {{ $altId }}
                                </td>

                                <td class="p-3 text-center border-r border-slate-100 font-mono">
                                    {{ $smart['rank'] }}
                                </td>

                                <td class="p-3 text-center border-r border-slate-100 font-mono">
                                    {{ $saw['rank'] ?? '-' }}
                                </td>

                                <td
                                    class="p-3 text-center border-r border-slate-100 font-mono
                            @if (!is_null($delta)) @if ($delta < 0) text-green-600
                                @elseif($delta > 0) text-red-600 @endif
                            @endif">
                                    {{ $delta !== null ? number_format($delta, 2) : '-' }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Insight cepat --}}
        <div class="text-xs text-slate-600 mt-4 bg-slate-50 border border-slate-200 p-3 rounded">
            <p><b>Δ &lt; 0</b> = naik di SAW</p>
            <p><b>Δ &gt; 0</b> = turun di SAW</p>
            <p><b>Δ = 0</b> = tidak berubah</p>
        </div>

    </div>
@endif
