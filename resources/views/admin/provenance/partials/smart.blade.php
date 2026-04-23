@if (empty($traces) || count($traces) === 0)
    <div class="p-4 border border-black bg-yellow-50 text-sm font-bold">
        ⚠️ Tidak ada data SMART (trace kosong)
    </div>
@else
    <div class="space-y-8">

        @foreach ($traces as $userId => $alternatives)
            <div class="border rounded-xl p-4 bg-white dark:bg-slate-900/50">

                <h4 class="text-xs font-black mb-3">
                    {{ $userId ? 'DM ' . $userId : 'SYSTEM' }}
                </h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border">
                        <thead>
                            <tr class="bg-slate-100">
                                <th class="p-2 border text-left">Alternatif</th>
                                <th class="p-2 border text-left">Kriteria</th>
                                <th class="p-2 border text-left">Raw</th>
                                <th class="p-2 border text-left">Min</th>
                                <th class="p-2 border text-left">Max</th>
                                <th class="p-2 border text-left">Normalisasi</th>
                                <th class="p-2 border text-left">Utility</th>
                                <th class="p-2 border text-right">SMART</th>
                                <th class="p-2 border text-right">Bobot</th>
                                <th class="p-2 border text-right">Terbobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alternatives as $altId => $data)
                                @php $rowspan = count($data['steps']); @endphp

                                @foreach ($data['steps'] as $i => $step)
                                    <tr>
                                        @if ($i === 0)
                                            <td class="p-2 border font-bold align-top" rowspan="{{ $rowspan }}">
                                                {{ $data['code'] ?? 'A' . $altId }}
                                            </td>
                                        @endif

                                        <td class="p-2 border font-bold">
                                            C{{ $step['criteria_id'] }}
                                        </td>

                                        <td class="p-2 border text-right font-mono">
                                            {{ $step['raw_value'] }}
                                        </td>

                                        <td class="p-2 border text-right font-mono">
                                            {{ $step['min'] }}
                                        </td>

                                        <td class="p-2 border text-right font-mono">
                                            {{ $step['max'] }}
                                        </td>

                                        <td class="p-2 border text-right font-mono">
                                            {{ number_format($step['normalized'] ?? 0, 4) }}
                                        </td>

                                        <td class="p-2 border text-right font-mono">
                                            {{ number_format($step['utility'] ?? ($step['normalized'] ?? 0), 4) }}
                                        </td>

                                        @if ($i === 0)
                                            <td class="p-2 border text-right font-mono align-top"
                                                rowspan="{{ $rowspan }}">
                                                {{ number_format($data['smart_score'] ?? 0, 4) }}
                                            </td>

                                            <td class="p-2 border text-right font-mono align-top"
                                                rowspan="{{ $rowspan }}">
                                                {{ number_format($data['sector_weight'] ?? ($sectorWeights[$data['sector_id']] ?? 1), 4) }}
                                            </td>

                                            <td class="p-2 border text-right font-mono align-top"
                                                rowspan="{{ $rowspan }}">
                                                {{ number_format(($data['smart_score'] ?? 0) * ($data['sector_weight'] ?? ($sectorWeights[$data['sector_id']] ?? 1)), 4) }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endforeach

    </div>

@endif
