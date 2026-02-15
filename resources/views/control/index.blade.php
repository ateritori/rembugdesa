@extends('layouts.dashboard')

@section('title', 'Kontrol Sesi')

@section('content')

  {{-- 1. HEADER LOGIC: DEFINE SEMUA VARIABEL BIAR GAK ERROR --}}
  @php
      // Hitung Data Dasar
      $activeCriteriaCount = $decisionSession->criterias->where('is_active', true)->count();
      $activeAlternativesCount = $decisionSession->alternatives->where('is_active', true)->count();

      // Hitung Progres (Cek data di DB via relasi)
      $dmPairwiseDone = $decisionSession->dms->filter(function($dm) use ($decisionSession) {
          return \Illuminate\Support\Facades\DB::table('criteria_weights')
              ->where('decision_session_id', $decisionSession->id)
              ->where('dm_id', $dm->id)->exists();
      })->count();

      $dmAltDone = $decisionSession->dms->filter(function($dm) use ($decisionSession) {
          return \Illuminate\Support\Facades\DB::table('alternative_evaluations')
              ->where('decision_session_id', $decisionSession->id)
              ->where('dm_id', $dm->id)->exists();
      })->count();

      // Syarat Aktivasi
      $canActivate = ($activeCriteriaCount >= 2 && $activeAlternativesCount >= 2 && $assignedDmCount >= 1);
  @endphp

  {{-- TAB NAVIGASI SESI --}}
  @include('decision-sessions.partials.nav')

  @if (!in_array(request('tab'), ['hasil-akhir', 'analisis']))
  <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-8 duration-700">
    <div class="w-full space-y-10">

      {{-- SECTION 1: DASHBOARD RINGKASAN --}}
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @php
          $stats = [
              ['label' => 'Status Sesi', 'value' => $decisionSession->status, 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'blue'],
              ['label' => 'Kriteria Aktif', 'value' => $activeCriteriaCount, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'purple'],
              ['label' => 'Alternatif Aktif', 'value' => $activeAlternativesCount, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'indigo'],
              ['label' => 'Total DM', 'value' => $assignedDmCount, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'emerald'],
          ];
        @endphp

        @foreach ($stats as $stat)
          <div class="group rounded-[2rem] border-2 border-slate-100 bg-white p-8 shadow-sm transition-all duration-500 hover:scale-[1.03] hover:shadow-xl">
            <div class="flex items-center gap-6">
              <div class="bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 group-hover:bg-{{ $stat['color'] }}-600 flex h-16 w-16 items-center justify-center rounded-2xl shadow-inner transition-colors duration-500 group-hover:text-white">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                </svg>
              </div>
              <div class="min-w-0">
                <p class="mb-1 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">{{ $stat['label'] }}</p>
                <p class="text-3xl font-black capitalize tracking-tighter text-slate-800">{{ $stat['value'] }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- SECTION 2: PROGRES BAR --}}
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        @php
          $pairwisePercent = $assignedDmCount > 0 ? ($dmPairwiseDone / $assignedDmCount) * 100 : 0;
          $altPercent = $assignedDmCount > 0 ? ($dmAltDone / $assignedDmCount) * 100 : 0;
        @endphp

        <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-slate-100 bg-white p-10 shadow-sm">
          <div class="relative z-10">
            <div class="mb-8 flex items-end justify-between">
              <div>
                <h4 class="text-2xl font-black uppercase tracking-tight text-slate-800">Progres Pairwise</h4>
                <p class="mt-2 text-sm font-medium text-slate-400">Menghitung bobot kepentingan kriteria.</p>
              </div>
              <span class="text-4xl font-black tracking-tighter text-amber-500">{{ round($pairwisePercent) }}%</span>
            </div>
            <div class="h-5 w-full overflow-hidden rounded-full bg-slate-100 p-1 shadow-inner">
              <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-amber-600 shadow-lg transition-all duration-1000" style="width: {{ $pairwisePercent }}%"></div>
            </div>
            <div class="mt-4 flex justify-between text-[11px] font-black uppercase tracking-widest text-slate-400">
              <span>{{ $dmPairwiseDone }} DATA MASUK / TARGET: {{ $assignedDmCount }} DM</span>
            </div>
          </div>
        </div>

        <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-slate-100 bg-white p-10 shadow-sm">
          <div class="relative z-10">
            <div class="mb-8 flex items-end justify-between">
              <div>
                <h4 class="text-2xl font-black uppercase tracking-tight text-slate-800">Penilaian Alternatif</h4>
                <p class="mt-2 text-sm font-medium text-slate-400">Status penilaian kandidat per kriteria.</p>
              </div>
              <span class="text-4xl font-black tracking-tighter text-emerald-500">{{ round($altPercent) }}%</span>
            </div>
            <div class="h-5 w-full overflow-hidden rounded-full bg-slate-100 p-1 shadow-inner">
              <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 shadow-lg transition-all duration-1000" style="width: {{ $altPercent }}%"></div>
            </div>
            <div class="mt-4 flex justify-between text-[11px] font-black uppercase tracking-widest text-slate-400">
              <span>{{ $dmAltDone }} DATA MASUK / TARGET: {{ $assignedDmCount }} DM</span>
            </div>
          </div>
        </div>
      </div>

      {{-- SECTION 3: ACTION CENTER --}}
      <div class="w-full space-y-6">

        {{-- STATUS DRAFT --}}
        @if ($decisionSession->status === 'draft')
          @include('control.partials.action-card', [
              'border' => 'border-blue-100', 'badgeBg' => 'bg-blue-50', 'badgeText' => 'text-blue-600',
              'phase' => 'Phase 01: Preparation', 'title' => 'Aktifkan Sesi Sekarang',
              'description' => 'Sistem akan membuka akses penilaian bagi Decision Maker.',
              'right_path' => 'control.partials.buttons.draft-activate',
              'canActivate' => $canActivate
          ])
        @endif

        {{-- STATUS CONFIGURED --}}
        @if ($decisionSession->status === 'configured')
          @include('control.partials.action-card', [
              'border' => 'border-indigo-100', 'badgeBg' => 'bg-indigo-50', 'badgeText' => 'text-indigo-600',
              'phase' => 'Phase 02: Configured', 'title' => 'Sesi Siap Dinilai',
              'description' => 'Konfigurasi selesai. Menunggu seluruh DM mengisi pairwise.',
              'right_path' => 'control.partials.buttons.start-alternative',
              'canActivate' => true {{-- Asumsi: sudah lewat aktivasi berarti bisa lanjut --}}
          ])
        @endif

        {{-- STATUS SCORING --}}
        @if ($decisionSession->status === 'scoring')
          @include('control.partials.action-card', [
              'border' => 'border-amber-100', 'badgeBg' => 'bg-amber-50', 'badgeText' => 'text-amber-600',
              'phase' => 'Phase 03: Scoring', 'title' => 'Proses Penilaian Berlangsung',
              'description' => 'Decision Maker sedang melakukan penilaian.',
              'right_path' => 'control.partials.buttons.close-scoring',
              'canActivate' => ($dmAltDone >= $assignedDmCount) {{-- Hanya bisa tutup kalau semua DM beres --}}
          ])
        @endif

        {{-- STATUS CLOSED --}}
        @if ($decisionSession->status === 'closed')
          @include('control.partials.action-card', [
              'border' => 'border-slate-200', 'badgeBg' => 'bg-slate-100', 'badgeText' => 'text-slate-600',
              'phase' => 'Phase 04: Closed',
              'title' => 'Keputusan Ditutup',
              'description' => 'Keputusan akhir telah ditetapkan dan dapat dilihat.',
              'right_path' => 'control.partials.buttons.view-result',
              'canActivate' => true
          ])
        @endif

      </div>
    </div>
  </div>
  @endif
  {{-- =========================
       TAB CONTENT: HASIL AKHIR
       ========================= --}}
  @if (request('tab') === 'hasil-akhir' && $decisionSession->status === 'closed')
      <div class="mt-12 animate-in fade-in slide-in-from-bottom-4 duration-500">
          @include('control.result')
      </div>
  @endif
  {{-- =========================
       TAB CONTENT: ANALISIS
       ========================= --}}
  @if (request('tab') === 'analisis' && $decisionSession->status === 'closed')
      <div class="mt-12 animate-in fade-in slide-in-from-bottom-4 duration-500">
          @include('control.analysis')
      </div>
  @endif
</div>
@endsection
