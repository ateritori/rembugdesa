@php
    $criterias = $criterias ?? collect();
    $existingData = $existingPairwise ?? [];
    $isEditMode = !empty($existingData);

    if (!$isEditMode) {
        foreach ($criterias as $i => $ci) {
            foreach ($criterias as $j => $cj) {
                if ($i < $j) {
                    $key = min($ci->id, $cj->id) . '-' . max($ci->id, $cj->id);
                    $existingData[$key] = [
                        'id_i' => $ci->id,
                        'id_j' => $cj->id,
                        'pos' => 9,
                        'touched' => false,
                    ];
                }
            }
        }
    } else {
        foreach ($existingData as $key => $val) {
            $existingData[$key]['touched'] = true;
        }
    }
@endphp

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Custom Slider High Contrast */
    .range-input {
        -webkit-appearance: none;
        background: transparent;
        width: 100%;
        height: 32px;
    }

    .range-input::-webkit-slider-runnable-track {
        width: 100%;
        height: 10px;
        background: #f1f5f9;
        border: 2px solid #0f172a;
        border-radius: 999px;
    }

    .range-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 24px;
        width: 24px;
        border-radius: 50%;
        background: #ffffff;
        border: 4px solid #0f172a;
        margin-top: -9px;
        cursor: pointer;
        box-shadow: 0 4px 0px 0px #0f172a;
        transition: all 0.1s ease;
    }

    .range-input:active::-webkit-slider-thumb {
        transform: scale(1.1);
        background: #0f172a;
    }

    .pulse-slider::-webkit-slider-thumb {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.2);
    }
</style>

<div
    class="relative space-y-6 overflow-hidden rounded-2xl border-4 border-slate-900 bg-white p-6 shadow-[12px_12px_0px_0px_rgba(15,23,42,1)] md:p-10">

    {{-- Header Dokumen Tesis --}}
    <div class="border-b-4 border-slate-900 pb-6">
        <h2 class="text-2xl font-black tracking-tight text-slate-900">Matriks Perbandingan Berpasangan (Pairwise)</h2>
        <div class="mt-2 flex flex-wrap gap-2">
            <span class="bg-slate-900 px-3 py-1 text-[10px] font-black uppercase text-white">Metode: AHP</span>
            <span
                class="bg-slate-100 border-2 border-slate-900 px-3 py-1 text-[10px] font-black uppercase text-slate-900">Skala:
                Saaty (1-9)</span>
        </div>
    </div>

    <form method="POST" action="{{ route('decision-sessions.pairwise.store', $decisionSession->id) }}"
        x-data="crState({{ json_encode($existingData) }}, {{ json_encode($criterias->pluck('id')) }}, {{ json_encode($criterias->pluck('name', 'id')) }})" x-init="initData()">
        @csrf

        <input type="hidden" name="cr_value" :value="cr">
        <input type="hidden" name="final_weights" :value="JSON.stringify(weightsMap)">

        <div class="space-y-8">
            <template x-for="(pair, key) in pairs" :key="key">
                <div class="relative rounded-2xl border-2 border-slate-900 bg-white p-6 shadow-sm transition-all"
                    :class="offenders.includes(key) ? 'bg-amber-50 ring-4 ring-amber-500' : ''">

                    <div class="space-y-6">
                        {{-- Label Kriteria --}}
                        <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                            <div class="w-full md:w-[40%]">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Kriteria
                                    A</span>
                                <h3 class="text-base font-black uppercase text-slate-900"
                                    :class="getVal(pair.pos).dir === 'left' ? 'text-blue-700' : ''"
                                    x-text="criteriaNames[pair.id_i]"></h3>
                            </div>

                            <div class="flex shrink-0 items-center justify-center">
                                <div class="flex h-14 w-14 items-center justify-center rounded-xl border-4 border-slate-900 bg-slate-900 text-2xl font-black text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)]"
                                    :class="!pair.touched ? 'bg-amber-500 border-amber-600' : ''">
                                    <span x-text="getVal(pair.pos).val"></span>
                                </div>
                            </div>

                            <div class="w-full text-right md:w-[40%]">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Kriteria
                                    B</span>
                                <h3 class="text-base font-black uppercase text-slate-900"
                                    :class="getVal(pair.pos).dir === 'right' ? 'text-blue-700' : ''"
                                    x-text="criteriaNames[pair.id_j]"></h3>
                            </div>
                        </div>

                        {{-- Slider Input --}}
                        <div class="space-y-3 px-2">
                            <input type="range" min="1" max="17" step="1" x-model.number="pair.pos"
                                @input="
                                    pair.touched = true;
                                    const res = getVal(pair.pos);
                                    pair.a_ij = res.a_ij;
                                    pair.a_ji = res.a_ji;
                                    recalculate();
                                "
                                class="range-input" :class="!pair.touched ? 'pulse-slider' : ''">

                            {{-- Skala Angka 9-1-9 --}}
                            <div
                                class="grid grid-cols-[repeat(17,minmax(0,1fr))] text-[11px] font-black text-slate-300">
                                <template x-for="n in [9,8,7,6,5,4,3,2]" :key="'l' + n">
                                    <div class="text-center transition-all"
                                        :class="getVal(pair.pos).dir === 'left' && getVal(pair.pos).val == n ?
                                            'text-slate-900 scale-150' : ''"
                                        x-text="n"></div>
                                </template>
                                <div class="text-center transition-all"
                                    :class="getVal(pair.pos).val == 1 ? 'text-slate-900 scale-150' : ''">1</div>
                                <template x-for="n in [2,3,4,5,6,7,8,9]" :key="'r' + n">
                                    <div class="text-center transition-all"
                                        :class="getVal(pair.pos).dir === 'right' && getVal(pair.pos).val == n ?
                                            'text-slate-900 scale-150' : ''"
                                        x-text="n"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" :name="'pairwise[' + pair.id_i + '][' + pair.id_j + '][a_ij]'"
                        :value="pair.a_ij">
                    <input type="hidden" :name="'pairwise[' + pair.id_i + '][' + pair.id_j + '][a_ji]'"
                        :value="pair.a_ji">
                </div>
            </template>
        </div>

        {{-- Footer Analisis CR --}}
        <div
            class="mt-12 flex flex-col items-center justify-between gap-8 border-t-4 border-slate-900 pt-8 md:flex-row">
            <div class="flex items-center gap-6">
                <div class="border-4 border-slate-900 bg-white p-5 shadow-[6px_6px_0px_0px_rgba(15,23,42,1)]"
                    :class="cr <= 0.101 ? 'bg-emerald-50' : 'bg-rose-50'">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500">Consistency
                        Ratio (CR)</span>
                    <span class="text-4xl font-black leading-none text-slate-900" x-text="cr.toFixed(4)"></span>
                </div>

                <div class="max-w-[240px] space-y-1">
                    <template x-if="untouchedCount > 0">
                        <span class="text-xs font-black uppercase text-amber-600">⏳ Menunggu Input: <span
                                x-text="untouchedCount"></span> Data</span>
                    </template>
                    <template x-if="untouchedCount === 0">
                        <div class="flex items-center gap-2">
                            <span x-show="cr <= 0.101" class="text-xs font-black uppercase text-emerald-600">✅ Konsisten
                                (Valid)</span>
                            <span x-show="cr > 0.101" class="text-xs font-black uppercase text-rose-600">⚠️ Tidak
                                Konsisten (> 0.1)</span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex w-full gap-4 md:w-auto">
                <a href="{{ $isEditMode ? route('dms.index', ['decisionSession' => $decisionSession->id, 'tab' => 'penilaian-kriteria']) : route('dms.index', $decisionSession->id) }}"
                    class="flex-1 border-2 border-slate-900 bg-white px-8 py-4 text-xs font-black uppercase text-slate-900 transition-all hover:bg-slate-50 md:w-auto">
                    Batal
                </a>

                <button type="submit"
                    class="flex-[2] border-4 border-slate-900 px-10 py-4 text-xs font-black uppercase shadow-[6px_6px_0px_0px_rgba(15,23,42,1)] transition-all md:w-auto"
                    :class="(cr <= 0.101 && untouchedCount === 0) ?
                    'bg-slate-900 text-white hover:translate-x-[3px] hover:translate-y-[3px] hover:shadow-none' :
                    'bg-slate-200 text-slate-400 pointer-events-none'"
                    :disabled="cr > 0.101 || untouchedCount > 0">
                    Simpan Perhitungan
                </button>
            </div>
        </div>
    </form>
</div>

{{-- FULL JAVASCRIPT - TIDAK DIHAPUS --}}
<script>
    function crState(initialPairs, criteriaIds, criteriaNames) {
        return {
            pairs: initialPairs,
            criteriaIds: criteriaIds,
            criteriaNames: criteriaNames,
            cr: 0,
            weightsMap: {},
            offenders: [],
            untouchedCount: 0,

            initData() {
                Object.values(this.pairs).forEach(p => {
                    const res = this.getVal(p.pos);
                    p.a_ij = res.a_ij;
                    p.a_ji = res.a_ji;
                });
                this.recalculate();
            },

            getVal(pos) {
                const center = 9;
                const distance = Math.abs(pos - center);
                const value = distance + 1;
                if (pos < center) {
                    return {
                        dir: 'left',
                        val: value,
                        a_ij: value,
                        a_ji: 1 / value
                    };
                } else if (pos > center) {
                    return {
                        dir: 'right',
                        val: value,
                        a_ij: 1 / value,
                        a_ji: value
                    };
                } else {
                    return {
                        dir: 'equal',
                        val: 1,
                        a_ij: 1,
                        a_ji: 1
                    };
                }
            },

            recalculate() {
                this.untouchedCount = Object.values(this.pairs).filter(p => !p.touched).length;
                const n = this.criteriaIds.length;
                if (n < 2) return;

                const idxMap = {};
                this.criteriaIds.forEach((id, i) => idxMap[id] = i);
                const M = Array.from({
                    length: n
                }, () => Array(n).fill(1));

                Object.values(this.pairs).forEach(p => {
                    const i = idxMap[p.id_i];
                    const j = idxMap[p.id_j];
                    if (i === undefined || j === undefined) return;
                    const aij = p.a_ij ?? this.getVal(p.pos).a_ij;
                    const aji = p.a_ji ?? this.getVal(p.pos).a_ji;
                    if (i < j) {
                        M[i][j] = aij;
                        M[j][i] = aji;
                    } else {
                        M[i][j] = aji;
                        M[j][i] = aij;
                    }
                });

                let W = [];
                for (let i = 0; i < n; i++) {
                    let logSum = 0;
                    for (let j = 0; j < n; j++) {
                        logSum += Math.log(M[i][j]);
                    }
                    W[i] = Math.exp(logSum / n);
                }

                let sum = W.reduce((a, b) => a + b, 0);
                if (sum > 0) {
                    W = W.map(v => v / sum);
                }
                this.weightsMap = {};
                this.criteriaIds.forEach((id, i) => this.weightsMap[id] = W[i]);

                const Aw = M.map((row, i) => row.reduce((acc, v, j) => acc + v * W[j], 0));
                const lambdaMax = Aw.reduce((acc, val, i) => (W[i] === 0) ? acc : acc + (val / W[i]), 0) / n;
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
