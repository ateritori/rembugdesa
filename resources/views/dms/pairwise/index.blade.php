@php
    $criterias = $criterias ?? collect();
    $existingData = $existingPairwise ?? [];
    $isEditMode = !empty($existingData);

    // Persiapan data awal
    if (!$isEditMode) {
        // Jika Mode Create (Kosong), siapkan state default & wajib disentuh
        foreach ($criterias as $i => $ci) {
            foreach ($criterias as $j => $cj) {
                if ($i < $j) {
                    $key = min($ci->id, $cj->id) . '-' . max($ci->id, $cj->id);
                    $existingData[$key] = [
                        'id_i' => $ci->id,
                        'id_j' => $cj->id,
                        'pos' => 9,
                        'touched' => false, // WAJIB geser di mode Create
                    ];
                }
            }
        }
    } else {
        // Jika Mode UPDATE, set semua 'touched' jadi true agar tombol simpan aktif
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

    /* Efek visual hanya untuk yang belum disentuh (khusus Create Mode) */
    .pulse-slider::-webkit-slider-thumb {
        box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.2);
        border-color: #f59e0b !important;
    }
</style>

<div
    class="adaptive-card border-t-primary relative space-y-6 overflow-hidden border-t-4 bg-white/80 p-4 shadow-xl backdrop-blur-md md:p-6">
    <form method="POST" action="{{ route('decision-sessions.pairwise.store', $decisionSession->id) }}"
        x-data="crState({{ json_encode($existingData) }}, {{ json_encode($criterias->pluck('id')) }}, {{ json_encode($criterias->pluck('name', 'id')) }})" x-init="initData()">
        @csrf

        <input type="hidden" name="cr_value" :value="cr">
        <input type="hidden" name="final_weights" :value="JSON.stringify(weightsMap)">

        <div class="space-y-4">
            <template x-for="(pair, key) in pairs" :key="key">
                <div class="grid grid-cols-12 items-center gap-3 rounded-2xl border bg-white/40 p-4 transition-all duration-300 md:p-5"
                    :class="{
                        'border-amber-500 bg-amber-500/5 shadow-md scale-[1.01] z-10': offenders.includes(key),
                        'border-slate-100 hover:border-primary/20': !offenders.includes(key),
                        'border-dashed border-amber-300': !pair.touched
                    }">

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
                                <div class="shadow-lg flex h-10 w-10 rotate-2 items-center justify-center rounded-xl transition-colors duration-300"
                                    :class="!pair.touched ? 'bg-amber-500 shadow-amber-200' : 'bg-primary shadow-primary/20'">
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

                        {{-- Slider --}}
                        <div class="relative flex h-8 items-center px-1">
                            <input type="range" min="1" max="17" step="1" x-model.number="pair.pos"
                                @input="
                                    pair.touched = true;
                                    const res = getVal(pair.pos);
                                    pair.a_ij = res.a_ij;
                                    pair.a_ji = res.a_ji;
                                    recalculate();
                                "
                                class="accent-primary cursor-pointer" :class="!pair.touched ? 'pulse-slider' : ''">
                        </div>

                        {{-- Skala Angka --}}
                        <div class="overflow-hidden">
                            <div class="grid select-none text-[10px] font-bold"
                                style="grid-template-columns: repeat(18, minmax(0, 1fr));">
                                <template x-for="n in [9,8,7,6,5,4,3,2,1]" :key="'l' + n">
                                    <div @click="
                                        pair.pos = 10-n;
                                        pair.touched = true;
                                        const res = getVal(pair.pos);
                                        pair.a_ij = res.a_ij;
                                        pair.a_ji = res.a_ji;
                                        recalculate();
                                    "
                                        class="cursor-pointer text-center transition-all hover:text-primary"
                                        :class="getVal(pair.pos).dir === 'left' && getVal(pair.pos).val == n ?
                                            'text-primary scale-150' : 'text-slate-300'"
                                        x-text="n"></div>
                                </template>
                                <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="'r' + n">
                                    <div @click="
                                        pair.pos = 9+n;
                                        pair.touched = true;
                                        const res = getVal(pair.pos);
                                        pair.a_ij = res.a_ij;
                                        pair.a_ji = res.a_ji;
                                        recalculate();
                                    "
                                        class="cursor-pointer text-center transition-all hover:text-primary"
                                        :class="getVal(pair.pos).dir === 'right' && getVal(pair.pos).val == n ?
                                            'text-primary scale-150' : 'text-slate-300'"
                                        x-text="n"></div>
                                </template>
                            </div>
                        </div>

                        <input type="hidden" :name="'pairwise[' + pair.id_i + '][' + pair.id_j + '][a_ij]'"
                            :value="pair.a_ij">
                        <input type="hidden" :name="'pairwise[' + pair.id_i + '][' + pair.id_j + '][a_ji]'"
                            :value="pair.a_ji">
                    </div>
                </div>
            </template>
        </div>

        {{-- Footer --}}
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
                    <template x-if="untouchedCount > 0">
                        <span class="text-amber-500">⏳ Mode Baru: Lengkapi <span x-text="untouchedCount"></span> poin
                            lagi</span>
                    </template>
                    <template x-if="untouchedCount === 0">
                        <div>
                            <span x-show="cr > 0.101" class="text-rose-500 animate-pulse">⚠️ CR > 0.1 (Kurang
                                Konsisten)</span>
                            <span x-show="cr <= 0.101" class="text-emerald-500">✅ Data Siap Disimpan</span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex w-full gap-3 md:w-auto">
                <a href="{{ $isEditMode
                    ? route('dms.index', ['decisionSession' => $decisionSession->id, 'tab' => 'penilaian-kriteria'])
                    : route('dms.index', $decisionSession->id) }}"
                    class="w-full rounded-xl border border-slate-300 px-10 py-4 text-xs font-black uppercase text-slate-600 transition-all hover:bg-slate-100 md:w-auto">
                    Batal
                </a>

                <button type="submit"
                    class="w-full rounded-xl px-12 py-4 text-xs font-black uppercase text-white shadow-lg transition-all md:w-auto disabled:cursor-not-allowed"
                    :class="(cr <= 0.101 && untouchedCount === 0) ? 'bg-primary hover:scale-105 active:scale-95' :
                    'bg-slate-300 opacity-50'"
                    :disabled="cr > 0.101 || untouchedCount > 0">
                    <span x-text="untouchedCount > 0 ? 'Input Belum Lengkap' : 'Simpan Perubahan'"></span>
                </button>
            </div>
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
            untouchedCount: 0,

            initData() {
                // Inisialisasi a_ij dan a_ji agar tidak undefined (mode create & edit)
                Object.values(this.pairs).forEach(p => {
                    const res = this.getVal(p.pos);
                    p.a_ij = res.a_ij;
                    p.a_ji = res.a_ji;
                });

                this.recalculate();
            },

            getVal(pos) {
                const center = 9; // titik netral
                const distance = Math.abs(pos - center);
                const value = distance + 1; // skala 1–9

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

                console.log("JS criteriaIds (order):", JSON.stringify(this.criteriaIds));
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

                console.log("JS Matrix:", JSON.stringify(M));
                let W = Array(n).fill(1 / n);
                for (let i = 0; i < 100; i++) {
                    let nextW = M.map(row => row.reduce((acc, v, j) => acc + v * W[j], 0));
                    let sum = nextW.reduce((a, b) => a + b, 0);

                    if (sum > 0) {
                        W = nextW.map(v => v / sum);
                    }
                }
                this.weightsMap = {};
                this.criteriaIds.forEach((id, i) => this.weightsMap[id] = W[i]);

                const Aw = M.map((row, i) => row.reduce((acc, v, j) => acc + v * W[j], 0));
                const lambdaMax = Aw.reduce((acc, val, i) => acc + (val / W[i]), 0) / n;
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
