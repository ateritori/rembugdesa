@if (empty($sawBorda) || empty($sawBorda['ranking']))
    <div
        class="p-4 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-800 text-sm font-medium flex items-center gap-2">
        ⚠️ Tidak ada data Borda SAW (belum dihitung / kosong)
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- 🏆 LEFT COLUMN: FINAL RANKING (Sticky) --}}
        <div class="lg:col-span-4 lg:sticky lg:top-6">
            <div
                class="rounded-3xl overflow-hidden bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800">
                <div class="bg-slate-900 dark:bg-slate-800 px-6 py-5">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">
                        Final SAW Results
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach (collect($sawBorda['ranking'])->sortBy('rank') as $altId => $data)
                                @php
                                    $rowClass =
                                        $data['rank'] == 1
                                            ? 'bg-amber-50/30 dark:bg-amber-900/10'
                                            : 'hover:bg-slate-50 dark:hover:bg-slate-800/20';
                                @endphp
                                <tr class="{{ $rowClass }} transition-colors">
                                    <td class="px-6 py-5 w-12">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-black
                                            {{ $data['rank'] == 1 ? 'bg-amber-400 text-white' : 'bg-slate-900 text-white' }}">
                                            {{ $data['rank'] }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-5">
                                        <div class="font-black text-slate-900 dark:text-white text-base">
                                            A{{ $altId }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[120px]">Alternatif
                                            {{ $altId }}</div>
                                    </td>
                                    <td
                                        class="px-6 py-5 text-right font-mono font-black text-xl text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($data['score'], 1) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--  RIGHT COLUMN: DOMAIN BREAKDOWN --}}
        <div class="lg:col-span-8 space-y-8">
            <section>
                <h3 class="text-[10px] font-black mb-4 uppercase tracking-[0.3em] text-slate-400 ml-1">
                    Domain Aggregate (SAW)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($sawBorda['domain_borda'] as $domainId => $alts)
                        <div
                            class="rounded-2xl bg-white dark:bg-slate-900 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
                            <div
                                class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                                <h4
                                    class="text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-tighter">
                                    Domain {{ $domainId }}
                                </h4>
                            </div>
                            <table class="w-full text-xs">
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                    @foreach (collect($alts)->sortKeys() as $altId => $score)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-4 py-2 font-bold text-slate-700 dark:text-slate-300">
                                                A{{ $altId }}</td>
                                            <td
                                                class="px-4 py-2 text-right font-mono font-black text-slate-900 dark:text-slate-400">
                                                {{ number_format($score, 1) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 🔬 PROVENANCE MATRIX (Condensed View) --}}
            <section class="border-t-2 border-slate-900 dark:border-slate-800 pt-8">
                <h3
                    class="text-[10px] font-black mb-6 uppercase tracking-[0.3em] text-slate-900 dark:text-slate-400 ml-1">
                    Input Comparison Matrix (SAW Scores)
                </h3>

                @foreach ($sawBorda['domain_dm'] as $domainId => $dms)
                    <div class="mb-8 last:mb-0">
                        <h4
                            class="text-[11px] font-black text-indigo-600 dark:text-indigo-400 uppercase mb-3 px-1 flex items-center gap-2">
                            <span class="w-4 h-[2px] bg-indigo-600"></span>
                            Domain {{ $domainId }}
                        </h4>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-[11px] bg-white dark:bg-slate-900">
                                <thead>
                                    <tr class="bg-slate-900 text-white border-b border-slate-900">
                                        <th class="px-4 py-3 text-left font-bold uppercase">Alternatif</th>
                                        @foreach ($dms as $userId => $scores)
                                            <th class="px-4 py-3 text-right font-bold uppercase whitespace-nowrap">
                                                {{ $userId ? 'DM ' . $userId : 'SYSTEM' }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @php
                                        $allAlts = collect($dms)->flatMap(fn($s) => array_keys($s))->unique()->sort();
                                    @endphp
                                    @foreach ($allAlts as $altId)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td
                                                class="px-4 py-2 font-black text-slate-900 dark:text-white bg-slate-50/30 dark:bg-slate-800/20">
                                                A{{ $altId }}</td>
                                            @foreach ($dms as $userId => $scores)
                                                <td
                                                    class="px-4 py-2 text-right font-mono text-slate-600 dark:text-slate-400 border-l border-slate-50 dark:border-slate-800">
                                                    {{ isset($scores[$altId]) ? number_format($scores[$altId], 4) : '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </section>
        </div>
    </div>
@endif
