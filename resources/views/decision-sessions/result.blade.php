    @php
        $borda = $borda ?? collect();
        $smartByDm = $smartByDm ?? collect();
        $saw = $saw ?? null;
    @endphp
    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-xl font-bold text-slate-800">
                Hasil Akhir Keputusan
            </h1>
            <p class="text-sm text-slate-500">
                Keputusan kelompok berdasarkan agregasi Borda.
            </p>
        </div>

        {{-- Guard --}}
        @if ($decisionSession->status !== 'closed')
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-700 text-sm">
                Hasil akhir belum tersedia. Sesi belum ditutup.
            </div>
        @else
            {{-- =========================
             TABEL HASIL BORDA (FINAL)
             ========================= --}}
            <div class="rounded-lg border bg-white">
                <div class="border-b px-4 py-3 font-semibold text-slate-700">
                    Ranking Akhir (Borda)
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-2 text-left">Peringkat</th>
                            <th class="px-4 py-2 text-left">Alternatif</th>
                            <th class="px-4 py-2 text-right">Skor Borda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($borda as $row)
                            <tr class="border-t">
                                <td class="px-4 py-2 font-semibold">
                                    {{ $row->final_rank }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $row->alternative->name ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    {{ $row->borda_score }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- =========================
             OPSIONAL: ANALISIS SMART
             ========================= --}}
            @if (!empty($smartByDm) && $smartByDm->count())
                <div class="rounded-lg border bg-white">
                    <div class="border-b px-4 py-3 font-semibold text-slate-700">
                        Ringkasan Preferensi Decision Maker (SMART)
                    </div>

                    <div class="p-4 space-y-4">
                        @foreach ($smartByDm as $dmId => $rows)
                            <div>
                                <div class="text-sm font-semibold text-slate-600 mb-2">
                                    {{ optional($rows->first()->dm)->name ?? 'DM ' . $dmId }}
                                </div>

                                <table class="w-full text-sm border">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-3 py-1 text-left">Alternatif</th>
                                            <th class="px-3 py-1 text-right">Skor SMART</th>
                                            <th class="px-3 py-1 text-right">Rank DM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $r)
                                            <tr class="border-t">
                                                <td class="px-3 py-1">
                                                    {{ $r->alternative->name ?? '-' }}
                                                </td>
                                                <td class="px-3 py-1 text-right">
                                                    {{ $r->smart_score }}
                                                </td>
                                                <td class="px-3 py-1 text-right">
                                                    {{ $r->rank_dm }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- =========================
             OPSIONAL: BENCHMARK SAW
             ========================= --}}
            @if (!empty($saw))
                <div class="rounded-lg border bg-white">
                    <div class="border-b px-4 py-3 font-semibold text-slate-700">
                        Benchmark Metode SAW
                    </div>

                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Alternatif</th>
                                <th class="px-4 py-2 text-right">Skor SAW</th>
                                <th class="px-4 py-2 text-right">Rank SAW</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($saw as $altId => $row)
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        {{ $row['alternative'] ?? 'Alternatif ' . $altId }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        {{ $row['score'] }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        {{ $row['rank'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        @endif
    </div>
