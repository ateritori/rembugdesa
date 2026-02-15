@extends('layouts.dashboard')

@section('content')
    @php
        // 1. Definisikan tab aktif
        $tab = request('tab', 'workspace');

        // 2. Logic untuk switch tampilan ke mode form jika user klik tombol edit
        $isEditing = request('edit') == 1;

        // 3. PENGAMAN: Pastikan variabel ini ada, kalau dari controller gak ada, set default false
        // Ini supaya @include partials.nav di bawah gak teriak "Undefined variable"
        $dmHasCompleted = $dmHasCompleted ?? false;
        $hasCompletedEvaluation = $hasCompletedEvaluation ?? false;
    @endphp

    {{-- Navigasi Tab --}}
    {{-- Kita kirim $dmHasCompleted ke dalam partial nav --}}
    @include('dms.partials.nav', [
        'activeTab' => $tab,
        'dmHasCompleted' => $dmHasCompleted,
    ])

    <div class="mt-6">
        {{-- 1. WORKSPACE --}}
        @if ($tab === 'workspace')
            @include('dms.workspace.index')
        @endif

        {{-- 2. PENILAIAN KRITERIA (PAIRWISE / BOBOT) --}}
        @if ($tab === 'penilaian-kriteria')
            <div class="animate-in slide-in-from-right-5">

                {{-- FASE SCORING: tampilkan Bobot Kelompok (GM) --}}
                @if ($decisionSession->status !== 'configured')
                    @include('dms.group-weights.index')

                    {{-- FASE PAIRWISE --}}
                @else
                    @if ($dmHasCompleted && !$isEditing)
                        @include('dms.weights.dm-weights')
                    @else
                        @include('dms.pairwise.index')
                    @endif
                @endif

            </div>
        @endif

        {{-- 3. EVALUASI ALTERNATIF --}}
        @if ($tab === 'evaluasi-alternatif')
            <div class="animate-in slide-in-from-right-5">
                {{-- Jika sudah isi dan tidak sedang edit -> tampilkan hasil --}}
                {{-- Jika belum isi atau sedang edit -> tampilkan form --}}
                @if ($hasCompletedEvaluation && !$isEditing)
                    @include('dms.alternative-evaluations.results')
                @else
                    @include('dms.alternative-evaluations.index')
                @endif
            </div>
        @endif

        {{-- 4. HASIL AKHIR --}}
        @if ($tab === 'hasil-akhir')
            <div class="animate-in slide-in-from-bottom-5">
                @include('dms.partials.result')
            </div>
        @endif
    </div>
@endsection
