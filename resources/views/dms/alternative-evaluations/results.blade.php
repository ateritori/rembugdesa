<h2 class="text-xl font-semibold mb-4">Hasil Evaluasi Alternatif (Versi Anda)</h2>

@if (isset($decisionSession))
    @if ($decisionSession->status !== 'closed')
        <div class="mb-4">
            <a href="{{ request()->fullUrlWithQuery(['edit' => 1]) }}"
                class="inline-block px-3 py-1 text-xs font-bold text-white bg-blue-600 rounded hover:bg-blue-700">
                Ubah Nilai
            </a>
        </div>
    @elseif(request()->has('edit'))
        <div class="mb-4 text-xs font-bold text-red-600">
            Session sudah ditutup. Perubahan tidak diizinkan.
        </div>
    @endif
@endif

@if (
    !isset($smartTrace) ||
        (is_array($smartTrace) && count($smartTrace) === 0) ||
        ($smartTrace instanceof \Illuminate\Support\Collection && $smartTrace->isEmpty()))
    <div class="text-gray-500">
        Data belum tersedia atau evaluasi belum lengkap.
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full text-xs border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Kode</th>
                    <th class="px-3 py-2 border">Alternatif</th>
                    <th class="px-3 py-2 border">Parameter</th>

                    <th class="px-2 py-1 border">Raw</th>
                    <th class="px-2 py-1 border">Bounds</th>
                    <th class="px-2 py-1 border">Normalisasi</th>
                    <th class="px-2 py-1 border">Utility</th>
                    <th class="px-2 py-1 border">Type</th>

                    <th class="px-3 py-2 border">Smart Score</th>
                    <th class="px-3 py-2 border">Bobot Sektor</th>
                    <th class="px-3 py-2 border">Final Score</th>
                    <th class="px-3 py-2 border">Rank</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($smartTrace)->sortByDesc(function ($d) {
        return ($d['smart_score'] ?? 0) * ($d['sector_weight'] ?? 1);
    }) as $altId => $data)
                    @php $rowspan = count($data['steps']); @endphp

                    @foreach ($data['steps'] as $i => $step)
                        <tr>
                            @if ($i === 0)
                                <td rowspan="{{ $rowspan }}" class="px-3 py-2 border text-center">
                                    {{ $data['code'] ?? 'A' . $altId }}
                                </td>
                                <td rowspan="{{ $rowspan }}" class="px-3 py-2 border font-semibold">
                                    {{ $data['name'] ?? 'Alt #' . $altId }}
                                </td>
                            @endif

                            <td class="px-3 py-2 border text-center font-medium">
                                {{ $step['criteria_name'] ?? 'Parameter' }}
                            </td>

                            <td class="px-2 py-1 border text-center">
                                {{ $step['raw_value'] ?? '-' }}
                            </td>

                            <td class="px-2 py-1 border text-center">
                                {{ $step['min'] ?? '-' }} → {{ $step['max'] ?? '-' }}
                            </td>

                            <td class="px-2 py-1 border text-center">
                                {{ isset($step['normalized']) ? number_format($step['normalized'], 4) : '-' }}
                            </td>

                            <td class="px-2 py-1 border text-center">
                                {{ isset($step['utility']) ? number_format($step['utility'], 4) : '-' }}
                            </td>

                            <td class="px-2 py-1 border text-center">
                                {{ isset($step['utility_function'])
                                    ? ucfirst(strtolower($step['utility_function']))
                                    : (isset($step['type'])
                                        ? ucfirst(strtolower($step['type']))
                                        : '-') }}
                            </td>

                            @if ($i === 0)
                                <td rowspan="{{ $rowspan }}" class="px-3 py-2 border text-center">
                                    {{ isset($data['smart_score']) ? number_format($data['smart_score'], 4) : '-' }}
                                </td>
                                <td rowspan="{{ $rowspan }}" class="px-3 py-2 border text-center">
                                    {{ isset($data['sector_weight']) ? number_format($data['sector_weight'], 4) : '-' }}
                                </td>
                                <td rowspan="{{ $rowspan }}" class="px-3 py-2 border font-bold text-center">
                                    {{ number_format(($data['smart_score'] ?? 0) * ($data['sector_weight'] ?? 1), 4) }}
                                </td>
                                <td rowspan="{{ $rowspan }}" class="px-3 py-2 border text-center">
                                    {{ $loop->parent->iteration }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@endif
