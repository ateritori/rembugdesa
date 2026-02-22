@extends('layouts.dashboard')

@section('title', 'Log Perhitungan AHP')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Log Perhitungan AHP
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Audit perhitungan matriks pairwise, bobot tiap DM (D), dan kriteria (C).
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

        @if (!empty($log['dm']))
            @php
                $firstDm = collect($log['dm'])->first();
                $originalCriteria = !empty($log['criteria_names'])
                    ? $log['criteria_names']
                    : $firstDm['criteria_names'] ?? [];

                // 1. Mapping Kriteria (C1, C2...)
                $criteriaMapping = [];
                foreach ($originalCriteria as $index => $name) {
                    $criteriaMapping[$name] = 'C' . ($index + 1);
                }

                // 2. Mapping Decision Maker (D1, D2...) - FIX: Counter manual agar mulai dari 1
                $dmMapping = [];
                $dmLabels = []; // Untuk keperluan legenda
                $counter = 1;
                foreach ($log['dm'] as $dmData) {
                    $label = 'D' . $counter;
                    $dmMapping[$dmData['dm_id']] = $label;
                    $dmLabels[] = [
                        'label' => $label,
                        'id' => $dmData['dm_id'],
                    ];
                    $counter++;
                }

                $gmFinal = $log['gm_final'] ?? [];

                // --- SMART baseline DM filter logic ---
                $allDmLogs = $log['dm'];
                $selectedDm = request('dm_id');

                // Default: show first DM only
                if (!request()->filled('dm_id') && isset($allDmLogs[0])) {
                    $selectedDm = $allDmLogs[0]['dm_id'];
                    $log['dm'] = [$allDmLogs[0]];
                } elseif ($selectedDm === 'all') {
                    $log['dm'] = $allDmLogs;
                } elseif ($selectedDm) {
                    $log['dm'] = collect($allDmLogs)->where('dm_id', (int) $selectedDm)->values()->all();
                }
            @endphp

            {{-- LEGENDA REFERENSI (C & D) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                {{-- Legenda Kriteria --}}
                <div
                    class="adaptive-card p-4 border border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-gray-800/20">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 text-center md:text-left">
                        Referensi Kriteria (C)</h3>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        @foreach ($originalCriteria as $index => $name)
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 border rounded-lg shadow-sm"
                                title="{{ $name }}">
                                <span class="text-primary font-black text-xs italic">C{{ $index + 1 }}</span>
                                <span class="text-xs font-medium dark:text-gray-300">{{ $name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Legenda Decision Maker --}}
                <div
                    class="adaptive-card p-4 border border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-gray-800/20">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 text-center md:text-left">
                        Referensi Decision Maker (D)</h3>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        {{-- Tombol Semua --}}
                        <a href="?dm_id=all"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold border shadow-sm transition
                           {{ $selectedDm === 'all' ? 'bg-primary text-white border-primary' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            Semua
                        </a>

                        @foreach ($allDmLogs as $dmData)
                            @php
                                $dmId = $dmData['dm_id'];
                                $label = $dmMapping[$dmId];
                                $name = $dmData['dm_name'] ?? 'DM ' . $dmId;
                            @endphp
                            <a href="?dm_id={{ $dmId }}"
                                class="px-3 py-1.5 rounded-xl text-[11px] font-bold flex items-center gap-2 border shadow-sm transition
                               {{ (string) $selectedDm === (string) $dmId ? 'bg-orange-500 text-white border-orange-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <span class="text-orange-500 font-black text-xs italic">{{ $label }}</span>
                                <span class="text-xs font-medium">{{ $name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8">

                {{-- LOOPING INDIVIDU DM --}}
                @foreach ($log['dm'] as $dmLog)
                    @php $currentD = $dmMapping[$dmLog['dm_id']]; @endphp
                    <div class="adaptive-card p-5 border border-gray-200 dark:border-gray-700 rounded-2xl">
                        <div class="mb-4 flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span
                                    class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-black shadow-lg shadow-primary/20">
                                    {{ $currentD }}
                                </span>
                                <h2 class="text-xl font-black text-primary">Penilaian {{ $currentD }}</h2>
                            </div>

                            <span
                                class="text-sm font-medium bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full border dark:border-gray-700">
                                CR: {{ round($dmLog['cr'], 4) }}
                            </span>

                            @if ($dmLog['is_consistent'])
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                    Konsisten
                                </span>
                            @else
                                <span
                                    class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                    Tidak Konsisten
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                            {{-- 1. Matriks Pairwise --}}
                            <div>
                                <h3 class="font-bold mb-3 text-gray-500 text-xs uppercase tracking-tighter">1. Matriks
                                    Pairwise</h3>
                                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-3 py-2 border-b border-r dark:border-gray-700"></th>
                                                @foreach ($dmLog['criteria_names'] as $kName)
                                                    <th class="px-3 py-2 border-b text-center font-black cursor-help"
                                                        title="{{ $kName }}">
                                                        {{ $criteriaMapping[$kName] }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dmLog['matrix'] as $i => $row)
                                                <tr class="border-b dark:border-gray-700">
                                                    <th class="px-3 py-2 font-black border-r bg-gray-50/30 dark:bg-gray-800/20 text-center cursor-help"
                                                        title="{{ $dmLog['criteria_names'][$i] }}">
                                                        {{ $criteriaMapping[$dmLog['criteria_names'][$i]] }}
                                                    </th>
                                                    @foreach ($row as $value)
                                                        <td class="px-3 py-2 text-center">{{ round($value, 2) }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- 2. Normalisasi & Bobot --}}
                            <div>
                                <h3 class="font-bold mb-3 text-gray-500 text-xs uppercase tracking-tighter">2. Normalisasi &
                                    Bobot</h3>
                                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-3 py-2 border-b border-r dark:border-gray-700"></th>
                                                @foreach ($dmLog['criteria_names'] as $kName)
                                                    <th class="px-3 py-2 border-b text-center font-black cursor-help"
                                                        title="{{ $kName }}">
                                                        {{ $criteriaMapping[$kName] }}
                                                    </th>
                                                @endforeach
                                                <th
                                                    class="px-3 py-2 border-b border-l bg-primary text-white text-center italic">
                                                    Weight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dmLog['normalized_matrix'] as $i => $row)
                                                <tr class="border-b dark:border-gray-700">
                                                    <th class="px-3 py-2 font-black border-r bg-gray-50/30 dark:bg-gray-800/20 text-center cursor-help"
                                                        title="{{ $dmLog['criteria_names'][$i] }}">
                                                        {{ $criteriaMapping[$dmLog['criteria_names'][$i]] }}
                                                    </th>
                                                    @foreach ($row as $value)
                                                        <td class="px-3 py-2 text-center text-gray-400">
                                                            {{ round($value, 3) }}</td>
                                                    @endforeach
                                                    <td
                                                        class="px-3 py-2 text-center font-black bg-primary/5 text-primary border-l">
                                                        {{ round($dmLog['weights'][$i], 4) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- REKAP GABUNGAN --}}
                <div
                    class="adaptive-card p-3 md:p-6 border-2 border-primary/20 shadow-xl rounded-2xl bg-white dark:bg-gray-900 overflow-hidden">
                    <h2 class="text-lg md:text-xl font-black text-primary mb-4 flex items-center gap-2">
                        Rekap Akhir (GM)
                    </h2>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <table class="w-full text-[10px] md:text-sm text-left table-fixed border-collapse">
                            @php
                                $totalCriteria = count($originalCriteria);
                                // Hitung lebar kolom: Kolom DM 15%, sisanya dibagi rata untuk kriteria
                                $colWidth = 85 / max($totalCriteria, 1);
                            @endphp

                            <colgroup>
                                <col style="width: 15%;"> {{-- Kolom DM --}}
                                @foreach ($originalCriteria as $name)
                                    <col style="width: {{ $colWidth }}%;"> {{-- Kolom Kriteria --}}
                                @endforeach
                            </colgroup>

                            <thead class="uppercase bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-2 py-3 border-b border-r dark:border-gray-700 font-black text-gray-500 text-center">
                                        DM</th>
                                    @foreach ($originalCriteria as $index => $name)
                                        <th class="px-1 py-3 border-b text-center text-primary font-black cursor-help truncate"
                                            title="{{ $name }}">
                                            C{{ $index + 1 }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                @foreach ($allDmLogs as $dmLog)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <th
                                            class="px-2 py-2 font-black border-r text-gray-400 bg-gray-50/30 dark:bg-gray-800/20 text-center">
                                            {{ $dmMapping[$dmLog['dm_id']] }}
                                        </th>
                                        @foreach ($dmLog['weights'] ?? [] as $weight)
                                            <td
                                                class="px-1 py-2 text-center font-medium tabular-nums border-r last:border-r-0 dark:border-gray-700">
                                                {{ number_format($weight, 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr class="bg-primary text-white">
                                    <th
                                        class="px-2 py-3 border-r border-white/10 text-center text-[9px] md:text-xs uppercase font-black">
                                        GM
                                    </th>
                                    @foreach ($originalCriteria as $idx => $name)
                                        <td class="px-1 py-3 text-center font-black text-xs md:text-base cursor-help tabular-nums border-r last:border-r-0 border-white/10"
                                            title="{{ $name }}">
                                            @php $val = $gmFinal[$name] ?? ($gmFinal[$idx] ?? null); @endphp
                                            {{ $val !== null ? number_format($val, 4) : '.0000' }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Info bantuan untuk layar sentuh --}}
                    <div class="mt-3 flex items-center justify-between text-[10px] text-gray-400 italic">
                        <span>* Kolom kriteria terbagi rata otomatis</span>
                        <span class="md:hidden">Tap angka untuk nama kriteria</span>
                    </div>
                </div>

            </div>
        @else
            <div
                class="adaptive-card p-20 text-center flex flex-col items-center justify-center rounded-3xl border-2 border-gray-200 border-dashed">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-gray-500 font-bold text-lg">Belum ada data log perhitungan.</p>
                <p class="text-gray-400 text-sm">Pastikan semua Decision Maker sudah mengisi penilaian.</p>
            </div>
        @endif
    </div>
@endsection
