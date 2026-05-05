@if (empty($borda['ranking']) || empty($sawBorda['ranking']))
  <div
    class="flex items-center gap-2 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm font-medium text-yellow-800">
    ⚠️ Data perbandingan SMART vs SAW belum tersedia (salah satu metode belum dihitung)
  </div>
@else
  <div class="space-y-8">

    @php
      // 🔥 Hitung Spearman Rank Correlation (RANK.AVG - tie safe)

      // helper rank avg
      $rankAvg = function ($ranking) {
          // ambil skor borda
          $scores = collect($ranking)->mapWithKeys(fn($v, $k) => [$k => $v['score']])->toArray();

          arsort($scores);

          $ranks = [];
          $i = 1;

          while (!empty($scores)) {
              $value = current($scores);

              $ties = array_keys($scores, $value, true);
              $count = count($ties);

              $avgRank = ($i + ($i + $count - 1)) / 2;

              foreach ($ties as $key) {
                  $ranks[$key] = $avgRank;
                  unset($scores[$key]);
              }

              $i += $count;
          }

          return $ranks;
      };

      $rankSmartAvg = $rankAvg($borda['ranking']);
      $rankSawAvg = $rankAvg($sawBorda['ranking']);

      $n = count($rankSmartAvg);
      $sum_d2 = 0;

      foreach ($rankSmartAvg as $altId => $r1) {
          $r2 = $rankSawAvg[$altId] ?? null;

          if ($r2 !== null) {
              $d = $r1 - $r2;
              $sum_d2 += pow($d, 2);
          }
      }

      $spearman = $n > 1 ? 1 - (6 * $sum_d2) / ($n * (pow($n, 2) - 1)) : null;
    @endphp

    {{-- 📊 TOP HEADER: SPEARMAN CORRELATION --}}
    <div
      class="flex flex-col justify-between gap-4 border-b-2 border-slate-900 pb-6 md:flex-row md:items-end dark:border-slate-800">
      <div>
        <h3 class="mb-1 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">
          Method Comparison Analysis
        </h3>
        <h2 class="text-2xl font-black text-slate-900 dark:text-white">
          SMART vs SAW Ranking
        </h2>
      </div>

      <div
        class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-800/50 dark:bg-indigo-900/20">
        <div class="mb-1 text-[10px] font-black uppercase tracking-widest text-indigo-500 dark:text-indigo-400">
          Spearman Correlation
        </div>
        <div class="font-mono text-2xl font-black text-indigo-600 dark:text-indigo-300">
          {{ $spearman !== null ? number_format($spearman, 4) : 'N/A' }}
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
      {{-- 🏆 TABLE COLUMN --}}
      <div class="lg:col-span-8">
        <div
          class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-slate-900 text-white">
                  <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Alternatif</th>
                  <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest">SMART Rank</th>
                  <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest">SAW Rank</th>
                  <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest">Delta (Δ)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach (collect($borda['ranking'])->sortBy('rank') as $altId => $smart)
                  @php
                    $saw = $sawBorda['ranking'][$altId] ?? null;
                    $r1 = $rankSmartAvg[$altId] ?? null;
                    $r2 = $rankSawAvg[$altId] ?? null;
                    $delta = $r2 !== null && $r1 !== null ? $r2 - $r1 : null;

                    $isTop = $smart['rank'] == 1;
                  @endphp
                  <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/20">
                    <td class="px-6 py-5">
                      <div class="text-base font-black text-slate-900 dark:text-white">
                        A{{ $altId }}
                      </div>
                      <div class="text-[10px] text-slate-400">Alternatif {{ $altId }}</div>
                    </td>
                    <td class="px-6 py-5 text-center">
                      <span
                        class="{{ $isTop ? 'bg-amber-400 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }} inline-flex h-8 w-8 items-center justify-center rounded-xl text-xs font-black">
                        {{ $smart['rank'] }}
                      </span>
                    </td>
                    <td class="px-6 py-5 text-center">
                      <span
                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-xs font-black text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                        {{ $saw['rank'] ?? '-' }}
                      </span>
                    </td>
                    <td class="px-6 py-5 text-right font-mono text-lg font-black">
                      @if ($delta === null)
                        <span class="text-slate-300">-</span>
                      @elseif($delta < 0)
                        <span class="text-green-600 dark:text-green-400">↑ {{ abs($delta) }}</span>
                      @elseif($delta > 0)
                        <span class="text-red-600 dark:text-red-400">↓ {{ abs($delta) }}</span>
                      @else
                        <span class="text-slate-400">0</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- 💡 LEGEND/INSIGHT COLUMN --}}
      <div class="space-y-6 lg:col-span-4">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-800/50">
          <h4 class="mb-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
            Interpretation Key
          </h4>
          <ul class="space-y-4">
            <li class="flex items-start gap-3">
              <span
                class="flex h-5 w-5 items-center justify-center rounded-lg bg-green-100 text-xs font-bold text-green-600 dark:bg-green-900/30">↑</span>
              <div>
                <div class="text-xs font-black text-slate-900 dark:text-white">Rank Improvement</div>
                <div class="text-[10px] text-slate-500">Alternatif memiliki performa lebih baik (naik peringkat) saat
                  menggunakan metode SAW.</div>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span
                class="flex h-5 w-5 items-center justify-center rounded-lg bg-red-100 text-xs font-bold text-red-600 dark:bg-red-900/30">↓</span>
              <div>
                <div class="text-xs font-black text-slate-900 dark:text-white">Rank Decrease</div>
                <div class="text-[10px] text-slate-500">Alternatif turun peringkat pada metode SAW dibandingkan SMART.
                </div>
              </div>
            </li>
          </ul>
        </div>

        <div class="rounded-3xl bg-indigo-600 p-6 text-white shadow-lg shadow-indigo-200 dark:shadow-none">
          <p class="text-xs font-medium leading-relaxed opacity-90">
            Korelasi Spearman mendekati <b>1.0</b> menunjukkan kedua metode menghasilkan urutan ranking yang hampir
            identik.
          </p>
        </div>
      </div>
    </div>
  </div>
@endif
