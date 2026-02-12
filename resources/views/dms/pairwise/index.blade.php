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

    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: #ffffff;
        border: 3px solid var(--primary-color, #3b82f6);
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease;
    }
</style>

{{-- Card Utama: Padding dirampingkan dari p-8 ke p-6 --}}
<div class="adaptive-card p-4 md:p-6 shadow-xl space-y-6 border-t-4 border-t-primary relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>

    @if ($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
            class="rounded-xl border-2 border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-600 font-black flex items-center gap-2 relative z-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('decision-sessions.pairwise.store', $decisionSession->id) }}"
        x-data="crState()" x-init="$nextTick(() => {
            isDirty = {{ count($existingPairwise) > 0 ? 'true' : 'false' }};
            recalculate();
        })" @pairwise-changed.window="recalculate()">
        @csrf

        <input type="hidden" name="debug_frontend" :value="JSON.stringify(pairs)">

        <div class="space-y-4"> {{-- Jarak antar baris kriteria dipersempit --}}
            @foreach ($criterias as $i => $ci)
                @foreach ($criterias as $j => $cj)
                    @if ($i < $j)
                        @php
                            $pairKey = min($ci->id, $cj->id) . '-' . max($ci->id, $cj->id);
                            $existing = $existingPairwise[$pairKey] ?? null;
                        @endphp

                        {{-- Card Row: Border dirampingkan, background tetap transparan --}}
                        <div class="grid grid-cols-12 items-center gap-3 p-4 md:p-5 rounded-2xl border transition-all duration-300 bg-white/40 backdrop-blur-sm"
                            :class="{
                                'border-amber-500 bg-amber-500/10 shadow-md scale-[1.01] z-10': status === 'block' &&
                                    offenders.includes('{{ $pairKey }}'),
                                'border-slate-200 hover:border-primary/30': !offenders.includes('{{ $pairKey }}')
                            }">

                            <div class="col-span-12 space-y-4" x-data="{
                                pos: {{ $existing ? ($existing->direction === 'left' ? 10 - min(9, max(1, $existing->value)) : 9 + min(9, max(1, $existing->value))) : 9 }},
                                get direction() { return this.pos <= 9 ? 'left' : 'right'; },
                                get value() {
                                    const v = this.pos <= 9 ? 10 - this.pos : this.pos - 9;
                                    return Math.min(9, Math.max(1, v));
                                }
                            }">

                                {{-- Header: Lebih Compact tapi tetap center di desktop --}}
                                <div
                                    class="relative flex flex-col md:flex-row items-center justify-between gap-3 md:gap-0 px-1">
                                    <div
                                        class="flex flex-row md:flex-col items-center md:items-start gap-3 md:gap-1 w-full md:w-[42%]">
                                        <span class="text-[7px] font-black uppercase tracking-widest opacity-40">KRIT.
                                            A</span>
                                        <span class="text-xs md:text-sm font-black transition-all"
                                            :class="direction === 'left' ? 'text-primary' : 'text-slate-400 opacity-40'">
                                            {{ $ci->name }}
                                        </span>
                                    </div>

                                    {{-- Center Badge: Ukuran diperkecil dari w-12 ke w-10 --}}
                                    <div
                                        class="relative md:absolute md:left-1/2 md:-translate-x-1/2 flex justify-center shrink-0 z-20">
                                        <div
                                            class="w-10 h-10 bg-primary flex items-center justify-center rounded-xl shadow-lg shadow-primary/20 rotate-2">
                                            <span class="text-base font-black text-white -rotate-2"
                                                x-text="value"></span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-row-reverse md:flex-col items-center md:items-end gap-3 md:gap-1 w-full md:w-[42%] text-right">
                                        <span class="text-[7px] font-black uppercase tracking-widest opacity-40">KRIT.
                                            B</span>
                                        <span class="text-xs md:text-sm font-black transition-all text-right"
                                            :class="direction === 'right' ? 'text-primary' : 'text-slate-400 opacity-40'">
                                            {{ $cj->name }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Slider Input: Lebih ramping --}}
                                <div class="relative flex items-center px-1">
                                    <input type="range" min="1" max="18" step="1" x-model="pos"
                                        @input="isDirty = true; $dispatch('pairwise-changed')"
                                        class="w-full h-2 bg-slate-200 rounded-full appearance-none cursor-pointer accent-primary">
                                </div>

                                {{-- Skala Angka: Ukuran font diperkecil --}}
                                <div class="overflow-x-auto no-scrollbar">
                                    <div class="grid min-w-[320px] text-[9px] md:text-[11px] font-black select-none items-center"
                                        style="grid-template-columns: repeat(18, minmax(0, 1fr));">
                                        <template x-for="n in [9,8,7,6,5,4,3,2,1]" :key="'l' + n">
                                            <div class="text-center cursor-pointer hover:text-primary py-1.5"
                                                @click="pos = 10 - n; isDirty = true; $dispatch('pairwise-changed')"
                                                :class="direction === 'left' && value == n ?
                                                    'text-primary scale-125 opacity-100' : 'text-slate-300 opacity-60'"
                                                x-text="n"></div>
                                        </template>
                                        <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="'r' + n">
                                            <div class="text-center cursor-pointer hover:text-primary py-1.5"
                                                @click="pos = 9 + n; isDirty = true; $dispatch('pairwise-changed')"
                                                :class="direction === 'right' && value == n ?
                                                    'text-primary scale-125 opacity-100' : 'text-slate-300 opacity-60'"
                                                x-text="n"></div>
                                        </template>
                                    </div>
                                </div>

                                <input type="hidden" name="pairwise[{{ $ci->id }}][{{ $cj->id }}][a_ij]"
                                    :value="direction === 'left' ? value : (1 / value)">
                                <input type="hidden" name="pairwise[{{ $ci->id }}][{{ $cj->id }}][a_ji]"
                                    :value="direction === 'left' ? (1 / value) : value">
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>

        {{-- Footer Action: Lebih Efisien Ruang --}}
        <div class="pt-6 flex flex-col md:flex-row items-center justify-between border-t border-slate-100 mt-8 gap-6">
            <div class="flex items-center gap-5">
                <div class="relative px-5 py-3 bg-white rounded-2xl border flex flex-col items-center min-w-[110px]"
                    :class="status === 'ok' ? 'border-emerald-500' : 'border-rose-500'">
                    <span class="text-[8px] font-black uppercase opacity-40 tracking-wider">CR Ratio</span>
                    <span class="text-xl font-black"
                        :class="status === 'ok' ? 'text-emerald-500' : 'text-rose-500'"
                        x-text="isNaN(cr) ? '0.0000' : cr.toFixed(4)"></span>
                </div>
                <div class="hidden sm:block">
                    <template x-if="!isDirty">
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest italic">Menunggu
                            Input...</p>
                    </template>
                    <template x-if="isDirty && status === 'block'">
                        <p class="text-[10px] font-black text-rose-500 uppercase leading-tight italic">⚠️ Tidak
                            Konsisten<br><span class="opacity-50 font-bold">Cek baris oranye</span></p>
                    </template>
                    <template x-if="isDirty && status === 'ok'">
                        <p class="text-[10px] font-black text-emerald-500 uppercase leading-tight">✅ Konsisten<br><span
                                class="opacity-50 font-bold">Siap disimpan</span></p>
                    </template>
                </div>
            </div>

            <button type="submit"
                class="w-full md:w-auto px-12 py-4 rounded-xl font-black text-xs uppercase tracking-[0.2em] transition-all bg-primary text-white disabled:opacity-30 shadow-lg shadow-primary/20"
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
            canSubmit() {
                return this.isDirty && this.status === 'ok';
            },
            recalculate() {
                this.offenders = [];
                this.pairs = {};
                const inputs = document.querySelectorAll('input[name^="pairwise"]');
                inputs.forEach(input => {
                    const m = input.name.match(/pairwise\[(\d+)\]\[(\d+)\]\[(a_ij|a_ji)\]/);
                    if (!m) return;
                    const [, a, b, type] = m;
                    const key = [a, b].map(Number).sort((x, y) => x - y).join('-');
                    this.pairs[key] ??= {};
                    this.pairs[key][type] = parseFloat(input.value);
                });
                const n = this.criteriaIds.length;
                if (n < 2) return;
                const required = (n * (n - 1)) / 2;
                const actual = Object.values(this.pairs).filter(p => !isNaN(p.a_ij)).length;
                if (actual < required) return;
                const index = Object.fromEntries(this.criteriaIds.map((id, i) => [id, i]));
                const M = Array.from({
                    length: n
                }, () => Array(n).fill(1));
                Object.entries(this.pairs).forEach(([key, p]) => {
                    const [a, b] = key.split('-').map(Number);
                    M[index[a]][index[b]] = p.a_ij;
                    M[index[b]][index[a]] = p.a_ji;
                });
                let W = Array(n).fill(1 / n);
                for (let iter = 0; iter < 100; iter++) {
                    const Wnext = M.map(row => row.reduce((sum, val, j) => sum + val * W[j], 0));
                    const sum = Wnext.reduce((a, b) => a + b, 0);
                    const Wnorm = Wnext.map(v => v / sum);
                    if (Wnorm.reduce((s, v, i) => s + Math.abs(v - W[i]), 0) < 1e-8) {
                        W = Wnorm;
                        break;
                    }
                    W = Wnorm;
                }
                const lambdaMax = M.map((row, i) => row.reduce((sum, val, j) => sum + val * W[j], 0) / W[i]).reduce((a,
                    b) => a + b, 0) / n;
                const CI = (lambdaMax - n) / (n - 1);
                const RI = {
                    1: 0,
                    2: 0,
                    3: 0.58,
                    4: 0.9,
                    5: 1.12,
                    6: 1.24,
                    7: 1.32,
                    8: 1.41,
                    9: 1.45,
                    10: 1.49
                } [n] ?? 1.49;
                this.cr = (RI === 0 || CI < 0) ? 0 : CI / RI;
                this.status = (this.cr <= 0.10) ? 'ok' : 'block';
                if (this.status === 'block') {
                    this.offenders = Object.entries(this.pairs).map(([key, p]) => {
                        const [a, b] = key.split('-').map(Number);
                        const expected = W[index[a]] / W[index[b]];
                        return {
                            key,
                            dev: Math.abs(Math.log(p.a_ij / expected))
                        };
                    }).sort((a, b) => b.dev - a.dev).slice(0, 2).map(d => d.key);
                }
            }
        }
    }
</script>
