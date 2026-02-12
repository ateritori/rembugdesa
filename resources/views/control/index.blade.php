@extends('layouts.dashboard')

@section('title', 'Kontrol Sesi')

@section('content')

  {{-- TAB NAVIGASI SESI --}}
  @include('decision-sessions.partials.nav')

  <div class="animate-in fade-in slide-in-from-bottom-4 w-full px-4 py-8 duration-700">

    <div class="w-full space-y-10">

      {{-- SECTION 1: DASHBOARD RINGKASAN (Gagah Stat Cards) --}}
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @php
          $stats = [
              [
                  'label' => 'Status Sesi',
                  'value' => $decisionSession->status,
                  'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                  'color' => 'blue',
              ],
              [
                  'label' => 'Kriteria Aktif',
                  'value' => $activeCriteriaCount,
                  'icon' =>
                      'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                  'color' => 'purple',
              ],
              [
                  'label' => 'Alternatif Aktif',
                  'value' => $activeAlternativesCount,
                  'icon' =>
                      'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                  'color' => 'indigo',
              ],
              [
                  'label' => 'Total DM',
                  'value' => $assignedDmCount,
                  'icon' =>
                      'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                  'color' => 'emerald',
              ],
          ];
        @endphp

        @foreach ($stats as $stat)
          <div
            class="group rounded-[2rem] border-2 border-slate-100 bg-white p-8 shadow-sm transition-all duration-500 hover:scale-[1.03] hover:shadow-xl">
            <div class="flex items-center gap-6">
              <div
                class="bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 group-hover:bg-{{ $stat['color'] }}-600 flex h-16 w-16 items-center justify-center rounded-2xl shadow-inner transition-colors duration-500 group-hover:text-white">
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

      {{-- SECTION 2: PROGRES BAR (Lebar & Visual) --}}
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        @php
          $pairwisePercent = $assignedDmCount > 0 ? ($dmPairwiseDone / $assignedDmCount) * 100 : 0;
          $dmAltDone = $dmAltDone ?? 0; // Pastikan variabel tersedia
          $altPercent = $assignedDmCount > 0 ? ($dmAltDone / $assignedDmCount) * 100 : 0;
        @endphp

        {{-- Card Pairwise --}}
        <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-slate-100 bg-white p-10 shadow-sm">
          <div class="absolute right-0 top-0 p-8 opacity-5 transition-transform duration-700 group-hover:scale-110">
            <svg class="h-32 w-32 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14l-5-4.87 6.91-1.01L12 2z" />
            </svg>
          </div>
          <div class="relative z-10">
            <div class="mb-8 flex items-end justify-between">
              <div>
                <h4 class="text-2xl font-black uppercase tracking-tight text-slate-800">Progres Pairwise</h4>
                <p class="mt-2 text-sm font-medium text-slate-400">Menghitung bobot kepentingan kriteria.</p>
              </div>
              <span class="text-4xl font-black tracking-tighter text-amber-500">{{ round($pairwisePercent) }}%</span>
            </div>
            <div class="h-5 w-full overflow-hidden rounded-full bg-slate-100 p-1 shadow-inner">
              <div
                class="h-full rounded-full bg-gradient-to-r from-amber-400 to-amber-600 shadow-lg transition-all duration-1000"
                style="width: {{ $pairwisePercent }}%"></div>
            </div>
            <div class="mt-4 flex justify-between text-[11px] font-black uppercase tracking-widest text-slate-400">
              <span>{{ $dmPairwiseDone }} DATA MASUK</span>
              <span>TARGET: {{ $assignedDmCount }} DM</span>
            </div>
          </div>
        </div>

        {{-- Card Alternatif --}}
        <div class="group relative overflow-hidden rounded-[2.5rem] border-2 border-slate-100 bg-white p-10 shadow-sm">
          <div class="absolute right-0 top-0 p-8 opacity-5 transition-transform duration-700 group-hover:scale-110">
            <svg class="h-32 w-32 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
            </svg>
          </div>
          <div class="relative z-10">
            <div class="mb-8 flex items-end justify-between">
              <div>
                <h4 class="text-2xl font-black uppercase tracking-tight text-slate-800">Penilaian Alternatif</h4>
                <p class="mt-2 text-sm font-medium text-slate-400">Status penilaian kandidat per kriteria.</p>
              </div>
              <span class="text-4xl font-black tracking-tighter text-emerald-500">{{ round($altPercent) }}%</span>
            </div>
            <div class="h-5 w-full overflow-hidden rounded-full bg-slate-100 p-1 shadow-inner">
              <div
                class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 shadow-lg transition-all duration-1000"
                style="width: {{ $altPercent }}%"></div>
            </div>
            <div class="mt-4 flex justify-between text-[11px] font-black uppercase tracking-widest text-slate-400">
              <span>{{ $dmAltDone }} DATA MASUK</span>
              <span>TARGET: {{ $assignedDmCount }} DM</span>
            </div>
          </div>
        </div>
      </div>

      {{-- SECTION 3: ACTION CENTER (The Big Buttons) --}}
      <div class="w-full">

        {{-- 1. DRAFT -> ACTIVE --}}
        @if ($decisionSession->status === 'draft')
          <div
            class="flex flex-col items-center justify-between gap-10 rounded-[3rem] border-2 border-blue-100 bg-white p-12 shadow-2xl shadow-blue-100/50 lg:flex-row">
            <div class="flex-1 text-center lg:text-left">
              <span
                class="rounded-full bg-blue-50 px-5 py-2 text-[11px] font-black uppercase tracking-[0.3em] text-blue-600">Phase
                01: Preparation</span>
              <h3 class="mt-6 text-4xl font-black uppercase tracking-tighter text-slate-800">Aktifkan Sesi Sekarang</h3>
              <p class="mt-4 max-w-2xl text-lg font-medium leading-relaxed text-slate-500">Sistem akan membuka akses
                penilaian bagi Decision Maker. Pastikan semua Kriteria & Alternatif sudah benar.</p>

              @php $canActivate = $activeCriteriaCount >= 2 && $activeAlternativesCount >= 2 && $assignedDmCount >= 1; @endphp
              @if (!$canActivate)
                <div
                  class="mx-auto mt-8 flex w-fit items-center gap-4 rounded-[2rem] border-2 border-rose-100 bg-rose-50 px-8 py-4 text-rose-600 lg:mx-0">
                  <svg class="h-6 w-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <span class="text-xs font-black uppercase tracking-widest">Lengkapi Data: Min. 2 Kriteria, 2 Alternatif,
                    & 1 DM</span>
                </div>
              @endif
            </div>

            <form method="POST" action="{{ route('decision-sessions.activate', $decisionSession->id) }}"
              onsubmit="return confirm('Aktifkan sesi?')">
              @csrf @method('PATCH')
              <button type="submit" {{ $canActivate ? '' : 'disabled' }}
                class="rounded-[2rem] bg-blue-600 px-16 py-8 text-sm font-black uppercase tracking-[0.3em] text-white shadow-[0_20px_50px_rgba(37,99,235,0.3)] transition-all hover:scale-105 hover:brightness-110 active:scale-95 disabled:opacity-20 disabled:grayscale">
                Buka Sesi <span class="ml-2">→</span>
              </button>
            </form>
          </div>
        @endif

        {{-- 2. ACTIVE -> CRITERIA LOCK --}}
        @if ($decisionSession->status === 'active')
          <div
            class="flex flex-col items-center justify-between gap-10 rounded-[3rem] border-2 border-amber-100 bg-white p-12 shadow-2xl shadow-amber-100/50 lg:flex-row">
            <div class="text-center lg:text-left">
              <span
                class="rounded-full bg-amber-50 px-5 py-2 text-[11px] font-black uppercase tracking-[0.3em] text-amber-600">Phase
                02: Criteria Weighting</span>
              <h3 class="mt-6 text-4xl font-black uppercase tracking-tighter text-slate-800">Kunci Bobot Kriteria</h3>
              <p class="mt-4 max-w-2xl text-lg font-medium text-slate-500">Kunci penilaian pairwise untuk menghitung
                prioritas kriteria secara permanen.</p>
            </div>
            <form method="POST" action="{{ route('decision-sessions.lock-criteria', $decisionSession->id) }}">
              @csrf @method('PATCH')
              <button type="submit"
                class="rounded-[2rem] bg-amber-500 px-16 py-8 text-sm font-black uppercase tracking-[0.3em] text-white shadow-[0_20px_50px_rgba(245,158,11,0.3)] transition-all hover:scale-105 active:scale-95">
                Kunci & Lanjut
              </button>
            </form>
          </div>
        @endif

        {{-- 3. CLOSED STATE (Premium Card) --}}
        @if ($decisionSession->status === 'closed')
          <div class="relative overflow-hidden rounded-[4rem] bg-slate-900 p-20 text-center shadow-2xl">
            <div
              class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10">
            </div>
            <div class="relative z-10">
              <div
                class="mx-auto mb-10 flex h-32 w-32 rotate-6 items-center justify-center rounded-[2.5rem] bg-emerald-500 text-white shadow-2xl shadow-emerald-500/40">
                <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-5xl font-black uppercase tracking-tighter text-white">Keputusan Final</h3>
              <p class="mx-auto mt-6 max-w-2xl text-xl font-medium text-slate-400">Seluruh proses perhitungan telah
                selesai. Hasil ranking telah diverifikasi oleh sistem.</p>

              <div class="mt-16 flex flex-wrap justify-center gap-6">
                <a href="{{ route('reports.index') }}"
                  class="rounded-2xl bg-emerald-500 px-12 py-6 text-sm font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-emerald-500/20 transition-all hover:scale-105">
                  Buka Laporan Hasil
                </a>
                <a href="{{ route('decision-sessions.index') }}"
                  class="rounded-2xl bg-slate-800 px-12 py-6 text-sm font-black uppercase tracking-[0.2em] text-slate-400 transition-all hover:bg-slate-700">
                  Kembali ke Dashboard
                </a>
              </div>
            </div>
          </div>
        @endif
      </div>

    </div>
  </div>

@endsection
