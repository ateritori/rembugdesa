@extends('layouts.dashboard')

@section('content')
  {{-- Navigasi Tab --}}
  @include('dms.partials.nav', ['activeTab' => request('tab')])

  <div class="mt-6">
    @php
      $tab = request('tab', 'workspace');
      // Logic untuk switch tampilan ke mode form jika user klik tombol edit
      $isEditing = request('edit') == 1;
    @endphp

    {{-- 1. WORKSPACE --}}
    @if ($tab === 'workspace')
      @include('dms.workspace.index')
    @endif

    {{-- 2. PENILAIAN KRITERIA (PAIRWISE / BOBOT) --}}
    @if ($tab === 'penilaian-kriteria')
      <div class="animate-in slide-in-from-right-5">
        {{-- Jika sudah isi dan tidak sedang mode edit -> tampilkan hasil (Weights) --}}
        {{-- Jika belum isi atau sedang mode edit -> tampilkan form (Pairwise) --}}
        @if ($dmHasCompleted && !$isEditing)
          @include('dms.weights.dm-weights')
        @else
          @include('dms.pairwise.index')
        @endif
      </div>
    @endif

    {{-- 3. EVALUASI ALTERNATIF --}}
    @if ($tab === 'evaluasi-alternatif')
      <div class="animate-in slide-in-from-right-5">
        @if ($decisionSession->dmEvaluationFinished ?? false)
          @include('dms.alternative-evaluations.results')
        @else
          @include('dms.alternative-evaluations.index')
        @endif
      </div>
    @endif

    {{-- 4. HASIL AKHIR --}}
    @if ($tab === 'hasil-akhir')
      <div class="animate-in slide-in-from-bottom-5">
        @include('dms.summary.index')
      </div>
    @endif
  </div>
@endsection
