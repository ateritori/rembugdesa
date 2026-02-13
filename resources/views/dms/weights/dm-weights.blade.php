@php
  $criteriaWeights = $criteriaWeights ?? null;
  $criterias = $criterias ?? collect();
@endphp

<div class="space-y-6">
  {{-- Header Status --}}
  <div
    class="relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
    <div class="bg-primary absolute left-0 top-0 h-full w-1"></div>
    <div>
      <h2 class="text-primary text-[10px] font-black uppercase tracking-[0.2em]">
        Prioritas Kriteria Anda
      </h2>
      <p class="mt-0.5 text-[10px] font-bold uppercase tracking-tighter text-slate-400">
        Hasil kalkulasi perbandingan berpasangan
      </p>
    </div>
    <div class="min-w-[100px] rounded-xl border border-slate-100 bg-slate-50 px-4 py-2 text-right">
      <span class="mb-0.5 block text-[8px] font-black uppercase tracking-wider opacity-40">CR Ratio</span>
      <span class="{{ $criteriaWeights->cr <= 0.1 ? 'text-emerald-500' : 'text-rose-500' }} text-sm font-black">
        {{ number_format($criteriaWeights->cr, 4) }}
      </span>
    </div>
  </div>

  {{-- Visual List Bobot --}}
  <div class="grid gap-3">
    @php
      $sortedWeights = collect($criteriaWeights->weights)->sortDesc();
      $maxWeight = $sortedWeights->first() ?: 1;
    @endphp

    @foreach ($sortedWeights as $criteriaId => $weight)
      @php
        $criteria = $criterias->firstWhere('id', $criteriaId);
        $percentage = $weight * 100;
        $visualWidth = ($weight / $maxWeight) * 100;
      @endphp

      <div
        class="hover:border-primary/30 group relative rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-all duration-300">
        <div class="relative z-10 mb-3 flex items-center justify-between">
          <div class="flex min-w-0 items-center gap-3">
            <div
              class="bg-primary/10 text-primary group-hover:bg-primary flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition-all duration-500 group-hover:text-white">
              <span class="text-[10px] font-black italic">#{{ $loop->iteration }}</span>
            </div>
            <span class="truncate text-xs font-black uppercase tracking-tight text-slate-700 md:text-sm">
              {{ $criteria->name ?? 'Unknown' }}
            </span>
          </div>
          <div class="text-right">
            <span class="text-primary text-sm font-black md:text-base">
              {{ number_format($percentage, 1) }}%
            </span>
          </div>
        </div>

        {{-- Progress Bar --}}
        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
          <div class="from-primary h-full rounded-full bg-gradient-to-r to-blue-400 transition-all duration-1000"
            style="width: {{ $visualWidth }}%">
          </div>
        </div>

        <div class="mt-2 flex items-center justify-between px-0.5">
          <span class="text-[8px] font-black uppercase tracking-widest text-slate-300">Eigenvector</span>
          <span class="text-[9px] font-bold tabular-nums tracking-tighter text-slate-400">
            {{ number_format($weight, 4) }}
          </span>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Navigasi Edit (DIBERSIHKAN DARI DOBEL) --}}
  @if ($decisionSession->status === 'configured')
    <div class="pt-4">
      <a href="{{ route('decision-sessions.pairwise.index', [
          'decisionSession' => $decisionSession->id,
          'tab' => 'penilaian-kriteria',
          'edit' => 1,
      ]) }}"
        class="group flex w-full items-center justify-center gap-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-4 transition-all duration-300 hover:border-amber-400 hover:bg-amber-50/30">

        <div
          class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white shadow-sm transition-all duration-300 group-hover:border-amber-500 group-hover:bg-amber-500">
          <svg class="h-5 w-5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </div>

        <div class="text-left">
          <span class="mb-1 block text-[9px] font-black uppercase leading-none tracking-[0.15em] text-slate-400">Ingin
            merevisi?</span>
          <span class="text-[11px] font-black uppercase text-slate-700">Ubah Perbandingan Berpasangan</span>
        </div>
      </a>
    </div>
  @endif

  {{-- Waiting State Info --}}
  <div class="flex items-start gap-4 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
    <div
      class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white shadow-sm">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
      </svg>
    </div>
    <div>
      <h4 class="text-xs font-black uppercase tracking-tight text-emerald-800">Data Tersimpan</h4>
      <p class="mt-1 text-[10px] font-medium leading-relaxed text-emerald-600">
        Penilaian Anda telah berhasil direkam. Tahap selanjutnya akan dibuka oleh **Administrator** setelah seluruh
        responden menyelesaikan pengisian.
      </p>
    </div>
  </div>
</div>
