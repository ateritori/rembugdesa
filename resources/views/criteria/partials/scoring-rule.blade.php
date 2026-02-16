@if ($decisionSession->status === 'draft')
    @php
        $isEdit = $rule !== null;

        $semantics = $rule ? $rule->getParameter('scale_semantics') ?? [] : [];
        $utilities = $rule ? $rule->getParameter('scale_utilities') ?? [] : [];
        $rangeArr = $rule ? $rule->getParameter('scale_range') ?? ['min' => 1, 'max' => 5] : ['min' => 1, 'max' => 5];

        if (is_string($rangeArr)) {
            $rangeArr = json_decode($rangeArr, true);
        }
        $minVal = $rangeArr['min'] ?? 1;
        $maxVal = $rangeArr['max'] ?? 5;
    @endphp

    <div x-data="scoringRule({
        isEdit: {{ $isEdit ? 'true' : 'false' }},
        inputType: '{{ $rule->input_type ?? '' }}',
        preferenceType: '{{ $rule->preference_type ?? 'linear' }}',
        min: {{ $minVal }},
        max: {{ $maxVal }},
        semantics: @js($semantics),
        utilities: @js($utilities)
    })" :key="'rule-{{ $c->id }}'" class="w-full">

        <form x-show="openScoring" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0" method="POST"
            action="{{ $isEdit ? route('criteria.scoring.update', [$c->id, $rule->id]) : route('criteria.scoring.store', $c->id) }}"
            class="space-y-4 rounded-xl border border-slate-100 bg-white p-4 md:p-5 dark:border-slate-700 dark:bg-slate-800">

            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            {{-- Header Form --}}
            <div
                class="flex flex-col justify-between gap-2 border-b border-slate-50 pb-4 sm:flex-row sm:items-center dark:border-slate-700">
                <div>
                    <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Konfigurasi Parameter
                    </h4>
                    <p class="text-sm font-bold tracking-tight text-slate-800 dark:text-slate-100">{{ $c->name }}
                    </p>
                </div>
                @if ($isEdit)
                    <span
                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50 px-2 py-1 text-[8px] font-black uppercase tracking-wider text-indigo-600 dark:border-indigo-900/50 dark:bg-indigo-900/30 dark:text-indigo-400">
                        <span class="h-1 w-1 animate-pulse rounded-full bg-indigo-500"></span>
                        Update Mode
                    </span>
                @endif
            </div>

            {{-- Konfigurasi Utama --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="ml-1 text-[9px] font-black uppercase tracking-widest text-slate-500">Mekanisme
                        Input</label>
                    <select x-model="inputType" name="input_type" required
                        class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-700 transition-all focus:ring-4 focus:ring-indigo-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <option value="">Pilih Mekanisme</option>
                        <option value="scale">Skala (Pilihan/Likert)</option>
                        <option value="numeric">Input Angka Langsung</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="ml-1 text-[9px] font-black uppercase tracking-widest text-slate-500">Fungsi
                        Utilitas</label>
                    <select x-model="preferenceType" name="preference_type"
                        class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-700 transition-all focus:ring-4 focus:ring-indigo-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <option value="linear">Linear (Stabil)</option>
                        <option value="concave">Concave (Menurun)</option>
                        <option value="convex">Convex (Meningkat)</option>
                    </select>
                </div>
            </div>

            {{-- Rentang Skala --}}
            <div x-show="inputType === 'scale'" x-collapse
                class="grid grid-cols-2 gap-3 rounded-xl border border-dashed border-slate-200 bg-slate-50/50 p-4 dark:border-slate-700 dark:bg-slate-900/50">
                <div class="space-y-1">
                    <label class="ml-1 text-[9px] font-bold uppercase text-slate-500">Min</label>
                    <input type="number" x-model.number="min" name="scale_min"
                        class="w-full rounded-lg border-slate-200 px-3 py-2 text-xs font-bold dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                </div>
                <div class="space-y-1">
                    <label class="ml-1 text-[9px] font-bold uppercase text-slate-500">Max</label>
                    <input type="number" x-model.number="max" name="scale_max"
                        class="w-full rounded-lg border-slate-200 px-3 py-2 text-xs font-bold dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                </div>
            </div>

            {{-- Daftar Input Dinamis --}}
            <div class="space-y-2" x-show="inputType === 'scale'" x-collapse>
                <div class="grid grid-cols-1 gap-2">
                    <template x-for="i in range()" :key="i">
                        <div
                            class="group flex flex-col items-stretch gap-3 rounded-xl border border-slate-100 p-2 hover:bg-indigo-50/30 sm:flex-row sm:items-center dark:border-slate-700 dark:hover:bg-indigo-900/20">
                            <div class="flex h-8 w-8 flex-none items-center justify-center rounded-lg bg-slate-800 text-[10px] font-black text-white"
                                x-text="i"></div>
                            <div class="min-w-0 flex-1">
                                <input type="text" :name="`semantics[${i}]`" x-model="semantics[i]"
                                    placeholder="Label semantik..."
                                    class="w-full border-none bg-transparent py-1 text-xs font-bold text-slate-800 focus:ring-0 dark:text-slate-200">
                            </div>
                            <div
                                class="flex items-center gap-2 rounded-lg border border-slate-50 bg-white px-2 py-1 dark:border-slate-700 dark:bg-slate-900">
                                <span class="text-[8px] font-black uppercase text-slate-400">Util:</span>
                                <input type="number" step="0.01" :name="`utilities[${i}]`" x-model="utilities[i]"
                                    class="w-16 rounded-md border-none bg-slate-50 py-1 text-center text-[10px] font-black text-indigo-600 focus:ring-0 dark:bg-slate-800 dark:text-indigo-400">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Aksi Form --}}
            <div
                class="flex flex-col items-center justify-end gap-2 border-t border-slate-50 pt-4 sm:flex-row dark:border-slate-700">
                <button type="button" @click="openScoring = false"
                    class="w-full rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all sm:w-auto dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500">
                    Batal
                </button>

                <button type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-8 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-md hover:bg-indigo-700 transition-all sm:w-auto">
                    {{ $isEdit ? 'Simpan' : 'Tetapkan' }}
                </button>
            </div>
        </form>
    </div>
@endif
