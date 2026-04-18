@extends('layouts.dashboard')

@section('title', 'SMART Debug')

@section('content')
    <div class="p-4 md:p-8 bg-slate-50 min-h-screen print:bg-white print:p-0 transition-all duration-300">

        <div
            class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 print:mb-6 print:border-b-2 print:border-slate-800 print:pb-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight print:text-2xl">
                    SMART <span class="text-indigo-600 print:text-black">Provenance</span>
                </h1>
                <p class="text-sm text-slate-500 mt-1 print:text-slate-700">
                    Raw debugging and traceability for decision support system.
                    <span class="hidden print:inline ml-2 text-xs text-slate-400">Generated: {{ date('d/m/Y H:i') }}</span>
                </p>
            </div>

            <div class="flex items-center gap-3 print:hidden">
                <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-all active:scale-95">
                    <span class="mr-2 text-lg">🖨️</span> Print Report
                </button>
                <button onclick="exportPDF()"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                    <span class="mr-2 text-lg">📄</span> Export PDF
                </button>
            </div>
        </div>

        @php
            $trace = $data['pipeline']['smart']['trace'] ?? [];
        @endphp

        <div class="space-y-8 print:space-y-6">
            @foreach ($trace as $alt)
                <div
                    class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden print:shadow-none print:border-slate-300 print:rounded-lg page-break-inside-avoid ring-1 ring-black/5">

                    <div
                        class="p-5 border-b border-slate-100 bg-slate-50/50 md:flex md:items-center md:justify-between print:bg-white print:border-b-2">
                        <div class="flex items-center gap-4">
                            <div
                                class="h-12 w-12 flex flex-none items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 font-black text-xl print:bg-white print:border-2 print:border-slate-200">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <h2 class="font-bold text-slate-900 text-lg leading-tight print:text-xl">
                                    {{ $alt['name'] ?? 'N/A' }}</h2>
                                <p
                                    class="text-xs text-slate-400 uppercase tracking-widest font-bold mt-0.5 print:text-slate-600">
                                    ID: {{ $alt['alternative_id'] ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 md:mt-0 flex flex-wrap gap-2 md:gap-3 print:mt-0">
                            <div
                                class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm text-right min-w-[100px] print:border-slate-300">
                                <p class="text-[9px] text-slate-400 uppercase font-black print:text-slate-500">SMART Score
                                </p>
                                <p class="text-sm font-mono font-bold text-slate-800">
                                    {{ isset($alt['smart_score']) ? number_format($alt['smart_score'], 4) : '-' }}
                                </p>
                            </div>
                            <div
                                class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm text-right min-w-[100px] print:border-slate-300">
                                <p class="text-[9px] text-slate-400 uppercase font-black print:text-slate-500">Weight</p>
                                <p class="text-sm font-mono font-bold text-slate-800">
                                    {{ isset($alt['sector_weight']) ? number_format($alt['sector_weight'], 4) : '-' }}
                                </p>
                            </div>
                            <div
                                class="bg-indigo-600 px-5 py-2 rounded-xl shadow-lg shadow-indigo-100 text-right min-w-[110px] print:bg-slate-900 print:shadow-none">
                                <p class="text-[9px] text-indigo-100 uppercase font-black print:text-slate-300">Final Score
                                </p>
                                <p class="text-sm font-mono font-bold text-white">
                                    {{ isset($alt['final_score']) ? number_format($alt['final_score'], 4) : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[640px] print:min-w-full">
                            <thead>
                                <tr
                                    class="bg-white text-slate-500 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 print:bg-slate-50 print:text-slate-900 print:border-b-2 print:border-slate-800">
                                    <th class="px-6 py-4 print:px-3 print:py-2">Criteria Name</th>
                                    <th class="px-4 py-4 text-center print:px-3 print:py-2">Type</th>
                                    <th class="px-4 py-4 text-right print:px-3 print:py-2">Raw Value</th>
                                    <th class="px-4 py-4 text-right print:px-3 print:py-2">Utility</th>
                                    <th class="px-4 py-4 text-right text-indigo-600 print:px-3 print:py-2 print:text-black">
                                        Weight</th>
                                    <th class="px-6 py-4 text-right print:px-3 print:py-2">Contribution</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm print:divide-slate-200">
                                @foreach ($alt['steps'] ?? [] as $step)
                                    <tr class="hover:bg-slate-50/50 transition-colors group">
                                        <td
                                            class="px-6 py-4 font-semibold text-slate-700 print:px-3 print:py-2 print:font-bold">
                                            @php
                                                $isInteractive = in_array(strtolower($step['criteria_name'] ?? ''), [
                                                    'urgensi',
                                                    'dampak',
                                                ]);
                                            @endphp

                                            @if ($isInteractive)
                                                <button
                                                    onclick="openDMModal({{ $alt['alternative_id'] }}, {{ $step['criteria_id'] ?? 'null' }})"
                                                    class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group-hover:translate-x-1 transition-all print:hidden underline decoration-dotted decoration-indigo-300 underline-offset-4">
                                                    {{ $step['criteria_name'] }}
                                                    <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <span class="hidden print:inline">{{ $step['criteria_name'] }}</span>
                                            @else
                                                <span class="text-slate-600">{{ $step['criteria_name'] ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center print:px-3">
                                            <span
                                                class="px-2.5 py-1 rounded text-[10px] font-black tracking-tight {{ ($step['type'] ?? '') == 'benefit' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} print:bg-white print:border print:border-slate-300 print:text-black">
                                                {{ strtoupper($step['type'] ?? '-') }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-4 py-4 text-right font-mono text-slate-500 text-xs print:px-3 print:text-slate-800">
                                            {{ is_numeric($step['raw_value'] ?? null) ? number_format($step['raw_value'], 4) : '-' }}
                                        </td>
                                        <td
                                            class="px-4 py-4 text-right font-mono font-medium text-slate-600 print:px-3 print:text-black">
                                            {{ is_numeric($step['utility'] ?? null) ? number_format($step['utility'], 4) : '-' }}
                                        </td>
                                        <td
                                            class="px-4 py-4 text-right font-mono text-indigo-600 font-bold print:px-3 print:text-black">
                                            {{ is_numeric($step['weight'] ?? null) ? number_format($step['weight'], 4) : '-' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right font-mono font-black text-slate-900 bg-slate-50/30 print:px-3 print:bg-white">
                                            {{ is_numeric($step['contribution'] ?? null) ? number_format($step['contribution'], 4) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="dmModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center p-4 transition-all duration-300 opacity-0 print:hidden">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDMModal()"></div>

        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl relative z-10 overflow-hidden transform transition-all scale-95 duration-300"
            id="dmModalPanel">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white">
                <div>
                    <h3 class="font-black text-slate-900 text-lg tracking-tight">Detail Penilaian Consensus</h3>
                    <p class="text-xs text-slate-400 font-medium">Breakdown scores from all decision makers</p>
                </div>
                <button onclick="closeDMModal()"
                    class="p-2 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="dmModalContent" class="p-6 max-h-[60vh] overflow-y-auto bg-white scroll-smooth">
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="relative w-10 h-10">
                        <div class="absolute inset-0 border-4 border-indigo-100 rounded-full"></div>
                        <div
                            class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin">
                        </div>
                    </div>
                    <p class="mt-4 text-slate-500 font-medium text-sm">Synchronizing data...</p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                <button onclick="closeDMModal()"
                    class="px-6 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all active:scale-95">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function exportPDF() {
            const originalTitle = document.title;
            document.title = "SMART_Provenance_Report_{{ date('Y-m-d') }}";
            window.print();
            document.title = originalTitle;
        }

        function openDMModal(alternativeId, criteriaId) {
            if (!criteriaId) return;

            const modal = document.getElementById('dmModal');
            const panel = document.getElementById('dmModalPanel');
            const content = document.getElementById('dmModalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                modal.classList.add('opacity-100');
                panel.classList.remove('scale-95');
                panel.classList.add('scale-100');
            }, 10);

            const sessionId = {{ request()->route('decisionSession')->id ?? 1 }};
            fetch(`/admin/smart-dm-detail?session_id=${sessionId}&alternative_id=${alternativeId}&criteria_id=${criteriaId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.data || !data.data.length) {
                        content.innerHTML =
                            `<div class="text-center py-12 text-slate-400 font-medium">No decision maker data found.</div>`;
                        return;
                    }

                    let html = `
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100/50 shadow-inner">
                                <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest">Mean Score</p>
                                <p class="text-2xl font-black text-indigo-700">${data.stats.avg}</p>
                            </div>
                            <div class="bg-slate-100/50 p-4 rounded-2xl border border-slate-200/50 shadow-inner">
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Respondents</p>
                                <p class="text-2xl font-black text-slate-700">${data.stats.count} <span class="text-xs text-slate-400 font-medium">/ ${data.assigned_count}</span></p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Individual Scores</p>
                            <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-5 py-3 text-slate-500 text-left text-[10px] font-black uppercase tracking-tighter">Stakeholder</th>
                                            <th class="px-5 py-3 text-slate-500 text-right text-[10px] font-black uppercase tracking-tighter">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                    `;

                    data.data.forEach(row => {
                        html += `
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-slate-700">${row.dm_name}</td>
                                <td class="px-5 py-3.5 text-right font-mono font-black text-indigo-600">${parseFloat(row.value).toFixed(2)}</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table></div></div>`;
                    content.innerHTML = html;
                })
                .catch(err => {
                    content.innerHTML =
                        `<div class="text-red-500 p-4 border-2 border-red-50 bg-red-50/50 rounded-2xl text-sm font-bold text-center">Error communicating with server.</div>`;
                });
        }

        function closeDMModal() {
            const modal = document.getElementById('dmModal');
            const panel = document.getElementById('dmModalPanel');
            modal.classList.remove('opacity-100');
            panel.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    </script>

    <style>
        /* Modern Scrollbar */
        #dmModalContent::-webkit-scrollbar {
            width: 5px;
        }

        #dmModalContent::-webkit-scrollbar-track {
            background: transparent;
        }

        #dmModalContent::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        #dmModalContent::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4 portrait;
                margin: 1cm;
            }

            body {
                background: white !important;
                font-family: sans-serif;
            }

            .page-break-inside-avoid {
                page-break-inside: avoid !important;
                break-inside: avoid;
            }

            nav,
            .sidebar,
            .navbar,
            .print\:hidden {
                display: none !important;
            }

            /* Border and Text clarity for Print */
            .text-indigo-600 {
                color: #4f46e5 !important;
            }

            .bg-indigo-600 {
                background-color: #1e293b !important;
                color: white !important;
            }

            /* Darker for better contrast in grayscale print */
            .border-slate-200 {
                border-color: #e2e8f0 !important;
            }

            .bg-slate-50\/50 {
                background-color: #f8fafc !important;
            }
        }
    </style>
@endsection
