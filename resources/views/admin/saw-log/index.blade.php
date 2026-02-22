@extends('layouts.dashboard')

@section('title', 'Log Perhitungan SAW')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span
                        class="bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md">
                        Metode SAW Audit
                    </span>
                </div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Log Perhitungan SAW
                </h1>
                <p class="adaptive-text-sub mt-1 max-w-xl text-sm leading-relaxed">
                    Audit detail transformasi nilai: Raw → Normalized (Min-Max) → Weighted → Skor Akhir.
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

        @if (!empty($sawLogs))

            @php
                $allSawLogs = $sawLogs;
                $selectedDm = request('dm_id');

                if (!request()->filled('dm_id') && isset($allSawLogs[0])) {
                    $selectedDm = $allSawLogs[0]['dm']->id;
                    $sawLogs = [$allSawLogs[0]];
                } elseif ($selectedDm === 'all') {
                    $sawLogs = $allSawLogs;
                } elseif ($selectedDm) {
                    $sawLogs = collect($allSawLogs)->where('dm.id', (int) $selectedDm)->values()->all();
                }

                $dmMapping = [];
                $counter = 1;
                foreach ($allSawLogs as $logItem) {
                    $dmMapping[$logItem['dm']->id] = 'D' . $counter++;
                }
            @endphp

            {{-- REFERENSI SECTION (SMART Style) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Kriteria & Weights --}}
                <div class="adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
                    <h3 class="text-[11px] font-black uppercase tracking-widest opacity-70 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        Referensi Kriteria & Bobot (W)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $weightModel = isset($decisionSession)
                                ? \App\Models\CriteriaWeight::where('decision_session_id', $decisionSession->id)
                                    ->whereNull('dm_id')
                                    ->first()
                                : null;
                            $weights = $weightModel->weights ?? [];
                        @endphp
                        @foreach ($decisionSession->criteria->where('is_active', true)->sortBy('order') as $i => $crit)
                            <div
                                class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl text-[11px] flex items-center gap-2 shadow-sm">
                                <span class="text-primary font-black italic">C{{ $i + 1 }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $crit->name }}</span>
                                <span
                                    class="text-indigo-600 dark:text-indigo-400 font-black px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-md">
                                    {{ number_format($weights[$crit->id] ?? 0, 4) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Decision Maker --}}
                <div class="adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
                    <h3 class="text-[11px] font-black uppercase tracking-widest opacity-70 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        Decision Maker (DM)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="?dm_id=all"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold border shadow-sm transition
                            {{ $selectedDm === 'all' ? 'bg-primary text-white border-primary' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                            Semua
                        </a>

                        @foreach ($allSawLogs as $logItem)
                            @php $dmId = $logItem['dm']->id; @endphp
                            <a href="?dm_id={{ $dmId }}"
                                class="px-3 py-1.5 rounded-xl text-[11px] font-bold flex items-center gap-2 border shadow-sm transition
                                {{ (!request('dm_id') && $loop->first) || (string) $selectedDm === (string) $dmId
                                    ? 'bg-orange-500 text-white border-orange-500'
                                    : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                                <span class="font-black italic">
                                    {{ $dmMapping[$dmId] }}
                                </span>
                                <span>
                                    {{ $logItem['dm']->name }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- INFO AKTIF DM --}}
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">
                    @if ($selectedDm === 'all')
                        Menampilkan: <span class="text-primary">Semua Decision Maker</span>
                    @else
                        @php
                            $activeDm = collect($allSawLogs)->firstWhere('dm.id', (int) $selectedDm);
                        @endphp
                        Menampilkan:
                        <span class="text-primary">
                            {{ $dmMapping[$selectedDm] ?? '' }}
                            {{ $activeDm['dm']->name ?? '' }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="space-y-12">
                @foreach ($sawLogs as $log)
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></div>

                            <div
                                class="flex items-center gap-3 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-2xl border dark:border-slate-700">
                                <span
                                    class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-black text-sm shadow-lg shadow-primary/20">
                                    {{ $dmMapping[$log['dm']->id] }}
                                </span>
                                <h2 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">
                                    Audit Perhitungan {{ $log['dm']->name }}
                                </h2>
                            </div>

                            <div class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></div>
                        </div>

                        <div class="adaptive-card overflow-hidden border shadow-xl rounded-3xl bg-white dark:bg-slate-900">
                            <div class="overflow-x-auto">
                                <table class="w-full text-[11px] text-center border-collapse table-fixed">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-800 border-b dark:border-slate-700">
                                            <th
                                                class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-800 px-6 py-4 text-left font-black uppercase tracking-wider text-slate-500 border-r dark:border-slate-700 w-[220px]">
                                                Alternatif
                                            </th>
                                            <th
                                                class="px-2 py-4 font-black uppercase tracking-wider text-slate-400 border-r dark:border-slate-700 w-[80px]">
                                                Metrik
                                            </th>
                                            @php
                                                $weightModel = \App\Models\CriteriaWeight::where(
                                                    'decision_session_id',
                                                    $decisionSession->id,
                                                )
                                                    ->whereNull('dm_id')
                                                    ->first();
                                                $headerWeights = $weightModel->weights ?? [];
                                                $orderedCriteria = $decisionSession->criteria
                                                    ->where('is_active', true)
                                                    ->sortBy('order')
                                                    ->values();
                                            @endphp
                                            @foreach ($log['criteria_names'] as $index => $name)
                                                @php
                                                    $critModel = $orderedCriteria[$index] ?? null;
                                                    $weight = $critModel ? $headerWeights[$critModel->id] ?? 0 : 0;
                                                @endphp
                                                <th
                                                    class="px-2 py-4 font-black text-primary border-r dark:border-slate-700 w-[110px]">
                                                    <div class="flex flex-col items-center leading-tight">
                                                        <span>C{{ $index + 1 }}</span>
                                                        <span class="text-[9px] font-bold text-indigo-500">
                                                            W: {{ number_format($weight, 4) }}
                                                        </span>
                                                    </div>
                                                </th>
                                            @endforeach
                                            <th
                                                class="px-4 py-4 bg-primary text-white font-black uppercase tracking-wider border-r dark:border-slate-700 w-[110px]">
                                                Final Score
                                            </th>
                                            <th
                                                class="px-2 py-4 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-black uppercase tracking-wider w-[60px]">
                                                Rank
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y dark:divide-slate-700">
                                        @foreach ($log['alternatives'] as $alt)
                                            <tr class="border-b-2 dark:border-slate-700">
                                                <td class="sticky left-0 z-10 bg-white dark:bg-slate-900 px-6 py-4 text-left border-r dark:border-slate-700"
                                                    rowspan="3">
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="font-black text-slate-800 dark:text-slate-100 text-sm leading-snug">
                                                            {{ $alt['alternative']->name }}
                                                        </span>

                                                        @if (!empty($alt['alternative']->code))
                                                            <span
                                                                class="px-1.5 py-0.5 text-[9px] font-black uppercase tracking-tight bg-slate-100 dark:bg-slate-800 text-primary rounded border dark:border-slate-700">
                                                                {{ $alt['alternative']->code }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-2 py-2 font-bold text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 text-[9px] uppercase border-r dark:border-slate-700">
                                                    Raw
                                                </td>
                                                @foreach ($alt['criteria'] as $crit)
                                                    <td class="px-2 py-2 border-r dark:border-slate-700 font-mono">
                                                        <div class="flex flex-col items-center leading-tight">
                                                            <span>{{ $crit['raw'] }}</span>
                                                            @if (!empty($crit['semantic']))
                                                                <span class="text-[9px] text-slate-400 italic">
                                                                    {{ $crit['semantic'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endforeach
                                                <td rowspan="3"
                                                    class="px-4 py-4 bg-primary/5 border-r dark:border-slate-700">
                                                    <span class="text-base font-black text-primary">
                                                        {{ number_format($alt['total_score'], 4) }}
                                                    </span>
                                                </td>
                                                <td rowspan="3" class="px-2 py-4">
                                                    <span
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-800 text-white font-black">
                                                        {{ $alt['rank'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    class="px-2 py-2 font-bold text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 text-[9px] uppercase border-r dark:border-slate-700">
                                                    Normalized
                                                </td>
                                                @foreach ($alt['criteria'] as $crit)
                                                    <td
                                                        class="px-2 py-2 border-r dark:border-slate-700 text-indigo-500 font-bold">
                                                        {{ number_format($crit['normalized'], 4) }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                            <tr class="bg-primary/5">
                                                <td
                                                    class="px-2 py-2 font-black text-primary bg-primary/10 text-[9px] uppercase border-r dark:border-slate-700">
                                                    N × W
                                                </td>
                                                @foreach ($alt['criteria'] as $crit)
                                                    <td class="px-2 py-2 border-r dark:border-slate-700 font-black">
                                                        {{ number_format($crit['weighted'], 4) }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="adaptive-card p-20 text-center border-dashed border-2 rounded-3xl opacity-50">
                <p class="text-slate-500 font-black uppercase tracking-widest text-xs">Belum ada data log SAW.</p>
            </div>
        @endif
    </div>
@endsection
