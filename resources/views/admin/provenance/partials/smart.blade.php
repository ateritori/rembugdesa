@if (empty($traces) || count($traces) === 0)
    <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-2xl">
        <p class="text-slate-400 font-medium">⚠️ Tidak ada trace data SMART yang tersedia.</p>
    </div>
@else
    <div class="space-y-8 antialiased text-slate-800">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3 bg-slate-100 p-2 rounded-lg">
                <span class="text-[10px] font-black uppercase pl-2">Filter View:</span>
                <select id="filter-dm"
                    class="text-xs bg-white border border-slate-300 rounded md px-3 py-1 font-bold outline-none focus:ring-2 focus:ring-black">
                    <option value="">Tampilkan Semua DM</option>
                    @foreach ($traces as $userId => $_)
                        <option value="{{ $userId }}">{{ $userId ? 'Decision Maker ' . $userId : 'SYSTEM' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @foreach ($traces as $userId => $alternatives)
            <div class="calculation-block group transition-all" data-dm="{{ $userId }}">

                <div class="flex items-baseline gap-4 mb-4 border-b border-slate-200 pb-2">
                    <h2 class="text-lg font-black text-slate-900 shadow-slate-200">
                        {{ $userId ? 'DECISION MAKER ' . $userId : 'SYSTEM CALCULATIONS' }}
                    </h2>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        Data Source: {{ $userId ? 'User Input ID ' . $userId : 'Automated System' }}
                    </span>
                </div>

                <div class="bg-white border border-slate-300 shadow-[4px_4px_0px_0px_rgba(0,0,0,0.05)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-[12px] border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white uppercase tracking-wider text-[11px]">
                                    <th class="p-4 border-r border-slate-700 text-left font-black">Alternatif</th>
                                    <th class="p-4 border-r border-slate-700 text-center font-black">Kriteria</th>
                                    <th class="p-4 text-right font-black">Raw</th>
                                    <th class="p-4 text-center italic opacity-80 font-medium border-r border-slate-700">
                                        Bounds (Min-Max)</th>
                                    <th class="p-4 text-right font-black border-r border-slate-700">Normalisasi</th>
                                    <th
                                        class="p-4 text-right bg-blue-700 font-black border-r border-slate-700 shadow-[inset_0_-2px_0_rgba(0,0,0,0.2)]">
                                        Utility</th>
                                    <th class="p-4 text-center font-black border-r border-slate-700">Type</th>
                                    <th class="p-4 text-right bg-slate-800 font-black">SMART Score</th>
                                    <th class="p-4 text-right bg-slate-800 font-black border-x border-slate-700">Weight
                                    </th>
                                    <th
                                        class="p-4 text-right bg-emerald-800 font-black shadow-[inset_0_-2px_0_rgba(0,0,0,0.2)]">
                                        Final Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($alternatives as $altId => $data)
                                    @php $rowspan = count($data['steps']); @endphp

                                    @foreach ($data['steps'] as $i => $step)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            @if ($i === 0)
                                                <td class="p-3 font-black text-slate-900 border-r border-slate-200 align-top bg-slate-50/50"
                                                    rowspan="{{ $rowspan }}">
                                                    <div class="sticky top-0 text-sm tracking-tighter">
                                                        {{ $data['code'] ?? 'A' . $altId }}</div>
                                                </td>
                                            @endif

                                            <td
                                                class="p-3 text-center border-r border-slate-100 font-bold text-slate-600">
                                                C{{ $step['criteria_id'] }}
                                            </td>

                                            <td class="p-3 text-right font-mono text-slate-600">
                                                {{ $step['raw_value'] }}
                                            </td>

                                            <td
                                                class="p-3 text-center font-mono text-[11px] text-slate-400 border-r border-slate-100">
                                                {{ $step['min'] }} <span class="mx-1 text-slate-300">→</span>
                                                {{ $step['max'] }}
                                            </td>

                                            <td
                                                class="p-3 text-right font-mono text-slate-500 border-r border-slate-100">
                                                {{ number_format($step['normalized'] ?? 0, 4) }}
                                            </td>

                                            <td
                                                class="p-3 text-right font-mono font-bold text-blue-700 bg-blue-50/30 border-r border-slate-100">
                                                {{ number_format($step['utility'] ?? ($step['normalized'] ?? 0), 4) }}
                                            </td>

                                            <td class="p-3 text-center border-r border-slate-100">
                                                <span
                                                    class="text-[9px] font-black text-slate-500 px-1.5 py-0.5 rounded border border-slate-300 uppercase bg-white">
                                                    {{ $step['utility_function'] ?? '-' }}
                                                </span>
                                            </td>

                                            @if ($i === 0)
                                                <td class="p-3 text-right font-mono font-bold text-slate-900 border-l border-slate-200 align-top bg-slate-100/50"
                                                    rowspan="{{ $rowspan }}">
                                                    {{ number_format($data['smart_score'] ?? 0, 4) }}
                                                </td>

                                                <td class="p-3 text-right font-mono text-slate-500 align-top bg-slate-100/50"
                                                    rowspan="{{ $rowspan }}">
                                                    {{ number_format($data['sector_weight'] ?? ($sectorWeights[$data['sector_id']] ?? 1), 4) }}
                                                </td>

                                                <td class="p-3 text-right font-mono font-black text-emerald-700 align-top bg-emerald-50 border-l border-emerald-100"
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
            </div>
        @endforeach
    </div>
@endif

<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;700;900&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
    }

    /* Fix table header clipping on some browsers */
    thead th {
        position: relative;
        z-index: 10;
    }
</style>

<script>
    function applyFilters() {
        const dmVal = document.getElementById('filter-dm')?.value || '';
        document.querySelectorAll('[data-dm]').forEach(container => {
            const isVisible = !dmVal || container.dataset.dm == dmVal;
            container.style.display = isVisible ? 'block' : 'none';
        });
    }
    document.getElementById('filter-dm')?.addEventListener('change', applyFilters);
</script>
