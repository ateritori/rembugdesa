@extends('layouts.dashboard')

@section('title', 'Audit Log Borda')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span
                        class="bg-primary/10 text-primary text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md">
                        Metode Borda Count
                    </span>
                </div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Matriks Agregasi Borda
                </h1>
                <p class="adaptive-text-sub mt-1 max-w-xl text-sm leading-relaxed">
                    Perhitungan agregasi ranking dari seluruh Decision Maker untuk menentukan urutan final.
                </p>
            </div>

            <a href="{{ route('decision-sessions.index') }}"
                class="group flex items-center gap-2 rounded-xl border-2 border-slate-200 dark:border-slate-700 px-5 py-2.5 text-sm font-black text-slate-600 dark:text-slate-300 transition-all hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Kembali</span>
            </a>
        </div>

        {{-- LEGENDA & REFERENSI --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Rumus --}}
            <div class="adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-1.5 bg-blue-500/10 rounded-lg text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-[11px] font-black uppercase tracking-widest opacity-70">
                        Logika Poin Borda
                    </h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span
                            class="flex-shrink-0 w-5 h-5 bg-slate-100 dark:bg-slate-800 flex items-center justify-center rounded text-[10px] font-black">R</span>
                        <p class="text-xs text-slate-500 leading-tight">Ranking dari Decision Maker</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span
                            class="flex-shrink-0 w-5 h-5 bg-slate-100 dark:bg-slate-800 flex items-center justify-center rounded text-[10px] font-black">P</span>
                        <div class="text-xs text-slate-500 leading-tight">
                            Poin: <span class="font-mono bg-slate-100 dark:bg-slate-800 px-1 rounded">(n - R + 1)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Decision Maker --}}
            <div class="lg:col-span-2 adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-1.5 bg-orange-500/10 rounded-lg text-orange-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-[11px] font-black uppercase tracking-widest opacity-70">
                        Daftar Penilai (DM)
                    </h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($dmMapping as $dmId => $label)
                        <div
                            class="group flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl transition-all hover:border-orange-200">
                            <span class="text-orange-600 font-black italic text-[11px]">
                                {{ $label }}
                            </span>
                            <div class="w-[1px] h-3 bg-slate-300 dark:bg-slate-600"></div>
                            <span class="font-bold text-slate-700 dark:text-slate-200 text-xs">
                                {{ $dms[$dmId]->name ?? 'DM ' . $dmId }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- MAIN TABLE --}}
        <div class="adaptive-card overflow-hidden border shadow-lg rounded-2xl bg-white dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-center border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b dark:border-slate-700">
                            <th
                                class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-800 px-6 py-4 text-left font-black uppercase tracking-wider text-slate-500 border-r dark:border-slate-700">
                                Alternatif
                            </th>

                            @if (!empty($matrix))
                                @foreach ($dmMapping as $dmId => $label)
                                    <th class="px-4 py-4 font-black text-orange-600 border-r dark:border-slate-700">
                                        {{ $label }}
                                    </th>
                                @endforeach
                            @endif

                            <th
                                class="px-6 py-4 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 font-black uppercase tracking-wider border-r dark:border-slate-700">
                                Total Skor
                            </th>
                            <th class="px-6 py-4 bg-primary text-white font-black uppercase tracking-wider">
                                Rank
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-slate-700">
                        @forelse ($matrix as $row)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td
                                    class="sticky left-0 z-10 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-slate-800 px-6 py-4 text-left border-r dark:border-slate-700 transition-colors">
                                    @php
                                        $alt = $alternatives[$row['alternative_id']] ?? null;
                                    @endphp
                                    <div class="font-black text-slate-800 dark:text-slate-100 text-sm">
                                        {{ $alt->name ?? ($row['alternative_name'] ?? $row['alternative_id']) }}
                                        @if ($alt && !empty($alt->code))
                                            <span class="text-[11px] font-bold text-slate-400 ml-1">
                                                ({{ $alt->code }})
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                @foreach ($row['ranks'] as $rank)
                                    @php
                                        $n = count($matrix);
                                        $point = is_numeric($rank) ? $n - $rank + 1 : '-';
                                    @endphp
                                    <td class="px-4 py-4 border-r dark:border-slate-700">
                                        <div
                                            class="inline-flex flex-col items-center bg-white dark:bg-slate-800 border dark:border-slate-700 px-2.5 py-1 rounded-md shadow-sm">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[11px] text-slate-400 font-bold uppercase">R</span>
                                                <span
                                                    class="font-black text-sm text-slate-800 dark:text-white">{{ $rank }}</span>
                                            </div>
                                            <div class="w-full h-[1px] bg-slate-100 dark:bg-slate-700 my-1"></div>
                                            <div class="flex items-center gap-1 text-primary">
                                                <span class="text-[11px] font-bold uppercase">P</span>
                                                <span class="font-black text-sm">{{ $point }}</span>
                                            </div>
                                        </div>
                                    </td>
                                @endforeach

                                <td
                                    class="px-6 py-4 border-r dark:border-slate-700 font-black text-base bg-indigo-50/30 dark:bg-indigo-900/10 text-indigo-700 dark:text-indigo-400">
                                    {{ $row['borda_score'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-black shadow-lg shadow-primary/20">
                                        {{ $row['final_rank'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center opacity-20">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="font-black uppercase tracking-widest text-xs">Data Tidak Ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
