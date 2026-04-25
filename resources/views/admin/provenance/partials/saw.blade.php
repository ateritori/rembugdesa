@if (empty($sawTraces) || count($sawTraces) === 0)
    <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-2xl">
        <p class="text-slate-400 font-medium">⚠️ Tidak ada trace data SAW yang tersedia.</p>
    </div>
@else
    <div class="space-y-8 antialiased text-slate-800">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3 bg-slate-100 p-2 rounded-lg">
                <span class="text-[10px] font-black uppercase pl-2">Filter View:</span>
                <select id="filter-dm" onchange="applyFilters()"
                    class="text-xs bg-white border border-slate-300 rounded md px-3 py-1 font-bold outline-none focus:ring-2 focus:ring-black">
                    <option value="">Tampilkan Semua DM</option>
                    @foreach ($sawTraces as $userId => $_)
                        <option value="{{ $userId ?? 'system' }}">
                            {{ $userId ? 'Decision Maker ' . $userId : 'SYSTEM' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @foreach ($sawTraces as $userId => $alternatives)
            <div class="calculation-block group transition-all" data-dm="{{ $userId ?? 'system' }}">

                <div class="flex items-baseline gap-4 mb-4 border-b border-slate-200 pb-2">
                    <h2 class="text-lg font-black text-slate-900">
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
                                    <th class="p-4 text-left font-black">Alternatif</th>
                                    <th class="p-4 text-center font-black">Kriteria</th>
                                    <th class="p-4 text-right font-black">Raw</th>
                                    <th class="p-4 text-center font-medium">Bounds</th>
                                    <th class="p-4 text-right font-black">Normalisasi</th>
                                    <th class="p-4 text-right font-black bg-blue-700">SAW</th>
                                    <th class="p-4 text-right font-black bg-slate-800">Bobot</th>
                                    <th class="p-4 text-right font-black bg-emerald-800">Final</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($alternatives as $altId => $data)
                                    @php $rowspan = count($data['steps']); @endphp

                                    @foreach ($data['steps'] as $i => $step)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            @if ($i === 0)
                                                <td class="p-3 font-black border-r align-top bg-slate-50/50"
                                                    rowspan="{{ $rowspan }}">
                                                    {{ $data['code'] ?? 'A' . $altId }}
                                                </td>
                                            @endif

                                            <td class="p-3 text-center font-bold">
                                                C{{ $step['criteria_id'] }}
                                            </td>

                                            <td class="p-3 text-right font-mono">
                                                {{ $step['raw_value'] }}
                                            </td>

                                            <td class="p-3 text-center font-mono text-[11px] text-slate-400">
                                                {{ $step['min'] }} → {{ $step['max'] }}
                                            </td>

                                            <td class="p-3 text-right font-mono">
                                                {{ number_format($step['normalized'] ?? 0, 4) }}
                                            </td>

                                            @if ($i === 0)
                                                <td class="p-3 text-right font-mono font-bold text-blue-700"
                                                    rowspan="{{ $rowspan }}">
                                                    {{ number_format($data['saw_score'] ?? 0, 4) }}
                                                </td>

                                                <td class="p-3 text-right font-mono text-slate-500"
                                                    rowspan="{{ $rowspan }}">
                                                    {{ number_format($data['sector_weight'] ?? ($sectorWeights[$data['sector_id']] ?? 1), 4) }}
                                                </td>

                                                <td class="p-3 text-right font-mono font-black text-emerald-700"
                                                    rowspan="{{ $rowspan }}">
                                                    {{ number_format($data['final_score'] ?? 0, 4) }}
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

    thead th {
        position: relative;
        z-index: 10;
    }
</style>

<script>
    function applyFilters() {
        const filter = document.getElementById('filter-dm');
        const dmVal = filter ? filter.value : '';

        document.querySelectorAll('[data-dm]').forEach(container => {
            const isVisible = !dmVal || String(container.dataset.dm) === String(dmVal);
            container.style.display = isVisible ? 'block' : 'none';
        });
    }

    // auto-run once (important for partial render)
    setTimeout(applyFilters, 0);
</script>
