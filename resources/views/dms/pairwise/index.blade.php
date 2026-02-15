@php
    // Inisialisasi data dari Controller
    $criterias = $criterias ?? collect();
    $existingData = $existingPairwise ?? [];

    // Jika Mode Create (Kosong), siapkan state default tengah (pos 9)
    if (empty($existingData)) {
        foreach ($criterias as $i => $ci) {
            foreach ($criterias as $j => $cj) {
                if ($i < $j) {
                    $key = min($ci->id, $cj->id) . '-' . max($ci->id, $cj->id);
                    $existingData[$key] = [
                        'id_i' => $ci->id,
                        'id_j' => $cj->id,
                        'pos' => 9,
                    ];
                }
            }
        }
    }
@endphp

<style>
    /* STYLE ASLI ANDA - TIDAK DIUBAH */
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
        width: 100%;
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
    <form method="POST" action="{{ route('decision-sessions.pairwise.store', $decisionSession->id) }}"
        x-data="crState({{ json_encode($existingData) }}, {{ json_encode($criterias->pluck('id')) }}, {{ json_encode($criterias->pluck('name', 'id')) }})" x-init="initData()">
        @csrf

        {{-- Hidden fields untuk integrasi Backend --}}
        <input type="hidden" name="cr_value" :value="cr">
        <input type="hidden" name="final_weights" :value="JSON.stringify(weightsMap)">

        <div class="space-y-4">
            {{-- Loop menggunakan Template Alpine agar reaktif terhadap data --}}
            <template x-for="(pair, key) in pairs" :key="key">
                <div class="grid grid-cols-12 items-center gap-3 rounded-2xl border bg-white/40 p-4 transition-all duration-300 md:p-5"
                    :class="{ 'border-amber-500 bg-amber-500/5 shadow-md scale-[1.01] z-10': offenders.includes(
                        key), 'border-slate-100 hover:border-primary/20': !offenders.includes(key) }">

                    <div class="col-span-12 space-y-4">
                        <div class="relative flex flex-col items-center justify-between gap-3 px-1 md:flex-row">
                            {{-- Kriteria A --}}
                            <div class="flex w-full flex-row items-center gap-3 md:w-[42%] md:flex-col md:items-start">
                                <span class="text-[8px] font-black uppercase opacity-40">Kriteria A</span>
                                <span class="text-xs font-black md:text-sm"
                                    :class="getVal(pair.pos).dir === 'left' ? 'text-primary' : 'text-slate-400'"
                                    x-text="criteriaNames[pair.id_i]"></span>
                            </div>

                            <div class="relative z-20 flex justify-center md:absolute md:left-1/2 md:-translate-x-1/2">
                                <div
                                    class="bg-primary shadow-primary/20 flex h-10 w-10 rotate-2 items-center justify-center rounded-xl shadow-lg">
                                    <span class="text-base font-black text-white" x-text="getVal(pair.pos).val"></span>
                                </div>
                            </div>

                            {{-- Kriteria B --}}
                            <div
                                class="flex w-full flex-row-reverse items-center gap-3 text-right md:w-[42%] md:flex-col md:items-end">
                                <span class="text-[8px] font-black uppercase opacity-40">Kriteria B</span>
                                <span class="text-xs font-black md:text-sm"
                                    :class="getVal(pair.pos).dir === 'right' ? 'text-primary' : 'text-slate-400'"
                                    x-text="criteriaNames[pair.id_j]"></span>
                            </div>
                        </div>

                        {{-- Input Range (Slider) --}}
                        <div class="relative flex h-8 items-center px-1">
                            <input type="range" min="1" max="18" step="1" x-model.number="pair.pos"
                                @input="recalculate()" class="accent-primary cursor-pointer">
                        </div>

                        {{-- Skala Angka Clickable --}}
                        <div class="overflow-hidden">
                            <div class="grid select-none text-[10px] font-bold"
                                style="grid-template-columns: repeat(18, minmax(0, 1fr));">
                                <template x-for="n in [9,8,7,6,5,4,3,2,1]" :key="'l' + n">
                                    <div @click="pair.pos = 10-n; recalculate()"
                                        class="cursor-pointer text-center transition-all"
                                        :class="getVal(pair.pos).dir === 'left' && getVal(pair.pos).val == n ?
                                            'text-primary scale-150' : 'text-slate-300'"
                                        x-text="n"></div>
                                </template>
                                <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="'r' + n">
                                    <div @click="pair.pos = 9+n; recalculate()"
                                        class="cursor-pointer text-center transition-all"
                                        :class="getVal(pair.pos).dir === 'right' && getVal(pair.pos).val == n ?
                                            'text-primary scale-150' : 'text-slate-300'"
                                        x-text="n"></div>
                                </template>
                            </div>
                        </div>

                        {{-- Hidden inputs untuk dikirim ke Request --}}
                        <input type="hidden" :name="'pairwise[' + pair.id_i + '][' + pair.id_j + '][a_ij]'"
                            :value="getVal(pair.pos).a_ij">
                        <input type="hidden" :name="'pairwise[' + pair.id_i + '][' + pair.id_j + '][a_ji]'"
                            :value="getVal(pair.pos).a_ji">
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer - CR & Submit Button --}}
        <div class="mt-8 flex flex-col items-center justify-between gap-6 border-t border-slate-100 pt-6 md:flex-row">
            <div class="flex items-center gap-5">
                <div class="relative flex min-w-[120px] flex-col items-center rounded-2xl border bg-white px-5 py-3"
                    :class="cr <= 0.101 ? 'border-emerald-500 bg-emerald-50' : 'border-rose-500 bg-rose-50'">
                    <span class="text-[8px] font-black uppercase tracking-wider opacity-60">Consistency Ratio
                        (CR)</span>
                    <span class="text-xl font-black" :class="cr <= 0.101 ? 'text-emerald-600' : 'text-rose-600'"
                        x-text="cr.toFixed(4)"></span>
                </div>
                <div class="text-[10px] font-black uppercase italic">
                    <span x-show="cr > 0.101" class="text-rose-500 animate-pulse">⚠️ CR > 0.1 (Kurang Konsisten)</span>
                    <span x-show="cr <= 0.101" class="text-emerald-500">✅ Konsisten & Siap Simpan</span>
                </div>
            </div>

            <button type="submit"
                class="bg-primary w-full rounded-xl px-12 py-4 text-xs font-black uppercase text-white shadow-lg transition-all hover:scale-105 active:scale-95 disabled:opacity-30 md:w-auto"
                :disabled="cr > 0.101">
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<script>
    function crState(initialPairs, criteriaIds, criteriaNames) {
        return {
            pairs: initialPairs,
            criteriaIds: criteriaIds,
            criteriaNames: criteriaNames,
            cr: 0,
            weightsMap: {},
            offenders: [],

            initData() {
                this.recalculate();
            },

            getVal(pos) {
                const dir = pos <= 9 ? 'left' : 'right';
                const val = pos <= 9 ? (10 - pos) : (pos - 9);
                return {
                    dir: dir,
                    val: val,
                    a_ij: dir === 'left' ? val : (1 / val),
                    a_ji: dir === 'left' ? (1 / val) : val
                };
            },

            recalculate() {
                const n = this.criteriaIds.length;
                if (n < 2) return;

                const idxMap = {};
                this.criteriaIds.forEach((id, i) => idxMap[id] = i);
                const M = Array.from({
                    length: n
                }, () => Array(n).fill(1));

                Object.values(this.pairs).forEach(p => {
                    const res = this.getVal(p.pos);
                    M[idxMap[p.id_i]][idxMap[p.id_j]] = res.a_ij;
                    M[idxMap[p.id_j]][idxMap[p.id_i]] = res.a_ji;
                });

                let W = Array(n).fill(1 / n);
                for (let i = 0; i < 20; i++) {
                    let nextW = M.map(row => row.reduce((acc, v, j) => acc + v * W[j], 0));
                    let sum = nextW.reduce((a, b) => a + b, 0);
                    W = nextW.map(v => v / (sum || 1));
                }
                this.weightsMap = {};
                this.criteriaIds.forEach((id, i) => this.weightsMap[id] = W[i]);

                const Aw = M.map((row, i) => row.reduce((acc, v, j) => acc + v * W[j], 0));
                const lambdaMax = Aw.reduce((acc, val, i) => acc + (val / (W[i] || 1)), 0) / n;
                const CI = (lambdaMax - n) / (Math.max(1, n - 1));
                const RI = [0, 0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49][n] || 1.49;
                this.cr = RI === 0 ? 0 : CI / RI;

                this.offenders = [];
                if (this.cr > 0.101) {
                    let devs = [];
                    for (let i = 0; i < n; i++) {
                        for (let j = i + 1; j < n; j++) {
                            devs.push({
                                key: `${this.criteriaIds[i]}-${this.criteriaIds[j]}`,
                                d: Math.abs(Math.log(M[i][j] / (W[i] / W[j])))
                            });
                        }
                    }
                    this.offenders = devs.sort((a, b) => b.d - a.d).slice(0, 2).map(o => o.key);
                }
            }
        }
    }
</script>
