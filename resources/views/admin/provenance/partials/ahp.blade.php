@if (empty($individualProvenance) || count($individualProvenance) === 0)
    <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-2xl">
        <p class="text-slate-400 font-medium">⚠️ Tidak ada data AHP yang tersedia.</p>
    </div>
@else
    <div class="space-y-8 antialiased text-slate-800">

        <h3 class="text-sm font-black uppercase">
            Log Perhitungan AHP
        </h3>

        {{-- ============================= --}}
        {{-- REFERENSI --}}
        {{-- ============================= --}}
        <div class="grid grid-cols-2 gap-6 text-xs">

            <div>
                <div class="font-bold mb-2">Referensi Kriteria (C)</div>
                @php $i = 1; @endphp
                @foreach ($session->criteria as $c)
                    <div>C{{ $i++ }} - {{ $c->name }}</div>
                @endforeach
            </div>

            <div>
                <div class="font-bold mb-2">Referensi Decision Maker (D)</div>
                @php $i = 1; @endphp
                @foreach ($session->decisionMakers ?? [] as $dm)
                    <div>D{{ $i++ }} - {{ $dm->name }}</div>
                @endforeach
            </div>

        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3 bg-slate-100 p-2 rounded-lg">
                <span class="text-[10px] font-black uppercase pl-2">Filter DM:</span>
                <select id="filter-dm"
                    class="text-xs bg-white border border-slate-300 rounded px-3 py-1 font-bold outline-none focus:ring-2 focus:ring-black">
                    <option value="">Tampilkan Semua</option>
                    @foreach ($individualProvenance as $dmId => $_)
                        <option value="{{ $dmId }}">DM {{ $dmId }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- PER DM --}}
        {{-- ============================= --}}
        @foreach ($individualProvenance as $dmId => $prov)
            <div class="calculation-block group transition-all bg-white border border-slate-300 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)] p-4 space-y-4"
                data-dm="{{ $dmId }}">

                <div class="font-bold text-xs">
                    Penilaian DM {{ $dmId }}
                </div>

                {{-- CR --}}
                <div class="text-[11px]">
                    CR: {{ number_format($prov['cr'], 4) }}
                    @if ($prov['is_consistent'])
                        <span class="text-green-600 font-bold">Konsisten</span>
                    @else
                        <span class="text-red-600 font-bold">Tidak Konsisten</span>
                    @endif
                </div>

                {{-- MATRIX --}}
                <div>
                    <div class="text-[11px] font-semibold mb-1">1. Matriks Pairwise</div>
                    <table class="w-full text-[12px] border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white uppercase text-[11px]">
                                <th class="border px-2"></th>
                                @foreach ($prov['criteria_ids'] as $idx => $cid)
                                    <th class="border px-2">C{{ $idx + 1 }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prov['matrix'] as $i => $row)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="border px-2 font-bold">C{{ $i + 1 }}</td>
                                    @foreach ($row as $val)
                                        <td class="border px-2 text-center">
                                            {{ number_format($val, 2) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- NORMALIZATION --}}
                <div>
                    <div class="text-[11px] font-semibold mb-1">2. Normalisasi Matriks</div>

                    @php
                        $matrix = $prov['matrix'];
                        $n = count($matrix);

                        // hitung jumlah kolom
                        $colSums = [];
                        for ($j = 0; $j < $n; $j++) {
                            $sum = 0;
                            for ($i = 0; $i < $n; $i++) {
                                $sum += $matrix[$i][$j];
                            }
                            $colSums[$j] = $sum;
                        }
                    @endphp

                    <table class="w-full text-[12px] border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white uppercase text-[11px]">
                                <th class="border px-2"></th>
                                @foreach ($prov['criteria_ids'] as $idx => $cid)
                                    <th class="border px-2">C{{ $idx + 1 }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($matrix as $i => $row)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="border px-2 font-bold">C{{ $i + 1 }}</td>
                                    @foreach ($row as $j => $val)
                                        <td class="border px-2 text-center">
                                            {{ $colSums[$j] != 0 ? number_format($val / $colSums[$j], 3) : 0 }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- WEIGHTS --}}
                <div>
                    <div class="text-[11px] font-semibold mb-1">3. Bobot (Geometric Mean)</div>
                    <table class="w-full text-[12px] border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white uppercase text-[11px]">
                                @foreach ($prov['criteria_ids'] as $idx => $cid)
                                    <th class="border px-2">C{{ $idx + 1 }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-slate-50 transition-colors">
                                @foreach ($prov['weights'] as $w)
                                    <td class="border px-2 text-center">
                                        {{ number_format($w, 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        @endforeach

        {{-- ============================= --}}
        {{-- GROUP RESULT --}}
        {{-- ============================= --}}
        @if (!empty($groupProvenance))
            <div class="border p-4 bg-white space-y-3">

                <div class="font-bold text-xs">
                    Rekap Akhir (Group)
                </div>

                <table class="w-full text-[12px] border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase text-[11px]">
                            <th class="border px-2">Kriteria</th>
                            @foreach ($groupProvenance['weights'] as $k => $v)
                                <th class="border px-2">C{{ $k }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>

                        {{-- Per DM --}}
                        @foreach ($individualProvenance as $dmId => $prov)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="border px-2 font-bold">D{{ $dmId }}</td>
                                @foreach ($prov['weights'] as $w)
                                    <td class="border px-2 text-center">
                                        {{ number_format($w, 4) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- GM --}}
                        <tr class="bg-slate-100">
                            <td class="border px-2 font-bold">GM</td>
                            @foreach ($groupProvenance['weights'] as $w)
                                <td class="border px-2 text-center font-bold">
                                    {{ number_format($w, 4) }}
                                </td>
                            @endforeach
                        </tr>

                    </tbody>
                </table>

                <div class="text-[10px]">
                    CR: {{ number_format($groupProvenance['cr'] ?? 0, 4) }}
                </div>

            </div>
        @endif

    </div>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-mono {
            font-family: monospace !important;
        }
    </style>

    <script>
        document.getElementById('filter-dm')?.addEventListener('change', function() {
            const val = this.value;
            document.querySelectorAll('[data-dm]').forEach(el => {
                el.style.display = (!val || el.dataset.dm == val) ? 'block' : 'none';
            });
        });
    </script>
@endif
