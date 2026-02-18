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
    @include('admin.partials.session-nav')

    <div x-data="{ openDmProgress: false, dmMode: 'criteria' }">

        @if (!in_array(request('tab'), ['hasil-akhir', 'analisis']))
            <div
                class="animate-in fade-in slide-in-from-bottom-2 w-full px-4 py-4 md:px-6 md:py-6 duration-500 dark:bg-slate-900">
                <div class="w-full space-y-8">

                    {{-- SECTION 1 --}}
                    @include('control.partials.stats')

                    {{-- SECTION 2 --}}
                    @include('control.partials.progress')

                    {{-- SECTION 3 --}}
                    @include('control.partials.action-card', [
                        'decisionSession' => $decisionSession,
                        'assignedDmCount' => $assignedDmCount,
                        'dmPairwiseDone' => $dmPairwiseDone,
                        'dmAltDone' => $dmAltDone,
                        'canActivate' => $canActivate,
                    ])
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
                @include('analysis.index')
            </div>
        @endif


        {{-- SLIDE-OVER: DETAIL PROGRES DM --}}
        @include('control.partials.dm-slide', [
            'decisionSession' => $decisionSession,
        ])

    </div>

@endsection
