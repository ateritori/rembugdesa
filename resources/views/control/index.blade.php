@extends('layouts.dashboard')

@section('title', 'Kontrol Sesi')

@section('content')

    {{-- 1. HEADER LOGIC --}}
    @php
        $activeCriteriaCount = $decisionSession->criterias->where('is_active', true)->count();
        $activeAlternativesCount = $decisionSession->alternatives->where('is_active', true)->count();

        $dmPairwiseDone = $decisionSession->dms
            ->filter(function ($dm) use ($decisionSession) {
                return \Illuminate\Support\Facades\DB::table('criteria_weights')
                    ->where('decision_session_id', $decisionSession->id)
                    ->where('dm_id', $dm->id)
                    ->exists();
            })
            ->count();

        $dmAltDone = $decisionSession->dms
            ->filter(function ($dm) use ($decisionSession) {
                return \Illuminate\Support\Facades\DB::table('alternative_evaluations')
                    ->where('decision_session_id', $decisionSession->id)
                    ->where('dm_id', $dm->id)
                    ->exists();
            })
            ->count();

        $canActivate = $activeCriteriaCount >= 2 && $activeAlternativesCount >= 2 && $assignedDmCount >= 1;
    @endphp

    {{-- TAB NAVIGASI SESI --}}
    @include('decision-sessions.partials.nav')

    @if (!in_array(request('tab'), ['hasil-akhir', 'analisis']))
        <div
            class="animate-in fade-in slide-in-from-bottom-2 w-full px-4 py-4 md:px-6 md:py-6 duration-500 dark:bg-slate-900">
            <div class="w-full space-y-8">

                {{-- SECTION 1: DASHBOARD RINGKASAN --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @php
                        $stats = [
                            [
                                'label' => 'Status Sesi',
                                'value' => $decisionSession->status,
                                'icon' =>
                                    'M11.241 4.817c.121-.696.927-1.023 1.511-.524l9.464 8.112a.75.75 0 0 1-.428 1.328H18v6.25a.75.75 0 0 1-.75.75H14.5a.75.75 0 0 1-.75-.75v-4.5a.25.25 0 0 0-.25-.25h-3a.25.25 0 0 0-.25.25v4.5a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V13.733H3.213a.75.75 0 0 1-.428-1.328l9.464-8.112c.125-.107.292-.158.459-.15z',
                                'color' => 'from-blue-600 to-indigo-600',
                                'shadow' => 'shadow-blue-500/20',
                            ],
                            [
                                'label' => 'Kriteria Aktif',
                                'value' => $activeCriteriaCount,
                                'icon' =>
                                    'M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6z',
                                'color' => 'from-indigo-600 to-violet-600',
                                'shadow' => 'shadow-indigo-500/20',
                            ],
                            [
                                'label' => 'Alternatif Aktif',
                                'value' => $activeAlternativesCount,
                                'icon' =>
                                    'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
                                'color' => 'from-purple-600 to-fuchsia-600',
                                'shadow' => 'shadow-purple-500/20',
                            ],
                            [
                                'label' => 'Total DM',
                                'value' => $assignedDmCount,
                                'icon' =>
                                    'M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18.75a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75ZM5.106 17.834a.75.75 0 0 0 1.06 1.06l1.591-1.59a.75.75 0 1 0-1.06-1.061l-1.591 1.59ZM3 12a.75.75 0 0 1 .75-.75h2.25a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm3.166-5.894a.75.75 0 0 0 1.06 1.06l1.59-1.591a.75.75 0 1 0-1.06-1.061l-1.591 1.59Z',
                                'color' => 'from-emerald-600 to-teal-600',
                                'shadow' => 'shadow-emerald-500/20',
                            ],
                        ];
                    @endphp

                    @foreach ($stats as $stat)
                        <div
                            class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-500 hover:-translate-y-1 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
                            <div class="relative flex items-center gap-4">
                                {{-- Box Icon yang dipertegas --}}
                                <div
                                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $stat['color'] }} {{ $stat['shadow'] }} shadow-lg transition-all duration-500 group-hover:rotate-6 group-hover:scale-110">
                                    <svg class="h-8 w-8 text-white fill-current" viewBox="0 0 24 24">
                                        <path d="{{ $stat['icon'] }}" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[9px] font-black uppercase tracking-[.2em] text-slate-400">
                                        {{ $stat['label'] }}</p>
                                    <p
                                        class="text-lg font-black uppercase tracking-tight text-slate-800 dark:text-slate-100">
                                        {{ $stat['value'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- SECTION 2: PROGRES BAR (Firm & Aligned) --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    @php
                        $pairwisePercent = $assignedDmCount > 0 ? ($dmPairwiseDone / $assignedDmCount) * 100 : 0;
                        $altPercent = $assignedDmCount > 0 ? ($dmAltDone / $assignedDmCount) * 100 : 0;
                    @endphp

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 transition-all duration-300 hover:border-slate-300">
                        <div class="mb-4 flex items-end justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">Progres
                                        Pairwise</p>
                                </div>
                                <h4 class="text-sm font-bold text-slate-400">Pembobotan Kriteria</h4>
                            </div>
                            <span
                                class="text-2xl font-black tracking-tighter text-slate-800 dark:text-white">{{ round($pairwisePercent) }}%</span>
                        </div>
                        <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-900">
                            <div class="h-full bg-slate-800 transition-all duration-1000 dark:bg-blue-500"
                                style="width: {{ $pairwisePercent }}%"></div>
                        </div>
                        <div
                            class="mt-3 flex justify-between text-[9px] font-bold uppercase tracking-widest text-slate-400">
                            <span>Status: {{ $dmPairwiseDone }} / {{ $assignedDmCount }} DM</span>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 transition-all duration-300 hover:border-slate-300">
                        <div class="mb-4 flex items-end justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">Penilaian
                                        Alternatif</p>
                                </div>
                                <h4 class="text-sm font-bold text-slate-400">Evaluasi Alternatif</h4>
                            </div>
                            <span
                                class="text-2xl font-black tracking-tighter text-slate-800 dark:text-white">{{ round($altPercent) }}%</span>
                        </div>
                        <div class="h-3 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-900">
                            <div class="h-full bg-emerald-500 transition-all duration-1000"
                                style="width: {{ $altPercent }}%"></div>
                        </div>
                        <div
                            class="mt-3 flex justify-between text-[9px] font-bold uppercase tracking-widest text-slate-400">
                            <span>Status: {{ $dmAltDone }} / {{ $assignedDmCount }} DM</span>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: ACTION CENTER --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 px-1">
                        <span class="h-1.5 w-6 rounded-full bg-slate-900 dark:bg-primary"></span>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-900 dark:text-primary">Control
                            Center</p>
                    </div>

                    @php
                        $currentStatus = $decisionSession->status;
                        $actionConfig = [
                            'draft' => [
                                'phase' => '01',
                                'label' => 'Preparation',
                                'title' => 'Aktifkan Sesi',
                                'ready_msg' => 'Parameter siap. Klik untuk membuka akses penilaian bagi DM.',
                                'wait_msg' => 'Lengkapi minimal 2 Kriteria, 2 Alternatif, dan 1 DM untuk mengaktifkan.',
                                'path' => 'control.partials.buttons.draft-activate',
                                'check' => $canActivate,
                            ],
                            'configured' => [
                                'phase' => '02',
                                'label' => 'Configured',
                                'title' => 'Buka Penilaian Alternatif',
                                'ready_msg' => 'Pairwise selesai. Klik untuk memulai tahap evaluasi alternatif.',
                                'wait_msg' =>
                                    'Menunggu ' .
                                    ($assignedDmCount - $dmPairwiseDone) .
                                    ' DM lagi untuk menyelesaikan Pairwise.',
                                'path' => 'control.partials.buttons.start-alternative',
                                'check' => $dmPairwiseDone >= $assignedDmCount,
                            ],
                            'scoring' => [
                                'phase' => '03',
                                'label' => 'Scoring',
                                'title' => 'Finalisasi Sesi',
                                'ready_msg' => 'Seluruh evaluasi selesai. Klik untuk mengunci dan hitung hasil akhir.',
                                'wait_msg' =>
                                    'Menunggu ' .
                                    ($assignedDmCount - $dmAltDone) .
                                    ' DM lagi menyelesaikan penilaian alternatif.',
                                'path' => 'control.partials.buttons.close-scoring',
                                'check' => $dmAltDone >= $assignedDmCount,
                            ],
                            'closed' => [
                                'phase' => '04',
                                'label' => 'Closed',
                                'title' => 'Keputusan Final',
                                'ready_msg' => 'Sesi telah dikunci. Hasil akhir dapat dilihat di tab analisis.',
                                'wait_msg' => '',
                                'path' => 'control.partials.buttons.view-result',
                                'check' => true,
                            ],
                        ];
                        $act = $actionConfig[$currentStatus] ?? null;
                    @endphp

                    @if ($act)
                        <div
                            class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            <div class="flex flex-col items-center justify-between gap-6 p-6 md:flex-row md:p-8">
                                <div class="flex items-center gap-6">
                                    {{-- Phase Number --}}
                                    <div
                                        class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-slate-900 font-black text-white shadow-lg transition-transform duration-500 group-hover:scale-105 dark:bg-slate-700">
                                        <span class="text-[10px] leading-none opacity-50">PH</span>
                                        <span class="text-xl leading-none">{{ $act['phase'] }}</span>
                                    </div>

                                    {{-- Text Info --}}
                                    <div>
                                        <span
                                            class="inline-block rounded-md bg-slate-100 px-2 py-0.5 text-[8px] font-black uppercase tracking-widest text-slate-500 dark:bg-slate-900">
                                            {{ $act['label'] }}
                                        </span>
                                        <h3
                                            class="mt-1 text-xl font-black uppercase tracking-tight text-slate-800 dark:text-white">
                                            {{ $act['title'] }}
                                        </h3>

                                        {{-- Logic Deskripsi Tunggal --}}
                                        <div class="mt-2 flex items-center gap-2">
                                            @if (!$act['check'])
                                                <span class="flex h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
                                                <p class="text-xs font-bold text-amber-600 tracking-tight italic">
                                                    {{ $act['wait_msg'] }}
                                                </p>
                                            @else
                                                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                                <p class="text-xs font-bold text-slate-500 tracking-tight">
                                                    {{ $act['ready_msg'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Button Action --}}
                                <div
                                    class="w-full transition-all duration-300 @if ($act['check']) hover:scale-105 @else opacity-50 grayscale @endif md:w-auto">
                                    @include($act['path'], ['canActivate' => $act['check']])
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
    @endif

    {{-- TAB CONTENT: HASIL & ANALISIS --}}
    @if (request('tab') === 'hasil-akhir' && $decisionSession->status === 'closed')
        <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
            @include('control.result')
        </div>
    @endif

    @if (request('tab') === 'analisis' && $decisionSession->status === 'closed')
        <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
            @include('control.analysis')
        </div>
    @endif

@endsection
