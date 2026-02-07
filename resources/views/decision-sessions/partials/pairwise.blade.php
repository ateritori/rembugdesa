<div class="bg-card p-6 rounded shadow space-y-6">
    @if ($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
            class="rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('decision-sessions.pairwise.store', $decisionSession->id) }}"
        x-data="crState()" x-init="$nextTick(() => recalculate())" @pairwise-changed.window="recalculate()">
        @csrf

        {{-- Data yang dikirim ke Controller --}}
        <input type="hidden" name="debug_frontend" :value="JSON.stringify(pairs)">

        <div class="space-y-4">
            @foreach ($criterias as $i => $ci)
                @foreach ($criterias as $j => $cj)
                    @if ($i < $j)
                        @php
                            $pairKey = min($ci->id, $cj->id) . '-' . max($ci->id, $cj->id);
                            $existing = $existingPairwise[$pairKey] ?? null;
                        @endphp
                        <div class="grid grid-cols-12 items-center gap-4 p-3 rounded transition"
                            :class="{
                                'ring-1 ring-yellow-300 bg-yellow-50/30': status === 'block' && offenders.includes(
                                    '{{ $pairKey }}'),
                                'bg-gray-50': !offenders.includes('{{ $pairKey }}')
                            }">

                            <div class="col-span-12 space-y-3" x-data="{
                                pos: {{ $existing ? ($existing->direction === 'left' ? 10 - $existing->value : 9 + $existing->value) : 9 }},
                                get direction() { return this.pos <= 9 ? 'left' : 'right'; },
                                get value() {
                                    const v = this.pos <= 9 ? 10 - this.pos : this.pos - 9;
                                    return Math.min(9, Math.max(1, v));
                                }
                            }">

                                <div class="flex justify-between text-sm font-medium text-gray-600 px-1">
                                    <span
                                        :class="direction === 'left' ? 'text-primary font-bold' : ''">{{ $ci->name }}</span>
                                    <span
                                        :class="direction === 'right' ? 'text-primary font-bold' : ''">{{ $cj->name }}</span>
                                </div>

                                <input type="range" min="1" max="18" step="1" x-model="pos"
                                    @input="isDirty = true; $dispatch('pairwise-changed')"
                                    class="w-full accent-primary cursor-pointer">

                                <div class="grid text-xs select-none items-center"
                                    style="grid-template-columns: repeat(18, minmax(0, 1fr));">
                                    <template x-for="n in [9,8,7,6,5,4,3,2,1]" :key="'l' + n">
                                        <div class="text-center"
                                            :class="direction === 'left' && value == n ? 'text-primary font-bold' :
                                                'text-gray-400'"
                                            x-text="n"></div>
                                    </template>
                                    <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="'r' + n">
                                        <div class="text-center"
                                            :class="direction === 'right' && value == n ? 'text-primary font-bold' :
                                                'text-gray-400'"
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

        <div class="pt-6 flex items-center justify-between border-t mt-6">
            <div class="text-sm">
                <template x-if="!isDirty">
                    <span class="text-amber-600 font-medium">👋 Gunakan slider untuk memberikan penilaian
                        kriteria.</span>
                </template>
                <template x-if="isDirty && status === 'block'">
                    <span class="text-red-600 font-medium">⚠️ Rasio konsistensi (CR) terlalu tinggi (> 0.1). Perbaiki
                        baris kuning.</span>
                </template>
                <template x-if="isDirty && status === 'ok'">
                    <span class="text-green-600 font-medium">✅ Penilaian konsisten dan siap disimpan.</span>
                </template>
            </div>

            <button type="submit"
                class="px-6 py-2 rounded font-bold transition bg-primary text-white disabled:opacity-50 disabled:cursor-not-allowed hover:brightness-110"
                :disabled="!canSubmit()">
                <span x-text="!isDirty ? 'Lengkapi Penilaian' : 'Simpan Penilaian'"></span>
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
            // isDirty otomatis true jika sudah ada data di DB (mode edit)
            isDirty: {{ count($existingPairwise) > 0 ? 'true' : 'false' }},
            offenders: [],
            pairs: {},
            actualPairCount: 0,
            requiredPairCount: 0,
            debugMatrix: [],

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
                this.debugMatrix = M;

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
