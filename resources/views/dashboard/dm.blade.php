@extends('layouts.dashboard')

@section('content')
  <div class="animate-in fade-in slide-in-from-bottom-4 space-y-8 pb-10 duration-700">

    {{-- HEADER --}}
    <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white">
          Dashboard Decision Maker
        </h1>
        <p class="text-sm font-bold text-slate-400">
          Kelola dan pantau partisipasi Anda dalam pengambilan keputusan.
        </p>
      </div>
      <div class="border-primary/20 bg-primary/10 rounded-2xl border px-4 py-2">
        <span class="text-primary text-[10px] font-black uppercase tracking-[0.2em]">
          Role: Decision Maker
        </span>
      </div>
    </div>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ([['label' => 'Total Sesi', 'value' => $assignedCount], ['label' => 'Sesi Aktif', 'value' => $activeCount], ['label' => 'Tugas Pending', 'value' => $pendingTaskCount]] as $card)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
          <span class="text-[11px] font-black uppercase tracking-widest text-slate-400">
            {{ $card['label'] }}
          </span>
          <div class="mt-2 text-4xl font-black text-slate-800 dark:text-white">
            {{ $card['value'] }}
          </div>
        </div>
      @endforeach
    </div>

    {{-- DAFTAR SESI --}}
    <div class="flex items-center gap-4 text-slate-400">
      <h2 class="text-xs font-black uppercase tracking-[0.3em]">Daftar Penilaian Aktif</h2>
      <div class="h-px flex-1 bg-slate-200 dark:bg-slate-700"></div>
    </div>

    {{-- SESSION CARDS --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
      @forelse ($assignedSessions as $session)
        @php
          // OPSI 2: LINK TIDAK PERNAH LOMPAT KE STATUS
          $statusMessage = 'Belum Dibuka Admin';
          $statusColor = 'text-slate-400';
          $url = '#';
          $btnLabel = '';
          $isLocked = true;

          if ($session->status === 'configured') {
              $url = route('dms.index', [$session->id, 'tab' => 'penilaian-kriteria']);
              $btnLabel = 'Bobot Kriteria';
              $isLocked = false;

              if ($session->dmHasCompleted) {
                  $statusMessage = 'Pairwise sudah diisi';
                  $statusColor = 'text-emerald-500';
              } else {
                  $statusMessage = 'Perlu Input Pairwise';
                  $statusColor = 'text-amber-500';
              }
          }

          if ($session->status === 'scoring') {
              $url = route('dms.index', [$session->id, 'tab' => 'evaluasi-alternatif']);
              $btnLabel = 'Nilai Alternatif';
              $isLocked = false;

              if ($session->hasCompletedEvaluation) {
                  $statusMessage = 'Evaluasi sudah diisi';
                  $statusColor = 'text-emerald-500';
              } else {
                  $statusMessage = 'Perlu Evaluasi Alternatif';
                  $statusColor = 'text-blue-500';
              }
          }

          if ($session->status === 'closed') {
              $url = route('dms.index', [$session->id, 'tab' => 'hasil-akhir']);
              $btnLabel = 'Lihat Hasil Akhir';
              $statusMessage = 'Sesi Selesai & Final';
              $statusColor = 'text-emerald-600';
              $isLocked = false;
          }
        @endphp

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
          <h3 class="text-lg font-black text-slate-800 dark:text-white">
            {{ $session->name }}
          </h3>
          <p class="mt-1 text-xs font-bold text-slate-400">
            Periode {{ $session->year }}
          </p>

          <div class="mt-4 rounded-xl bg-slate-50 px-3 py-2 dark:bg-slate-900">
            <p class="{{ $statusColor }} text-[10px] font-black uppercase">
              {{ $statusMessage }}
            </p>
          </div>

          <div class="mt-6">
            @if (!$isLocked)
              <a href="{{ $url }}"
                class="dark:bg-primary block w-full rounded-xl bg-slate-900 px-4 py-3 text-center text-[11px] font-black uppercase tracking-widest text-white">
                {{ $btnLabel }}
              </a>

              @if (in_array($session->status, ['scoring', 'closed']))
                <a href="{{ route('usability.responses.create', ['decision_session_id' => $session->id]) }}"
                  class="mt-3 block w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-[10px] font-black uppercase tracking-widest text-slate-700 dark:border-slate-700 dark:text-slate-200">
                  Isi SUS
                </a>
              @endif
            @else
              <div
                class="w-full rounded-xl bg-slate-100 px-4 py-3 text-center text-[10px] font-black uppercase tracking-widest text-slate-400 dark:bg-slate-900">
                Akses Terkunci
              </div>
            @endif
          </div>
        </div>
      @empty
        <div
          class="col-span-full rounded-2xl border-2 border-dashed border-slate-200 py-24 text-center dark:border-slate-700">
          <p class="text-xs font-black uppercase tracking-widest text-slate-300">
            Belum ada sesi yang ditugaskan
          </p>
        </div>
      @endforelse
    </div>
  </div>
@endsection
