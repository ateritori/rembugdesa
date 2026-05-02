@if (empty($sawBorda) || empty($sawBorda['ranking']))
    <div class="p-4 border border-black bg-yellow-50 text-sm font-bold">
        ⚠️ Tidak ada data Borda SAW (belum dihitung / kosong)
    </div>
@else
    <div class="space-y-8 antialiased text-slate-800">

        {{-- 🔥 FINAL RANKING --}}
        <div class="bg-white border border-slate-300 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)] p-4 space-y-4">
            <div class="flex items-baseline gap-4 border-b border-slate-200 pb-2">
                <h3 class="text-lg font-black text-slate-900">
                    Final Ranking SAW (Nested Borda)
                </h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Borda Analysis
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[12px] border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase tracking-wider text-[11px]">
                            <th class="p-2 border text-left">Rank</th>
                            <th class="p-2 border text-left">Alternatif</th>
                            <th class="p-2 border text-right">Final Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (collect($sawBorda['ranking'])->sortBy('rank') as $altId => $data)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-3 font-black text-slate-900 border-r border-slate-200 bg-slate-50/50">
                                    {{ $data['rank'] }}
                                </td>
                                <td class="p-3 font-black text-slate-900 border-r border-slate-200 bg-slate-50/50">
                                    A{{ $altId }}
                                </td>
                                <td class="p-3 text-right font-mono border-r border-slate-100">
                                    {{ rtrim(rtrim(number_format($data['score'], 2), '0'), '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🔥 DOMAIN LEVEL --}}
        <div class="bg-white border border-slate-300 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)] p-4 space-y-4">
            <div class="flex items-baseline gap-4 border-b border-slate-200 pb-2">
                <h3 class="text-lg font-black text-slate-900">
                    Borda SAW per Domain
                </h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Borda Analysis
                </span>
            </div>

            @foreach ($sawBorda['domain_borda'] as $domainId => $alts)
                <div class="mb-6">
                    <h4 class="text-sm font-black text-slate-900 mb-2">
                        Domain {{ $domainId }}
                    </h4>

                    <div class="overflow-x-auto">
                        <table class="w-full text-[12px] border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white uppercase tracking-wider text-[11px]">
                                    <th class="p-2 border text-left">Alternatif</th>
                                    <th class="p-2 border text-right">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (collect($alts)->sortKeys() as $altId => $score)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td
                                            class="p-3 font-black text-slate-900 border-r border-slate-200 bg-slate-50/50">
                                            A{{ $altId }}
                                        </td>
                                        <td class="p-3 text-right font-mono border-r border-slate-100">
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
        <div class="bg-white border border-slate-300 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)] p-4 space-y-4">
            <div class="flex items-baseline gap-4 border-b border-slate-200 pb-2">
                <h3 class="text-lg font-black text-slate-900">
                    Skor per Domain SAW (Input Borda)
                </h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Borda Analysis
                </span>
            </div>

            @foreach ($sawBorda['domain_dm'] as $domainId => $dms)
                <div class="mb-6">
                    <h4 class="text-sm font-black text-slate-900 mb-2">
                        Domain {{ $domainId }}
                    </h4>
                    <div class="text-[11px] text-slate-500 mb-2">
                        DM:
                        {{ collect($dms)->keys()->map(function ($u) {return $u ? 'DM ' . $u : 'SYSTEM';})->implode(', ') }}
                    </div>

                    @foreach ($dms as $userId => $alts)
                        <div class="mb-4">
                            <div class="text-[11px] font-black mb-1 text-slate-700">
                                {{ $userId ? 'DM ' . $userId : 'SYSTEM' }}
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-[12px] border-collapse">
                                    <thead>
                                        <tr class="bg-slate-900 text-white uppercase tracking-wider text-[11px]">
                                            <th class="p-2 border text-left">Alternatif</th>
                                            <th class="p-2 border text-right">SAW Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (collect($alts)->sortKeys() as $altId => $score)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td
                                                    class="p-3 font-black text-slate-900 border-r border-slate-200 bg-slate-50/50">
                                                    A{{ $altId }}
                                                </td>
                                                <td class="p-3 text-right font-mono border-r border-slate-100">
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
