@extends('layouts.dashboard')

@section('title', 'Log Perhitungan SMART')

@section('content')
    <div class="animate-in fade-in space-y-8 pb-10 duration-500">

        {{-- HEADER --}}
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="adaptive-text-main text-3xl font-black leading-tight tracking-tight">
                    Log Perhitungan SMART
                </h1>
                <p class="adaptive-text-sub mt-2 max-w-xl text-sm leading-relaxed">
                    Audit perhitungan Raw → Utility → Weighted → Ranking tiap Decision Maker.
                </p>
            </div>

            <a href="{{ route('decision-sessions.index') }}"
                class="bg-primary shadow-primary/20 group flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-black text-white shadow-lg transition-all hover:scale-105 active:scale-95">
                <span>Kembali ke Sesi</span>
            </a>
        </div>

        @if (!empty($smartLogs))

            @php
                // Use criteria names sent from controller (already ordered)
                $criteriaNames = $criteriaNames ?? [];

                $dmMapping = [];
                $counter = 1;
                foreach ($smartLogs as $log) {
                    $dmMapping[$log['dm']->id] = 'D' . $counter++;
                }
            @endphp

            {{-- LEGENDA --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Kriteria --}}
                <div class="adaptive-card p-4 border rounded-2xl bg-gray-50/50">
                    <h3 class="text-xs font-bold uppercase tracking-widest mb-3">
                        Referensi Kriteria (C)
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
                            @php
                                $weight = $groupWeights[$crit->id] ?? 0;
                            @endphp
                            <div class="px-3 py-1.5 bg-white border rounded-lg shadow-sm text-xs flex items-center gap-2">
                                <span class="text-primary font-black italic">
                                    C{{ $i + 1 }}
                                </span>
                                <span class="text-gray-400">=</span>
                                <span class="font-semibold text-gray-700">
                                    {{ $crit->name }}
                                </span>
                                <span class="ml-2 text-indigo-600 font-bold">
                                    (W: {{ number_format($weight, 4) }})
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Decision Maker --}}
                <div class="adaptive-card p-4 border rounded-2xl bg-gray-50/50">
                    <h3 class="text-xs font-bold uppercase tracking-widest mb-3">
                        Referensi Decision Maker (D)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($smartLogs as $log)
                            <div class="px-3 py-1.5 bg-white border rounded-lg shadow-sm text-xs">
                                <span class="text-orange-500 font-black italic">
                                    {{ $dmMapping[$log['dm']->id] }}
                                </span>
                                <span>{{ $log['dm']->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LOOP PER DM --}}
            <div class="grid grid-cols-1 gap-8">

                @foreach ($smartLogs as $log)
                    @php
                        $currentD = $dmMapping[$log['dm']->id];
                    @endphp

                    <div class="adaptive-card p-5 border rounded-2xl">

                        <div class="mb-4 flex items-center gap-3">
                            <span
                                class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-black">
                                {{ $currentD }}
                            </span>
                            <h2 class="text-xl font-black text-primary">
                                Perhitungan {{ $currentD }}
                            </h2>
                        </div>

                        <div class="overflow-x-auto rounded-xl border">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 border-r w-40">Alternatif</th>
                                        <th class="px-3 py-2 border-r w-32 text-center">Informasi</th>

                                        @foreach ($criteriaNames as $index => $name)
                                            <th class="px-3 py-2 text-center border-r" title="{{ $name }}">
                                                C{{ $index + 1 }}
                                            </th>
                                        @endforeach

                                        <th class="px-3 py-2 text-center bg-primary text-white border-r">
                                            Total
                                        </th>
                                        <th class="px-3 py-2 text-center bg-gray-200">
                                            Rank
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($log['alternatives'] as $alt)
                                        {{-- ROW 1 : SEMANTIC --}}
                                        <tr class="border-b">
                                            <td class="px-3 py-2 font-bold align-top border-r" rowspan="4">
                                                {{ $alt['alternative']->name ?? 'Alternatif #' . $loop->iteration }}
                                            </td>

                                            <td class="px-3 py-2 font-semibold border-r bg-gray-50">
                                                Semantic
                                            </td>

                                            @foreach ($alt['criteria'] as $crit)
                                                <td class="px-3 py-2 text-center border-r">
                                                    {{ $crit['semantic'] ?? '-' }}
                                                </td>
                                            @endforeach

                                            <td class="px-3 py-2 text-center font-black text-primary align-top border-r"
                                                rowspan="4">
                                                {{ number_format($alt['total_score'], 4) }}
                                            </td>

                                            <td class="px-3 py-2 text-center font-black align-top" rowspan="4">
                                                {{ $alt['rank'] }}
                                            </td>
                                        </tr>

                                        {{-- ROW 2 : RAW VALUE --}}
                                        <tr class="border-b">
                                            <td class="px-3 py-2 font-semibold border-r bg-gray-50">
                                                Raw Value
                                            </td>

                                            @foreach ($alt['criteria'] as $crit)
                                                <td class="px-3 py-2 text-center border-r">
                                                    {{ $crit['raw'] }}
                                                </td>
                                            @endforeach
                                        </tr>

                                        {{-- ROW 3 : UTILITY VALUE --}}
                                        <tr class="border-b">
                                            <td class="px-3 py-2 font-semibold border-r bg-gray-50">
                                                Utility Value
                                            </td>

                                            @foreach ($alt['criteria'] as $crit)
                                                <td class="px-3 py-2 text-center border-r">
                                                    {{ number_format($crit['utility'], 4) }}
                                                </td>
                                            @endforeach
                                        </tr>

                                        {{-- ROW 4 : U × W --}}
                                        <tr class="border-b bg-gray-50/40">
                                            <td class="px-3 py-2 font-semibold border-r">
                                                U × W
                                            </td>

                                            @foreach ($alt['criteria'] as $crit)
                                                <td class="px-3 py-2 text-center font-semibold border-r">
                                                    {{ number_format($crit['weighted'], 4) }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                @endforeach

            </div>
        @else
            <div class="adaptive-card p-20 text-center border-dashed border-2 rounded-3xl">
                <p class="text-gray-500 font-bold text-lg">
                    Belum ada data log SMART.
                </p>
            </div>
        @endif

    </div>
@endsection
