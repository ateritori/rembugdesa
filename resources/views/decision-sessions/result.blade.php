@extends('layouts.dashboard')

@section('title', 'Hasil Akhir')

@section('content')
    <div class="space-y-8">
        {{-- Header Section --}}
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between px-2">
            <div class="space-y-1">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Hasil Akhir Keputusan
                </h2>
                <p class="text-slate-500 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Konsensus Grup: {{ $decisionSession->name }} ({{ $decisionSession->year }})
                </p>
            </div>
        </div>

        {{-- Info Banner: Metode --}}
        <div class="p-5 rounded-3xl border border-slate-200 bg-slate-50/50 text-sm text-slate-600 leading-relaxed">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 shadow-sm">
                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block font-bold text-slate-900">Agregasi SMART & Borda</span>
                    Peringkat di bawah ini merupakan hasil akhir yang telah divalidasi menggunakan preferensi seluruh
                    pembuat keputusan.
                </div>
            </div>
        </div>

        {{-- Main Result Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
            {{-- Card Header --}}
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-xs font-black uppercase tracking-[0.15em] text-slate-400">
                    Ranking Final Alternatif
                </h3>
                <span class="text-[10px] font-bold bg-white px-2 py-1 rounded-full border border-slate-200 text-slate-400">
                    {{ count($results) }} Kandidat Terpilih
                </span>
            </div>

            {{-- Table Wrapper --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-white">
                            <th
                                class="w-20 px-6 py-4 text-left font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                Rank</th>
                            <th class="px-6 py-4 text-left font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                Alternatif</th>
                            <th class="px-6 py-4 text-right font-bold uppercase tracking-wider text-slate-400 text-[11px]">
                                Skor Borda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($results as $row)
                            @php
                                // Match locked rank colors
                                $rankColor = match ((int) $row->final_rank) {
                                    1 => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
                                    2 => 'bg-slate-100 text-slate-600 ring-slate-200',
                                    3 => 'bg-orange-100 text-orange-700 ring-orange-200',
                                    default => 'bg-white text-slate-500 ring-slate-100',
                                };
                            @endphp
                            <tr class="group transition-colors hover:bg-slate-50/80">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-black text-xs ring-1 shadow-sm {{ $rankColor }}">
                                        {{ $row->final_rank }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 group-hover:text-primary transition-colors">
                                        {{ $row->alternative->name }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wide font-medium">ID:
                                        {{ str_pad($row->alternative->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-mono font-black text-primary text-base">
                                        {{ number_format($row->borda_score, 0) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Action --}}
        <div class="flex justify-start px-2">
            <a href="{{ route('decision-sessions.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600 shadow-sm transition-all hover:bg-slate-50 hover:border-slate-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Sesi
            </a>
        </div>
    </div>
@endsection
