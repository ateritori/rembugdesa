@if (empty($ahp['individual']) && empty($ahp['group']))
  <div
    class="flex items-center justify-center rounded-3xl border border-yellow-200 bg-yellow-50 p-8 text-sm font-medium text-yellow-800">
    ⚠️ Tidak ada data AHP tersedia.
  </div>
@else
  <div class="space-y-8 text-slate-800">

    <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h3 class="mb-1 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">AHP Analysis</h3>
        <h2 class="text-2xl font-black text-slate-900">Detail Analisis AHP</h2>
      </div>
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
          CR ≤ 0.1 dianggap konsisten
        </div>
        <div class="flex items-center gap-3 rounded-2xl bg-slate-100 p-2">
          <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Filter DM</span>
          <select id="filter-dm"
            class="rounded border border-slate-300 bg-white px-3 py-1 text-xs font-bold outline-none focus:ring-2 focus:ring-black">
            <option value="">Tampilkan Semua DM</option>
            @foreach ($ahp['individual'] ?? [] as $userId => $_)
              <option value="{{ $userId }}">Decision Maker {{ $userId }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    @foreach ($ahp['individual'] ?? [] as $dmId => $prov)
      <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50"
        data-dm="{{ $dmId }}">
        <div class="px-6 py-6">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
              <h3 class="text-lg font-black text-slate-900">DM {{ $dmId }}</h3>
              <p class="text-xs text-slate-500">Detail matriks dan bobot kriteria</p>
            </div>
            <div class="flex flex-col gap-2 text-right text-xs font-semibold">
              <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                CR: {{ number_format($prov['cr'] ?? 0, 4) }}
              </span>
              <span
                class="{{ ($prov['cr'] ?? 1) <= 0.1 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} rounded-full px-3 py-1">
                {{ ($prov['cr'] ?? 1) <= 0.1 ? 'Konsisten' : 'Tidak Konsisten' }}
              </span>
            </div>
          </div>

          <div class="mt-8 space-y-6">
            <div>
              <div class="mb-3 text-xs font-black uppercase tracking-[0.18em] text-slate-500">Matriks Pairwise</div>
              <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-slate-50 shadow-sm">
                <table class="w-full text-sm">
                  <thead>
                    <tr class="bg-slate-900 text-white">
                      <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest">-</th>
                      @foreach ($prov['criteria_ids'] as $c)
                        <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-widest">
                          C{{ $c }}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-200">
                    @foreach ($prov['matrix'] as $i => $row)
                      <tr class="bg-white">
                        <td class="bg-slate-50 px-4 py-3 font-bold text-slate-700">C{{ $prov['criteria_ids'][$i] }}</td>
                        @foreach ($row as $val)
                          <td class="px-4 py-3 text-center text-slate-700">{{ number_format($val, 2) }}</td>
                        @endforeach
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <div>
              <div class="mb-3 text-xs font-black uppercase tracking-[0.18em] text-slate-500">Normalisasi Matriks</div>
              @php
                $matrix = $prov['matrix'] ?? [];
                $n = count($matrix);
                $colSums = [];

                for ($j = 0; $j < $n; $j++) {
                    $sum = 0;
                    for ($i = 0; $i < $n; $i++) {
                        $sum += $matrix[$i][$j] ?? 0;
                    }
                    $colSums[$j] = $sum;
                }
              @endphp
              <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-slate-50 shadow-sm">
                <table class="w-full text-sm">
                  <thead>
                    <tr class="bg-slate-900 text-white">
                      <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest">-</th>
                      @foreach ($prov['criteria_ids'] as $c)
                        <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-widest">
                          C{{ $c }}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-200">
                    @foreach ($matrix as $i => $row)
                      <tr class="bg-white">
                        <td class="bg-slate-50 px-4 py-3 font-bold text-slate-700">C{{ $prov['criteria_ids'][$i] }}
                        </td>
                        @foreach ($row as $j => $val)
                          <td class="px-4 py-3 text-center text-slate-700">
                            {{ ($colSums[$j] ?? 0) != 0 ? number_format($val / $colSums[$j], 3) : 0 }}
                          </td>
                        @endforeach
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <div>
              <div class="mb-3 text-xs font-black uppercase tracking-[0.18em] text-slate-500">Bobot</div>
              <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                @foreach ($prov['weights'] as $i => $w)
                  <div
                    class="rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-xs font-semibold text-slate-700">
                    <div class="mb-1">C{{ $prov['criteria_ids'][$i] }}</div>
                    <div class="text-lg font-black text-slate-900">{{ number_format($w, 4) }}</div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach

    {{-- GROUP --}}
    @if (!empty($ahp['group']))
      <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 shadow-xl shadow-slate-200/50">
        <div class="px-6 py-6">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
              <h3 class="text-lg font-black text-slate-900">Rekap Group</h3>
              <p class="text-xs text-slate-500">Perbandingan bobot antar DM dan bobot grup</p>
            </div>
            <div class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700">
              CR: {{ number_format($ahp['group']['cr'] ?? 0, 4) }}
            </div>
          </div>

          <div class="mt-6 overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-slate-900 text-white">
                  <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest">DM</th>
                  @foreach ($ahp['individual'] ?? [] as $first)
                    @foreach ($first['criteria_ids'] as $c)
                      <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-widest">
                        C{{ $c }}</th>
                    @endforeach
                  @break
                @endforeach
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              @foreach ($ahp['individual'] ?? [] as $dmId => $prov)
                <tr class="bg-white hover:bg-slate-50">
                  <td class="px-4 py-3 font-bold text-slate-700">D{{ $dmId }}</td>
                  @foreach ($prov['weights'] as $w)
                    <td class="px-4 py-3 text-center text-slate-700">{{ number_format($w, 4) }}</td>
                  @endforeach
                </tr>
              @endforeach
              <tr class="bg-slate-100 font-bold text-slate-900">
                <td class="px-4 py-3">GM</td>
                @foreach ($ahp['group']['weights'] ?? [] as $w)
                  <td class="px-4 py-3 text-center">{{ number_format($w, 4) }}</td>
                @endforeach
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif
</div>

<script>
  function applyAhpDmFilter() {
    const dmVal = document.getElementById('filter-dm')?.value || '';
    document.querySelectorAll('[data-dm]').forEach(el => {
      el.style.display = !dmVal || el.dataset.dm === dmVal ? 'block' : 'none';
    });
  }
  document.getElementById('filter-dm')?.addEventListener('change', applyAhpDmFilter);
</script>
@endif
