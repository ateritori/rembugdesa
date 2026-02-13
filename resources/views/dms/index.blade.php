@extends('layouts.dashboard')

@section('content')
    @include('dms.partials.nav', ['activeTab' => request('tab')])

    <div class="mt-6">
        @php $tab = request('tab', 'workspace'); @endphp

        {{-- ========================================================== --}}
        {{-- TAMPILAN 1: WORKSPACE (Hanya Header & Card Index)         --}}
        {{-- ========================================================== --}}
        @if ($tab === 'workspace')
            @include('dms.workspace.index') {{-- Mengarah ke folder workspace --}}
        @endif

        {{-- ========================================================== --}}
        {{-- TAMPILAN 2: PENILAIAN KRITERIA (Dinamis: Pairwise / Bobot) --}}
        {{-- ========================================================== --}}
        @if ($tab === 'penilaian-kriteria')
            <div class="animate-in slide-in-from-right-5">
                @if ($dmHasCompleted)
                    @include('dms.weights.dm-weights') {{-- Tampil Bobot Individu --}}
                @else
                    @include('dms.pairwise.index') {{-- Tampil Penilaian Pairwise --}}
                @endif
            </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAMPILAN 3: EVALUASI (Dinamis: Input / Hasil)            --}}
        {{-- ========================================================== --}}
        @if ($tab === 'evaluasi-alternatif')
            <div class="animate-in slide-in-from-right-5">
                @if ($decisionSession->dmEvaluationFinished ?? false)
                    @include('dms.alternative-evaluations.results') {{-- Tampil Hasil Penilaian --}}
                @else
                    @include('dms.alternative-evaluations.index') {{-- Tampil Form Penilaian --}}
                @endif
            </div>
        @endif

        {{-- ========================================================== --}}
        {{-- TAMPILAN 4: HASIL AKHIR                                   --}}
        {{-- ========================================================== --}}
        @if ($tab === 'hasil-akhir')
            <div class="animate-in slide-in-from-bottom-5">
                @include('dms.summary.index')
            </div>
        @endif
    </div>
@endsection
