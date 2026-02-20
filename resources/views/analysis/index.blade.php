{{-- =========================================================
 | TAB ANALISIS: Perbandingan Hasil Keputusan (Full Version)
 | ========================================================= --}}

@php
  $borda = $borda ?? collect();
  $sawBorda = $sawBorda ?? collect();

  // --- LOGIKA INTERPRETASI SPEARMAN ρ ---
  $rhoValue = $rho ?? 0;
  $percentage = $rhoValue * 100;

  // Klasifikasi Kekuatan Hubungan
  if ($rhoValue >= 0.8) {
      $interpretation = 'Sangat Kuat';
      $colorClass = 'text-emerald-700 bg-emerald-50 border-emerald-200';
      $barClass = 'bg-emerald-500';
  } elseif ($rhoValue >= 0.6) {
      $interpretation = 'Kuat';
      $colorClass = 'text-blue-700 bg-blue-50 border-blue-200';
      $barClass = 'bg-blue-500';
  } elseif ($rhoValue >= 0.4) {
      $interpretation = 'Cukup';
      $colorClass = 'text-amber-700 bg-amber-50 border-amber-200';
      $barClass = 'bg-amber-500';
  } else {
      $interpretation = 'Rendah / Lemah';
      $colorClass = 'text-rose-700 bg-rose-50 border-rose-200';
      $barClass = 'bg-rose-500';
  }
@endphp

<div class="animate-in fade-in space-y-8 duration-500">

  {{-- HEADER & SPEARMAN STATS CARD --}}
  <div class="grid grid-cols-1 items-center gap-6 lg:grid-cols-3">
    <div class="border-l-4 border-indigo-500 pl-4 lg:col-span-2">
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">
        Analisis Perbandingan Metode
      </h1>
      <p class="mt-1 text-sm text-slate-500">
        Membandingkan efektivitas <span class="font-semibold text-indigo-600">AHP+SMART</span>
        terhadap <span class="font-semibold text-slate-700">AHP+SAW</span> untuk validasi peringkat.
      </p>
    </div>

    @if (isset($rho))
      <div class="{{ $colorClass }} relative overflow-hidden rounded-2xl border p-5 shadow-sm">
        <div class="relative z-10 flex flex-col items-center text-center">
          <span class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-80">Spearman Correlation (ρ)</span>
          <div class="my-1 flex items-baseline gap-1">
            <span class="text-4xl font-black tracking-tighter">{{ number_format($percentage, 1) }}%</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-current"></span>
            <span class="text-xs font-extrabold uppercase tracking-wider">Hubungan {{ $interpretation }}</span>
          </div>

          {{-- Progress Bar --}}
          <div class="mt-3 h-1.5 w-full max-w-[150px] overflow-hidden rounded-full bg-slate-200/50">
            <div class="{{ $barClass }} h-full transition-all duration-1000" style="width: {{ $percentage }}%">
            </div>
          </div>
        </div>
        {{-- Decorative Shape --}}
        <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-current opacity-5"></div>
      </div>
    @endif
  </div>

  {{-- GRID: TABEL METODE UTAMA VS BENCHMARK --}}
  <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

    {{-- SMART + BORDA --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between border-b bg-slate-50 px-5 py-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">AHP + SMART + Borda</span>
        <span
          class="rounded-full bg-indigo-100 px-2 py-1 text-[10px] font-bold uppercase text-indigo-700">Primary</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b text-[10px] uppercase tracking-widest text-slate-400">
              <th class="px-5 py-3 text-left font-bold">Rank</th>
              <th class="px-5 py-3 text-left font-bold">Alternatif</th>
              <th class="px-5 py-3 text-right font-bold">Skor Borda</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($borda as $row)
              <tr class="transition-colors hover:bg-indigo-50/30">
                <td class="w-16 px-5 py-3 text-center">
                  <span
                    class="{{ $loop->first ? 'bg-amber-100 text-amber-700 font-bold' : 'bg-slate-100 text-slate-600' }} inline-flex h-7 w-7 items-center justify-center rounded-full text-xs">
                    {{ $row->final_rank }}
                  </span>
                </td>
                <td class="px-5 py-3 font-semibold text-slate-700">{{ $row->alternative->name ?? '-' }}</td>
                <td class="px-5 py-3 text-right font-mono text-slate-500">{{ $row->borda_score }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-5 py-10 text-center italic text-slate-400">Data tidak ditemukan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- SAW + BORDA --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b bg-slate-50 px-5 py-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">AHP + SAW + Borda</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b text-[10px] uppercase tracking-widest text-slate-400">
              <th class="px-5 py-3 text-left font-bold">Rank</th>
              <th class="px-5 py-3 text-left font-bold">Alternatif</th>
              <th class="px-5 py-3 text-right font-bold">Skor Borda</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($sawBorda as $row)
              <tr class="transition-colors hover:bg-slate-50">
                <td class="w-16 px-5 py-3 text-center">
                  <span
                    class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">
                    {{ $row->final_rank }}
                  </span>
                </td>
                <td class="px-5 py-3 text-slate-600">{{ $row->alternative->name ?? '-' }}</td>
                <td class="px-5 py-3 text-right font-mono text-slate-400">{{ $row->borda_score }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-5 py-10 text-center italic text-slate-400">Data benchmark kosong.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MATRIKS SINKRONISASI --}}
  <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-md">
    <div class="border-b bg-white px-6 py-4">
      <h3 class="text-lg font-bold text-slate-800">Matriks Sinkronisasi & Selisih</h3>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b bg-slate-50 text-slate-600">
            <th class="px-6 py-4 text-center font-bold">#</th>
            <th class="px-6 py-4 text-left font-bold">Alternatif</th>
            <th class="px-6 py-4 text-center font-bold">Rank SMART</th>
            <th class="px-6 py-4 text-center font-bold">Rank SAW</th>
            <th class="px-6 py-4 text-center font-bold">Selisih</th>
            <th class="px-6 py-4 text-center font-bold">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse ($borda as $row)
            @php
              $saw = $sawBorda->firstWhere('alternative_id', $row->alternative_id);
              $rankSmart = $row->final_rank;
              $rankSaw = $saw?->final_rank;
              $diff = is_null($rankSaw) ? null : abs($rankSmart - $rankSaw);
            @endphp
            <tr class="transition-colors hover:bg-slate-50/80">
              <td class="px-6 py-4 text-center">
                <span class="text-[10px] font-bold uppercase text-slate-400">ID: {{ $row->alternative_id }}</span>
              </td>
              <td class="px-6 py-4 font-bold uppercase text-slate-700">
                {{ $row->alternative->name ?? '-' }}
              </td>
              <td class="bg-indigo-50/30 px-6 py-4 text-center font-bold text-indigo-600">{{ $rankSmart }}</td>
              <td class="px-6 py-4 text-center font-medium text-slate-500">{{ $rankSaw ?? '-' }}</td>
              <td class="px-6 py-4 text-center">
                @if ($diff === 0)
                  <span
                    class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-600">0</span>
                @else
                  <span
                    class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-100 text-xs font-bold text-rose-600">{{ $diff }}</span>
                @endif
              </td>
              <td class="px-6 py-4 text-center">
                @if (is_null($diff))
                  <span class="text-slate-300">-</span>
                @elseif ($diff === 0)
                  <span
                    class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black uppercase text-emerald-700">
                    MATCH
                  </span>
                @else
                  <span
                    class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-black uppercase text-amber-700">
                    SHIFT
                  </span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center italic text-slate-400">Belum ada data untuk dianalisis.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
