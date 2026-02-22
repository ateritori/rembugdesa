@extends('layouts.dashboard')

@section('title', 'Log Perhitungan SMART')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span
                        class="bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md">
                        Metode SMART Audit
                    </span>
                </div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Log Perhitungan SMART
                </h1>
                <p class="adaptive-text-sub mt-1 max-w-xl text-sm leading-relaxed">
                    Audit detail transformasi nilai: Raw &rarr; Utility &rarr; Weighted &rarr; Skor Akhir.
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

        @if (!empty($smartLogs))
            @php
                $criteriaNames = $criteriaNames ?? [];
                $dmMapping = [];
                $counter = 1;
                foreach ($smartLogs as $log) {
                    $dmMapping[$log['dm']->id] = 'D' . $counter++;
                }
            @endphp

            @php
                $allSmartLogs = $smartLogs;
                $selectedDm = request('dm_id');

                // DEFAULT: jika tidak ada atau kosong dm_id → tampilkan DM pertama saja
                if (!request()->filled('dm_id') && isset($allSmartLogs[0])) {
                    $selectedDm = $allSmartLogs[0]['dm']->id;
                    $smartLogs = [$allSmartLogs[0]];
                }
                // Jika pilih semua
                elseif ($selectedDm === 'all') {
                    $smartLogs = $allSmartLogs;
                }
                // Jika pilih DM tertentu
                elseif ($selectedDm) {
                    $smartLogs = collect($allSmartLogs)->where('dm.id', (int) $selectedDm)->values()->all();
                }
            @endphp

            {{-- REFERENSI SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Kriteria & Weights --}}
                <div class="adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
                    <h3 class="text-[11px] font-black uppercase tracking-widest opacity-70 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        Referensi Kriteria & Bobot (W)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $groupWeightModel = isset($decisionSession)
                                ? \App\Models\CriteriaWeight::where('decision_session_id', $decisionSession->id)
                                    ->whereNull('dm_id')
                                    ->first()
                                : null;
                            $groupWeights = $groupWeightModel->weights ?? [];
                        @endphp
                        @foreach ($decisionSession->criteria->where('is_active', true)->sortBy('order') as $i => $crit)
                            <div
                                class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl text-[11px] flex items-center gap-2 shadow-sm">
                                <span class="text-primary font-black italic">C{{ $i + 1 }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $crit->name }}</span>
                                <span
                                    class="text-indigo-600 dark:text-indigo-400 font-black px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-md">
                                    {{ number_format($groupWeights[$crit->id] ?? 0, 4) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Decision Makers --}}
                <div class="adaptive-card p-5 border shadow-sm rounded-2xl bg-white dark:bg-slate-900/50">
                    <h3 class="text-[11px] font-black uppercase tracking-widest opacity-70 mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        Decision Maker (DM)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        {{-- Tombol Semua --}}
                        <a href="?dm_id=all"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold border shadow-sm transition
                            {{ $selectedDm === 'all' ? 'bg-primary text-white border-primary' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                            Semua
                        </a>

                        @foreach ($allSmartLogs as $log)
                            @php $dmId = $log['dm']->id; @endphp
                            <a href="?dm_id={{ $dmId }}"
                                class="px-3 py-1.5 rounded-xl text-[11px] font-bold flex items-center gap-2 border shadow-sm transition
                                {{ (!request('dm_id') && $loop->first) || (string) $selectedDm === (string) $dmId
                                    ? 'bg-orange-500 text-white border-orange-500'
                                    : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                                <span class="font-black italic">
                                    {{ $dmMapping[$dmId] }}
                                </span>
                                <span>
                                    {{ $log['dm']->name }}
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
                            $activeDm = collect($allSmartLogs)->firstWhere('dm.id', (int) $selectedDm);
                        @endphp
                        Menampilkan:
                        <span class="text-primary">
                            {{ $dmMapping[$selectedDm] ?? '' }}
                            {{ $activeDm['dm']->name ?? '' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- LOOP PER DM --}}
            <div class="space-y-12">
                @foreach ($smartLogs as $log)
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
                                                $weightModel = isset($decisionSession)
                                                    ? \App\Models\CriteriaWeight::where(
                                                        'decision_session_id',
                                                        $decisionSession->id,
                                                    )
                                                        ->whereNull('dm_id')
                                                        ->first()
                                                    : null;
                                                $headerWeights = $weightModel->weights ?? [];
                                            @endphp
                                            @foreach ($criteriaNames as $index => $name)
                                                @php
                                                    $criteria =
                                                        $decisionSession->criteria
                                                            ->where('is_active', true)
                                                            ->sortBy('order')
                                                            ->values()[$index] ?? null;
                                                    $weight = $criteria ? $headerWeights[$criteria->id] ?? 0 : 0;
                                                @endphp
                                                <th class="px-2 py-4 font-black text-primary border-r dark:border-slate-700 w-[110px]"
                                                    title="{{ $name }}">
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
                                                {{-- Name Column with Code --}}
                                                <td class="sticky left-0 z-10 bg-white dark:bg-slate-900 px-6 py-4 text-left border-r dark:border-slate-700"
                                                    rowspan="4">
                                                    <div class="flex flex-col gap-1">
                                                        <span
                                                            class="font-black text-slate-800 dark:text-slate-100 text-sm leading-snug">
                                                            {{ $alt['alternative']->name ?? 'Alt #' . $loop->iteration }}
                                                        </span>
                                                        @if (isset($alt['alternative']->code))
                                                            <span
                                                                class="w-fit px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-primary font-black text-[9px] rounded border dark:border-slate-700 uppercase tracking-tighter">
                                                                {{ $alt['alternative']->code }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                {{-- Semantic Row --}}
                                                <td
                                                    class="px-2 py-2 font-bold text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 text-[9px] uppercase border-r dark:border-slate-700">
                                                    Semantic</td>
                                                @foreach ($alt['criteria'] as $crit)
                                                    <td class="px-2 py-2 border-r dark:border-slate-700 italic text-slate-500 truncate"
                                                        title="{{ $crit['semantic'] ?? '-' }}">
                                                        {{ $crit['semantic'] ?? '-' }}
                                                    </td>
                                                @endforeach

                                                {{-- Result Score --}}
                                                <td class="px-4 py-4 text-center border-r dark:border-slate-700 bg-primary/5"
                                                    rowspan="4">
                                                    <span
                                                        class="text-base font-black text-primary">{{ number_format($alt['total_score'], 4) }}</span>
                                                </td>

                                                {{-- Rank --}}
                                                <td class="px-2 py-4 text-center" rowspan="4">
                                                    <span
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-800 text-white font-black">
                                                        {{ $alt['rank'] }}
                                                    </span>
                                                </td>
                                            </tr>

                                            {{-- Raw Row --}}
                                            <tr>
                                                <td
                                                    class="px-2 py-2 font-bold text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 text-[9px] uppercase border-r dark:border-slate-700">
                                                    Raw</td>
                                                @foreach ($alt['criteria'] as $crit)
                                                    <td
                                                        class="px-2 py-2 border-r dark:border-slate-700 font-mono text-slate-600 dark:text-slate-400">
                                                        {{ $crit['raw'] }}</td>
                                                @endforeach
                                            </tr>

                                            {{-- Utility Row --}}
                                            <tr>
                                                <td
                                                    class="px-2 py-2 font-bold text-slate-400 bg-slate-50/50 dark:bg-slate-800/30 text-[9px] uppercase border-r dark:border-slate-700">
                                                    Utility</td>
                                                @foreach ($alt['criteria'] as $crit)
                                                    <td
                                                        class="px-2 py-2 border-r dark:border-slate-700 text-indigo-500 font-bold">
                                                        {{ number_format($crit['utility'], 4) }}</td>
                                                @endforeach
                                            </tr>

                                            {{-- Weighted Row --}}
                                            <tr class="bg-primary/5">
                                                <td
                                                    class="px-2 py-2 font-black text-primary bg-primary/10 text-[9px] uppercase border-r dark:border-slate-700">
                                                    U × W</td>
                                                @foreach ($alt['criteria'] as $crit)
                                                    <td
                                                        class="px-2 py-2 border-r dark:border-slate-700 font-black text-slate-800 dark:text-slate-100">
                                                        {{ number_format($crit['weighted'], 4) }}</td>
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
            {{-- Empty State --}}
            <div class="adaptive-card p-20 text-center border-dashed border-2 rounded-3xl opacity-50">
                <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-6a2 2 0 012-2h6m0 0l-3-3m3 3l-3 3M5 7h6" />
                </svg>
                <p class="text-slate-500 font-black uppercase tracking-widest text-xs">Belum ada data log SMART.</p>
            </div>
        @endif
    </div>
@endsection
