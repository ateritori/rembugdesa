@if ($decisionSession->status === 'draft')
    @php
        $dbRange = $rule?->getParameter('scale_range');
        $dbSemantics = $rule?->getParameter('scale_semantics') ?? new stdClass();
        $dbUtilities = $rule?->getParameter('scale_utilities') ?? new stdClass();
    @endphp

    <form x-show="openScoring" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak
        method="POST" action="{{ route('criteria.scoring.store', $c->id) }}" x-data="{
            inputType: '{{ $rule?->input_type ?? '' }}',
            utilityType: '{{ $rule?->preference_type ?? '' }}',
            scaleMin: {{ $dbRange['min'] ?? 1 }},
            scaleMax: {{ $dbRange['max'] ?? 5 }},
            utilities: @json($dbUtilities),
            semantics: @json($dbSemantics)
        }"
        x-effect="if (utilityType) { utilities = {}; }"
        class="mt-4 p-6 rounded-2xl border border-app bg-card shadow-xl shadow-primary/5 space-y-6 relative overflow-hidden"
        @click.outside="openScoring = false">

        @csrf

        {{-- Aksesori Dekoratif --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>

        <div class="flex items-center gap-2 border-b border-app pb-4">
            <div class="p-2 bg-primary/10 rounded-lg">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
            </div>
            <div>
                <h4 class="font-black text-app tracking-tight">Aturan Penilaian</h4>
                <p class="text-[10px] uppercase font-bold text-primary tracking-widest">{{ $c->name }}</p>
            </div>
        </div>

        {{-- Konfigurasi Utama --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5">
                <label class="text-[11px] font-black text-app uppercase tracking-widest opacity-60">Jenis Input</label>
                <select name="input_type" x-model="inputType"
                    class="w-full bg-app/50 border-app rounded-xl text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                    <option value="">Pilih Mekanisme</option>
                    <option value="scale">Skala (Pilihan Terukur)</option>
                    <option value="numeric">Angka Bebas (Raw Data)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-black text-app uppercase tracking-widest opacity-60">Tipe Fungsi
                    Utilitas</label>
                <select name="preference_type" x-model="utilityType"
                    class="w-full bg-app/50 border-app rounded-xl text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                    <option value="">Pilih Fungsi</option>
                    <option value="linear">Linear (Stabil)</option>
                    <option value="concave">Konkaf (Sensitif di Awal)</option>
                    <option value="convex">Konveks (Sensitif di Akhir)</option>
                </select>
            </div>
        </div>

        {{-- Range Skala --}}
        <div x-show="inputType === 'scale'" x-transition
            class="p-4 bg-app/30 rounded-2xl border border-app/50 grid grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase opacity-50">Batas Minimum</label>
                <input type="number" name="scale_min" x-model.number="scaleMin"
                    class="w-full bg-card border-app rounded-lg text-sm font-black focus:border-primary">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase opacity-50">Batas Maksimum</label>
                <input type="number" name="scale_max" x-model.number="scaleMax"
                    class="w-full bg-card border-app rounded-lg text-sm font-black focus:border-primary">
            </div>
        </div>

        {{-- Semantik / Label --}}
        <div x-show="inputType === 'scale'" x-transition
            x-effect="if(inputType === 'scale') {
                let min = parseInt(scaleMin); let max = parseInt(scaleMax);
                if (!isNaN(min) && !isNaN(max) && max >= min) {
                    for (let i = min; i <= max; i++) { if (!(i in semantics)) semantics[i] = ''; }
                    Object.keys(semantics).forEach(k => { if (k < min || k > max) delete semantics[k]; });
                }
            }">
            <label class="text-[11px] font-black text-app uppercase tracking-widest opacity-60 block mb-3">Label
                Keterangan (Semantik)</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <template x-for="(label, key) in semantics" :key="key">
                    <div
                        class="flex items-center gap-3 bg-app/20 p-2 rounded-xl border border-app group focus-within:border-primary/50 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center text-xs font-black shadow-sm"
                            x-text="key"></div>
                        <input type="text" :name="`semantics[${key}]`" x-model="semantics[key]"
                            placeholder="Misal: Sangat Baik"
                            class="flex-1 border-none bg-transparent text-xs font-bold focus:ring-0 placeholder:opacity-30">
                    </div>
                </template>
            </div>
        </div>

        {{-- Nilai Utilitas --}}
        <div x-show="inputType === 'scale' && utilityType && utilityType !== 'linear'" x-transition
            x-effect="if(inputType === 'scale' && utilityType !== 'linear') {
                let min = parseInt(scaleMin); let max = parseInt(scaleMax);
                let range = max - min;
                if (range > 0) {
                    for (let i = min; i <= max; i++) {
                        let x = (i - min) / range;
                        let val = utilityType === 'concave' ? Math.sqrt(x) : Math.pow(x, 2);
                        utilities[i] = Number(val.toFixed(2));
                    }
                    Object.keys(utilities).forEach(k => { if (k < min || k > max) delete utilities[k]; });
                }
            }">
            <label
                class="text-[11px] font-black text-app uppercase tracking-widest opacity-60 block mb-3 text-primary">Preview
                Bobot Utilitas (0-1)</label>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <template x-for="(val, key) in utilities" :key="key">
                    <div class="bg-card border border-app rounded-xl p-3 text-center shadow-sm">
                        <p class="text-[9px] font-black opacity-40 uppercase mb-1">Skala <span x-text="key"></span>
                        </p>
                        <input type="number" step="0.01" :name="`utilities[${key}]`" x-model.number="utilities[key]"
                            class="w-full border-none bg-transparent text-center font-black text-sm text-primary p-0 focus:ring-0">
                    </div>
                </template>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pt-6 border-t border-app">
            <p class="text-[10px] font-bold text-app opacity-40 max-w-[200px] leading-tight">Perubahan akan mempengaruhi
                kalkulasi normalisasi akhir.</p>
            <div class="flex gap-3">
                <button type="button" @click="openScoring = false"
                    class="px-5 py-2.5 text-xs font-black uppercase tracking-widest text-app opacity-60 hover:opacity-100 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="px-8 py-2.5 bg-primary text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 active:scale-95 transition-all">
                    Simpan Aturan
                </button>
            </div>
        </div>
    </form>
@endif
