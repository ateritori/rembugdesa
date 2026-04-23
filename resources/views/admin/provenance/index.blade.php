@extends('layouts.dashboard')

@section('title', 'Decision Provenance')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span
                        class="bg-primary/10 text-primary text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md">
                        Decision Provenance
                    </span>
                </div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Jejak Proses Keputusan
                </h1>
                <p class="adaptive-text-sub mt-1 max-w-xl text-sm leading-relaxed">
                    Menampilkan alur proses perhitungan dan keterlacakan keputusan dari setiap metode.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                    class="px-4 py-2 text-xs font-black rounded-xl bg-primary text-white hover:opacity-90">
                    Print / Export PDF
                </button>

                <a href="{{ route('decision-sessions.index') }}"
                    class="group flex items-center gap-2 rounded-xl border-2 border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-black text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Kembali
                </a>
            </div>
        </div>


        @if (isset($traces) && count($traces) > 0)
            <div class="adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
                <h3 class="text-xs font-black uppercase tracking-widest mb-3">
                    SMART Trace (Detail Per Kriteria)
                </h3>

                @foreach ($traces as $userId => $alternatives)
                    <div class="mb-6">
                        <h4 class="text-xs font-bold mb-2">
                            {{ $userId ? 'DM ' . $userId : 'SYSTEM' }}
                        </h4>

                        <div class="overflow-auto">
                            <table class="w-full text-xs border">
                                <thead>
                                    <tr class="bg-slate-100">
                                        <th class="p-2 border text-left">Alternatif</th>
                                        <th class="p-2 border text-right">Skor</th>
                                        <th class="p-2 border text-left">Kriteria</th>
                                        <th class="p-2 border text-left">Raw</th>
                                        <th class="p-2 border text-left">Min</th>
                                        <th class="p-2 border text-left">Max</th>
                                        <th class="p-2 border text-left">Normalisasi</th>
                                        <th class="p-2 border text-left">Jenis</th>
                                        <th class="p-2 border text-left">Transformasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alternatives as $altId => $data)
                                        @php $rowspan = count($data['steps']); @endphp

                                        @foreach ($data['steps'] as $index => $step)
                                            <tr class="border-t">
                                                @if ($index === 0)
                                                    <td class="p-2 border font-bold align-top"
                                                        rowspan="{{ $rowspan }}">
                                                        {{ $data['code'] ?? 'A' . $altId }}
                                                    </td>
                                                    <td class="p-2 border text-right font-mono align-top"
                                                        rowspan="{{ $rowspan }}">
                                                        {{ number_format($data['smart_score'] ?? 0, 4) }}
                                                    </td>
                                                @endif

                                                <td class="p-2 border font-bold">
                                                    C{{ $step['criteria_id'] }}
                                                </td>
                                                <td class="p-2 border font-mono text-right">
                                                    {{ $step['raw_value'] }}
                                                </td>
                                                <td class="p-2 border font-mono text-right">
                                                    {{ $step['min'] }}
                                                </td>
                                                <td class="p-2 border font-mono text-right">
                                                    {{ $step['max'] }}
                                                </td>
                                                <td class="p-2 border font-mono text-right">
                                                    {{ number_format($step['normalized'] ?? 0, 4) }}
                                                </td>
                                                <td class="p-2 border text-gray-500">
                                                    {{ $step['utility_function'] ?? 'linear' }}
                                                </td>
                                                <td class="p-2 border font-mono text-right">
                                                    {{ number_format($step['utility'] ?? ($step['normalized'] ?? 0), 4) }}
                                                </td>
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

        {{-- EMPTY --}}
        @if (isset($results) && $results->isEmpty())
            <div class="adaptive-card p-20 text-center border-dashed border-2 rounded-3xl opacity-50">
                <p class="text-slate-500 font-black uppercase tracking-widest text-xs">
                    Tidak ada data SMART.
                </p>
            </div>
        @endif

    </div>

    {{-- PRINT STYLE --}}
    <style>
        @media print {

            button,
            a {
                display: none !important;
            }

            .print-section {
                page-break-inside: avoid;
            }

            table {
                font-size: 10px;
            }
        }
    </style>

@endsection
