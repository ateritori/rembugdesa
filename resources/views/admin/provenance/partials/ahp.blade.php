@if (empty($ahp['individual']) && empty($ahp['group']))
    <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-2xl">
        <p class="text-slate-400 font-medium">⚠️ Tidak ada data AHP tersedia.</p>
    </div>
@else
    <div class="space-y-8 text-slate-800">

        {{-- INDIVIDUAL --}}
        @foreach ($ahp['individual'] ?? [] as $dmId => $prov)
            <div class="border rounded-xl p-4 bg-white shadow-sm">

                <div class="flex justify-between items-center mb-3 border-b pb-2">
                    <h2 class="text-sm font-black">
                        DM {{ $dmId }}
                    </h2>

                    <div class="text-xs font-bold">
                        CR: {{ number_format($prov['cr'] ?? 0, 4) }}
                        <span class="{{ ($prov['cr'] ?? 1) <= 0.1 ? 'text-green-600' : 'text-red-600' }}">
                            {{ ($prov['cr'] ?? 1) <= 0.1 ? 'Konsisten' : 'Tidak Konsisten' }}
                        </span>
                    </div>
                </div>

                {{-- MATRIX --}}
                <div class="mb-4">
                    <div class="text-xs font-bold mb-2">Matriks Pairwise</div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="p-2">-</th>
                                    @foreach ($prov['criteria_ids'] as $c)
                                        <th class="p-2">C{{ $c }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prov['matrix'] as $i => $row)
                                    <tr>
                                        <td class="p-2 font-bold bg-slate-50">C{{ $prov['criteria_ids'][$i] }}</td>
                                        @foreach ($row as $val)
                                            <td class="p-2 text-center">{{ number_format($val, 2) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- NORMALIZATION --}}
                <div class="mb-4">
                    <div class="text-xs font-bold mb-2">Normalisasi Matriks</div>

                    @php
                        $matrix = $prov['matrix'] ?? [];
                        $n = count($matrix);
                        $colSums = [];

                        for ($j = 0; $j < $n; $j++) {
                            $sum = 0;
                            for ($i = 0; $i < $n; $i++) {
                                $sum += $matrix[$i][$j] ?? 0;
                            }
                            $colSums[$j] = $sum;
                        }
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="p-2">-</th>
                                    @foreach ($prov['criteria_ids'] as $c)
                                        <th class="p-2">C{{ $c }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matrix as $i => $row)
                                    <tr>
                                        <td class="p-2 font-bold bg-slate-50">C{{ $prov['criteria_ids'][$i] }}</td>
                                        @foreach ($row as $j => $val)
                                            <td class="p-2 text-center">
                                                {{ ($colSums[$j] ?? 0) != 0 ? number_format($val / $colSums[$j], 3) : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- WEIGHTS --}}
                <div>
                    <div class="text-xs font-bold mb-2">Bobot</div>

                    <div class="grid grid-cols-5 gap-2 text-xs">
                        @foreach ($prov['weights'] as $i => $w)
                            <div class="p-2 border rounded bg-slate-50 text-center">
                                C{{ $prov['criteria_ids'][$i] }}<br>
                                <span class="font-bold">{{ number_format($w, 4) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        @endforeach


        {{-- GROUP --}}
        @if (!empty($ahp['group']))

            <div class="border rounded-xl p-4 bg-slate-100">

                <h2 class="text-sm font-black mb-3">Rekap Group</h2>

                <div class="text-xs mb-2">
                    CR: {{ number_format($ahp['group']['cr'] ?? 0, 4) }}
                </div>

                {{-- DETAIL PER DM --}}
                <div class="overflow-x-auto mb-4">
                    <table class="w-full text-xs border">
                        <thead>
                            <tr class="bg-slate-200">
                                <th class="p-2">DM</th>
                                @foreach ($ahp['individual'] ?? [] as $first)
                                    @foreach ($first['criteria_ids'] as $c)
                                        <th class="p-2">C{{ $c }}</th>
                                    @endforeach
                                @break
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ahp['individual'] ?? [] as $dmId => $prov)
                            <tr>
                                <td class="p-2 font-bold">D{{ $dmId }}</td>
                                @foreach ($prov['weights'] as $w)
                                    <td class="p-2 text-center">{{ number_format($w, 4) }}</td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- GROUP (GM) --}}
                        <tr class="bg-slate-300 font-bold">
                            <td class="p-2">GM</td>
                            @foreach ($ahp['group']['weights'] ?? [] as $w)
                                <td class="p-2 text-center">{{ number_format($w, 4) }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    @endif

</div>

@endif
