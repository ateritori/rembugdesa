@if (empty($sawBorda) || empty($sawBorda['ranking']))
    <div class="p-4 border border-black bg-yellow-50 text-sm font-bold">
        ⚠️ Tidak ada data Borda SAW (belum dihitung / kosong)
    </div>
@else
    <div class="space-y-8">

        {{-- 🔥 FINAL RANKING --}}
        <div class="border rounded-xl p-4 bg-white dark:bg-slate-900/50">
            <h3 class="text-xs font-black mb-3 uppercase tracking-widest">
                Final Ranking SAW (Nested Borda)
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-xs border">
                    <thead>
                        <tr class="bg-slate-100">
                            <th class="p-2 border text-left">Rank</th>
                            <th class="p-2 border text-left">Alternatif</th>
                            <th class="p-2 border text-right">Final Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (collect($sawBorda['ranking'])->sortBy('rank') as $altId => $data)
                            <tr class="border-t">
                                <td class="p-2 border font-bold">
                                    {{ $data['rank'] }}
                                </td>
                                <td class="p-2 border font-bold">
                                    A{{ $altId }}
                                </td>
                                <td class="p-2 border text-right font-mono">
                                    {{ rtrim(rtrim(number_format($data['score'], 2), '0'), '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🔥 DOMAIN LEVEL --}}
        <div class="border rounded-xl p-4 bg-white dark:bg-slate-900/50">
            <h3 class="text-xs font-black mb-3 uppercase tracking-widest">
                Borda SAW per Domain
            </h3>

            @foreach ($sawBorda['domain_borda'] as $domainId => $alts)
                <div class="mb-6">
                    <h4 class="text-xs font-bold mb-2">
                        Domain {{ $domainId }}
                    </h4>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="p-2 border text-left">Alternatif</th>
                                    <th class="p-2 border text-right">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (collect($alts)->sortKeys() as $altId => $score)
                                    <tr class="border-t">
                                        <td class="p-2 border font-bold">
                                            A{{ $altId }}
                                        </td>
                                        <td class="p-2 border text-right font-mono">
                                            {{ rtrim(rtrim(number_format($score, 2), '0'), '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🔥 DM LEVEL (SMART RESULT BASE) --}}
        <div class="border rounded-xl p-4 bg-white dark:bg-slate-900/50">
            <h3 class="text-xs font-black mb-3 uppercase tracking-widest">
                Skor per Domain SAW (Input Borda)
            </h3>

            @foreach ($sawBorda['domain_dm'] as $domainId => $dms)
                <div class="mb-6">
                    <h4 class="text-xs font-bold mb-1">
                        Domain {{ $domainId }}
                    </h4>
                    <div class="text-[11px] text-slate-500 mb-2">
                        DM:
                        {{ collect($dms)->keys()->map(function ($u) {return $u ? 'DM ' . $u : 'SYSTEM';})->implode(', ') }}
                    </div>

                    @foreach ($dms as $userId => $alts)
                        <div class="mb-4">
                            <div class="text-[11px] font-semibold mb-1 text-slate-600">
                                {{ $userId ? 'DM ' . $userId : 'SYSTEM' }}
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-xs border">
                                    <thead>
                                        <tr class="bg-slate-100">
                                            <th class="p-2 border text-left">Alternatif</th>
                                            <th class="p-2 border text-right">SAW Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (collect($alts)->sortKeys() as $altId => $score)
                                            <tr class="border-t">
                                                <td class="p-2 border font-bold">
                                                    A{{ $altId }}
                                                </td>
                                                <td class="p-2 border text-right font-mono">
                                                    {{ number_format($score, 4) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endforeach
        </div>

    </div>

@endif
