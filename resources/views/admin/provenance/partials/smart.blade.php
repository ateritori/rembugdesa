@if ($results->isEmpty())
    <div class="p-4 border border-black bg-yellow-50 text-sm font-bold">
        ⚠️ Tidak ada data SMART (evaluation_results kosong / belum sesuai filter)
    </div>
@else
    @php
        // kumpulkan semua alternatif unik
        $allAlternatives = collect();
        foreach ($results as $rows) {
            foreach ($rows as $row) {
                $allAlternatives->push($row->alternative_id);
            }
        }
        $allAlternatives = $allAlternatives->unique()->sort()->values();
    @endphp

    <div class="overflow-x-auto">
        <table class="w-full border border-black text-sm">
            <thead>
                <tr class="bg-black text-white">
                    <th class="border p-2 text-left">Alternatif</th>
                    @foreach ($results as $userId => $rows)
                        <th class="border p-2 text-center">
                            {{ $userId ? 'DM ' . $userId : 'SYSTEM' }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($allAlternatives as $altId)
                    <tr>
                        <td class="border p-2 font-bold">
                            Alt {{ $altId }}
                        </td>

                        @foreach ($results as $userId => $rows)
                            @php
                                $found = $rows->firstWhere('alternative_id', $altId);
                            @endphp
                            <td class="border p-2 text-center font-mono">
                                @if ($found)
                                    <div>{{ number_format($found->evaluation_score, 4) }}</div>

                                    @if (isset($debug[$userId][$altId]))
                                        <div class="text-[10px] text-left mt-1 space-y-1">
                                            @foreach ($debug[$userId][$altId] as $item)
                                                <div>
                                                    C{{ $item['criteria_id'] }}:
                                                    {{ number_format($item['normalized'], 4) }}
                                                    <br>
                                                    <span class="text-gray-500">
                                                        raw={{ $item['raw_value'] }},
                                                        min={{ $item['min'] }},
                                                        max={{ $item['max'] }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endif
