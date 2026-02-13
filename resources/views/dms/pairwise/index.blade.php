@php
  $criterias = $criterias ?? ($criteria ?? collect());
  $existingPairwise = $existingPairwise ?? [];
@endphp

<style>
  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }

  .no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  input[type=range] {
    -webkit-appearance: none;
    background: transparent;
  }

  input[type=range]::-webkit-slider-runnable-track {
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 999px;
  }

  input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 22px;
    width: 22px;
    border-radius: 50%;
    background: #ffffff;
    border: 4px solid #3b82f6;
    margin-top: -7px;
    cursor: pointer;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    transition: all 0.2s ease;
  }

  input[type=range]:active::-webkit-slider-thumb {
    transform: scale(1.2);
    background: #3b82f6;
  }
</style>

<div
  class="adaptive-card border-t-primary relative space-y-6 overflow-hidden border-t-4 bg-white/80 p-4 shadow-xl backdrop-blur-md md:p-6">
  <div class="bg-primary/5 absolute right-0 top-0 -mr-32 -mt-32 h-64 w-64 rounded-full blur-3xl"></div>

  @if ($errors->any())
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
      class="relative z-50 flex items-center gap-2 rounded-xl border-2 border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm font-black text-rose-600">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('decision-sessions.pairwise.store', $decisionSession->id) }}"
    x-data="crState()" x-init="initData()" @pairwise-changed.window.debounce.100ms="recalculate()">
    @csrf

    <input type="hidden" name="debug_frontend" :value="JSON.stringify(pairs)">
    <input type="hidden" name="cr_value" :value="isNaN(cr) ? 0 : cr">
    <input type="hidden" name="final_weights" :value="JSON.stringify(weightsMap)">

    <div class="space-y-4">
      @foreach ($criterias as $i => $ci)
        @foreach ($criterias as $j => $cj)
          @if ($i < $j)
            @php
              $pairKey = min($ci->id, $cj->id) . '-' . max($ci->id, $cj->id);
              $existing = $existingPairwise[$pairKey] ?? null;
              $initialPos = 9;
              $valAIJ = 1;
              $valAJI = 1;
              if ($existing) {
                  $initialPos = $existing->direction === 'left' ? 10 - $existing->value : 9 + $existing->value;
                  $valAIJ = $existing->direction === 'left' ? $existing->value : 1 / $existing->value;
                  $valAJI = $existing->direction === 'left' ? 1 / $existing->value : $existing->value;
              }
            @endphp

            <div
              class="grid grid-cols-12 items-center gap-3 rounded-2xl border bg-white/40 p-4 transition-all duration-300 md:p-5"
              :class="{ 'border-amber-500 bg-amber-500/5 shadow-md scale-[1.01] z-10': offenders.includes(
                      '{{ $pairKey }}'), 'border-slate-100 hover:border-primary/20': !offenders.includes(
                      '{{ $pairKey }}') }"
              x-data="{ pos: {{ $initialPos }}, get direction() { return this.pos <= 9 ? 'left' : 'right'; }, get value() { const v = this.pos <= 9 ? 10 - this.pos : this.pos - 9; return Math.min(9, Math.max(1, v)); } }">
              <div class="col-span-12 space-y-4">
                <div class="relative flex flex-col items-center justify-between gap-3 px-1 md:flex-row">
                  <div class="flex w-full flex-row items-center gap-3 md:w-[42%] md:flex-col md:items-start">
                    <span class="text-[8px] font-black uppercase opacity-40">Kriteria A</span>
                    <span class="text-xs font-black md:text-sm"
                      :class="direction === 'left' ? 'text-primary' : 'text-slate-400'">{{ $ci->name }}</span>
                  </div>
                  <div class="relative z-20 flex justify-center md:absolute md:left-1/2 md:-translate-x-1/2">
                    <div
                      class="bg-primary shadow-primary/20 flex h-10 w-10 rotate-2 items-center justify-center rounded-xl shadow-lg">
                      <span class="text-base font-black text-white" x-text="value"></span>
                    </div>
                  </div>
                  <div
                    class="flex w-full flex-row-reverse items-center gap-3 text-right md:w-[42%] md:flex-col md:items-end">
                    <span class="text-[8px] font-black uppercase opacity-40">Kriteria B</span>
                    <span class="text-xs font-black md:text-sm"
                      :class="direction === 'right' ? 'text-primary' : 'text-slate-400'">{{ $cj->name }}</span>
                  </div>
                </div>
                <div class="relative flex h-8 items-center px-1">
                  <input type="range" min="1" max="18" step="1" x-model.number="pos"
                    @input="isDirty = true; $dispatch('pairwise-changed')" class="accent-primary w-full cursor-pointer">
                </div>
                <div class="overflow-hidden">
                  <div class="grid select-none text-[10px] font-bold"
                    style="grid-template-columns: repeat(18, minmax(0, 1fr));">
                    <template x-for="n in [9,8,7,6,5,4,3,2,1]" :key="'l' + n">
                      <div @click="pos = 10-n; isDirty = true; $dispatch('pairwise-changed')"
                        class="cursor-pointer text-center transition-transform"
                        :class="direction === 'left' && value == n ? 'text-primary scale-125' : 'text-slate-300'"
                        x-text="n"></div>
                    </template>
                    <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="'r' + n">
                      <div @click="pos = 9+n; isDirty = true; $dispatch('pairwise-changed')"
                        class="cursor-pointer text-center transition-transform"
                        :class="direction === 'right' && value == n ? 'text-primary scale-125' : 'text-slate-300'"
                        x-text="n"></div>
                    </template>
                  </div>
                </div>
                <input type="hidden"
                  :name="'pairwise[' + {{ $ci->id }} + '][' + {{ $cj->id }} + '][a_ij]'"
                  :value="direction === 'left' ? value : (1 / value)" value="{{ $valAIJ }}">
                <input type="hidden"
                  :name="'pairwise[' + {{ $ci->id }} + '][' + {{ $cj->id }} + '][a_ji]'"
                  :value="direction === 'left' ? (1 / value) : value" value="{{ $valAJI }}">
              </div>
            </div>
          @endif
        @endforeach
      @endforeach
    </div>

    <div class="mt-8 flex flex-col items-center justify-between gap-6 border-t border-slate-100 pt-6 md:flex-row">
      <div class="flex items-center gap-5">
        <div class="relative flex min-w-[120px] flex-col items-center rounded-2xl border bg-white px-5 py-3"
          :class="status === 'ok' ? 'border-emerald-500 bg-emerald-50' : 'border-rose-500 bg-rose-50'">
          <span class="text-[8px] font-black uppercase tracking-wider opacity-60">CR Ratio</span>
          <span class="text-xl font-black" :class="status === 'ok' ? 'text-emerald-600' : 'text-rose-600'"
            x-text="isNaN(cr) ? '0.0000' : cr.toFixed(4)"></span>
        </div>
        <div class="text-[10px] font-black uppercase italic tracking-widest">
          <span x-show="!isDirty" class="text-amber-500">Menunggu Input...</span>
          <span x-show="isDirty && status === 'block'" class="text-rose-500">⚠️ Tidak Konsisten (Cek Baris
            Oranye)</span>
          <span x-show="isDirty && status === 'ok'" class="text-emerald-500">✅ Konsisten & Siap Simpan</span>
        </div>
      </div>
      <button type="submit"
        class="bg-primary shadow-primary/20 w-full rounded-xl px-12 py-4 text-xs font-black uppercase tracking-[0.2em] text-white shadow-lg transition-all hover:scale-105 active:scale-95 disabled:opacity-30 md:w-auto"
        :disabled="!canSubmit()">
        <span x-text="!isDirty ? 'Lengkapi Data' : 'Simpan Hasil'"></span>
      </button>
    </div>
  </form>
</div>

<script>
  function crState() {
    return {
      criteriaIds: @json($criterias->pluck('id')->values()),
      cr: NaN,
      status: 'block',
      isDirty: {{ count($existingPairwise) > 0 ? 'true' : 'false' }},
      offenders: [],
      pairs: {},
      weightsMap: {},

      initData() {
        this.$nextTick(() => {
          this.recalculate();
        });
      },

      canSubmit() {
        return this.isDirty && this.status === 'ok';
      },

      recalculate() {
        this.offenders = [];
        const newPairs = {};
        const inputs = document.querySelectorAll('input[name^="pairwise"]');
        inputs.forEach(input => {
          const m = input.name.match(/pairwise\[(\d+)\]\[(\d+)\]\[(a_ij|a_ji)\]/);
          if (!m) return;
          const [, a, b, type] = m;
          const key = [a, b].sort((x, y) => x - y).join('-');
          newPairs[key] = newPairs[key] || {};
          newPairs[key][type] = parseFloat(input.value);
        });
        this.pairs = newPairs;
        const n = this.criteriaIds.length;
        if (n < 2) return;
        const indexMap = {};
        this.criteriaIds.forEach((id, i) => indexMap[id] = i);
        const M = Array.from({
          length: n
        }, () => Array(n).fill(1));
        Object.entries(this.pairs).forEach(([key, p]) => {
          const [id1, id2] = key.split('-').map(Number);
          if (indexMap[id1] !== undefined && indexMap[id2] !== undefined) {
            M[indexMap[id1]][indexMap[id2]] = p.a_ij;
            M[indexMap[id2]][indexMap[id1]] = p.a_ji;
          }
        });
        let W = Array(n).fill(1 / n);
        for (let iter = 0; iter < 50; iter++) {
          const Wnext = M.map(row => row.reduce((sum, val, j) => sum + val * W[j], 0));
          const sum = Wnext.reduce((a, b) => a + b, 0);
          const Wnorm = Wnext.map(v => v / sum);
          const diff = Wnorm.reduce((s, v, i) => s + Math.abs(v - W[i]), 0);
          W = Wnorm;
          if (diff < 1e-10) break;
        }

        // Simpan Map Bobot (ID => Nilai) untuk dikirim ke Controller
        const currentWeightsMap = {};
        this.criteriaIds.forEach((id, i) => {
          currentWeightsMap[id] = W[i];
        });
        this.weightsMap = currentWeightsMap;

        const lambdaMax = M.map((row, i) => row.reduce((sum, val, j) => sum + val * W[j], 0) / W[i]).reduce((a, b) =>
          a + b, 0) / n;
        const CI = (lambdaMax - n) / (Math.max(1, n - 1));
        const RI = [0, 0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49][n] || 1.49;
        this.cr = RI === 0 ? 0 : CI / RI;
        this.status = (this.cr <= 0.10) ? 'ok' : 'block';
        if (this.status === 'block') {
          const devs = Object.entries(this.pairs).map(([key, p]) => {
            const [id1, id2] = key.split('-').map(Number);
            const expected = W[indexMap[id1]] / W[indexMap[id2]];
            return {
              key,
              dev: Math.abs(Math.log(p.a_ij / expected))
            };
          });
          this.offenders = devs.sort((a, b) => b.dev - a.dev).slice(0, 2).map(d => d.key);
        }
      }
    }
  }
</script>
