@extends('layouts.dashboard')

@section('content')
    @php
        $tab = request('tab', 'workspace');
        $isEditing = request('edit') == 1;
        $dmHasCompleted = $dmHasCompleted ?? false;
        $hasCompletedEvaluation = $hasCompletedEvaluation ?? false;
    @endphp

    {{-- Navigasi --}}
    @include('dms.partials.nav', [
        'activeTab' => $tab,
        'dmHasCompleted' => $dmHasCompleted,
    ])

    <div class="mt-6">
        {{-- 1. TAB WORKSPACE --}}
        @if ($tab === 'workspace')
            @include('dms.workspace.index')

            {{-- 2. TAB PENILAIAN KRITERIA --}}
        @elseif ($tab === 'penilaian-kriteria')
            <div class="animate-in slide-in-from-right-5">
                @if ($isEditing || !$dmHasCompleted)
                    @include('dms.pairwise.index')
                @elseif (!in_array($decisionSession->status, ['draft', 'configured']))
                    @include('dms.group-weights.index')
                @else
                    @include('dms.weights.dm-weights')
                @endif
            </div>

            {{-- 3. TAB EVALUASI ALTERNATIF --}}
        @elseif ($tab === 'evaluasi-alternatif')
            <div class="animate-in slide-in-from-right-5">
                @if ($hasCompletedEvaluation && !$isEditing)
                    @include('dms.alternative-evaluations.results')
                @else
                    @include('dms.alternative-evaluations.index')
                @endif
            </div>

            {{-- 4. TAB HASIL AKHIR --}}
        @elseif ($tab === 'hasil-akhir')
            <div class="animate-in slide-in-from-bottom-5">
                @include('dms.partials.result')
            </div>
        @endif
    </div>
@endsection
