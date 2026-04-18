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

        {{-- SUMMARY --}}
        <div class="adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
            <h3 class="text-xs font-black uppercase tracking-widest mb-3">
                Ringkasan
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-lg font-black text-primary">
                        {{ count($data['trace'] ?? []) }}
                    </div>
                    <div class="text-[10px] uppercase text-slate-400">
                        Alternatif
                    </div>
                </div>

                <div>
                    <div class="text-lg font-black text-indigo-500">
                        {{ isset($data['pipeline']['smart']) ? count($data['pipeline']['smart']['results']) : 0 }}
                    </div>
                    <div class="text-[10px] uppercase text-slate-400">
                        SMART Result
                    </div>
                </div>

                <div>
                    <div class="text-lg font-black text-orange-500">
                        {{ isset($data['pipeline']['smart']['trace']) ? count($data['pipeline']['smart']['trace']) : 0 }}
                    </div>
                    <div class="text-[10px] uppercase text-slate-400">
                        Trace
                    </div>
                </div>

                <div>
                    <div class="text-lg font-black text-emerald-500">
                        {{ number_format(collect($data['trace'] ?? [])->avg('delta'), 6) }}
                    </div>
                    <div class="text-[10px] uppercase text-slate-400">
                        Avg Delta
                    </div>
                </div>
            </div>
        </div>

        {{-- SMART TRACE --}}
        @if (!empty($data['pipeline']['smart']['trace']))
            @include('admin.provenance.partials.smart', ['data' => $data])
        @endif

        {{-- EMPTY --}}
        @if (empty($data['trace']))
            <div class="adaptive-card p-20 text-center border-dashed border-2 rounded-3xl opacity-50">
                <p class="text-slate-500 font-black uppercase tracking-widest text-xs">
                    Belum ada data provenance.
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
