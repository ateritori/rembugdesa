@php
    $criterias = $criterias ?? ($criteria ?? collect());
    $existingPairwise = $existingPairwise ?? [];
@endphp

{{-- Menambahkan border atas tebal untuk identitas warna --}}
<div class="adaptive-card p-8 shadow-2xl space-y-8 border-t-4 border-t-primary relative overflow-hidden">
    {{-- Dekorasi background agar tidak pucat --}}
    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>

    @if ($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
            class="rounded-xl border-2 border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-600 font-black flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('decision-sessions.pairwise.store', $decisionSession->id) }}"
        x-data="crState()" x-init="$nextTick(() => { isDirty = {{ count($existingPairwise) > 0 ? 'true' : 'false' }};
            recalculate(); })" @pairwise-changed.window="recalculate()">
        @csrf

        <input type="hidden" name="debug_frontend" :value="JSON.stringify(pairs)">

        <div class="space-y-6">
            @foreach ($criterias as $i => $ci)
                @foreach ($criterias as $j => $cj)
                    @if ($i < $j)
                        @php
                            $pairKey = min($ci->id, $cj->id) . '-' . max($ci->id, $cj->id);
                            $existing = $existingPairwise[$pairKey] ?? null;
                        @endphp

                        {{-- Perkuatan kontras pada Row --}}
                        <div class="grid grid-cols-12 items-center gap-4 p-6 rounded-3xl border-2 transition-all duration-500"
                            :class="{
                                'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/20 scale-[1.01] z-10': status === 'block' &&
                                    offenders.includes('{{ $pairKey }}'),
                                'border-app/50 bg-app/40 hover:border-primary/40': !offenders.includes(
                                    '{{ $pairKey }}')
                            }">

                            <div class="col-span-12 space-y-6" x-data="{
                                pos: {{ $existing
                                    ? ($existing->direction === 'left'
                                        ? 10 - min(9, max(1, $existing->value))
                                        : 9 + min(9, max(1, $existing->value)))
                                    : 9 }},
                                get direction() { return this.pos <= 9 ? 'left' : 'right'; },
                                get value() {
                                    const v = this.pos <= 9 ? 10 - this.pos : this.pos - 9;
                                    return Math.min(9, Math.max(1, v));
                                }
                            }">

                                {{-- Header Label dengan kontras tinggi --}}
                                <div class="flex justify-between items-center px-2">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-1">Kriteria
                                            A</span>
                                        <span class="text-sm font-black transition-all duration-300"
                                            :class="direction === 'left' ? 'text-primary scale-110 origin-left' :
                                                'text-app opacity-30'">
                                            {{ $ci->name }}
                                        </span>
                                    </div>

                                    {{-- Center Badge yang Bold --}}
                                    <div
                                        class="w-12 h-12 bg-primary flex items-center justify-center rounded-2xl shadow-lg shadow-primary/30 rotate-3 transition-transform group-hover:rotate-0">
                                        <span class="text-xl font-black text-white -rotate-3" x-text="value"></span>
                                    </div>

                                    <div class="flex flex-col items-end">
                                        <span
                                            class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-1">Kriteria
                                            B</span>
                                        <span class="text-sm font-black transition-all duration-300 text-right"
                                            :class="direction === 'right' ? 'text-primary scale-110 origin-right' :
                                                'text-app opacity-30'">
                                            {{ $cj->name }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Slider Input - Dibuat lebih tebal --}}
                                <div class="relative flex items-center px-1">
                                    <input type="range" min="1" max="18" step="1" x-model="pos"
                                        @input="isDirty = true; $dispatch('pairwise-changed')"
                                        class="w-full h-2.5 bg-app rounded-full appearance-none cursor-pointer accent-primary border border-white/5">
                                </div>

                                {{-- Skala Angka (Clickable) - Dibuat Bold & Berwarna --}}
                                <div class="grid text-xs font-black select-none items-center"
                                    style="grid-template-columns: repeat(18, minmax(0, 1fr));">

                                    <template x-for="n in [9,8,7,6,5,4,3,2,1]" :key="'l' + n">
                                        <div class="text-center transition-all cursor-pointer hover:text-primary hover:scale-150 py-2"
                                            @click="pos = 10 - n; isDirty = true; $dispatch('pairwise-changed')"
                                            :class="direction === 'left' && value == n ?
                                                'text-primary scale-150 opacity-100' :
                                                'text-app opacity-20'"
                                            x-text="n"></div>
                                    </template>

                                    <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="'r' + n">
                                        <div class="text-center transition-all cursor-pointer hover:text-primary hover:scale-150 py-2"
                                            @click="pos = 9 + n; isDirty = true; $dispatch('pairwise-changed')"
                                            :class="direction === 'right' && value == n ?
                                                'text-primary scale-150 opacity-100' :
                                                'text-app opacity-20'"
                                            x-text="n"></div>
                                    </template>
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

        {{-- Action Footer dengan High Contrast --}}
        <div class="pt-10 flex flex-col md:flex-row items-center justify-between border-t-2 border-app mt-10 gap-6">
            <div class="flex items-center gap-6">
                {{-- CR Display yang Mencolok --}}
                <div class="relative group">
                    <div
                        class="absolute inset-0 bg-primary blur-xl opacity-10 group-hover:opacity-20 transition-opacity">
                    </div>
                    <div class="relative px-6 py-3 bg-app/50 rounded-2xl border-2 flex flex-col items-center min-w-[120px]"
                        :class="status === 'ok' ? 'border-emerald-500' : 'border-rose-500'">
                        <span class="text-[9px] font-black uppercase opacity-60 tracking-widest text-app">Ratio
                            Konsistensi</span>
                        <span class="text-2xl font-black"
                            :class="status === 'ok' ? 'text-emerald-500' : 'text-rose-500'"
                            x-text="isNaN(cr) ? '0.0000' : cr.toFixed(4)"></span>
                    </div>
                </div>

                <div class="max-w-xs">
                    <template x-if="!isDirty">
                        <p
                            class="text-[11px] font-black text-amber-500 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Input Penilaian Kriteria
                        </p>
                    </template>
                    <template x-if="isDirty && status === 'block'">
                        <p class="text-[11px] font-black text-rose-500 uppercase leading-tight tracking-widest italic">
                            ⚠️ Tidak Konsisten (CR > 0.1)<br><span class="opacity-60">Sesuaikan baris yang menyala
                                oranye</span>
                        </p>
                    </template>
                    <template x-if="isDirty && status === 'ok'">
                        <p class="text-[11px] font-black text-emerald-500 uppercase leading-tight tracking-widest">
                            ✅ Data Sangat Konsisten<br><span class="opacity-60">Penilaian siap untuk disimpan</span>
                        </p>
                    </template>
                </div>
            </div>

            <button type="submit"
                class="w-full md:w-auto px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-2xl bg-primary text-white disabled:opacity-30 disabled:grayscale disabled:cursor-not-allowed hover:scale-105 active:scale-95 shadow-primary/30"
                :disabled="!canSubmit()">
                <span x-text="!isDirty ? 'Lengkapi Penilaian' : 'Simpan Penilaian'"></span>
            </button>
        </div>
    </form>
</div>

<script>
    // Logic crState tetap sama persis sesuai permintaan (Hanya script recalculate yang ada di dalam)
    function crState() {
        return {
            criteriaIds: @json($criterias->pluck('id')->values()),
            cr: NaN,
            status: 'block',
            isDirty: {{ count($existingPairwise) > 0 ? 'true' : 'false' }},
            offenders: [],
            pairs: {},
            actualPairCount: 0,
            requiredPairCount: 0,
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
                this.requiredPairCount = (n * (n - 1)) / 2;
                this.actualPairCount = Object.values(this.pairs).filter(p => !isNaN(p.a_ij)).length;
                if (this.actualPairCount < this.requiredPairCount) return;
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
                this.status = this.cr <= 0.10 ? 'ok' : 'block';
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
